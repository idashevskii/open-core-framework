<?php

declare(strict_types=1);

/**
 * @license   MIT
 *
 * @author    Ilya Dashevsky
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace OpenCore;

use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Relay\Relay;
use ErrorException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use OpenCore\RouterConfig;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

final class App implements RequestHandlerInterface {

  public const INJECT_SRC_DIR = '$$fcSrcDir';
  private const INJECT_MIDDLEWARES = '$$fcMiddlewares';

  public static function create(
      string $srcDir,
      array $middlewares = null,
      string $config = null,
      string $logger = null,
  ) {

    set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
      throw new ErrorException(message: $errstr, code: 1, severity: $errno, filename: $errfile, line: $errline);
    });

    $injector = Injector::create();
    $injector->alias(ContainerInterface::class, Injector::class);
    $injector->alias(LoggerInterface::class, $logger ?? Logger::class);

    $injector->alias(ResponseFactoryInterface::class, Psr17Factory::class);
    $injector->alias(RequestFactoryInterface::class, Psr17Factory::class);
    $injector->alias(ServerRequestFactoryInterface::class, Psr17Factory::class);
    $injector->alias(UriFactoryInterface::class, Psr17Factory::class);
    $injector->alias(UploadedFileFactoryInterface::class, Psr17Factory::class);
    $injector->alias(StreamFactoryInterface::class, Psr17Factory::class);
    $injector->alias(FrameworkConfig::class, $config ?? DefaultFrameworkConfig::class);
    $injector->alias(RouterConfig::class, FrameworkConfig::class);

    $injector->set(self::INJECT_SRC_DIR, $srcDir);
    $injector->set(self::INJECT_MIDDLEWARES, $middlewares ?? [
          Router::class,
          RequestHandler::class,
    ]);

    return $injector->instantiate(self::class, noCache: true);
  }

  public function __construct(
      #[Inject(self::INJECT_MIDDLEWARES)] private array $middlewares,
      private FrameworkConfig $config,
      private Injector $injector,
  ) {
    
  }

  public function handle(ServerRequestInterface $request): ResponseInterface {
    if ($this->config->isViewsEnabled()) {
      AbstractView::$internalInjector = $this->injector;
    }
    $queue = [];
    foreach ($this->middlewares as $class) {
      $queue[] = $this->injector->get($class);
    }
    return (new Relay($queue))->handle($request);
  }

  public function run(ServerRequestInterface $request = null, string $emitter = null) {
    if ($request === null) {
      $request = $this->injector->instantiate(ServerRequestCreator::class, noCache: true)->fromGlobals();
    }
    $response = $this->handle($request);
    $this->injector->get($emitter ?? DefaultEmitter::class)->emit($response);
  }

}

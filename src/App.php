<?php declare(strict_types=1);

/**
 * @license   MIT
 *
 * @author    Ilya Dashevsky
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace OpenCore;

use OpenCore\HttpMessage\DeferredServerRequest;
use OpenCore\HttpMessage\LimitedPsr17Factory;
use OpenCore\Router\RequestHandler;
use OpenCore\Router\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use ErrorException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use OpenCore\Router\RouterConfig;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final class App implements RequestHandlerInterface {
  public const INJECT_SRC_DIR = '$$fcSrcDir';
  private const INJECT_MIDDLEWARES = '$$fcMiddlewares';

  public static function create(
    string $srcDir,
    array $middlewares = null,
    string $config = null,
    string $routerConfig = null,
    string $logger = null,
    string $loggerWriter = null,
    string $psrFactory = null,
  ) {
    set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
      throw new ErrorException(message: $errstr, code: 1, severity: $errno, filename: $errfile, line: $errline);
    });

    $injector = Injector::create();
    $injector->alias(ContainerInterface::class, Injector::class);
    $injector->alias(LoggerInterface::class, $logger ?? Logger::class);
    if ($loggerWriter !== null) {
      $injector->alias(LoggerWriter::class, $loggerWriter);
    }
    $injector->alias(ResponseFactoryInterface::class, $psrFactory ?? LimitedPsr17Factory::class);
    $injector->alias(UriFactoryInterface::class, $psrFactory ?? LimitedPsr17Factory::class);
    $injector->alias(StreamFactoryInterface::class, $psrFactory ?? LimitedPsr17Factory::class);
    $injector->alias(FrameworkConfig::class, $config ?? DefaultFrameworkConfig::class);
    $injector->alias(RouterConfig::class, $routerConfig ?? DefaultRouterConfig::class);

    $injector->set(self::INJECT_SRC_DIR, $srcDir);
    $injector->set(self::INJECT_MIDDLEWARES, $middlewares ?? [
      Router::class,
      RequestHandler::class,
    ]);

    return $injector->instantiate(self::class, noCache: true);
  }

  public function __construct(
    #[Inject(self::INJECT_MIDDLEWARES)]
    private array $middlewares,
    private FrameworkConfig $config,
    private Injector $injector,
    private LoggerInterface $logger,
  ) {
    if ($this->config->isViewsEnabled()) {
      AbstractView::$internalInjector = $this->injector;
    }
  }

  public function handle(ServerRequestInterface $request): ResponseInterface {
    $mw = $this->injector->get(current($this->middlewares));
    next($this->middlewares);
    if ($mw instanceof MiddlewareInterface) {
      return $mw->process($request, $this);
    }
    if ($mw instanceof RequestHandlerInterface) {
      return $mw->handle($request);
    }
    throw new ErrorException('Invalid middleware');
  }

  public function run(ServerRequestInterface $request = null, string $emitter = null) {
    if ($request === null) {
      $request = new DeferredServerRequest();
    }
    reset($this->middlewares);
    $response = $this->handle($request);
    try {
      $this->injector->get($emitter ?? DefaultEmitter::class)->emit($request, $response);
    } catch(Throwable $ex) {
      $this->logger->error('Failed to emit', ['exception' => $ex]);
    }
  }
}

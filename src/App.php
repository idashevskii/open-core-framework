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
use Closure;
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

final class App {

  public static function run(
      array $controllerDirs = null,
      bool $routerCacheDisabled = false,
      Closure $emitter = null,
      ServerRequestInterface $request = null,
      string $logger = null,
      bool $enableViews = false,
      array $middlewares = null,
  ) {

    set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
      throw new ErrorException(message: $errstr, code: 1, severity: $errno, filename: $errfile, line: $errline);
    });

    $injector = Injector::create();
    $injector->set(ContainerInterface::class, $injector);
    $injector->set(Injector::class, $injector);

    $injector->set(LoggerInterface::class, $injector->instantiate($logger ?? Logger::class, noCache: true));

    $psrFactory = new Psr17Factory();
    $injector->set(ResponseFactoryInterface::class, $psrFactory);
    $injector->set(RequestFactoryInterface::class, $psrFactory);
    $injector->set(ServerRequestFactoryInterface::class, $psrFactory);
    $injector->set(UriFactoryInterface::class, $psrFactory);
    $injector->set(UploadedFileFactoryInterface::class, $psrFactory);
    $injector->set(StreamFactoryInterface::class, $psrFactory);

    $injector->set(RouterConfig::class, new class($controllerDirs, !$routerCacheDisabled) implements RouterConfig {

      public function __construct(private $controllerDirs, private $useCache) {
        
      }

      public function define(RouterCompiler $compiler) {
        foreach ($this->controllerDirs as $dir => $ns) {
          $compiler->scan($ns, $dir);
        }
      }

      public function isCacheEnabled(): bool {
        return $this->useCache;
      }
    });

    if ($enableViews) {
      AbstractView::$internalInjector = $injector;
    }

    if ($middlewares === null) {
      $middlewares = [
        RouterMiddleware::class,
        RequestHandler::class,
      ];
    }

    $queue = [];
    foreach ($middlewares as $mwClass) {
      $queue[] = $injector->instantiate($mwClass, noCache: true);
    }

    if ($request === null) {
      $request = (new ServerRequestCreator($psrFactory, $psrFactory, $psrFactory, $psrFactory))->fromGlobals();
    }

    $relay = new Relay($queue);
    $response = $relay->handle($request);

    if ($emitter !== null) {
      $emitter($response);
    } else {
      (new Emitter())->emit($response);
    }
  }

}

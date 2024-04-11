<?php declare(strict_types=1);

/**
 * @license   MIT
 *
 * @author    Ilya Dashevsky
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace OpenCore\HttpMessage;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class LimitedPsr17Factory implements StreamFactoryInterface, UriFactoryInterface, ResponseFactoryInterface {
  public function createStream(string $content = ''): StreamInterface {
    return new LimitedStringStream($content);
  }
  public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface {
    throw self::notImplemented();
  }
  public function createStreamFromResource($resource): StreamInterface {
    throw self::notImplemented();
  }

  public function createUri(string $uri = ''): UriInterface {
    return new LimitedUri($uri);
  }
  public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface {
    return new LimitedResponse($code);
  }
  private static function notImplemented(): \Throwable {
    throw new \ErrorException('Not implemented');
  }
}

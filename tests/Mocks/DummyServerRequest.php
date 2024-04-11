<?php declare(strict_types=1);

/**
 * @license   MIT
 *
 * @author    Ilya Dashevsky
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Mocks;

use OpenCore\HttpMessage\DeferredServerRequest;
use OpenCore\HttpMessage\LimitedStringStream;
use OpenCore\HttpMessage\LimitedUri;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class DummyServerRequest extends DeferredServerRequest {

  private UriInterface $uri;

  function __construct(
    private string $method,
    string $uri,
    private ?StreamInterface $body,
  ) {
    $this->uri = new LimitedUri($uri);
  }
  public function getQueryParams() {
    parse_str($this->uri->getQuery(), $result);
    return $result;
  }
  public function getMethod() {
    return $this->method;
  }
  public function getUri() {
    return $this->uri;
  }
  public function getBody() {
    return $this->body ?? new LimitedStringStream('');
  }
}

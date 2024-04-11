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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class LimitedResponse implements ResponseInterface {

  private ?StreamInterface $body = null;
  private array $headers = [];

  public function __construct(private int $statusCode) {
  }

  public function getStatusCode() {
    return $this->statusCode;
  }
  public function withStatus(int $code, string $reasonPhrase = '') {
    $ret = clone $this;
    $ret->statusCode = $code;
    return $ret;
  }
  public function getBody() {
    if ($this->body === null) {
      $this->body = new LimitedStringStream('');
    }
    return $this->body;
  }
  public function withBody(StreamInterface $body) {
    $ret = clone $this;
    $ret->body = $body;
    return $ret;
  }
  public function getHeader(string $name) {
    return $this->headers[$name] ?? [];
  }
  public function getHeaderLine(string $name) {
    return implode(', ', $this->getHeader($name));
  }
  public function getHeaders() {
    return $this->headers;
  }
  public function hasHeader(string $name) {
    return isset($this->headers[$name]);
  }
  public function withHeader(string $name, $value) {
    $ret = clone $this;
    $ret->headers[$name] = [$value];
    return $ret;
  }
  public function withAddedHeader(string $name, $value) {
    $ret = clone $this;
    $ret->headers[$name][] = $value;
    return $ret;
  }
  public function withoutHeader(string $name) {
    $ret = clone $this;
    unset($ret->headers[$name]);
    return $ret;
  }
  public function getReasonPhrase() {
    throw self::notImplemented();
  }
  public function getProtocolVersion() {
    throw self::notImplemented();
  }
  public function withProtocolVersion(string $version) {
    throw self::notImplemented();
  }
  private static function notImplemented(): \Throwable {
    throw new \ErrorException('Not implemented');
  }
}

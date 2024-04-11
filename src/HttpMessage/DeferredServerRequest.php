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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class DeferredServerRequest implements ServerRequestInterface {

  private array $attributes = [];

  private ?UriInterface $uri = null;
  private ?array $headers = null;

  public function getCookieParams() {
    throw self::notImplemented();
  }
  public function getParsedBody() {
    throw self::notImplemented();
  }
  public function getQueryParams() {
    return $_GET;
  }
  public function getServerParams() {
    throw self::notImplemented();
  }
  public function getUploadedFiles() {
    throw self::notImplemented();
  }
  public function withCookieParams(array $cookies) {
    throw self::notImplemented();
  }
  public function withParsedBody($data) {
    throw self::notImplemented();
  }
  public function withQueryParams(array $query) {
    throw self::notImplemented();
  }
  public function withUploadedFiles(array $uploadedFiles) {
    throw self::notImplemented();
  }
  public function getMethod() {
    return $_SERVER['REQUEST_METHOD'];
  }
  public function getRequestTarget() {
    throw self::notImplemented();
  }
  public function getUri() {
    if ($this->uri === null) {
      // real schema and host must not be determined here
      $this->uri = new LimitedUri('http://host' . $_SERVER['REQUEST_URI']);
    }
    return $this->uri;
  }
  public function withMethod(string $method) {
    throw self::notImplemented();
  }
  public function withRequestTarget(string $requestTarget) {
    throw self::notImplemented();
  }
  public function withUri(UriInterface $uri, bool $preserveHost = false) {
    throw self::notImplemented();
  }
  public function getBody() {
    return new DeferredInputStream();
  }
  public function getHeader(string $name) {
    if ($this->headers === null) {
      $this->headers = getallheaders();
    }
    return isset($this->headers[$name]) ? [$this->headers[$name]] : [];
  }
  public function getHeaderLine(string $name) {
    throw self::notImplemented();
  }
  public function getHeaders() {
    throw self::notImplemented();
  }
  public function getProtocolVersion() {
    throw self::notImplemented();
  }
  public function hasHeader(string $name) {
    throw self::notImplemented();
  }
  public function withAddedHeader(string $name, $value) {
    throw self::notImplemented();
  }
  public function withBody(\Psr\Http\Message\StreamInterface $body) {
    throw self::notImplemented();
  }
  public function withHeader(string $name, $value) {
    throw self::notImplemented();
  }
  public function withoutHeader(string $name) {
    throw self::notImplemented();
  }
  public function withProtocolVersion(string $version) {
    throw self::notImplemented();
  }
  public function getAttributes(): array {
    return $this->attributes;
  }
  public function getAttribute($attribute, $default = null) {
    return $this->attributes[$attribute] ?? $default;
  }
  public function withAttribute($attribute, $value): ServerRequestInterface {
    $ret = clone $this;
    $ret->attributes[$attribute] = $value;
    return $ret;
  }
  public function withoutAttribute($attribute): ServerRequestInterface {
    $ret = clone $this;
    unset($ret->attributes[$attribute]);
    return $ret;
  }
  private static function notImplemented(): \Throwable {
    throw new \ErrorException('Not implemented');
  }
}

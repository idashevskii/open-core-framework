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

use Psr\Http\Message\UriInterface;

class LimitedUri implements UriInterface {

  private string $scheme;
  private string $host;
  private ?int $port;
  private string $path;
  private string $query;
  private string $fragment;
  private bool $parsed = false;

  public function __construct(private ?string $uri) {
  }

  public function getAuthority() {
    throw self::notImplemented();
  }
  public function getFragment() {
    if (!$this->parsed) {
      $this->parse();
    }
    return $this->fragment;
  }
  public function getHost() {
    if (!$this->parsed) {
      $this->parse();
    }
    return $this->host;
  }
  public function getPath() {
    if (!$this->parsed) {
      $this->parse();
    }
    return $this->path;
  }
  public function getPort() {
    if (!$this->parsed) {
      $this->parse();
    }
    return $this->port;
  }
  public function getQuery() {
    if (!$this->parsed) {
      $this->parse();
    }
    return $this->query;
  }
  public function getScheme() {
    if (!$this->parsed) {
      $this->parse();
    }
    return $this->scheme;
  }
  public function getUserInfo() {
    throw self::notImplemented();
  }
  public function withFragment(string $fragment) {
    if (!$this->parsed) {
      $this->parse();
    }
    $ret = clone $this;
    $ret->fragment = $fragment;
    $ret->uri = null;
    return $ret;
  }
  public function withHost(string $host) {
    if (!$this->parsed) {
      $this->parse();
    }
    $ret = clone $this;
    $ret->host = $host;
    $ret->uri = null;
    return $ret;
  }
  public function withPath(string $path) {
    if (!$this->parsed) {
      $this->parse();
    }
    $ret = clone $this;
    $ret->path = $path;
    $ret->uri = null;
    return $ret;
  }
  public function withPort(int|null $port) {
    if (!$this->parsed) {
      $this->parse();
    }
    $ret = clone $this;
    $ret->port = $port;
    $ret->uri = null;
    return $ret;
  }
  public function withQuery(string $query) {
    if (!$this->parsed) {
      $this->parse();
    }
    $ret = clone $this;
    $ret->query = $query;
    $ret->uri = null;
    return $ret;
  }
  public function withScheme(string $scheme) {
    if (!$this->parsed) {
      $this->parse();
    }
    $ret = clone $this;
    $ret->scheme = $scheme;
    $ret->uri = null;
    return $ret;
  }
  public function withUserInfo(string $user, string|null $password = null) {
    throw self::notImplemented();
  }
  public function __toString() {
    if ($this->uri === null) {
      $uri = '';
      if ($this->host) {
        $uri = $this->scheme . '://' . $this->host;
        if ($this->port !== null) {
          $uri .= ':' . $this->port;
        }
      }
      $uri .= $this->path;
      if ($this->query) {
        $uri .= '?' . $this->query;
      }
      if ($this->fragment) {
        $uri .= '#' . $this->fragment;
      }
      $this->uri = $uri;
    }
    return $this->uri;
  }

  private function parse() {
    $this->parsed = true;

    $parts = parse_url($this->uri);
    if ($parts === false) {
      throw new \InvalidArgumentException("Invalid URI: $this->uri");
    }

    // Apply parse_url parts to a URI.
    $this->scheme = $parts['scheme'] ?? '';
    $this->host = $parts['host'] ?? '';
    $this->port = isset($parts['port']) ? (int) $parts['port'] : null;
    $this->path = $parts['path'] ?? '';
    $this->query = $parts['query'] ?? '';
    $this->fragment = $parts['fragment'] ?? '';
  }

  private static function notImplemented(): \Throwable {
    throw new \ErrorException('Not implemented');
  }
}

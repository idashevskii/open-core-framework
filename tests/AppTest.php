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

use PHPUnit\Framework\TestCase;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

final class AppTest extends TestCase {

  private static function parseResponse(ResponseInterface $response, bool $stripSpaces = false): string|array {
    $body = (string) $response->getBody();
    if (str_contains($response->getHeaderLine('Content-Type'), 'application/json')) {
      $body = json_decode($body, true);
    }
    if (is_string($body) && $stripSpaces) {
      $body = preg_replace('/\s/', '', $body);
    }
    return $body;
  }

  public function request(string $method, string $uri, mixed $payload = null): ResponseInterface {
    $psrFactory = new Psr17Factory();
    $serverRequest = $psrFactory->createServerRequest($method, $uri);
    if ($payload !== null) {
      $serverRequest = $serverRequest->withBody($psrFactory->createStream(json_encode($payload)));
    }
    $response = null;
    /** @var ResponseInterface $response */
    App::run(
        controllerDirs: [__DIR__ => __NAMESPACE__],
        request: $serverRequest,
        routerCacheDisabled: true,
        enableViews: true,
        emitter: function (ResponseInterface $r)use (&$response) {
          $response = $r;
        }
    );
    return $response;
  }

  public function testHomePage() {
    $response = $this->request('GET', '/');
    $this->assertEquals($response->getStatusCode(), 200);
    $this->assertEquals(self::parseResponse($response), 'Welcome!');
  }

  public function testHtmlEcho() {
    $payload = ['hello' => 'world'];
    $response = $this->request('POST', '/echo', payload: $payload);
    $this->assertEquals($response->getStatusCode(), 200);
    $expected = '<html><body>' . json_encode($payload) . '</body></html>';
    $this->assertEquals(self::parseResponse($response, stripSpaces: true), $expected);
  }

  public function testMultiSlotViews() {
    $title = 'title';
    $content = 'content';
    $response = $this->request('GET', "/multi-slot?title=$title&content=$content");
    $this->assertEquals($response->getStatusCode(), 200);
    $expected = "<html>"
        . "<head><title>$title</title></head>"
        . "<body><main><p>$content</p></main></body>"
        . "</html>";
    $this->assertEquals(self::parseResponse($response, stripSpaces: true), $expected);
  }

  public function testJsonEcho() {
    $payload = ['hello' => 'world'];
    $response = $this->request('POST', '/api/echo', payload: $payload);
    $this->assertEquals($response->getStatusCode(), 200);
    $this->assertEquals(self::parseResponse($response), $payload);
  }

//  public function testViewCollection() {
//    $payload = ['hello' => 'world'];
//    $response = $this->request('POST', '/echo', payload: $payload);
//    $this->assertEquals($response->getStatusCode(), 200);
//    $expected='<html>'. json_encode($payload).'</html>';
//    $this->assertEquals(self::parseResponse($response, stripSpaces: true), $expected);
//  }
}

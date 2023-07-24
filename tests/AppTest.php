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

namespace App;

use PHPUnit\Framework\TestCase;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use OpenCore\App;

final class AppTest extends TestCase {

  private static function parseResponse(ResponseInterface $response, bool $stripSpaces = false): string|array {
    ob_start();
    $body = (string) $response->getBody();
    $body .= ob_get_clean();
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
    $request = $psrFactory->createServerRequest($method, $uri);
    if ($payload !== null) {
      $request = $request->withBody($psrFactory->createStream(json_encode($payload)));
    }
    return App::create(srcDir: __DIR__)->handle($request);
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

  public function testHtmlEchoStream() {
    $payload = ['hello' => 'world'];
    $response = $this->request('POST', '/echo-stream', payload: $payload);
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

}

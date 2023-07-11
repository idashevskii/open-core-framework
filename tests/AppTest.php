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

final class AppTest extends TestCase {
  
  public function request(string $method, string $uri, string $payload=null){
    ob_start();

    $psrFactory = new Psr17Factory();
    $serverRequest = $psrFactory->createServerRequest($method, $uri);
    if ($payload !== null) {
      $serverRequest = $serverRequest->withBody($psrFactory->createStream($payload));
    }
    $app=new App();
    $app->addControllersNs(__NAMESPACE__, __DIR__);
    $app->run($serverRequest);

    return ob_get_clean();
  }
  
  public function testHomePage() {
    $actual=$this->request('GET', '/');
    $expected='';
    $this->assertEquals($actual, $expected);
  }

//  public function testHomePage() {
//    $actual=$this->request('GET', '/');
//    $expected='';
//    $this->assertEquals($actual, $expected);
//  }

//  public function testBaseUrl() {
//    
//  }

}

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
use OpenCore\Uitls\Logger;
use Psr\Log\LoggerInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use OpenCore\Uitls\ControllerResolver;
use Relay\Relay;
use OpenCore\Uitls\Emitter;
use Closure;

final class App {
  
  private array $routerNs=[];
  private bool $routerCacheDisabled=false;
  private ?Closure $emitter=null;
  
  public function addControllersNs(string $ns, string $dir){
    $this->routerNs[]=[$ns, $dir];
  }

  public function disableRouterCache(){
    $this->routerCacheDisabled=true;
  }

  public function setEmitter(Closure $emitter){
    $this->emitter=$emitter;
  }

  public function run(?ServerRequestInterface $request=null){
    $psrFactory = new Psr17Factory();
    if($request===null){
      $creator = new ServerRequestCreator($psrFactory, $psrFactory, $psrFactory, $psrFactory);
      $request = $creator->fromGlobals(); 
    }

    $logger = new Logger();
    $injector=Injector::create();
    
    $cacheFile=$this->routerCacheDisabled ? null : sys_get_temp_dir().'/router-'.md5(__FILE__).'.php';
    $queue = [
      Router::create($cacheFile, $psrFactory, function (RouterCompiler $r) {
        foreach ($this->routerNs as list($ns, $dir)) {
          $r->scan($ns, $dir);
        }
      }),
      new RequestParser($psrFactory),
      new Executor($psrFactory, $injector),
      new ResponseSerializer($psrFactory, $psrFactory),
    ];
    foreach ($queue as $loggerAware) {
      $loggerAware->setLogger($logger);
    }
    $relay = new Relay($queue);
    $response=$relay->handle($request);
    if($this->emitter){
      ($this->emitter)($response);
    }else{
      (new Emitter())->emit($response);
    }
  }
  
}

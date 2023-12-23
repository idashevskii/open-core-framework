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

use Closure;
use ErrorException;
use Psr\Http\Message\StreamInterface;

abstract class AbstractView {

  private ?array $props;
  public static ?Injector $internalInjector = null;

  public abstract function render();

  public static function stream(array $props = null): StreamInterface {
    return new DeferredOutputStream(function ()use ($props) {
          static::tag($props);
        });
  }

  public function __call($name, $arguments) {
    return ($this->props[$name])(...$arguments);
  }

  public function __get($name) {
    return $this->props[$name];
  }

  public function __isset($name) {
    return isset($this->props[$name]);
  }

  public static function tag(array $props = null) {
    if (!self::$internalInjector) {
      throw new ErrorException('Views not enabled in App');
    }
    $instance = self::$internalInjector->instantiate(static::class, noCache: false);

    $instance->props = $props;

    $instance->render();
  }

}

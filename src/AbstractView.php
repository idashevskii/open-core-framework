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

abstract class AbstractView {

  private ?array $props;
  private ?array $slots;
  public static ?Injector $internalInjector = null;

  public abstract function render();

  public static function get(array $props = null, Closure|array $slots = null): string {
    ob_start();
    static::tag($props, $slots);
    return ob_get_clean();
  }

  public function __call($name, $arguments) {
    return isset($this->slots[$name]) ? ($this->slots[$name])(...$arguments) : null;
  }

  public function __get($name) {
    return $this->props[$name] ?? null;
  }

  public function hasSlot($name) {
    return isset($this->slots[$name]);
  }

  public function hasProp($name) {
    return isset($this->props[$name]);
  }

  public static function tag(array $props = null, Closure|array $slots = null) {
    if (!self::$internalInjector) {
      throw new ErrorException('Views not enabled in App');
    }
    $instance = self::$internalInjector->instantiate(static::class, noCache: false);

    $instance->slots = ($slots instanceof Closure) ? ['default' => $slots] : $slots;
    $instance->props = $props;

    $instance->render();
  }

}

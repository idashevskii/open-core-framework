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
use OpenCore\Router\Exceptions\RoutingException;
use OpenCore\Router\RouterConfig;

class DefaultRouterConfig implements RouterConfig {

  public function __construct(#[Inject(App::INJECT_SRC_DIR)] protected string $srcDir) {
    
  }

  public function storeCompiledData(Closure $dataProvider): array {
    return $dataProvider();
  }

  public function getControllerDirs(): array {
    return ['App\\Controllers' => $this->srcDir . '/Controllers'];
  }

  function deserialize(string $type, string $value): mixed {
    return match ($type) {
      'string' => $value,
      'int' => (int) $value,
      'float' => (float) $value,
      'bool' => match ($value) {
          'true', '1' => true,
          'false', '0' => false,
          default => throw new RoutingException("Boolean param has invalid boolean value", code: 400),
        },
      'array' => self::parseJson($value),
    };
  }

  private static function parseJson(string $json) {
    try {
      return json_decode($json, flags: JSON_THROW_ON_ERROR | JSON_OBJECT_AS_ARRAY);
    } catch (\JsonException $ex) {
      throw new RoutingException('Body parse error', code: 400, previous: $ex);
    }
  }

}

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

class DefaultRouterConfig implements RouterConfig {

  public function __construct(#[Inject(App::INJECT_SRC_DIR)] protected string $srcDir) {
    
  }

  public function storeCompiledData(Closure $dataProvider): array {
    return $dataProvider();
  }

  public function getControllerDirs(): array {
    return ['App\\Controllers' => $this->srcDir . '/Controllers'];
  }

}

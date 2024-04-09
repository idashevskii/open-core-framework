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

namespace App\Controllers;

use OpenCore\Router\{Controller, Route, Body};

#[Controller('api')]
class Api {

  public function __construct() {
    
  }

  #[Route('POST', 'echo')]
  public function echo(#[Body] array $data) {
    return $data;
  }

}

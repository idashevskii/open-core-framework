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

namespace OpenCore\Uitls;

use Psr\Http\Message\ResponseInterface;

class Emitter {

  public function emit(ResponseInterface $response) {
    $statusCode=$response->getStatusCode();
    foreach ($response->getHeaders() as $name => $values) {
      $first = $name !== 'Set-Cookie';
      foreach ($values as $value) {
        header("$name: $value", $first, $statusCode);
        $first = false;
      }
    }
    echo (string)$response->getBody();
    flush();
  }

}

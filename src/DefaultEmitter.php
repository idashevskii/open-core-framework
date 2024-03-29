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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DefaultEmitter implements Emitter {

  public function emit(ServerRequestInterface $request, ResponseInterface $response): void {
    foreach ($response->getHeaders() as $name => $values) {
      $first = $name !== 'Set-Cookie';
      foreach ($values as $value) {
        header("$name: $value", replace: $first);
        $first = false;
      }
    }
    http_response_code($response->getStatusCode());
    if ($request->getMethod() !== 'HEAD') {
      echo (string) $response->getBody();
    }
  }

}

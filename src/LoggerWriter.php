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

class LoggerWriter {

  public function write(array $data) {
    file_put_contents('php://stderr', $data['message'] . "\n");
  }

}

<?php declare(strict_types=1);

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
  public function write(array $event) {
    $msg = strtoupper($event['level']) . ': ' . $event['message'];
    $extraAttrs = null;
    foreach ($event as $k => $v) {
      if ($k === 'level' || $k === 'message') {
        continue;
      }
      $extraAttrs[] = "$k=$v";
    }
    if ($extraAttrs !== null) {
      $msg .= ': '.implode(' ', $extraAttrs);
    }
    file_put_contents('php://stderr', $msg. "\n");
  }
}

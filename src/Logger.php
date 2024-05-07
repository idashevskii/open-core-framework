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

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger {
  public function __construct(private LoggerWriter $writer) {}

  public function log($level, string|\Stringable $message, array $context = []): void {
    $search = null;
    $replace = null;
    $event = ['level' => $level, 'message' => $message];
    foreach ($context as $key => $val) {
      if (is_object($val) && method_exists($val, '__toString')) {
        $strVal = (string) $val;
      } else if (is_array($val) || is_object($val)) {
        $strVal = json_encode($val, flags: JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
      } else {
        $strVal = (string) $val;
      }
      $placeholder = '{' . $key . '}';
      if (strpos($event['message'], $placeholder) !== false) {
        $search[] = '{' . $key . '}';
        $replace[] = $strVal;
      } else {
        $event[$key] = $strVal;
      }
    }
    if ($search !== null) {
      $event['message'] = str_replace($search, $replace, $message);
    }
    $this->writer->write($event);
  }
}

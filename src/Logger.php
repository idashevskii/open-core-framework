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

use Psr\Log\AbstractLogger;
use Exception;

class Logger extends AbstractLogger {

  public function __construct(private LoggerWriter $writer) {
    ;
  }

  public function log($level, string|\Stringable $message, array $context = []): void {
    $search = [];
    $replace = [];
    foreach ($context as $key => $val) {
      if ($key === 'exception' && $val instanceof Exception) {
        $search[] = '{' . $key . '}';
        $replace[] = "Exception '" . $val::class . "'\n"
            . "with message '" . $val->getMessage() . "'\n"
            . "in '" . $val->getFile() . ":" . $val->getLine() . "'\n"
            . $val->getTraceAsString();
        continue;
      }
      if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
        $search[] = '{' . $key . '}';
        $replace[] = (string) $val;
      }
    }
    $context['message'] = strtoupper($level) . ': ' . str_replace($search, $replace, $message);
    $context['format'] = $message;
    $context['level'] = $level;
    $this->writer->write($context);
  }

}

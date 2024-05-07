<?php declare(strict_types=1);

/**
 * @license   MIT
 *
 * @author    Ilya Dashevsky
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use OpenCore\Logger;
use OpenCore\LoggerWriter;

final class LoggerTest extends TestCase {
  private LoggerInterface $logger;
  public string $errMsg = '';

  protected function setUp(): void {
    $this->logger = new Logger(new class ($this) extends LoggerWriter {
      public function __construct(private LoggerTest $test) {}

      public function write(array $data) {
        $this->test->errMsg = strtoupper($data['level']).': '.$data['message'];
      }
    });
  }

  private function log(string $level, string $message, array $context = []) {
    $this->errMsg = '';
    $this->logger->log($level, $message, $context);
    return $this->errMsg;
  }

  public function testStaticMessage() {
    $msg = 'Some message';

    $this->assertEquals('DEBUG: ' . $msg, $this->log(LogLevel::DEBUG, $msg));
  }

  public function testFormettedMessage() {
    $str = 'some str';
    $num = 42;
    $msg = '{str} {num} {undefined}';

    $this->assertEquals("INFO: $str $num {undefined}", $this->log(LogLevel::INFO, $msg, [
          'str' => $str,
          'num' => $num,
    ]));
  }

  public function testExceptionMessage() {
    $msg = 'some msg';
    $ex = new \Exception($msg);

    $this->assertEquals('ERROR: ' . (string)$ex, $this->log(LogLevel::ERROR, '{exception}', [
          'exception' => $ex,
    ]));
  }
}

<?php declare(strict_types=1);

/**
 * @license   MIT
 *
 * @author    Ilya Dashevsky
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace OpenCore\HttpMessage;

trait LimitedStreamTrait {

  public function close(): void {

  }

  public function detach() {

  }

  public function eof(): bool {
    return false;
  }

  public function getContents(): string {
    return '';
  }

  public function getMetadata(mixed $key = null) {

  }

  public function getSize() {

  }

  public function isReadable(): bool {
    return true;
  }

  public function isSeekable(): bool {
    return false;
  }

  public function isWritable(): bool {
    return false;
  }

  public function read(int $length): string {
    return '';
  }

  public function rewind() {

  }

  public function seek(int $offset, int $whence = SEEK_SET) {

  }

  public function tell(): int {
    return 0;
  }

  public function write(string $string): int {
    return 0;
  }

}

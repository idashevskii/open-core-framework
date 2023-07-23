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

use Psr\Http\Message\StreamInterface;
use Closure;

class DeferredOutputStream implements StreamInterface {

  public function __construct(private Closure $outputProducer) {
    
  }

  public function __toString(): string {
    ($this->outputProducer)();
    return '';
  }

  public function close(): void {
    
  }

  public function detach() {
    
  }

  public function eof(): bool {
    
  }

  public function getContents(): string {
    
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
    
  }

  public function rewind() {
    
  }

  public function seek(int $offset, int $whence = SEEK_SET) {
    
  }

  public function tell(): int {
    
  }

  public function write(string $string): int {
    
  }

}

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

use Psr\Http\Message\StreamInterface;
use Closure;

class DeferredOutputStream implements StreamInterface {

  use LimitedStreamTrait;

  public function __construct(private Closure $outputProducer) {

  }

  public function __toString(): string {
    ($this->outputProducer)();
    return '';
  }

}

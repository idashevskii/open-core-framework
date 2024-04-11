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

class LimitedStringStream implements StreamInterface {

  use LimitedStreamTrait;

  public function __construct(private string $str) {
  }

  public function __toString(): string {
    return $this->str;
  }
}

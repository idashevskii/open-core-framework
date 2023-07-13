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

namespace OpenCore\Views;

use OpenCore\AbstractView;
use Psr\Http\Message\ServerRequestInterface;

class BaseLayout extends AbstractView {

  private $data;

  public function __construct(ServerRequestInterface $request) {
    $this->data = (string) $request->getBody();
  }

  public function render() {
    ?>

    <html>
      <head>
        <title><?= $this->title ?></title>
      </head>
      <body>
        <main>
          <?= $this->main() ?>
        </main>
        <?php if ($this->hasSlot('sidebar')): ?>
          <aside>
            <?= $this->sidebar() ?>
          </aside>
        <?php endif; ?>
      </body>
    </html>

    <?php
  }

}

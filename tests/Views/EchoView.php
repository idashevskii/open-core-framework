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

namespace App\Views;

use OpenCore\AbstractView;

class EchoView extends AbstractView {

  public function render() {
    ?>

    <html>
      <body>
        <?= $this->data ?>
      </body>
    </html>

    <?php
  }

}

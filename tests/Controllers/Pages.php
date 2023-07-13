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

namespace OpenCore\Controllers;

use OpenCore\Controller;
use OpenCore\Route;
use OpenCore\Views\EchoView;
use OpenCore\Views\BaseLayout;

#[Controller('')]
class Pages {

  public function __construct() {
    
  }

  #[Route('GET', '')]
  public function getHomePage() {
    return 'Welcome!';
  }

  #[Route('POST', 'echo')]
  public function echo() {
    return EchoView::get();
  }

  #[Route('GET', 'multi-slot')]
  public function multiSlot(string $title = null, string $content = null) {
    return BaseLayout::get(['title' => $title], ['main' => function ()use ($content) {
            ?>

            <p><?= $content ?></p>

            <?php
          }]);
  }

}

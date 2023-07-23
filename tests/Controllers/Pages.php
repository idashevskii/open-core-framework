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
use OpenCore\Body;

#[Controller('')]
class Pages {

  public function __construct() {
    
  }

  #[Route('GET', '')]
  public function getHomePage() {
    return 'Welcome!';
  }

  #[Route('POST', 'echo')]
  public function echo(#[Body] array $body) {
    return EchoView::stream(['data' => json_encode($body)]);
  }

  #[Route('POST', 'echo-stream')]
  public function echoStream(#[Body] array $body) {
    return EchoView::stream(['data' => json_encode($body)]);
  }

  #[Route('GET', 'multi-slot')]
  public function multiSlot(string $title = null, string $content = null) {
    return BaseLayout::stream([
          'title' => $title,
          'main' => function ()use ($content) {
            ?>

            <p><?= $content ?></p>

            <?php
          }
    ]);
  }

}

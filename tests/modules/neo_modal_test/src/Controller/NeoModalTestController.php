<?php

declare(strict_types=1);

namespace Drupal\neo_modal_test\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Url;
use Drupal\neo_modal\Ajax\NeoModalCommand;
use Drupal\neo_modal\Modal;

/**
 * Pages for the neo_modal browser tests.
 */
final class NeoModalTestController {

  /**
   * A page with a modal trigger, a gallery and an AJAX command link.
   */
  public function page(): array {
    $build = [];

    $build['single'] = $this->link('Open single', 'single');
    (new Modal(['#markup' => '<p id="single-content">Single content</p>']))
      ->setTitle('Single')
      ->applyTo($build['single']);

    foreach ([1, 2, 3] as $delta) {
      $build['gallery_' . $delta] = $this->link('Gallery ' . $delta, 'gallery-' . $delta);
      (new Modal(['#markup' => '<p class="gallery-item">Gallery item ' . $delta . '</p>']))
        ->setGroup('test-gallery')
        ->applyTo($build['gallery_' . $delta]);
    }

    $build['command'] = $this->commandLink();
    return $build;
  }

  /**
   * A page with no modal on it, only a link whose response opens one.
   */
  public function bare(): array {
    return ['command' => $this->commandLink()];
  }

  /**
   * Opens a modal by AJAX command.
   */
  public function command(): AjaxResponse {
    $response = new AjaxResponse();
    $response->addCommand(new NeoModalCommand('<p id="command-content">Command content</p>', ['title' => 'By command']));
    return $response;
  }

  /**
   * A plain link to act as a trigger.
   */
  private function link(string $title, string $id): array {
    return [
      '#type' => 'link',
      '#title' => $title,
      '#url' => Url::fromRoute('<none>'),
      '#attributes' => ['id' => $id],
    ];
  }

  /**
   * A link that opens a modal by AJAX command.
   */
  private function commandLink(): array {
    return [
      '#type' => 'link',
      '#title' => 'Open by command',
      '#url' => Url::fromRoute('neo_modal_test.command'),
      '#attributes' => ['id' => 'command', 'class' => ['use-ajax']],
      '#attached' => ['library' => ['core/drupal.ajax']],
    ];
  }

}

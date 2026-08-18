<?php

declare(strict_types=1);

namespace Drupal\neo_modal\Controller;

use Drupal\block\BlockInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\neo_modal\Plugin\Block\NeoModalBlockInterface;

/**
 * Returns responses for Neo | Modal routes.
 */
final class NeoModalBlockController extends ControllerBase {

  /**
   * Builds the response.
   *
   * @param \Drupal\block\BlockInterface $block
   *   The block whose modal content is being requested.
   *
   * @return array
   *   A render array.
   */
  public function __invoke(BlockInterface $block): array {
    $build = [];
    $plugin = $block->getPlugin();
    if ($plugin instanceof NeoModalBlockInterface) {
      $build = $plugin->buildModalContent();
    }
    return $build;
  }

  /**
   * Title callback for the route.
   *
   * @param \Drupal\block\BlockInterface $block
   *   The block whose modal title is being requested.
   *
   * @return string
   *   The modal title.
   */
  public function getTitle(BlockInterface $block): string {
    $title = '';
    $plugin = $block->getPlugin();
    if ($plugin instanceof NeoModalBlockInterface) {
      $title = $plugin->getModalTitle();
    }
    return $title;
  }

}

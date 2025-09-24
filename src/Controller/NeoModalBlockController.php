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
   *   The eXo toolbar item.
   * @param string|null $arg1
   *   An optional argument.
   * @param string|null $arg2
   *   An optional argument.
   *
   * @return array
   *   A render array.
   */
  public function __invoke(BlockInterface $block, ?string $arg1 = NULL, ?string $arg2 = NULL): array {
    $build = [];
    $plugin = $block->getPlugin();
    if ($plugin instanceof NeoModalBlockInterface) {
      $build = $plugin->buildModalContent($arg1, $arg2);
    }
    return $build;
  }

  /**
   * Title callback for the route.
   *
   * @param \Drupal\block\BlockInterface $block
   *   The eXo toolbar item.
   * @param string|null $arg1
   *   An optional argument.
   * @param string|null $arg2
   *   An optional argument.
   *
   * @return string
   *   The modal title.
   */
  public function getTitle(BlockInterface $block, ?string $arg1 = NULL, ?string $arg2 = NULL): string {
    $title = '';
    $plugin = $block->getPlugin();
    if ($plugin instanceof NeoModalBlockInterface) {
      $title = $plugin->getModalTitle($arg1, $arg2);
    }
    return $title;
  }

}

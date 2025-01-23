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
   * @param string|null $arg
   *   An optional argument.
   *
   * @return array
   *   A render array.
   */
  public function __invoke(BlockInterface $block, string $arg = NULL): array {
    $build = [];
    $plugin = $block->getPlugin();
    if ($plugin instanceof NeoModalBlockInterface) {
      $build = $plugin->buildModalContent($arg);
    }
    return $build;
  }

}

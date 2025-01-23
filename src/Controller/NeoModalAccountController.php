<?php

declare(strict_types=1);

namespace Drupal\neo_modal\Controller;

use Drupal\block\BlockInterface;
use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Neo | Modal routes.
 */
final class NeoModalAccountController extends ControllerBase {

  /**
   * Builds the response.
   *
   * @return array
   *   A render array.
   */
  public function login(BlockInterface $block): array {
    return $this->formBuilder()->getForm('\Drupal\neo_modal\Form\NeoModalAccountLoginForm', $block);
  }

  /**
   * Builds the response.
   *
   * @return array
   *   A render array.
   */
  public function register(BlockInterface $block): array {
    $user = $this->entityTypeManager()->getStorage('user')->create();
    return $this->entityFormBuilder()->getForm($user, 'register', [
      'block' => $block,
      'neo_modal_account' => TRUE,
    ]);
  }

  /**
   * Builds the response.
   *
   * @return array
   *   A render array.
   */
  public function password(BlockInterface $block): array {
    return $this->formBuilder()->getForm('\Drupal\neo_modal\Form\NeoModalAccountPasswordForm', $block);
  }

}

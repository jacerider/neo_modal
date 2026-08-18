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
    // No form-state extras: the 'block' and 'neo_modal_account' keys passed
    // here had no reader anywhere, so they looked like an extension point that
    // did not exist.
    return $this->entityFormBuilder()->getForm($user, 'register');
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

<?php

namespace Drupal\neo_modal\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a block to display a view within a modal.
 *
 * @Block(
 *   id = "neo_modal",
 *   admin_label = @Translation("Modal"),
 *   provider = "neo_modal"
 * )
 */
class NeoModalBlock extends NeoModalBlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'block' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $form['block'] = [
      '#type' => 'details',
      '#title' => $this->t('Blocks (Content)'),
      '#open' => TRUE,
    ];

    $form['block']['block'] = $this->buildBlocksForm([], $form_state, $this->configuration['block']);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['block'] = $form_state->getValue(['block', 'block']);
  }

  /**
   * {@inheritdoc}
   */
  public function buildModalContent(): array {
    $build = parent::buildModalContent();
    $build['content'] = $this->buildBlockContent($this->configuration['block']);
    return $build;
  }

}

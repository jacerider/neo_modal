<?php

namespace Drupal\neo_modal\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\neo_modal\Modal;

/**
 * Provides a helper for Modal blocks.
 */
trait NeoModalBlockTrait {

  /**
   * {@inheritdoc}
   */
  public function neoModalDefaultConfiguration() {
    return [
      'modal_preset' => '',
      'modal' => [],
    ];
  }

  /**
   * Process the modal settings.
   */
  public function neoModalProcess(&$form, FormStateInterface $form_state, &$complete_form) {
    $modalVariation = $form_state->getValue(array_merge($form['#parents'], ['modal_preset']), $this->configuration['modal_preset']);
    $form['modal_preset'] = [
      '#type' => 'neo_settings_variation',
      '#title' => $this->t('Modal Preset'),
      '#settings_repository_id' => 'neo_modal.settings',
      '#default_value' => $modalVariation,
      '#ajax' => [
        'callback' => [__CLASS__, 'blockFormAjax'],
        'wrapper' => 'block-settings',
      ],
    ];

    $form['modal'] = [
      '#type' => 'neo_settings',
      '#title' => $this->t('Modal settings'),
      '#settings_id' => 'neo_modal',
      '#settings_variation' => $modalVariation,
      '#default_value' => $this->configuration['modal'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function neoModalBlockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['modal_preset'] = $form_state->getValue(['modal_preset']);
    $this->configuration['modal'] = $form_state->getValue(['modal'], []);
  }

  /**
   * Forced modal configuration.
   *
   * The forced modal configuration is used to override the modal configuration.
   *
   * @return array
   *   The forced modal configuration.
   */
  protected function neoModalForceConfiguration() {
    return [];
  }

  /**
   * Builds the modal object.
   *
   * @return \Drupal\neo_modal\Modal
   *   The modal object.
   */
  protected function buildModal(): Modal {
    return new Modal(NULL, $this->neoModalForceConfiguration() + $this->configuration['modal'], $this->configuration['modal_preset']);
  }

}

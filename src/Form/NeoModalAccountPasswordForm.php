<?php

namespace Drupal\neo_modal\Form;

use Drupal\block\BlockInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Form\UserPasswordForm;

/**
 * Provides a user login form.
 *
 * @internal
 */
class NeoModalAccountPasswordForm extends UserPasswordForm {

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, BlockInterface $block = NULL) {
    $form = parent::buildForm($form, $form_state);
    $form['#id'] = 'exo-modal-account-password';
    $form['#attributes']['class'][] = 'exo-modal-account--password';

    $form['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#attributes' => [
        'class' => ['form--title'],
      ],
      '#value' => $this->t('Need help getting into your account?'),
      '#weight' => -100,
    ];

    $form['description'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ['form--description'],
      ],
      '#value' => $this->t("Enter your email address - if it's attached to your account, check your inbox within a few minutes for a link to help you access your account."),
      '#weight' => -100,
    ];

    $form['mail']['#access'] = FALSE;

    $form['actions']['#weight'] = 1000;
    $form['actions']['#attributes']['class'][] = 'flex gap-4';
    $form['actions']['close'] = [
      '#type' => 'neo_modal_close',
      '#title' => $this->t('Cancel'),
      '#attributes' => [
        'class' => ['w-full'],
      ],
      '#check_ajax' => TRUE,
    ];

    return $form;
  }

}

<?php

namespace Drupal\neo_modal\Form;

use Drupal\block\BlockInterface;
use Drupal\Core\Ajax\AjaxHelperTrait;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user\Form\UserLoginForm;

/**
 * Provides a user login form.
 *
 * @internal
 */
class NeoModalAccountLoginForm extends UserLoginForm {

  use AjaxHelperTrait;

  /**
   * The settings.
   *
   * @var array
   */
  protected $modalSettings;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, BlockInterface $block = NULL) {
    $form = parent::buildForm($form, $form_state);
    $form['#id'] = 'neo-modal-account-login';
    $form['#neo_style'] = 'clean';
    $form['#action'] = Url::fromRoute('user.login')->toString();

    $form['forgot'] = [
      '#type' => 'neo_modal_link',
      '#title' => $this->t('Forgot your password?'),
      '#url' => Url::fromRoute('neo_modal.api.account.password', ['block' => $block->id()]),
    ];

    $form['actions']['#attributes']['class'][] = 'flex gap-4';
    $form['actions']['close'] = [
      '#type' => 'neo_modal_close',
      '#title' => $this->t('Cancel'),
      '#attributes' => [
        'class' => ['w-full'],
      ],
      '#check_ajax' => TRUE,
    ];

    $configuration = $block->getPlugin()->getConfiguration();
    if ($this->isAjax() && $configuration['modal'] ?? NULL) {
      $form['forgot']['#modal'] = ['nest' => TRUE, 'smartActions' => TRUE] + $configuration['modal'];
      $form['forgot']['#modal_preset'] = $configuration['modal_preset'] ?? NULL;
    }
    return $form;
  }

}

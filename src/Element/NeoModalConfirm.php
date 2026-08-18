<?php

namespace Drupal\neo_modal\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Attribute\RenderElement;

/**
 * Provides a render element that wraps child elements in a modal.
 *
 * Surrounds child elements with a <div> and adds attributes such as classes or
 * an HTML ID.
 *
 * Properties:
 * - #optional: Indicates whether the modal should render when it has no
 *   visible children. Defaults to FALSE.
 *
 * Usage example:
 * @code
 * $form['author'] = [
 *   '#type' => 'neo_modal_confirm',
 *   '#title' => $this->icon('Delete this thing', 'trash'),
 *   '#description' => $this->t('Are you sure you want to delete this thing?.'),
 *   '#confirm_text' => $this->t('Delete'),
 *   '#submit' => [
 *     '::deleteSubmit',
 *   ],
 *   '#attributes' => [
 *     'class' => ['btn'],
 *   ],
 *   '#wrapper_attributes' => [
 *     'class' => ['mt-4'],
 *   ],
 * ];
 *
 * $form['author']['name'] = [
 *   '#type' => 'textfield',
 *   '#title' => $this->t('Name'),
 * ];
 * @endcode
 */
#[RenderElement('neo_modal_confirm')]
class NeoModalConfirm extends NeoModal {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#description' => t('This action cannot be undone.'),
      '#confirm_text' => t('Confirm'),
      '#cancel_text' => t('Cancel'),
      '#submit' => [],
      '#submit_element' => [],
      '#modal_title' => t('Confirm'),
      '#modal_preset' => 'shelf_top',
    ] + parent::getInfo();
  }

  /**
   * {@inheritdoc}
   *
   * These used to live in #modal here, where a caller passing their own #modal
   * replaced the array wholesale and silently lost them. smartActions is no
   * longer listed: a confirm is always a form element, and form modals default
   * to smart actions already.
   */
  protected static function modalDefaults(): array {
    return ['scope' => TRUE] + parent::modalDefaults();
  }

  /**
   * {@inheritdoc}
   */
  public static function processModal(&$element, FormStateInterface $form_state, &$complete_form) {
    if (!empty($element['#confirm_text']) || !empty($element['#cancel_text'])) {
      $element['actions'] = [
        '#type' => 'actions',
      ];
      if (!empty($element['#confirm_text'])) {
        $element['actions']['confirm'] = $element['#submit_element'] + [
          '#type' => 'submit',
          '#value' => $element['#confirm_text'],
          '#submit' => $element['#submit'],
          '#limit_validation_errors' => [],
          '#attributes' => [
            'class' => ['btn btn-primary'],
          ],
        ];
      }
      if (!empty($element['#cancel_text'])) {
        $element['actions']['cancel'] = [
          '#type' => 'button',
          '#value' => $element['#cancel_text'],
          '#attributes' => [
            'data-neo-modal-close' => 'true',
            'class' => ['btn btn-secondary'],
          ],
        ];
      }
    }
    return parent::processModal($element, $form_state, $complete_form);
  }

  /**
   * Prevents optional modals from rendering if they have no children.
   *
   * @param array $element
   *   An associative array containing the properties and children of the
   *   modal.
   *
   * @return array
   *   The modified element.
   */
  public static function preRenderModal($element) {
    if ($element['#modal_title']) {
      $element['#modal']['title'] = $element['#modal_title'];
    }
    if ($element['#description']) {
      $element['_description'] = [
        '#type' => 'markup',
        '#markup' => $element['#description'],
      ];
      $element['#description'] = NULL;
    }
    return parent::preRenderModal($element);
  }

}

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
 *   '#type' => 'neo_modal',
 *   '#title' => $this->t('Author'),
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
      '#modal' => [
        'smartActions' => TRUE,
      ],
    ] + parent::getInfo();
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
    }
    return parent::preRenderModal($element);
  }

}

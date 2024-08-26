<?php

namespace Drupal\neo_modal\Element;

use Drupal\Component\Utility\Html as HtmlUtility;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Attribute\RenderElement;
use Drupal\Core\Render\Element\RenderElementBase;
use Drupal\neo_modal\Modal;

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
#[RenderElement('neo_modal')]
class NeoModal extends RenderElementBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = static::class;
    return [
      '#title' => '',
      '#title_attributes' => [],
      '#close' => '',
      '#options' => [],
      '#optional' => FALSE,
      '#process' => [
        [$class, 'processGroup'],
        [$class, 'processAjaxForm'],
        [$class, 'processModal'],
      ],
      '#pre_render' => [
        [$class, 'preRenderGroup'],
        [$class, 'preRenderModal'],
      ],
      '#theme_wrappers' => ['neo_modal'],
      '#value' => NULL,
    ];
  }

  /**
   * Processes a modal element.
   *
   * @param array $element
   *   An associative array containing the properties and children of the
   *   modal.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @return array
   *   The processed element.
   */
  public static function processModal(&$element, FormStateInterface $form_state, &$complete_form) {
    // Generate the ID of the element if it's not explicitly given.
    if (!isset($element['#id'])) {
      $element['#id'] = HtmlUtility::getUniqueId(implode('-', $element['#parents']) . '-neo-modal');
    }
    return $element;
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
    // Do not render optional modal elements if there are no children.
    if (empty($element['#printed']) && !empty($element['#optional']) && !Element::getVisibleChildren($element)) {
      $element['#printed'] = TRUE;
    }
    // Extract all content.
    $content = [];
    foreach (Element::children($element) as $key) {
      $content[$key] = $element[$key];
      unset($element[$key]);
    }
    if (!empty($element['#close'])) {
      $content['close'] = [
        '#type' => 'button',
        '#value' => $element['#close'],
        '#attributes' => [
          'data-neo-modal-close' => 'true',
          'class' => [
            'neo-modal--btn',
          ],
        ],
      ];
    }
    $modal = new Modal($content, $element['#options']);
    $modal->mergeTriggerAttributes($element['#title_attributes']);
    if (!empty($element['#parents'])) {
      $modal->setSmartActions();
      $modal->applyToForm($element['#title'], $content);
      $element['trigger'] = $element['#title'];
      $element['content'] = $content;
    }
    else {
      $modal->applyTo($element['#title']);
      $element['trigger'] = $element['#title'];
    }
    return $element;
  }

}

<?php

namespace Drupal\neo_modal\Element;

use Drupal\Core\EventSubscriber\MainContentViewSubscriber;
use Drupal\Core\Render\Attribute\RenderElement;
use Drupal\Core\Render\Element\RenderElementBase;
use Drupal\Core\Template\Attribute;

/**
 * Provides a render element that will close a modal.
 */
#[RenderElement('neo_modal_close')]
class NeoModalClose extends RenderElementBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = static::class;
    return [
      '#title' => '',
      '#check_ajax' => FALSE,
      '#attributes' => [],
      '#process' => [
        [$class, 'processGroup'],
      ],
      '#pre_render' => [
        [$class, 'preRenderGroup'],
        [$class, 'preRenderModalClose'],
      ],
      '#value' => NULL,
    ];
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
  public static function preRenderModalClose($element) {
    $attributes = new Attribute($element['#attributes']);
    $attributes->setAttribute('data-neo-modal-close', TRUE);
    $attributes->addClass('neo-modal--btn');
    $element['close'] = [
      '#type' => 'button',
      '#value' => $element['#title'] ?? t('Close'),
      '#attributes' => $attributes,
    ];
    if ($element['#check_ajax'] ?? FALSE) {
      $wrapper_format = \Drupal::request()->get(MainContentViewSubscriber::WRAPPER_FORMAT) ?? '';
      $element['close']['#access'] = str_contains($wrapper_format, 'drupal_ajax') ||
        str_contains($wrapper_format, 'drupal_modal') ||
        str_contains($wrapper_format, 'drupal_dialog');
    }
    return $element;
  }

}

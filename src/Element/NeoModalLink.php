<?php

namespace Drupal\neo_modal\Element;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Render\Attribute\RenderElement;
use Drupal\Core\Render\Element\Link;

/**
 * Provides a render element that will open a link in a modal.
 */
#[RenderElement('neo_modal_link')]
class NeoModalLink extends Link {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = static::class;
    return [
      '#title' => '',
      '#modal' => [],
      '#modal_preset' => NULL,
      '#pre_render' => [
        [$class, 'preRenderModalLink'],
      ],
    ] + parent::getInfo();
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
  public static function preRenderModalLink($element) {
    $modal_settings = $element['#modal'] ?? [];
    if (!empty($element['#modal_preset'])) {
      $modal_settings['preset'] = $element['#modal_preset'];
    }
    if ($modal_settings) {
      $element['#attributes']['class'][] = 'use-ajax';
      $element['#attributes']['data-dialog-type'] = 'modal';
      $element['#attributes']['data-dialog-options'] = Json::encode(['neo' => $modal_settings]);
      $element['#attached']['library'][] = 'core/drupal.ajax';
    }
    $element = parent::preRenderLink($element);
    return $element;
  }

}

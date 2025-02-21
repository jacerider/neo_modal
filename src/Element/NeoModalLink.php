<?php

namespace Drupal\neo_modal\Element;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Render\Attribute\RenderElement;
use Drupal\Core\Render\Element\Link;
use Drupal\Core\Url;

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
      '#ajax_method' => 'POST',
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
    $element['#attributes']['class'][] = 'use-modal-ajax';
    $element['#attributes']['data-dialog-type'] = 'modal';
    $element['#attributes']['data-dialog-options'] = Json::encode(['neo' => $modal_settings]);
    $element['#attached']['library'][] = 'neo_modal/links';
    if (!empty($element['#ajax_url']) && $element['#ajax_url'] instanceof Url) {
      $element['#attributes']['data-ajax-href'] = $element['#ajax_url']->toString();
    }
    if ($element['#ajax_method'] !== 'POST') {
      $element['#attributes']['data-ajax-http-method'] = $element['#ajax_method'];
    }
    $element = parent::preRenderLink($element);
    return $element;
  }

}

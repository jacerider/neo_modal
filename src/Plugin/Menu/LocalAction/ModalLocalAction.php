<?php

namespace Drupal\neo_modal\Plugin\Menu\LocalAction;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Menu\LocalActionDefault;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Defines a local action plugin with the needed dialog attributes.
 */
class ModalLocalAction extends LocalActionDefault {

  /**
   * {@inheritdoc}
   */
  public function getOptions(RouteMatchInterface $route_match) {
    $options = parent::getOptions($route_match);
    $attributes = [
      'class' => ['use-ajax'],
      'data-dialog-type' => 'modal',
      'data-dialog-options' => Json::encode($this->pluginDefinition['modal'] ?: [
        'width' => '700px',
      ]),
    ];
    $options['attributes'] = $this->pluginDefinition['attributes'] ?? [];
    $options['attributes'] = NestedArray::mergeDeep($options['attributes'], $attributes);

    return $options;
  }

}

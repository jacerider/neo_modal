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
    $modalOptions = $this->pluginDefinition['modal'] ?? [
      'width' => '700px',
    ];
    $attributes = [
      'class' => ['use-ajax'],
      'data-dialog-type' => 'modal',
      'data-dialog-options' => Json::encode($modalOptions),
    ];
    // Merge onto what the parent returned. This used to overwrite it with
    // $this->pluginDefinition['attributes'] first, which is not a local-action
    // plugin key at all -- so it was always empty, and the assignment silently
    // discarded any attributes the action declared under the standard
    // options.attributes.
    $options['attributes'] = NestedArray::mergeDeep($options['attributes'] ?? [], $attributes);
    if (!empty($options['attributes']['class'])) {
      // mergeDeep appends numerically keyed values, so a caller that also
      // declares use-ajax would otherwise get it twice.
      $options['attributes']['class'] = array_values(array_unique($options['attributes']['class']));
    }

    return $options;
  }

}

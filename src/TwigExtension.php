<?php

namespace Drupal\neo_modal;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Defines Twig extensions.
 */
class TwigExtension extends AbstractExtension {

  /**
   * Gets a unique identifier for this Twig extension.
   *
   * @return string
   *   A unique identifier for this Twig extension.
   */
  public function getName() {
    return 'twig.neo_modal';
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [
      new TwigFilter('neo_modal', [$this, 'renderModal']),
    ];
  }

  /**
   * Render the neo image style.
   */
  public static function renderModal($build, mixed $trigger, array $options = [], string $preset = NULL, $attributes = []) {
    if (empty($build)) {
      return $build;
    }
    $modal = new Modal($build, $options + [
      'scope' => TRUE,
    ], $preset);

    $modal->applyTo($trigger, $attributes);
    return $trigger;
  }

}

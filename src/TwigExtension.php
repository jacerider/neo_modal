<?php

namespace Drupal\neo_modal;

use Drupal\Core\Render\Markup as RenderMarkup;
use Drupal\Core\Template\Attribute;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
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
  public function getFunctions() {
    return [
      new TwigFunction('neo_modal', [$this, 'renderModal']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [
      new TwigFilter('neo_modal', [$this, 'filterModal']),
    ];
  }

  /**
   * Renders a modal dialog with a trigger element.
   *
   * @param mixed $trigger
   *   The trigger element that will open the modal. Can be a render array
   *   or string.
   * @param array $options
   *   (Optional) An associative array of modal options. Defaults to an
   *   empty array.
   * @param string|null $preset
   *   (Optional) The preset configuration name for the modal. Defaults to NULL.
   * @param array $attributes
   *   (Optional) An associative array of HTML attributes to apply to the
   *   modal. Defaults to an empty array.
   * @param array $trigger_attributes
   *   (Optional) An associative array of HTML attributes to apply to the
   *   trigger element. Defaults to an empty array.
   *
   * @return mixed
   *   The trigger element with modal functionality applied.
   */
  public static function renderModal(mixed $trigger, array $options = [], ?string $preset = NULL, $attributes = [], $trigger_attributes = []) {
    // NULL, not a placeholder string. This function builds a modal whose body
    // comes from its options (image/video/iframe/attach), so it has no inline
    // content -- and Modal already models that as NULL. Passing text here made
    // applyTo() emit a <template> carrying it beside every trigger, which for
    // any option set other than the media ones rendered those literal words as
    // the modal body.
    $modal = new Modal(NULL, $options + [
      'scope' => TRUE,
    ], $preset);
    if ($trigger_attributes) {
      $trigger_attributes = new Attribute($trigger_attributes);
      $modal->getTriggerAttributes()->merge($trigger_attributes);
    }
    $modal->applyTo($trigger, $attributes);
    return $trigger;
  }

  /**
   * Applies a modal dialog to a trigger element.
   *
   * This method wraps content in a modal dialog and attaches it to a trigger
   * element. It handles various input types for both the modal content and
   * trigger, converting them to appropriate render arrays as needed.
   *
   * @param mixed $build
   *   The modal content to be displayed. Can be a string, Markup object, or
   *   render array.
   *   If empty, the build is returned unchanged.
   * @param mixed $trigger
   *   The element that triggers the modal. Can be a string, Markup object,
   *   or render array.
   * @param array $options
   *   (Optional) Configuration options for the modal. Defaults to an empty
   *   array.
   *   The 'scope' option is set to TRUE by default.
   * @param string|null $preset
   *   (Optional) The name of a preset modal configuration. Defaults to NULL.
   * @param array $attributes
   *   (Optional) HTML attributes to apply to the modal element. Defaults to
   *   an empty array.
   * @param array $trigger_attributes
   *   (Optional) HTML attributes to apply to the trigger element. Defaults
   *   to an empty array.
   *
   * @return mixed
   *   The trigger element with the modal applied to it.
   */
  public static function filterModal($build, mixed $trigger, array $options = [], ?string $preset = NULL, $attributes = [], $trigger_attributes = []) {
    if (empty($build)) {
      return $build;
    }
    if (is_string($build)) {
      $build = RenderMarkup::create($build);
    }
    if ($build instanceof Markup) {
      $build = [
        '#type' => 'inline_template',
        '#template' => (string) $build,
      ];
    }
    if ($trigger instanceof Markup) {
      $trigger = [
        '#markup' => (string) $trigger,
      ];
    }
    $modal = new Modal($build, $options + [
      'scope' => TRUE,
    ], $preset);

    if ($trigger_attributes) {
      $trigger_attributes = new Attribute($trigger_attributes);
      $modal->getTriggerAttributes()->merge($trigger_attributes);
    }

    $modal->applyTo($trigger, $attributes);
    return $trigger;
  }

}

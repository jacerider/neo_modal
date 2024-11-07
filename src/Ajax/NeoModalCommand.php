<?php

namespace Drupal\neo_modal\Ajax;

use Drupal\Core\Ajax\CommandInterface;
use Drupal\Core\Ajax\CommandWithAttachedAssetsInterface;
use Drupal\Core\Ajax\CommandWithAttachedAssetsTrait;

/**
 * Defines an AJAX command to create and open a modal.
 *
 * @ingroup ajax
 */
class NeoModalCommand implements CommandInterface, CommandWithAttachedAssetsInterface {

  use CommandWithAttachedAssetsTrait;

  /**
   * The content for the matched element(s).
   *
   * Either a render array or an HTML string.
   *
   * @var string|array
   */
  protected $content;

  /**
   * A settings array to be passed to any attached JavaScript behavior.
   *
   * @var array
   */
  protected $settings;

  /**
   * Constructs an InsertCommand object.
   *
   * @param string|array $content
   *   The content that will be inserted in the matched element(s), either a
   *   render array or an HTML string.
   * @param array $settings
   *   An array of NeoModal settings.
   */
  public function __construct($content = [], array $settings = []) {
    if (!is_array($content)) {
      $content = ['#markup' => $content];
    }
    $content['#attached']['library'][] = 'neo_modal/modal-ajax';
    $this->content = $content;
    $this->settings = $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    return [
      'command' => 'neoModal',
      'data' => $this->getRenderedContent(),
      'settings' => $this->settings,
    ];
  }

}

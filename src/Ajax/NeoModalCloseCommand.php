<?php

namespace Drupal\neo_modal\Ajax;

use Drupal\Core\Ajax\CommandInterface;
use Drupal\Core\Ajax\CommandWithAttachedAssetsInterface;
use Drupal\Core\Ajax\CommandWithAttachedAssetsTrait;

/**
 * Defines an AJAX command to close a modal.
 *
 * @ingroup ajax
 */
class NeoModalCloseCommand implements CommandInterface, CommandWithAttachedAssetsInterface {

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
   * Constructs an InsertCommand object.
   */
  public function __construct() {
    $content = [];
    $content['#attached']['library'][] = 'neo_modal/modal-ajax';
    $this->content = $content;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $this->getRenderedContent();
    return [
      'command' => 'neoModalClose',
    ];
  }

}

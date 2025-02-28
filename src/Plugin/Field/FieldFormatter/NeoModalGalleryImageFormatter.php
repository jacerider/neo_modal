<?php

namespace Drupal\neo_modal\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * Plugin implementation of the 'neo_modal_image_gallery' formatter.
 */
#[FieldFormatter(
  id: 'neo_modal_image_gallery',
  label: new TranslatableMarkup('Neo | Modal Gallery'),
  field_types: [
    'image',
  ]
)]
final class NeoModalGalleryImageFormatter extends NeoModalGalleryBaseFormatter {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return ($field_definition->getFieldStorageDefinition()->getSetting('target_type') == 'file');
  }

}

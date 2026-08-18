<?php

namespace Drupal\neo_modal\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\media\MediaInterface;
use Drupal\neo_image\NeoImage;
use Drupal\neo_modal\Modal;
use Drupal\neo_settings\Element\NeoSettingsVariation;

/**
 * Plugin implementation of the 'neo_modal_image_gallery' formatter.
 */
class NeoModalGalleryBaseFormatter extends EntityReferenceFormatterBase {

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a NeoModalMediaFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, RendererInterface $renderer) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'thumbnail' => [
        'dimensions' => [
          'sm' => [
            'width' => 640,
            'height' => '',
          ],
        ],
      ],
      'full' => [
        'dimensions' => [
          'sm' => [
            'width' => 1600,
            'height' => '',
          ],
        ],
      ],
      'thumbnail_title' => '',
      'modal_variation' => '',
      'modal_title' => '',
      'modal_group' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   *
   * This has to be overridden because FileFormatterBase expects $item to be
   * of type \Drupal\file\Plugin\Field\FieldType\FileItem and calls
   * isDisplayed() which is not in FieldItemInterface.
   */
  protected function needsEntityLoad(EntityReferenceItem $item) {
    return !$item->hasNewEntity();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['thumbnail'] = [
      '#type' => 'neo_settings',
      '#title' => $this->t('Thumbnail Image Settings'),
      '#settings_id' => 'neo_image',
      '#open' => FALSE,
      '#default_value' => $this->getSetting('thumbnail'),
    ];

    $element['full'] = [
      '#type' => 'neo_settings',
      '#title' => $this->t('Full Image Settings'),
      '#settings_id' => 'neo_image',
      '#open' => FALSE,
      '#default_value' => $this->getSetting('full'),
    ];

    $element['thumbnail_title'] = [
      '#type' => 'select',
      '#title' => $this->t('Thumbnail Title'),
      '#options' => $this->getTitleOptions(),
      '#default_value' => $this->getSetting('thumbnail_title'),
    ];

    $element['modal_variation'] = [
      '#type' => 'neo_settings_variation',
      '#title' => $this->t('Modal Preset'),
      '#settings_repository_id' => 'neo_modal.settings',
      '#default_value' => $this->getSetting('modal_variation'),
    ];

    $element['modal_title'] = [
      '#type' => 'select',
      '#title' => $this->t('Modal Title'),
      '#options' => $this->getTitleOptions(),
      '#default_value' => $this->getSetting('modal_title'),
    ];

    $element['modal_group'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Modal Group'),
      '#description' => $this->t('The group to which this gallery belongs. If left empty, the "gallery" group will be used.'),
      '#default_value' => $this->getSetting('modal_group'),
    ];

    return $element;
  }

  /**
   * Get the entity title options.
   *
   * @return array
   *   The entity title options.
   */
  protected function getTitleOptions() {
    return [
      '' => 'None',
      'entity_title' => 'Media Title',
      'image_alt' => 'Image Alt',
      'image_title' => 'Image Title',
    ];
  }

  /**
   * Resolves one of the getTitleOptions() choices to a string.
   *
   * The alt and title properties live on the field item, not on the rendered
   * image. Both title settings used to read them back out of
   * $elements[$delta]['image'], which is not built until later in the loop, so
   * 'image_alt' and 'image_title' resolved to NULL -- silently dropping the
   * thumbnail caption, and reaching the non-nullable Modal::setTitle() as a
   * TypeError for the modal title.
   *
   * @param string $setting
   *   One of the getTitleOptions() keys.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The media or file entity being displayed.
   *
   * @return string
   *   The resolved title, or an empty string when there is none.
   */
  protected function resolveTitle($setting, EntityInterface $entity) {
    switch ($setting) {
      case 'entity_title':
        return (string) $entity->label();

      case 'image_alt':
        return $this->getImageProperty($entity, 'alt');

      case 'image_title':
        return $this->getImageProperty($entity, 'title');
    }
    return '';
  }

  /**
   * Reads a property off the image item behind the displayed entity.
   *
   * For a media entity that is the source field's item; for a file it is the
   * referring image item, which is where an image field stores alt and title.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The media or file entity being displayed.
   * @param string $property
   *   The item property to read, 'alt' or 'title'.
   *
   * @return string
   *   The property value, or an empty string when it is unset or the item
   *   does not carry that property.
   */
  protected function getImageProperty(EntityInterface $entity, $property) {
    $item = NULL;
    if ($entity instanceof MediaInterface) {
      $source = $entity->getSource();
      $fieldName = $source->getSourceFieldDefinition($entity->bundle->entity)->getName();
      $item = $entity->get($fieldName)->first();
    }
    elseif (isset($entity->_referringItem)) {
      // Set by EntityReferenceFormatterBase::getEntitiesToView().
      $item = $entity->_referringItem;
    }
    if (!$item) {
      return '';
    }
    // Read the raw values rather than $item->{$property}, which throws when the
    // item type has no such property.
    $values = $item->getValue();
    return (string) ($values[$property] ?? '');
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $thumbnailSettings = $this->getSetting('thumbnail');
    $thumbnailDimensions = $thumbnailSettings['dimensions'] ?? [];
    if ($dimensionSummary = NeoImage::summaryFromDimensions($thumbnailDimensions)) {
      $summary[] = $this->t('<strong>@label:</strong>', [
        '@label' => $this->t('Thumbnail'),
      ]);
      foreach ($dimensionSummary as $sum) {
        $summary[] = '-- ' . $sum;
      }
    }

    $fullSettings = $this->getSetting('full');
    $fullDimensions = array_filter($fullSettings['dimensions'] ?? []);
    if ($dimensionSummary = NeoImage::summaryFromDimensions($fullDimensions)) {
      $summary[] = $this->t('<strong>@label:</strong>', [
        '@label' => $this->t('Full'),
      ]);
      foreach ($dimensionSummary as $sum) {
        $summary[] = '-- ' . $sum;
      }
    }

    if ($this->getSetting('thumbnail_title')) {
      $summary[] = $this->t('<strong>@label</strong> @value', [
        '@label' => $this->t('Thumbnail Title'),
        '@value' => $this->getTitleOptions()[$this->getSetting('thumbnail_title')] ?? 'None',
      ]);
    }

    // No full image means no modal is built at all, so describing the modal
    // settings below would advertise a lightbox this display does not have.
    // Clearing the Full Image fieldset is the supported way to render these
    // as plain images, so say that rather than listing inert settings.
    if (!$fullDimensions) {
      $summary[] = $this->t('No full image configured: images render without a modal.');
      return $summary;
    }

    if ($this->getSetting('modal_variation')) {
      $options = NeoSettingsVariation::getOptions('neo_modal.settings');
      $summary[] = $this->t('<strong>@label</strong> @value', [
        '@label' => $this->t('Modal Preset'),
        '@value' => $options[$this->getSetting('modal_variation')] ?? 'Default',
      ]);
    }

    if ($this->getSetting('modal_title')) {
      $summary[] = $this->t('<strong>@label</strong> @value', [
        '@label' => $this->t('Modal Title'),
        '@value' => $this->getTitleOptions()[$this->getSetting('modal_title')] ?? 'None',
      ]);
    }

    $summary[] = $this->t('<strong>@label</strong> @value', [
      '@label' => $this->t('Modal Group:'),
      '@value' => $this->getSetting('modal_group') ?: 'gallery',
    ]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $entity_items = $this->getEntitiesToView($items, $langcode);
    $thumbnailSettings = $this->getSetting('thumbnail');
    $thumbnailDimensions = $thumbnailSettings['dimensions'] ?? [];
    $fullSettings = $this->getSetting('full');
    // array_filter to match settingsSummary(): the two used to disagree about
    // what counts as "no full image configured", so the summary could describe
    // a modal that viewElements() then declined to build.
    $fullDimensions = array_filter($fullSettings['dimensions'] ?? []);

    foreach ($entity_items as $delta => $entity) {
      /** @var \Drupal\media\MediaInterface|\Drupal\file\FileInterface $entity */
      $title = $this->resolveTitle($this->getSetting('thumbnail_title'), $entity);

      $thumbnail = NeoImage::createFromEntity($entity, $title);
      $thumbnail->autoFromDimensions($thumbnailDimensions);
      $elements[$delta]['image'] = $thumbnail->toRenderable();

      if ($title) {
        $elements[$delta]['title'] = [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $title,
          '#attributes' => [
            'class' => ['neo-modal-entity-gallery-title'],
          ],
        ];
      }

      if (!empty($fullDimensions)) {
        $full = NeoImage::createFromEntity($entity);
        $full->autoFromDimensions($fullDimensions);
        $modal = new Modal($full->toRenderable(), [], $this->getSetting('modal_variation'));
        $modal->setGroup($this->getSetting('modal_group') ?: 'gallery');
        // setTitle() is not nullable, so an unresolvable title must not reach
        // it.
        if ($modalTitle = $this->resolveTitle($this->getSetting('modal_title'), $entity)) {
          $modal->setTitle($modalTitle);
        }
        if (in_array($entity->bundle(), ['remote_video', 'video'])) {
          $modal->setTriggerOverlay(t('View Video'), 'play-circle');
          $videoUrl = $entity->getSource()->getSourceFieldValue($entity);
          $modal->setVideo($videoUrl);
        }
        $modal->applyTo($elements[$delta]);
      }

      // Add cacheability of each item in the field.
      $this->renderer->addCacheableDependency($elements[$delta], $entity);
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity) {
    return $entity->access('view', NULL, TRUE)
      ->andIf(parent::checkAccess($entity));
  }

}

<?php

namespace Drupal\neo_modal\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\neo_image\NeoImage;
use Drupal\neo_modal\Modal;
use Drupal\neo_settings\Element\NeoSettingsVariation;

/**
 * Plugin implementation of the 'neo_modal_media' formatter.
 */
#[FieldFormatter(
  id: 'neo_modal_media_gallery',
  label: new TranslatableMarkup('Neo | Modal Gallery'),
  field_types: [
    'entity_reference',
  ]
)]
final class NeoModalMediaGalleryFormatter extends EntityReferenceFormatterBase {

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
   * Get the media title options.
   *
   * @return array
   *   The media title options.
   */
  protected function getTitleOptions() {
    return [
      '' => 'None',
      'media_title' => 'Media Title',
      'image_alt' => 'Image Alt',
      'image_title' => 'Image Title',
    ];
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
    $media_items = $this->getEntitiesToView($items, $langcode);
    $thumbnailSettings = $this->getSetting('thumbnail');
    $thumbnailDimensions = $thumbnailSettings['dimensions'] ?? [];
    $fullSettings = $this->getSetting('full');
    $fullDimensions = $fullSettings['dimensions'] ?? [];

    foreach ($media_items as $delta => $media) {
      /** @var \Drupal\media\MediaInterface $media */
      $title = '';
      switch ($this->getSetting('thumbnail_title')) {
        case 'media_title':
          $title = $media->label();
          break;

        case 'image_alt':
          $title = $elements[$delta]['image']['#alt'];
          break;

        case 'image_title':
          $title = $elements[$delta]['image']['#title'];
          break;
      }

      $thumbnail = NeoImage::createFromEntity($media, $title);
      $thumbnail->autoFromDimensions($thumbnailDimensions);
      $elements[$delta]['image'] = $thumbnail->toRenderable();

      if ($title) {
        $elements[$delta]['title'] = [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $title,
          '#attributes' => [
            'class' => ['neo-modal-media-gallery-title'],
          ],
        ];
      }

      if (!empty($fullDimensions)) {
        $full = NeoImage::createFromEntity($media);
        $full->autoFromDimensions($fullDimensions);
        $modal = new Modal($full->toRenderable(), [], $this->getSetting('modal_variation'));
        $modal->setGroup($this->getSetting('modal_group') ?: 'gallery');
        switch ($this->getSetting('modal_title')) {
          case 'media_title':
            $modal->setTitle($media->label());
            break;

          case 'image_alt':
            $modal->setTitle($elements[$delta]['image']['#alt']);
            break;

          case 'image_title':
            $modal->setTitle($elements[$delta]['image']['#title']);
            break;
        }
        if (in_array($media->bundle(), ['remote_video', 'video'])) {
          $modal->setTriggerOverlay(t('View Video'), 'play-circle');
          $videoUrl = $media->getSource()->getSourceFieldValue($media);
          $modal->setVideo($videoUrl);
        }
        $modal->applyTo($elements[$delta]);
      }

      // Add cacheability of each item in the field.
      $this->renderer->addCacheableDependency($elements[$delta], $media);
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    // This formatter is only available for entity types that reference
    // media items.
    return ($field_definition->getFieldStorageDefinition()->getSetting('target_type') == 'media');
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity) {
    return $entity->access('view', NULL, TRUE)
      ->andIf(parent::checkAccess($entity));
  }

}

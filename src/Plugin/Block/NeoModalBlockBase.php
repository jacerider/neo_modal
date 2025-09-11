<?php

namespace Drupal\neo_modal\Plugin\Block;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\neo\NeoLinkitTrait;
use Drupal\neo_icon\IconTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base for Neo modal blocks.
 */
abstract class NeoModalBlockBase extends BlockBase implements NeoModalBlockInterface, ContainerFactoryPluginInterface {

  use IconTrait;
  use NeoLinkitTrait;
  use NeoModalBlockTrait;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Creates a LocalActionsBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'block_id' => '',
      'blocks' => [
        'header' => [],
        'footer' => [],
      ],
      'trigger_text' => '',
      'trigger_icon' => '',
      'trigger_url' => '',
      'trigger_icon_position' => 'before',
      'modal_ajax' => FALSE,
    ] + $this->neoModalDefaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['#id'] = 'block-settings';
    $form['#type'] = 'container';
    $form['#process'][] = [$this, 'neoModalProcess'];

    $form['trigger_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Trigger Text'),
      '#default_value' => $this->configuration['trigger_text'],
    ];

    $form['trigger_icon'] = [
      '#type' => 'neo_icon_select',
      '#title' => $this->t('Trigger Icon'),
      '#default_value' => $this->configuration['trigger_icon'],
    ];

    $form['trigger_url'] = [
      '#title' => $this->t('Trigger URL Override (AJAX only)'),
      '#description' => $this->t('The optional URL that will be used for users without javascript.'),
    ] + $this->getLinkitElement($this->configuration['trigger_url']);

    $form['trigger_icon_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Trigger Icon Position'),
      '#options' => [
        'before' => $this->t('Before'),
        'after' => $this->t('After'),
      ],
      '#default_value' => $this->configuration['trigger_icon_position'],
      '#states' => [
        'visible' => [
          ':input[name="settings[trigger_icon][value]"]' => ['filled' => TRUE],
        ],
      ],
    ];

    $form['modal_ajax'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Load modal content via AJAX'),
      '#default_value' => $this->configuration['modal_ajax'],
    ];

    $form['blocks'] = [
      '#type' => 'details',
      '#title' => $this->t('Blocks (Header/Footer)'),
      '#weight' => 10,
    ];
    $form['blocks']['header'] = [
      '#type' => 'details',
      '#title' => $this->t('Header'),
    ];
    $form['blocks']['header']['blocks'] = $this->buildBlocksForm([], $form_state, $this->configuration['blocks']['header']);
    $form['blocks']['footer'] = [
      '#type' => 'details',
      '#title' => $this->t('Footer'),
    ];
    $form['blocks']['footer']['blocks'] = $this->buildBlocksForm([], $form_state, $this->configuration['blocks']['footer']);

    return $form;
  }

  /**
   * Ajax callback for the block form.
   */
  public static function blockFormAjax($form, FormStateInterface $form_state) {
    return NestedArray::getValue($form, array_slice($form_state->getTriggeringElement()['#array_parents'], 0, -1));
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    /** @var \Drupal\Core\Form\SubformStateInterface $form_state */
    parent::blockSubmit($form, $form_state);
    $this->configuration['block_id'] = $form_state->getCompleteFormState()->getValue('id');
    $this->configuration['blocks'] = [];
    foreach ($form_state->getValue('blocks') as $id => $data) {
      if (!empty($data['blocks'])) {
        $this->configuration['blocks'][$id] = $data['blocks'];
      }
    }
    $this->configuration['trigger_text'] = $form_state->getValue(['trigger_text'], '');
    $this->configuration['trigger_icon'] = $form_state->getValue(['trigger_icon'], '');
    $this->configuration['trigger_url'] = $form_state->getValue(['trigger_url'], '');
    $this->configuration['trigger_icon_position'] = $form_state->getValue(['trigger_icon_position'], '');
    $this->configuration['modal_ajax'] = $form_state->getValue(['modal_ajax']);

    // Submit modal settings.
    $this->neoModalBlockSubmit($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $modal = $this->buildModal();
    $blockId = $this->configuration['block_id'];
    $url = $blockId ? Url::fromRoute('neo_modal.api.block.view', [
      'block' => $blockId,
    ]) : Url::fromRoute('<current>');
    $build = [
      '#type' => 'link',
      '#title' => $this->icon($this->configuration['trigger_text'], $this->configuration['trigger_icon'])
        ->iconPosition($this->configuration['trigger_icon_position']),
      '#url' => $url,
    ];
    if ($blockId && $this->configuration['modal_ajax']) {
      $build['#type'] = 'neo_modal_link';
      $build['#modal'] = $modal->getValues();
      $build['#modal_preset'] = $this->configuration['modal_preset'];
      if ($url = $this->configuration['trigger_url']) {
        $build['#ajax_url'] = $build['#url'];
        $build['#url'] = Url::fromUserInput($url);
      }
    }
    else {
      $modal->setContent($this->buildModalContent());
      $modal->applyTo($build);
    }
    return $build;
  }

  /**
   * Builds the modal trigger link.
   *
   * This method constructs an array representing a link that triggers the
   * modal. The link includes an icon and text specified in the block
   * configuration. The URL for the link is generated from a route with the
   * block ID as a parameter.
   *
   * @return array
   *   A render array representing the modal trigger link.
   */
  protected function buildModalTrigger(): array {
    $modal = $this->buildModal();
    $build = [
      '#type' => 'link',
      '#title' => $this->icon($this->configuration['trigger_text'], $this->configuration['trigger_icon'])
        ->iconPosition($this->configuration['trigger_icon_position']),
      '#url' => Url::fromRoute('neo_modal.api.block.view', [
        'block' => $this->configuration['block_id'],
      ]),
    ];
    if ($this->configuration['modal_ajax']) {
      $build['#type'] = 'neo_modal_link';
      $build['#modal'] = $modal->getValues();
      $build['#modal_preset'] = $this->configuration['modal_preset'];
    }
    else {
      $modal->setContent($this->buildModalContent());
      $modal->applyTo($build);
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildModalContent(): array {
    $build = [];
    if ($blocksBuild = $this->buildBlockContent($this->configuration['blocks']['header'])) {
      $build['header'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#weight' => -1000,
        'block' => $blocksBuild,
      ];
    }
    if ($blocksBuild = $this->buildBlockContent($this->configuration['blocks']['footer'])) {
      $build['footer'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#weight' => 1000,
        'block' => $blocksBuild,
      ];
    }
    return $build;
  }

  /**
   * Build modal block content.
   *
   * @param array $config
   *   The block configuration. An array of block IDs and their settings.
   *
   * @return array
   *   The renderable array of block content.
   */
  protected function buildBlockContent(array $config) {
    $build = [];
    $count = 0;
    foreach ($config as $block_id => $settings) {
      $block = $this->entityTypeManager->getStorage('block')->load($block_id);
      if ($block && $block->access('view')) {
        $build[$block_id] = $this->entityTypeManager->getViewBuilder('block')->view($block);
        $build[$block_id]['#weight'] = $count;
      }
      $count++;
    }
    return $build;
  }

  /**
   * Build block selection form.
   */
  protected function buildBlocksForm(array $form, FormStateInterface $form_state, $settings = []) {
    $form = [
      '#type' => 'table',
      '#header' => [
        $this->t('Block'),
        $this->t('Weight'),
      ],
      '#element_validate' => [[get_class($this), 'validateBlocks']],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'menu-weight',
        ],
      ],
    ];

    $theme = $form_state->get('block_theme') ?: \Drupal::routeMatch()->getParameter('theme');
    if (!$theme) {
      // Drupal no longer sets the block_theme in the form state.
      if ($block = \Drupal::routeMatch()->getParameter('block')) {
        $theme = $block->getTheme();
      }
    }
    if (!$theme) {
      $theme = \Drupal::config('system.theme')->get('default');
    }
    $count = 0;
    foreach ($this->getBlockOptions($theme) as $id => $label) {
      $status = isset($settings[$id]);
      $weight = $status ? array_search($id, array_keys($settings)) : $count + 100;
      $form[$id]['#attributes']['class'][] = 'draggable';
      $form[$id]['#weight'] = $weight;
      $form[$id]['status'] = [
        '#type' => 'checkbox',
        '#title' => $label,
        '#default_value' => $status,
      ];
      $form[$id]['weight'] = [
        '#type' => 'number',
        '#title' => t('Weight for @title', ['@title' => $label]),
        '#title_display' => 'invisible',
        '#default_value' => 0,
        '#attributes' => ['class' => ['menu-weight']],
      ];
      $count++;
    }
    uasort($form, ['Drupal\Component\Utility\SortArray', 'sortByWeightProperty']);
    return $form;
  }

  /**
   * Given the menu form values, clean them into a simple array.
   */
  public static function validateBlocks($element, FormStateInterface $form_state) {
    $values = $form_state->getValue($element['#parents']) ?: [];
    $values = array_filter($values, function ($value) {
      return $value['status'] == 1;
    });
    array_walk($values, function (&$value) {
      unset($value['status'], $value['weight']);
      $value = array_filter($value);
    });
    $form_state->setValueForElement($element, $values);
  }

  /**
   * Get available blocks as options.
   */
  protected function getBlockOptions($theme) {
    $theme_blocks = $this->entityTypeManager->getStorage('block')->loadByProperties(['theme' => $theme]);
    $options = [];
    if (!empty($theme_blocks)) {
      foreach ($theme_blocks as $block) {
        $options[$block->id()] = $block->label();
      }
    }
    unset($options[$this->configuration['block_id']]);
    return $options;
  }

}

<?php

namespace Drupal\neo_modal\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a block to display a view within a modal.
 *
 * @Block(
 *   id = "neo_modal_slide_menu",
 *   admin_label = @Translation("Slide Menu"),
 *   provider = "neo_modal"
 * )
 */
class NeoModalSlideMenuBlock extends NeoModalBlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'menus' => [],
      'back_status' => FALSE,
      'all_status' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $form['back_status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Back Link'),
      '#default_value' => $this->configuration['back_status'],
    ];

    $form['all_status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable View All Link'),
      '#description' => $this->t('If enabled, a link will be added to nested menus that allows users to visit the parent.'),
      '#default_value' => $this->configuration['all_status'],
    ];

    $settings = $this->configuration['menus'];
    $limit_menus = NULL;
    $form['menus'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Menus'),
        $this->t('Weight'),
      ],
      '#element_validate' => [[get_class($this), 'validateMenuForm']],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'menu-weight',
        ],
      ],
    ];
    $count = 0;
    foreach ($this->getMenuOptions($settings, $limit_menus) as $id => $label) {
      $form['menus'][$id]['#attributes']['class'][] = 'draggable';
      $form['menus'][$id]['#weight'] = $count;
      $form['menus'][$id]['status'] = [
        '#type' => 'checkbox',
        '#title' => $label,
        '#default_value' => in_array($id, $settings),
      ];
      $form['menus'][$id]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight for @title', ['@title' => $label]),
        '#title_display' => 'invisible',
        '#default_value' => $count,
        '#attributes' => ['class' => ['menu-weight']],
      ];
      $count++;
    }

    return $form;
  }

  /**
   * Given the menu form values, clean them into a simple array.
   */
  public static function validateMenuForm($element, FormStateInterface $form_state) {
    $values = $form_state->getValue($element['#parents']);
    $values = array_filter($values, function ($menu) {
      return $menu['status'] == 1;
    });
    uasort($values, ['Drupal\Component\Utility\SortArray', 'sortByWeightElement']);
    $values = array_keys($values);
    $form_state->setValueForElement($element, $values);
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['back_status'] = (bool) $form_state->getValue(['back_status']);
    $this->configuration['all_status'] = (bool) $form_state->getValue(['all_status']);
    $this->configuration['menus'] = $form_state->getValue(['menus']);
  }

  /**
   * {@inheritdoc}
   */
  public function buildModalContent(): array {
    $build = parent::buildModalContent();
    $build['content'] = [
      '#type' => 'slide_menu',
      '#menu_ids' => $this->configuration['menus'],
      '#back_status' => $this->configuration['back_status'],
      '#all_status' => $this->configuration['all_status'],
    ];
    return $build;
  }

  /**
   * Gets a list of menu names for use as options.
   *
   * @param array $settings
   *   (optional) The current enabled menus as an array of menu ids.
   * @param array $limit_menus
   *   (optional) Array of menu names to limit the options, or NULL to load all.
   *
   * @return array
   *   Keys are menu names (ids) values are the menu labels.
   */
  protected function getMenuOptions(array $settings = [], ?array $limit_menus = NULL) {
    $menus = $this->entityTypeManager->getStorage('menu')->loadMultiple($limit_menus);
    $options = array_flip($settings);
    /** @var \Drupal\system\MenuInterface[] $menus */
    foreach ($menus as $menu) {
      $options[$menu->id()] = $menu->label();
    }
    return $options;
  }

}

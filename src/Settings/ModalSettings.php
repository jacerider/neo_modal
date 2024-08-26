<?php

namespace Drupal\neo_modal\Settings;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\neo_modal\Ajax\NeoModalCloseCommand;
use Drupal\neo_modal\Ajax\NeoModalCommand;
use Drupal\neo_modal\Modal;
use Drupal\neo_settings\Plugin\SettingsBase;

/**
 * Module settings.
 *
 * @Settings(
 *   id = "neo_modal",
 *   label = @Translation("Neo Modal"),
 *   config_name = "neo_modal.settings",
 *   menu_title = @Translation("Modal"),
 *   route = "/admin/config/neo/neo-modal",
 *   admin_permission = "administer neo_modal",
 *   variation_allow = true,
 *   variation_label = "preset",
 *   variation_label_plural = "presets",
 *   variation_conditions = false,
 *   variation_ordering = false,
 * )
 */
class ModalSettings extends SettingsBase {

  // /**
  //  * {@inheritdoc}
  //  */
  // public static function defaultFormConfig() {
  //   return [
  //     'mode' => 'simple',
  //   ] + parent::defaultFormConfig();
  // }

  /**
   * {@inheritdoc}
   *
   * Instance settings are settings that are set both in the base form and the
   * variation form. They are editable in both forms and the values are merged
   * together.
   */
  protected function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $mode = $this->getFormConfigValue('mode');

    $form['#type'] = 'container';

    $form['simple'] = [
      '#type' => 'link',
      '#title' => 'Simple',
      '#url' => Url::fromRoute('<front>'),
      '#attributes' => [
        'class' => [
          'btn',
          'w-full',
          'text-center',
        ],
      ],
    ];
    $level1 = [
      'text' => [
        '#markup' => 'Simple: Level 1<br><br>',
      ],
      'nested' => [
        '#type' => 'link',
        '#title' => 'Level 2',
        '#url' => Url::fromRoute('<front>'),
        '#attributes' => [
          'class' => [
            'btn',
            'w-full',
            'text-center',
          ],
        ],
      ],
    ];
    $level2 = [
      'text' => [
        '#markup' => 'Simple: Level 2<br><br>',
      ],
      'nested' => [
        '#type' => 'link',
        '#title' => 'Level 3',
        '#url' => Url::fromRoute('<front>'),
        '#attributes' => [
          'class' => [
            'btn',
            'w-full',
            'text-center',
          ],
        ],
      ],
    ];
    $level3 = [
      'text' => [
        '#markup' => 'Simple: Level 3<br><br>',
      ],
      'close' => [
        '#type' => 'link',
        '#title' => 'Close',
        '#url' => Url::fromRoute('<front>'),
        '#attributes' => [
          'data-neo-modal-close' => TRUE,
          'class' => [
            'btn',
            'w-full',
            'text-center',
          ],
        ],
      ],
    ];
    $modal = new Modal($level3);
    $modal->setColorScheme('accent-solid');
    $modal->applyTo($level2['nested']);
    $modal = new Modal($level2);
    $modal->setColorScheme('secondary-solid-dark');
    $modal->applyTo($level1['nested']);
    $modal = new Modal($level1);
    // $modal->setColorScheme('primary-solid');
    $modal->applyTo($form['simple']);

    $form['media'] = [
      '#type' => 'fieldset',
      '#title' => 'Media',
      '#attributes' => [
        'class' => ['form--inline'],
      ],
    ];
    $images = [
      [
        'title' => 'Derek Thomson',
        'path' => 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?ixlib=rb-1.2.1&ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&auto=format&fit=crop&q=80',
      ],
      [
        'title' => 'Kace Rodriguez',
        'path' => 'https://images.unsplash.com/photo-1461301214746-1e109215d6d3?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&q=80',
      ],
      [
        'title' => 'whoisbenjamin',
        'path' => 'https://images.unsplash.com/photo-1585338447937-7082f8fc763d?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&q=80',
      ],
      [
        'title' => 'David Marcu',
        'path' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&q=80',
      ],
      [
        'title' => 'Iris Papillon',
        'path' => 'https://images.unsplash.com/photo-1446630073557-fca43d580fbe?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&q=80',
      ],
    ];
    foreach ($images as $delta => $image) {
      $path = $image['path'];
      $form['media'][$delta] = [
        '#type' => 'link',
        '#title' => [
          '#markup' => '<img src="' . $path . '&w=140&h=140" alt="Image" />',
        ],
        '#url' => Url::fromUri($path . '&w=1600'),
      ];
      $modal = new Modal();
      $modal->setTitle($image['title']);
      // $modal->setIcon('image');
      $modal->setGroup('media');
      // $modal->setNumeration();
      // $modal->setContentPadding('0px');
      // $modal->setCloseButton('end-out');
      // $modal->setFit();
      // $modal->setHeaderInContent();
      // $modal->setCloseButton(FALSE);
      // $modal->setContentScroll();
      $modal->setImage($path . '&w=1600');
      $modal->applyTo($form['media'][$delta]);
    }

    $form['media']['youtube'] = [
      '#type' => 'link',
      '#title' => [
        '#type' => 'inline_template',
        '#template' => '<img style="height:140px;" src="https://img.youtube.com/vi/9bZkp7q19f0/hqdefault.jpg" alt="Youtube" />',
      ],
      '#url' => Url::fromUri('https://www.youtube.com/watch?v=9bZkp7q19f0'),
    ];
    $modal = new Modal();
    $modal->setTitle('Youtube Video');
    $modal->setSubtitle('This is a YouTube video');
    $modal->setGroup('media');
    // $modal->setContentPadding('200px');
    // $modal->setContentPadding('0px');
    // $modal->setWidth('100%');
    // $modal->setIcon('photo-video');
    // $modal->setGroup('video');
    // $modal->setFit();
    $modal->setVideo('https://www.youtube.com/watch?v=9bZkp7q19f0');
    $modal->setTriggerOverlay(t('Watch Video'), 'play-circle');
    $modal->applyTo($form['media']['youtube']);

    $form['media']['vimeo'] = [
      '#type' => 'link',
      '#title' => [
        '#type' => 'inline_template',
        '#template' => '<img style="height:140px;" src="https://vumbnail.com/951533874.jpg" alt="Vimeo" />',
      ],
      '#url' => Url::fromUri('https://vimeo.com/951533874'),
    ];
    $modal = new Modal();
    $modal->setTitle('Vimeo Video');
    $modal->setSubtitle('This is a Vimeo video');
    $modal->setGroup('media');
    // $modal->setContentPadding('200px');
    // $modal->setWidth('100vw');
    // $modal->setIcon('photo-video');
    // $modal->setGroup('video');
    $modal->setVideo('https://vimeo.com/951533874');
    $modal->setTriggerOverlay(t('Watch Video'), 'play-circle', TRUE);
    $modal->applyTo($form['media']['vimeo']);

    $parents = $form['#parents'];
    $form['tabs'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Modal Settings'),
    ];

    $form['features'] = [
      '#type' => 'details',
      '#title' => $this->t('Features'),
      '#group' => implode('][', $form['#parents']) . '][tabs',
      '#parents' => $parents,
    ];

    $form['features']['fit'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Fit'),
      '#description' => $this->t('Fit the modal content to the modal size. This is useful for images and videos.'),
      '#default_value' => $this->getValue('fit'),
    ];

    $form['features']['loader'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Loader'),
      '#description' => $this->t('Display loader when waiting for modal content to load.'),
      '#default_value' => $this->getValue('loader'),
    ];

    $form['features']['bodyLock'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Body Lock'),
      '#description' => $this->t('Automatically add form actions and buttons to the modal content footer.'),
      '#default_value' => $this->getValue('bodyLock'),
    ];

    $form['features']['backdrop'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Backdrop'),
      '#description' => $this->t('Show a backdrop behind the modal.'),
      '#default_value' => $this->getValue('backdrop'),
    ];

    $form['features']['smartActions'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Smart Actions'),
      '#description' => $this->t('Automatically add form actions and buttons to the modal content footer.'),
      '#default_value' => $this->getValue('smartActions'),
    ];

    // return $form;

    $form['header'] = [
      '#type' => 'details',
      '#title' => $this->t('Header'),
      '#group' => implode('][', $form['#parents']) . '][tabs',
      '#parents' => $parents,
    ];

    $form['header']['header'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show Header'),
      '#description' => $this->t('Enable the modal header.'),
      '#default_value' => $this->getValue('header'),
    ];

    $form['header']['headerInContent'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Header In Content'),
      '#description' => $this->t('Render the header inside the modal content.'),
      '#default_value' => $this->getValue('headerInContent'),
    ];

    $form['header']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#description' => $this->t('The title of the modal.'),
      '#default_value' => $this->getValue('title'),
    ];

    $form['header']['subtitle'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subtitle'),
      '#description' => $this->t('The subtitle of the modal.'),
      '#default_value' => $this->getValue('subtitle'),
    ];

    $form['header']['icon'] = [
      '#type' => 'neo_icon',
      '#title' => $this->t('Icon'),
      '#description' => $this->t('The icon of the modal.'),
      '#default_value' => $this->getValue('icon'),
      '#format' => 'selector',
    ];

    $form['header']['closeButton'] = [
      '#type' => 'select',
      '#title' => $this->t('Close Button'),
      '#description' => $this->t('The position of the close button within the header.'),
      '#default_value' => $this->getValue('closeButton'),
      '#options' => Modal::getCloseButtonPlacements(),
    ];

    $form['content'] = [
      '#type' => 'details',
      '#title' => $this->t('Content'),
      '#group' => implode('][', $form['#parents']) . '][tabs',
      '#parents' => $parents,
    ];

    $form['content']['contentPadding'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Content Padding'),
      '#description' => $this->t('The padding of the modal. Example: 20px, 1.5rem.'),
      '#default_value' => $this->getValue('contentPadding'),
    ];

    $form['content']['contentScroll'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Content Scroll'),
      '#description' => $this->t('Scroll the modal content if it is larger than the modal size.'),
      '#default_value' => $this->getValue('contentScroll'),
    ];

    $form['footer'] = [
      '#type' => 'details',
      '#title' => $this->t('Footer'),
      '#group' => implode('][', $form['#parents']) . '][tabs',
      '#parents' => $parents,
    ];

    $form['footer']['footer'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show Footer'),
      '#description' => $this->t('Enable the modal footer.'),
      '#default_value' => $this->getValue('footer'),
    ];

    $form['footer']['downloadLink'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Download Link'),
      '#description' => $this->t('Enable the download link for images and videos.'),
      '#default_value' => $this->getValue('downloadLink'),
    ];

    $form['footer']['shareLink'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Share Link'),
      '#description' => $this->t('Enable the share link for images and videos.'),
      '#default_value' => $this->getValue('shareLink'),
    ];

    $form['footer']['copyLink'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Copy Link'),
      '#description' => $this->t('Enable the copy link for images and videos.'),
      '#default_value' => $this->getValue('copyLink'),
    ];

    $form['group'] = [
      '#type' => 'details',
      '#title' => $this->t('Group'),
      '#group' => implode('][', $form['#parents']) . '][tabs',
      '#parents' => $parents,
    ];

    $form['group']['group'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Group'),
      '#description' => $this->t('The indentifier used to group modals together. Modals with the same group will be able to be navigated through.'),
      '#default_value' => $this->getValue('group'),
    ];

    $form['group']['numeration'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Numeration'),
      '#description' => $this->t('When grouped, show number of items and current item number.'),
      '#default_value' => $this->getValue('numeration'),
    ];

    $form['group']['drag'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Drag'),
      '#description' => $this->t('Allow the modal to be dragged left/right to navigate through the group modals.'),
      '#default_value' => $this->getValue('drag'),
    ];

    $form['placement'] = [
      '#type' => 'details',
      '#title' => $this->t('Size & Placement'),
      '#group' => implode('][', $form['#parents']) . '][tabs',
      '#parents' => $parents,
    ];

    $form['placement']['width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Width'),
      '#description' => $this->t('The width of the modal. Example: auto, full, 400px, 100%, 80vw.'),
      '#default_value' => $this->getValue('width'),
    ];

    $form['placement']['height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Height'),
      '#description' => $this->t('The width of the modal. Example: auto, full, 400px, 100%, 80vh.'),
      '#default_value' => $this->getValue('height'),
    ];

    $form['placement']['placement'] = [
      '#type' => 'select',
      '#title' => $this->t('Placement'),
      '#description' => $this->t('The position of the modal on the screen.'),
      '#default_value' => $this->getValue('placement'),
      '#options' => Modal::getPlacements(),
    ];

    $form['placement']['attach'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Attach To'),
      '#description' => $this->t('The element to which the modal should be attached.'),
      '#default_value' => $this->getValue('attach'),
    ];

    $form['placement']['attachPlacement'] = [
      '#type' => 'select',
      '#title' => $this->t('Attach Placement'),
      '#description' => $this->t('The position of the modal in relation to the attached element.'),
      '#default_value' => $this->getValue('attachPlacement'),
      '#options' => Modal::getAttachPlacements(),
    ];

    $form['placement']['appendTo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Append To'),
      '#description' => $this->t('The element to which the modal should be appended. If empty, the modal will be appended to the body.'),
      '#default_value' => $this->getValue('appendTo'),
    ];

    $form['placement']['appendToClosest'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Append To Closest'),
      '#description' => $this->t('The closest element to which the modal should be appended. If set, will override the appendTo value.'),
      '#default_value' => $this->getValue('appendToClosest'),
    ];

    $form['colors'] = [
      '#type' => 'details',
      '#title' => $this->t('Colors'),
      '#group' => implode('][', $form['#parents']) . '][tabs',
      '#parents' => $parents,
    ];

    $form['colors']['colorScheme'] = [
      '#type' => 'neo_scheme',
      '#title' => $this->t('Scheme'),
      '#description' => $this->t('The color scheme of the modal.'),
      '#default_value' => $this->getValue('colorScheme'),
      '#empty_option' => $this->t('Default'),
      '#format' => 'class',
    ];

    foreach ([
      'header' => [
        'label' => $this->t('Header'),
      ],
      'content' => [
        'label' => $this->t('Content'),
      ],
      'contentFooter' => [
        'label' => $this->t('Content Footer'),
      ],
      'footer' => [
        'label' => $this->t('Footer'),
      ],
      'nav' => [
        'label' => $this->t('Navigation'),
      ],
      'backdrop' => [
        'label' => $this->t('Backdrop'),
        'fg' => FALSE,
        'bgOpacity' => TRUE,
      ],
      'loader' => [
        'label' => $this->t('Loader'),
        'bgOpacity' => TRUE,
      ],
    ] as $key => $data) {
      $label = $data['label'];
      $hasBg = !isset($data['bg']) || !empty($data['bg']);
      $hasFg = !isset($data['fg']) || !empty($data['fg']);
      $hasBgOpacity = !empty($data['bgOpacity']);
      $hasFgOpacity = !empty($data['fgOpacity']);
      $form['colors'][$key] = [
        '#type' => 'details',
        '#title' => $label,
        '#open' => $this->getValue($key . 'ColorBg') || $this->getValue($key . 'Color'),
        '#parents' => $parents,
      ];
      if ($hasBg) {
        $form['colors'][$key][$key . 'ColorBg'] = [
          '#type' => 'neo_color',
          '#title' => $this->t('@label Background', [
            '@label' => $label,
          ]),
          '#description' => $this->t('The content background color. If empty, the default css variable color will be used.'),
          '#default_value' => $this->getValue($key . 'ColorBg'),
          '#format' => $hasBgOpacity ? 'rgba' : 'rgb',
          '#empty_option' => $this->t('Default'),
        ];
      }
      if ($hasFg) {
        $form['colors'][$key][$key . 'Color'] = [
          '#type' => 'neo_color',
          '#title' => $this->t('@label Color', [
            '@label' => $label,
          ]),
          '#description' => $this->t('The content foreground color. If empty, the default css variable color will be used.'),
          '#default_value' => $this->getValue($key . 'Color'),
          '#format' => $hasFgOpacity ? 'rgba' : 'rgb',
          '#empty_option' => $this->t('Default'),
        ];
      }
    }

    $form['animations'] = [
      '#type' => 'details',
      '#title' => $this->t('Animations'),
      '#group' => implode('][', $form['#parents']) . '][tabs',
      '#parents' => $parents,
    ];

    $form['animations']['headerAnimate'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Animate Header'),
      '#description' => $this->t('Allow the header contents to be animated.'),
      '#default_value' => $this->getValue('headerAnimate'),
    ];

    foreach ([
      'content' => [
        'label' => $this->t('Content'),
        'delay' => TRUE,
      ],
      'header' => [
        'label' => $this->t('Header'),
        'delay' => TRUE,
      ],
      'title' => [
        'label' => $this->t('Title'),
        'delay' => TRUE,
      ],
      'subtitle' => [
        'label' => $this->t('Subtitle'),
        'delay' => TRUE,
      ],
      'icon' => [
        'label' => $this->t('Icon'),
        'delay' => TRUE,
      ],
      'closeButton' => [
        'label' => $this->t('Close Button'),
        'delay' => TRUE,
      ],
      'navPrev' => [
        'label' => $this->t('Previous Button'),
        'delay' => TRUE,
      ],
      'navNext' => [
        'label' => $this->t('Next Button'),
        'delay' => TRUE,
      ],
      'footer' => [
        'label' => $this->t('Footer'),
        'delay' => TRUE,
      ],
      'prev' => [
        'label' => $this->t('Previous Modal'),
        'delay' => FALSE,
      ],
      'next' => [
        'label' => $this->t('Next Modal'),
        'delay' => FALSE,
      ],
      'backdrop' => [
        'label' => $this->t('Overlay'),
        'delay' => FALSE,
      ],
      'loader' => [
        'label' => $this->t('Loader'),
        'delay' => FALSE,
      ],
    ] as $key => $group) {
      $label = $group['label'];
      $hasDelay = !empty($group['delay']);
      $form['animations'][$key] = [
        '#type' => 'details',
        '#title' => $label,
        '#open' => FALSE,
      ];
      foreach ([
        'in' => $this->t('In'),
        'out' => $this->t('Out'),
      ] as $op => $labelOp) {
        $form['animations'][$key][$op] = [
          '#type' => 'container',
          '#parents' => $parents,
          '#attributes' => [
            'class' => [
              'form--inline',
            ],
          ],
        ];
        $settingKey = $key . 'Animate' . ucfirst($op);
        $form['animations'][$key][$op][$settingKey] = [
          '#type' => 'select',
          '#title' => $this->t('@label Animate @op', [
            '@label' => $label,
            '@op' => $labelOp,
          ]),
          '#default_value' => $this->getValue($settingKey),
          '#options' => $op == 'in' ? Modal::getAnimationsIn() : Modal::getAnimationsOut(),
        ];

        $form['animations'][$key][$op][$settingKey . 'Speed'] = [
          '#type' => 'select',
          '#title' => $this->t('@label Animate @op Speed', [
            '@label' => $label,
            '@op' => $labelOp,
          ]),
          '#default_value' => $this->getValue($settingKey . 'Speed'),
          '#options' => Modal::getAnimationsSpeed(),
        ];

        if ($hasDelay) {
          $form['animations'][$key][$op][$settingKey . 'Delay'] = [
            '#type' => 'select',
            '#title' => $this->t('@label Animate @op Delay', [
              '@label' => $label,
              '@op' => $labelOp,
            ]),
            '#default_value' => $this->getValue($settingKey . 'Delay'),
            '#options' => Modal::getAnimationsDelay(),
          ];
        }
      }
    }

    $form['animations']['bodyTransitionScale'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Body Scale'),
      '#description' => $this->t('Scale the body when modal is opened.'),
      '#default_value' => $this->getValue('bodyTransitionScale'),
    ];

    $form['animations']['bodyTransitionBlur'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Body Blur'),
      '#description' => $this->t('Blur the body when modal is opened.'),
      '#default_value' => $this->getValue('bodyTransitionBlur'),
    ];

    return $form;

    // As for element.
    $form['element'] = [
      '#type' => 'neo_modal',
      '#title' => $this->t('Trigger'),
      '#title_attributes' => [
        'class' => [
          'btn',
        ],
      ],
      '#close' => $this->t('Click me to close'),
      '#options' => [
        'title' => 'Modal Title',
        'headerInContent' => TRUE,
        'contentScroll' => TRUE,
      ],
    ];
    $form['element']['textfield'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text'),
    ];
    $form['element']['markup']['#markup'] = '<br><br><hr><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p>';

    $form['element']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#ajax' => [
        'callback' => [get_class(), 'ajaxModalClose'],
        'wrapper' => 'ajax-wrapper',
      ],
    ];

    $form['element']['actions'] = [
      '#type' => 'actions',
    ];
    $form['element']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Actions Submit'),
      '#button_type' => 'primary',
      '#ajax' => [
        'callback' => [get_class(), 'ajaxModalClose'],
        'wrapper' => 'ajax-wrapper',
      ],
    ];
    $form['element']['actions']['link'] = [
      '#markup' => '<a data-neo-modal-close>Close via Link</a>',
    ];

    $form['link'] = [
      '#type' => 'link',
      '#title' => 'Link',
      '#url' => Url::fromRoute('<front>'),
    ];
    $form['container'] = [
      '#type' => 'container',
    ];

    $form['container']['textfield'] = [
      '#type' => 'textfield',
      '#value' => $this->t('Text'),
    ];
    $form['container']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#ajax' => [
        'callback' => [get_class(), 'ajaxModalClose'],
        'wrapper' => 'ajax-wrapper',
      ],
    ];
    $modal = new Modal($form['container']);
    $modal->setTitle('A link!');
    $modal->applyToForm($form['link'], $form['container']);

    $form['simple'] = [
      '#type' => 'link',
      '#title' => 'Simple',
      '#url' => Url::fromRoute('<front>'),
    ];
    $modal = new Modal();
    $modal->setImage('https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?ixlib=rb-1.2.1&ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&auto=format&fit=crop&q=80');
    $modal->applyTo($form['simple']);

    // return $form;

    // $tooltip = new Tooltip('Hello');
    // $form['trigger'] = $tooltip->buildFromString('Tooltip Trigger');
    $modalContent = [
      'fieldset' => [
        '#type' => 'details',
        '#title' => 'Fieldset',
        'content' => [
          '#markup' => '<p>Fieldset content</p>',
        ],
      ],
      'link' => [
        '#type' => 'link',
        '#title' => 'Link',
        '#url' => Url::fromRoute('<front>'),
      ],
      'markup' => [
        '#markup' => '<p>START</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>Modal content</p><p>END</p>',
      ],
      'close' => [
        '#type' => 'button',
        '#value' => 'Close via AJAX',
        '#ajax' => [
          'callback' => [get_class(), 'ajaxModalClose'],
          'wrapper' => 'ajax-wrapper',
        ],
      ]
    ];
    $modal = new Modal($modalContent);
    $modal->setTitle('A link!');
    $modal->applyTo($modalContent['link']);

    $form['iframe'] = [
      '#type' => 'link',
      '#title' => 'iFrame',
      '#url' => Url::fromUri('https://askcrumbs.com'),
      '#attributes' => [
        'class' => ['btn'],
      ],
    ];
    $modal = new Modal();
    $modal->setTitle('iFrame');
    $modal->setWidth('800px');
    $modal->setHeight('600px');
    $modal->setIframe('https://askcrumbs.com');
    $modal->applyTo($form['iframe']);

    $form['map'] = [
      '#type' => 'link',
      '#title' => 'Map',
      '#url' => Url::fromUri('https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387142.85004765174!2d-74.25887720257019!3d40.70582992708322!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew+York!5e0!3m2!1sit!2sus!4v1488175057980'),
      '#attributes' => [
        'class' => ['btn'],
      ],
    ];
    $modal = new Modal();
    $modal->setTitle('Map');
    $modal->setWidth('calc(100% - 100px)');
    $modal->setHeight('calc(100% - 100px)');
    $modal->setIframe('https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387142.85004765174!2d-74.25887720257019!3d40.70582992708322!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew+York!5e0!3m2!1sit!2sus!4v1488175057980');
    $modal->applyTo($form['map']);

    $form['ajax'] = [
      '#id' => 'ajax-wrapper',
      '#type' => 'button',
      '#value' => 'Ajax',
      // '#attached' => [
      //   'library' => [
      //     'neo_modal/modal.ajax',
      //   ],
      // ],
      '#ajax' => [
        'callback' => [get_class(), 'ajaxModalOpen'],
        'wrapper' => 'ajax-wrapper',
      ],
    ];

    $form['media'] = [
      '#type' => 'fieldset',
      '#title' => 'Media',
      '#attributes' => [
        'class' => ['form--inline'],
      ],
    ];
    $images = [
      [
        'title' => 'Derek Thomson',
        'path' => 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?ixlib=rb-1.2.1&ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&auto=format&fit=crop&q=80',
      ],
      [
        'title' => 'Kace Rodriguez',
        'path' => 'https://images.unsplash.com/photo-1461301214746-1e109215d6d3?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&q=80',
      ],
      [
        'title' => 'whoisbenjamin',
        'path' => 'https://images.unsplash.com/photo-1585338447937-7082f8fc763d?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&q=80',
      ],
      [
        'title' => 'David Marcu',
        'path' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&q=80',
      ],
      [
        'title' => 'Iris Papillon',
        'path' => 'https://images.unsplash.com/photo-1446630073557-fca43d580fbe?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&q=80',
      ],
    ];
    foreach ($images as $delta => $image) {
      $path = $image['path'];
      $form['media'][$delta] = [
        '#type' => 'link',
        '#title' => [
          '#markup' => '<img src="' . $path . '&w=140&h=140" alt="Image" />',
        ],
        '#url' => Url::fromUri($path . '&w=1600'),
      ];
      $modal = new Modal();
      $modal->setTitle($image['title']);
      $modal->setIcon('image');
      $modal->setGroup('image');
      $modal->setNumeration();
      $modal->setContentPadding('0px');
      $modal->setCloseButton('end-out');
      $modal->setFit();
      // $modal->setHeaderInContent();
      $modal->setCloseButton(FALSE);
      // $modal->setContentScroll();
      $modal->setImage($path . '&w=1600');
      $modal->applyTo($form['media'][$delta]);
    }

    $form['video'] = [
      '#type' => 'fieldset',
      '#title' => 'Video',
    ];
    $form['video']['youtube'] = [
      '#type' => 'link',
      '#title' => 'Youtube',
      '#url' => Url::fromUri('https://www.youtube.com/watch?v=9bZkp7q19f0'),
      '#attributes' => [
        'class' => ['btn'],
      ],
    ];
    $modal = new Modal();
    $modal->setTitle('Youtube Video');
    $modal->setSubtitle('This is a YouTube video');
    // $modal->setContentPadding('200px');
    $modal->setContentPadding('0px');
    $modal->setWidth('100%');
    $modal->setIcon('photo-video');
    $modal->setGroup('video');
    $modal->setFit();
    $modal->setVideo('https://www.youtube.com/watch?v=9bZkp7q19f0');
    $modal->applyTo($form['video']['youtube']);

    $form['video']['vimeo'] = [
      '#type' => 'link',
      '#title' => 'Vimeo',
      '#url' => Url::fromUri('https://vimeo.com/951533874'),
      '#attributes' => [
        'class' => ['btn'],
      ],
    ];
    $modal = new Modal();
    $modal->setTitle('Vimeo Video');
    $modal->setSubtitle('This is a Vimeo video');
    // $modal->setContentPadding('200px');
    $modal->setWidth('100vw');
    $modal->setIcon('photo-video');
    $modal->setGroup('video');
    $modal->setVideo('https://vimeo.com/951533874');
    $modal->applyTo($form['video']['vimeo']);

    $modalContent['media'] = $form['media'];
    $form['modalTrigger1'] = [
      '#markup' => 'Modal Trigger 1',
      '#attributes' => [
        'class' => ['btn'],
      ],
    ];
    $modal = new Modal($modalContent);
    $modal->setTitleWithDynamicIcon('Log In', [
      'admin',
      'local_task',
    ]);
    $modal->setSubtitle('Modal Subtitle is really awesome!');
    // $modal->setIcon('air-freshener');
    // $modal->setBodyTransitionBlur();
    // $modal->setBodyTransitionScale();
    // $modal->setPlacementToBottom();
    // $modal->setContentScroll();
    $modal->setHeaderInContent();
    // $modal->setAttach('h1');
    // $modal->setAttachPlacement('bottom-start');
    $modal->applyTo($form['modalTrigger1']);

    $form['modalTrigger2'] = [
      '#markup' => 'Modal Trigger 2',
      '#attributes' => [
        'class' => ['btn'],
      ],
    ];
    $modal = new Modal($modalContent);
    $modal->setTitle('Modal Title is awesome!');
    $modal->setSubtitle('Modal Subtitle is really awesome!');
    $modal->setAttach('h1');
    $modal->setAttachPlacement('bottom-start');
    $modal->setBodyLock(FALSE);
    $modal->applyTo($form['modalTrigger2']);

    $form['content']['#markup'] = '<br><br><hr><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p>';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function ajaxModalOpen(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $build = [
      '#markup' => '<p>Modal content</p>',
    ];
    $response->addCommand(new NeoModalCommand(NULL, [
      'image' => 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?ixlib=rb-1.2.1&ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&auto=format&fit=crop&q=80',
      'fit' => TRUE,
    ]));
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public static function ajaxModalClose(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $response->addCommand(new NeoModalCloseCommand());
    return $response;
  }

}

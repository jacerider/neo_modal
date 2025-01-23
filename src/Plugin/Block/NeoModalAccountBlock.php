<?php

namespace Drupal\neo_modal\Plugin\Block;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\neo\NeoLinkitTrait;
use Drupal\user\Access\RegisterAccessCheck;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block to display a user account/login/register within a modal.
 *
 * @Block(
 *   id = "neo_modal_account",
 *   admin_label = @Translation("Modal Account"),
 *   provider = "neo_modal"
 * )
 */
class NeoModalAccountBlock extends NeoModalBlockBase {

  use NeoLinkitTrait;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * The register access check.
   *
   * @var \Drupal\user\Access\RegisterAccessCheck
   */
  protected $registerAccessCheck;

  /**
   * The menu link tree.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $menuLinkTree;

  /**
   * Creates a NeoModalAccountBlock instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    AccountInterface $current_user,
    FormBuilderInterface $form_builder,
    RegisterAccessCheck $register_access_check,
    MenuLinkTreeInterface $menu_link_tree
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager);
    $this->currentUser = $current_user;
    $this->formBuilder = $form_builder;
    $this->registerAccessCheck = $register_access_check;
    $this->menuLinkTree = $menu_link_tree;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('current_user'),
      $container->get('form_builder'),
      $container->get('access_check.user.register'),
      $container->get('menu.link_tree')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $config = parent::defaultConfiguration();
    $config['menu_account_anon'] = FALSE;
    $config['menu_account_auth'] = TRUE;
    $config['menu_tools_anon'] = FALSE;
    $config['menu_tools_auth'] = FALSE;
    $config['user_teaser'] = TRUE;
    $config['user_welcome'] = TRUE;
    $config['link_anon_text'] = '';
    $config['link_anon_url'] = '';
    $config['login_display'] = 'form';
    $config['login_text'] = 'Log In';
    $config['register_display'] = 'link';
    $config['register_title'] = 'New here?';
    $config['register_text'] = 'Create an account';
    return $config;
  }

  /**
   * {@inheritdoc}
   */
  protected function modalForceConfiguration() {
    return [
      'contentPadding' => '0',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $form['menu_account_anon'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show account menu for anonymous users'),
      '#default_value' => $this->configuration['menu_account_anon'],
    ];

    $form['menu_account_auth'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show account menu for authenticated users'),
      '#default_value' => $this->configuration['menu_account_auth'],
    ];

    $form['menu_tools_anon'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show tools menu for anonymous users'),
      '#default_value' => $this->configuration['menu_tools_anon'],
    ];

    $form['menu_tools_auth'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show tools menu for authenticated users'),
      '#default_value' => $this->configuration['menu_tools_auth'],
    ];

    $form['user_teaser'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show user teaser display for authenticated users'),
      '#default_value' => $this->configuration['user_teaser'],
    ];

    $form['user_welcome'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show user welcome message for authenticated users'),
      '#default_value' => $this->configuration['user_welcome'],
    ];

    $form['login'] = [
      '#type' => 'details',
      '#title' => $this->t('Login'),
      '#open' => FALSE,
    ];

    $form['login']['login_display'] = [
      '#type' => 'select',
      '#title' => $this->t('Display login as'),
      '#default_value' => $this->configuration['login_display'],
      '#options' => [
        'form' => $this->t('Form'),
        'link' => $this->t('Link'),
      ],
    ];
    $form['login']['login_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Login text'),
      '#default_value' => $this->configuration['login_text'],
    ];

    $form['register'] = [
      '#type' => 'details',
      '#title' => $this->t('Registration'),
      '#open' => FALSE,
    ];

    $form['register']['register_display'] = [
      '#type' => 'select',
      '#title' => $this->t('Display registeration as'),
      '#default_value' => $this->configuration['register_display'],
      '#empty_option' => $this->t('Hidden'),
      '#options' => [
        'link' => $this->t('Link'),
      ],
    ];

    $form['register']['register_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Registration title'),
      '#default_value' => $this->configuration['register_title'],
      '#states' => [
        'visible' => [
          ':input[name="settings[register][register_display]"]' => ['value' => 'link'],
        ],
      ],
    ];

    $form['register']['register_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Registration text'),
      '#default_value' => $this->configuration['register_text'],
      '#states' => [
        'visible' => [
          ':input[name="settings[register][register_display]"]' => ['value' => 'link'],
        ],
      ],
    ];

    $form['link_anon'] = [
      '#type' => 'details',
      '#title' => $this->t('Extra link for anonymous users'),
      '#open' => FALSE,
    ];
    $form['link_anon']['link_anon_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link text'),
      '#default_value' => $this->configuration['link_anon_text'],
    ];
    $form['link_anon']['link_anon_url'] = $this->getLinkitElement($this->configuration['link_anon_url']);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['menu_account_anon'] = $form_state->getValue(['menu_account_anon'], FALSE);
    $this->configuration['menu_account_auth'] = $form_state->getValue(['menu_account_auth'], TRUE);
    $this->configuration['menu_tools_anon'] = $form_state->getValue(['menu_tools_anon'], FALSE);
    $this->configuration['menu_tools_auth'] = $form_state->getValue(['menu_tools_auth'], FALSE);
    $this->configuration['user_teaser'] = $form_state->getValue(['user_teaser'], TRUE);
    $this->configuration['user_welcome'] = $form_state->getValue(['user_welcome'], TRUE);
    $this->configuration['login_display'] = $form_state->getValue([
      'login', 'login_display',
    ]);
    $this->configuration['login_text'] = $form_state->getValue([
      'login', 'login_text',
    ]);
    $this->configuration['register_display'] = $form_state->getValue([
      'register', 'register_display',
    ]);
    $this->configuration['register_title'] = $form_state->getValue([
      'register', 'register_title',
    ]);
    $this->configuration['register_text'] = $form_state->getValue([
      'register', 'register_text',
    ]);
    $this->configuration['link_anon_text'] = $form_state->getValue([
      'link_anon', 'link_anon_text',
    ]);
    $this->configuration['link_anon_url'] = $form_state->getValue([
      'link_anon', 'link_anon_url',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildModalContent(): array {
    $build = parent::buildModalContent();

    $build = [
      '#theme' => 'neo_modal_account',
      '#account' => $this->currentUser,
    ];

    $isAnon = $this->currentUser->isAnonymous();

    if (($isAnon && $this->configuration['menu_account_anon']) || $this->configuration['menu_account_auth']) {
      // Load the menu tree for the main menu.
      $parameters = new MenuTreeParameters();
      $parameters->setMaxDepth(1);
      $tree = $this->menuLinkTree->load('account', $parameters);
      $build['#menu_account'] = $this->menuLinkTree->build($tree);
      unset($build['#menu_account']['#items']['user.logout']);
    }

    if (($isAnon && $this->configuration['menu_tools_anon']) || $this->configuration['menu_tools_auth']) {
      // Load the menu tree for the main menu.
      $parameters = new MenuTreeParameters();
      $parameters->setMaxDepth(1);
      $tree = $this->menuLinkTree->load('tools', $parameters);
      $build['#menu_tools'] = $this->menuLinkTree->build($tree);
      unset($build['#menu_tools']['#items']['user.logout']);
    }

    if ($isAnon) {
      $build['#title'] = $this->t('Your Account');

      if ($this->configuration['link_anon_text'] && $this->configuration['link_anon_url']) {
        $build['#link'] = [
          '#type' => 'neo_modal_link',
          '#title' => $this->configuration['link_anon_text'],
          '#url' => Url::fromUserInput($this->configuration['link_anon_url']),
          '#modal' => ['nest' => TRUE, 'smartActions' => TRUE] + $this->configuration['modal'],
          '#modal_preset' => $this->configuration['modal_preset'],
        ];
      }

      switch ($this->configuration['login_display']) {
        case 'link':
          $build['#login'] = [
            '#type' => 'neo_modal_link',
            '#title' => $this->icon($this->configuration['login_text'], 'sign-in'),
            '#url' => Url::fromRoute('neo_modal.api.account.login', [
              'block' => $this->configuration['block_id'],
            ]),
            '#modal' => ['nest' => TRUE, 'smartActions' => TRUE] + $this->configuration['modal'],
            '#modal_preset' => $this->configuration['modal_preset'],
          ];
          break;

        case 'form':
          $build['#login'] = $this->formBuilder->getForm('\Drupal\neo_modal\Form\NeoModalAccountLoginForm', $this->configuration['modal'], $this->configuration['modal_preset']);
          break;
      }

      switch ($this->configuration['register_display']) {
        case 'link':
          if ($this->registerAccessCheck->access($this->currentUser)->isAllowed()) {
            $build['#register_title'] = $this->configuration['register_title'];
            $build['#register'] = [
              '#type' => 'neo_modal_link',
              '#title' => $this->icon($this->configuration['register_text'], 'user-plus'),
              '#url' => Url::fromRoute('neo_modal.api.account.register', [
                'block' => $this->configuration['block_id'],
              ]),
              '#modal' => ['nest' => TRUE, 'smartActions' => TRUE] + $this->configuration['modal'],
              '#modal_preset' => $this->configuration['modal_preset'],
            ];
          }
          break;
      }
    }
    else {
      /** @var \Drupal\user\UserInterface $user */
      $user = $this->entityTypeManager->getStorage('user')->load($this->currentUser->id());

      $build['#title'] = $this->t('Your Account');

      if ($this->configuration['user_welcome']) {
        $build['#message'] = $this->t('Hello, <span>@name</span>', ['@name' => $user->getDisplayName()]);
      }

      if ($this->configuration['user_teaser']) {
        $build['#user'] = $this->entityTypeManager->getViewBuilder('user')->view($user, 'teaser');
      }

      $build['#logout'] = [
        '#type' => 'link',
        '#title' => $this->icon('Log out', 'sign-out'),
        '#url' => Url::fromRoute('user.logout'),
      ];
    }

    return $build;
  }

}

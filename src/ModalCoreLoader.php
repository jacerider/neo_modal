<?php

declare(strict_types=1);

namespace Drupal\neo_modal;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Asset\AssetCollectionRendererInterface;
use Drupal\Core\Asset\AssetResolverInterface;
use Drupal\Core\Asset\AttachedAssets;
use Drupal\Core\Asset\AttachedAssetsInterface;
use Drupal\Core\Asset\LibraryDependencyResolverInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\State\StateInterface;
use Drupal\neo_build\ManifestResolver;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Loads the modal code on demand.
 *
 * Every page that can open a modal attaches `neo_modal/modal`, a small stub.
 * The modal code proper, `neo_modal/modal.core` with popper, tippy and
 * body-scroll-lock, is never attached at render time: the stub fetches it when
 * a visitor shows intent to open a modal. This hands the stub the script URLs
 * to fetch, worked out by core's own asset pipeline so they are exactly what
 * attaching the library would have added — one aggregate when aggregation is
 * on, minus whatever the page has already loaded.
 *
 * The `neo_modal_preload` setting is the escape hatch: in the scopes it names,
 * `neo_modal/modal` depends on `neo_modal/modal.core`, which then loads up
 * front as it always did, and the stub behaves synchronously. It defaults to
 * the back scope, where editors open modals constantly.
 *
 * @code
 * // settings.php: preload everywhere, as before on-demand loading existed.
 * $settings['neo_modal_preload'] = TRUE;
 * // Or name the scopes; [] loads on demand everywhere.
 * $settings['neo_modal_preload'] = ['front', 'back'];
 * @endcode
 *
 * Library definitions are cached, so a change to the setting needs a cache
 * rebuild.
 */
final class ModalCoreLoader {

  /**
   * The stub library every modal attaches.
   */
  public const STUB = 'neo_modal/modal';

  /**
   * The library holding the modal code.
   */
  public const CORE = 'neo_modal/modal.core';

  /**
   * The settings.php key of the escape hatch.
   */
  public const PRELOAD_SETTING = 'neo_modal_preload';

  /**
   * The scopes that preload when the setting is absent.
   */
  public const PRELOAD_DEFAULT = ['back'];

  /**
   * Whether the core's scripts are being worked out.
   *
   * Working them out runs core's asset resolver, which invokes
   * hook_js_settings_alter() again for the core's own assets.
   */
  private bool $resolving = FALSE;

  public function __construct(
    private readonly LibraryDependencyResolverInterface $dependencyResolver,
    private readonly AssetResolverInterface $assetResolver,
    private readonly AssetCollectionRendererInterface $jsCollectionRenderer,
    private readonly RequestStack $requestStack,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly StateInterface $state,
    private readonly ?ManifestResolver $manifestResolver = NULL,
  ) {}

  /**
   * Whether the modal code loads up front in the scope rendering now.
   */
  public function preloads(): bool {
    $setting = Settings::get(self::PRELOAD_SETTING, self::PRELOAD_DEFAULT);
    if (is_bool($setting)) {
      return $setting;
    }
    $scope = $this->manifestResolver?->getActiveScope()->value ?? 'front';
    return in_array($scope, (array) $setting, TRUE);
  }

  /**
   * Makes the stub depend on the core where the escape hatch asks for it.
   *
   * @param array $libraries
   *   The library definitions of neo_modal, from hook_library_info_alter().
   */
  public function alterLibraries(array &$libraries): void {
    if (isset($libraries['modal'], $libraries['modal.core']) && $this->preloads()) {
      $libraries['modal']['dependencies'][] = self::CORE;
    }
  }

  /**
   * Hands the stub the core's scripts.
   *
   * Runs for every response that sends settings, the page and each AJAX
   * response alike, and sends them whenever the stub is on the page and the
   * core is not, so they move with `ajaxPageState` as AJAX responses add
   * libraries.
   *
   * Sends `drupalSettings.neoModal.core`:
   * - js: the attributes of each script, `src` among them, in load order.
   * - base: `ajaxPageState.libraries` as this response leaves it.
   * - libraries: the same once the core is loaded, for the stub to record.
   *
   * @param array $settings
   *   The JavaScript settings, from hook_js_settings_alter().
   * @param \Drupal\Core\Asset\AttachedAssetsInterface $assets
   *   The assets of the response.
   */
  public function alterJsSettings(array &$settings, AttachedAssetsInterface $assets): void {
    if ($this->resolving) {
      return;
    }
    $page = array_values(array_unique(array_merge($assets->getLibraries(), $assets->getAlreadyLoadedLibraries())));
    $loaded = $this->dependencyResolver->getLibrariesWithDependencies($page);
    if (!in_array(self::STUB, $loaded, TRUE) || in_array(self::CORE, $loaded, TRUE)) {
      return;
    }

    // Only the loaded libraries the core shares. Excluding the rest would not
    // change what loads, and leaving them out keeps the aggregate URL the same
    // from page to page, so the browser fetches it once.
    $exclude = array_values(array_intersect($this->dependencyResolver->getLibrariesWithDependencies([self::CORE]), $loaded));

    $this->resolving = TRUE;
    try {
      $scripts = $this->scripts($exclude);
    }
    finally {
      $this->resolving = FALSE;
    }

    $settings['neoModal']['core'] = [
      'js' => $scripts,
      'base' => $this->pageState($page),
      'libraries' => $this->pageState(array_merge($page, [self::CORE])),
    ];
  }

  /**
   * Renders the core's scripts as core would, minus the excluded libraries.
   *
   * @param string[] $exclude
   *   The libraries already loaded.
   *
   * @return array[]
   *   The attributes of each script, in load order.
   */
  private function scripts(array $exclude): array {
    $maintenanceMode = defined('MAINTENANCE_MODE') || $this->state->get('system.maintenance_mode');
    $optimize = !$maintenanceMode && $this->configFactory->get('system.performance')->get('js.preprocess');

    $assets = (new AttachedAssets())
      ->setLibraries([self::CORE])
      ->setAlreadyLoadedLibraries($exclude);

    // The lazy JS optimizer builds the aggregate URL's `exclude` argument from
    // the request's ajax_page_state, not from the assets it is given. The
    // asset controller rebuilds the aggregate from that URL and refuses one
    // that does not match its hash, so the URL has to state what was
    // excluded here. With nothing excluded there must be no argument at all:
    // the controller rejects an empty library name.
    $request = $this->requestStack->getCurrentRequest();
    $hadState = $request?->attributes->has('ajax_page_state') ?? FALSE;
    $previousState = $request?->attributes->get('ajax_page_state');
    if ($exclude) {
      $request?->attributes->set('ajax_page_state', ['libraries' => implode(',', $exclude)]);
    }
    else {
      $request?->attributes->remove('ajax_page_state');
    }
    try {
      [$header, $footer] = $this->assetResolver->getJsAssets($assets, (bool) $optimize);
    }
    finally {
      if ($hadState) {
        $request?->attributes->set('ajax_page_state', $previousState);
      }
      else {
        $request?->attributes->remove('ajax_page_state');
      }
    }

    $scripts = [];
    foreach ($this->jsCollectionRenderer->render(array_merge($header, $footer)) as $element) {
      // Skip the settings: the page has its own.
      if (!empty($element['#attributes']['src'])) {
        $scripts[] = $element['#attributes'];
      }
    }
    return $scripts;
  }

  /**
   * Computes `ajaxPageState.libraries` as core sends it.
   *
   * @param string[] $libraries
   *   The libraries on the page.
   *
   * @return string
   *   The value, compressed as the client receives it.
   *
   * @see \Drupal\system\Hook\SystemHooks::jsSettingsAlter()
   * @see \Drupal\Core\Asset\AssetResolver::getJsAssets()
   */
  private function pageState(array $libraries): string {
    $minimal = $this->dependencyResolver->getMinimalRepresentativeSubset(array_values(array_unique($libraries)));
    sort($minimal);
    return UrlHelper::compressQueryParameter(implode(',', $minimal));
  }

}

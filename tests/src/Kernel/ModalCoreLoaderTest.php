<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_modal\Kernel;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Asset\AttachedAssets;
use Drupal\Core\Site\Settings;
use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * The modal code loads on demand, and the stub is told where to find it.
 *
 * `neo_modal/modal` is the stub every modal attaches; the modal code, with
 * popper, tippy and body-scroll-lock, is `neo_modal/modal.core`, which nothing
 * attaches at render time. These cover the server's half: what the stub is
 * handed in `drupalSettings.neoModal.core`, and the escape hatch that loads the
 * core up front instead.
 */
#[Group('neo_modal')]
class ModalCoreLoaderTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'neo',
    'neo_settings',
    'neo_tooltip',
    'neo_modal',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['system', 'neo_modal']);
    // One script per library, unless a test turns aggregation on.
    $this->config('system.performance')->set('js.preprocess', FALSE)->save();
    $this->setPreload([]);
  }

  /**
   * The stub library carries none of the modal code's weight.
   */
  public function testStubLeavesTheCoreOut(): void {
    $resolver = $this->container->get('library.dependency_resolver');
    $stub = $resolver->getLibrariesWithDependencies(['neo_modal/modal']);
    foreach (['neo_modal/modal.core', 'neo/library.popper', 'neo/library.body-scroll-lock', 'neo_tooltip/library.tippy'] as $library) {
      $this->assertNotContains($library, $stub);
    }
    $core = $resolver->getLibrariesWithDependencies(['neo_modal/modal.core']);
    foreach (['neo/library.popper', 'neo/library.body-scroll-lock', 'neo_tooltip/library.tippy'] as $library) {
      $this->assertContains($library, $core);
    }
  }

  /**
   * A page with the stub is handed the core's scripts, in load order.
   */
  public function testStubIsHandedTheCoreScripts(): void {
    $core = $this->coreSettings(['neo_modal/modal']);
    $sources = array_column($core['js'], 'src');
    $this->assertCount(4, $sources);
    $this->assertStringContainsString('popper', $sources[0]);
    $this->assertStringContainsString('bsl', $sources[1]);
    $this->assertStringContainsString('tippy', $sources[2]);
    $this->assertStringContainsString('modal-core', $sources[3]);

    // The page state before and after the core loads, as the client sees it.
    $this->assertSame('neo_modal/modal', UrlHelper::uncompressQueryParameter($core['base']));
    $this->assertSame('neo_modal/modal,neo_modal/modal.core', UrlHelper::uncompressQueryParameter($core['libraries']));
  }

  /**
   * What the page already loaded is not loaded again.
   */
  public function testLoadedLibrariesAreLeftOut(): void {
    $core = $this->coreSettings(['neo_modal/modal', 'neo_tooltip/library.tippy']);
    $sources = array_column($core['js'], 'src');
    $this->assertCount(2, $sources);
    $this->assertStringContainsString('bsl', $sources[0]);
    $this->assertStringContainsString('modal-core', $sources[1]);

    // The same holds for libraries an AJAX response's page already has.
    $core = $this->coreSettings(['neo_modal/modal'], ['neo/library.popper', 'neo/library.body-scroll-lock']);
    $sources = array_column($core['js'], 'src');
    $this->assertCount(2, $sources);
    $this->assertStringContainsString('tippy', $sources[0]);
    $this->assertStringContainsString('modal-core', $sources[1]);
  }

  /**
   * Nothing is handed over without the stub, or once the core is loaded.
   */
  public function testNothingWithoutTheStub(): void {
    $this->assertNull($this->coreSettings(['system/base']));
    $this->assertNull($this->coreSettings(['neo_modal/modal', 'neo_modal/modal.core']));
    $this->assertNull($this->coreSettings(['system/base'], ['neo_modal/modal.core']));
  }

  /**
   * With aggregation on, the core is one aggregate the controller will serve.
   */
  public function testAggregateUrlIsServed(): void {
    $this->config('system.performance')->set('js.preprocess', TRUE)->save();

    foreach ([[], ['neo_tooltip/library.tippy']] as $loaded) {
      $core = $this->coreSettings(array_merge(['neo_modal/modal'], $loaded));
      $this->assertCount(1, $core['js']);
      $url = $core['js'][0]['src'];
      parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
      // An empty exclude argument is a bad request, so with nothing loaded
      // there must be none.
      $this->assertSame((bool) $loaded, isset($query['exclude']));

      // The asset controller rebuilds the aggregate from the URL and only
      // serves it when the hash matches; otherwise it redirects.
      $request = Request::create($url);
      $fileName = basename((string) parse_url($url, PHP_URL_PATH));
      $response = $this->container->get('class_resolver')
        ->getInstanceFromDefinition('Drupal\system\Controller\JsAssetController')
        ->deliver($request, $fileName);
      $this->assertSame(200, $response->getStatusCode(), 'The aggregate URL matches its contents.');
      $content = $response instanceof BinaryFileResponse
        ? file_get_contents($response->getFile()->getPathname())
        : $response->getContent();
      // Tippy brings popper, so both are left out together.
      $this->assertSame(!$loaded, str_contains($content, 'tippy'), 'Tippy is aggregated only when the page lacks it.');
      $this->assertSame(!$loaded, str_contains($content, 'createPopper'), 'Popper is aggregated only when the page lacks it.');
      $this->assertStringContainsString('bodyScrollLock', $content);
    }
  }

  /**
   * The escape hatch makes the stub bring the core with it.
   */
  public function testPreloadAttachesTheCoreUpFront(): void {
    $this->setPreload(TRUE);
    $this->assertContains('neo_modal/modal.core', $this->stubDependencies());
    $this->assertNull($this->coreSettings(['neo_modal/modal']), 'Nothing to load on demand.');

    // A list names scopes; a kernel test renders in the front scope.
    $this->setPreload(['back']);
    $this->assertNotContains('neo_modal/modal.core', $this->stubDependencies());
    $this->setPreload(['front']);
    $this->assertContains('neo_modal/modal.core', $this->stubDependencies());
  }

  /**
   * Returns the stub library's declared dependencies.
   *
   * Read from the definition rather than the dependency resolver, which keeps
   * what it resolved for the life of the container.
   *
   * @return string[]
   *   The dependencies.
   */
  private function stubDependencies(): array {
    return $this->container->get('library.discovery')->getLibraryByName('neo_modal', 'modal')['dependencies'];
  }

  /**
   * Returns `drupalSettings.neoModal.core` for a response's assets.
   *
   * @param string[] $libraries
   *   The libraries the response attaches.
   * @param string[] $alreadyLoaded
   *   The libraries the page already has.
   *
   * @return array|null
   *   The settings, or NULL when none were sent.
   */
  private function coreSettings(array $libraries, array $alreadyLoaded = []): ?array {
    $assets = (new AttachedAssets())
      ->setLibraries(array_merge($libraries, ['core/drupalSettings']))
      ->setAlreadyLoadedLibraries($alreadyLoaded);
    $optimize = (bool) $this->config('system.performance')->get('js.preprocess');
    $this->container->get('asset.resolver')->getJsAssets($assets, $optimize);
    return $assets->getSettings()['neoModal']['core'] ?? NULL;
  }

  /**
   * Sets the escape hatch and rebuilds the library definitions.
   *
   * @param bool|string[] $value
   *   The setting.
   */
  private function setPreload(bool|array $value): void {
    $settings = Settings::getAll();
    $settings['neo_modal_preload'] = $value;
    new Settings($settings);
    $this->container->get('library.discovery')->clear();
  }

}

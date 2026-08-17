<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_modal\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\neo_modal\Modal;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests that a modal attaches the stylesheet its icon needs.
 *
 * An icon reaches the browser as a bare class name, so nothing else pulls in
 * the font that renders it. neo_icon attaches only its *global* libraries on
 * page load, so an icon from a non-global set renders as a blank glyph unless
 * the modal attaches that set itself.
 *
 * Those libraries are registered by neo_icon's hook_library_info_build(), so
 * they belong to the **neo_icon** extension. The modal used to name them
 * 'neo_modal/…' — a library that does not exist, which Drupal logs as
 * "Unable to resolve library" while attaching no stylesheet at all.
 *
 * The icon definitions and their font files live in archives this test does
 * not install, so these drive the attachment helper directly rather than
 * resolving a real glyph. That also means the registry cannot be asked whether
 * the resulting name resolves here — it does on an installed site, verified
 * separately; what is pinned below is the extension the name points at, which
 * is what regressed.
 */
#[Group('neo_modal')]
class IconLibraryAttachmentTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   *
   * The neo_icon module ships neo_config_file entities whose schema is
   * provided by a module outside this test's dependency set, so installing its
   * config trips the schema checker on something unrelated to what is under
   * test here.
   */
  protected $strictConfigSchema = FALSE;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'neo',
    'neo_settings',
    'neo_icon',
    'neo_tooltip',
    'neo_modal',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['neo_modal', 'neo_icon']);
  }

  /**
   * The attached library names the extension that actually defines it.
   */
  public function testIconLibraryUsesTheDefiningExtension(): void {
    $modal = new Modal();
    $this->attachIcon($modal, 'library.regular');

    $libraries = $modal->getAttachments()['library'] ?? [];
    $this->assertContains('neo_icon/library.regular', $libraries);
    $this->assertNotContains('neo_modal/library.regular', $libraries);
  }

  /**
   * The same icon set attached twice is only listed once.
   */
  public function testIconLibraryIsNotDuplicated(): void {
    $modal = new Modal();
    $this->attachIcon($modal, 'library.regular');
    $this->attachIcon($modal, 'library.regular');

    $libraries = $modal->getAttachments()['library'] ?? [];
    $this->assertSame(
      array_values(array_unique($libraries)),
      array_values($libraries),
      'Attaching the same icon set repeatedly does not repeat the library.'
    );
  }

  /**
   * Runs a modal's icon-library attachment for a given library name.
   *
   * @param \Drupal\neo_modal\Modal $modal
   *   The modal to attach to.
   * @param string $libraryName
   *   The library name an icon would report, e.g. "library.regular".
   */
  protected function attachIcon(Modal $modal, string $libraryName): void {
    $icon = new class($libraryName) {

      /**
       * The stub library.
       *
       * @var object
       */
      protected $library;

      /**
       * Constructs the icon stub.
       */
      public function __construct(string $libraryName) {
        $this->library = new class($libraryName) {

          /**
           * The library name.
           *
           * @var string
           */
          protected $name;

          /**
           * Constructs the library stub.
           */
          public function __construct(string $name) {
            $this->name = $name;
          }

          /**
           * Returns the library name.
           */
          public function getLibraryName(): string {
            return $this->name;
          }

        };
      }

      /**
       * Returns the library.
       */
      public function getLibrary() {
        return $this->library;
      }

    };

    $method = new \ReflectionMethod($modal, 'addIconLibrary');
    $method->setAccessible(TRUE);
    $method->invoke($modal, $icon);
  }

}

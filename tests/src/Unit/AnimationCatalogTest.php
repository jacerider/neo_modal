<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_modal\Unit;

use Drupal\neo_modal\Modal;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests that every offered animation has a class behind it.
 *
 * The animation selects are populated from hardcoded lists in Modal, while the
 * classes they name are defined in a different module's stylesheet. Nothing
 * connects the two, so a name can be offered that resolves to no CSS.
 *
 * That is not cosmetic. animate() fires its callback ONLY from the
 * animationend/animationcancel handler, and its `else if (callback)` escape is
 * unreachable once the option holds a non-empty string. A class that starts no
 * animation therefore produces no event, the callback never runs, and for an
 * out-animation that callback is what completes the close -- so picking the
 * option hangs the modal open.
 *
 * `bounceOut` was exactly that: offered by getAnimationsOut(), with only
 * bounceOutDown/Left/Right/Up defined in the catalog.
 */
#[Group('neo_modal')]
class AnimationCatalogTest extends UnitTestCase {

  /**
   * The animation catalog stylesheet, or NULL when it cannot be located.
   */
  protected ?string $catalog = NULL;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // The catalog lives in the sibling neo module.
    $path = __DIR__ . '/../../../../neo/src/css/animate.css';
    if (!is_file($path)) {
      $this->markTestSkipped('The neo animation catalog was not found at ' . $path);
    }
    $this->catalog = (string) file_get_contents($path);
  }

  /**
   * Every in and out animation names a class the catalog defines.
   */
  public function testOfferedAnimationsExistInTheCatalog(): void {
    $offered = Modal::getAnimationsIn() + Modal::getAnimationsOut();
    $this->assertMissing($offered, 'neo-animate--', 'animation');
  }

  /**
   * Every speed names a class the catalog defines.
   *
   * Speeds and delays draw from different vocabularies -- `default` is a valid
   * delay but not a valid speed -- which is easy to cross-wire.
   */
  public function testOfferedSpeedsExistInTheCatalog(): void {
    $this->assertMissing(Modal::getAnimationsSpeed(), 'neo-animate--', 'speed');
  }

  /**
   * Every delay names a class the catalog defines.
   */
  public function testOfferedDelaysExistInTheCatalog(): void {
    $this->assertMissing(Modal::getAnimationsDelay(), 'neo-animate--delay-', 'delay');
  }

  /**
   * Asserts that each offered key resolves to a class in the catalog.
   *
   * @param array $offered
   *   The offered options, keyed by the value written into the class name.
   * @param string $prefix
   *   The class-name prefix the value is appended to.
   * @param string $label
   *   What is being checked, for the failure message.
   */
  protected function assertMissing(array $offered, string $prefix, string $label): void {
    $missing = [];
    foreach (array_keys($offered) as $key) {
      // The empty key is the "None" choice and names no class.
      if ($key === '') {
        continue;
      }
      if (!str_contains($this->catalog, '.' . $prefix . $key . ' ') &&
        !str_contains($this->catalog, '.' . $prefix . $key . ',') &&
        !str_contains($this->catalog, '.' . $prefix . $key . '{') &&
        !str_contains($this->catalog, '.' . $prefix . $key . ':')) {
        $missing[] = $prefix . $key;
      }
    }
    $this->assertSame([], $missing, sprintf(
      'Every offered %s must name a class the catalog defines, or selecting it stalls the modal. Missing: %s',
      $label,
      implode(', ', $missing)
    ));
  }

}

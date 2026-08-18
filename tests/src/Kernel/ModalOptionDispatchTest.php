<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_modal\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\neo_modal\Modal;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests that options handed to Modal actually reach the rendered output.
 *
 * Modal's constructor resolves an option key to a setter by NAME --
 * 'set' . ucfirst(Str::camel($key)) -- and applies it only when
 * method_exists() agrees. There is no else branch and no log, so an option
 * whose key does not spell an existing method is discarded in silence.
 *
 * That made the failure invisible in the worst possible way: a value set
 * GLOBALLY still worked, because getAttachments() ships the global config diff
 * straight to drupalSettings without going through a setter. Only per-instance
 * values -- a preset, a block's modal settings, a #modal render array, the
 * |neo_modal twig filter -- were dropped. "Works on the site settings form,
 * ignored on the block" is a hard bug to attribute.
 *
 * These tests pin the round trip that the name-based dispatch depends on.
 */
#[Group('neo_modal')]
class ModalOptionDispatchTest extends KernelTestBase {

  /**
   * The boolean options that getValues() is able to emit per instance.
   *
   * Kept as a literal rather than derived, so that dropping a key from
   * getValues() shows up here as a deliberate edit rather than as a silently
   * shrinking test.
   *
   * @see \Drupal\neo_modal\Modal::getValues()
   */
  protected const EMITTABLE_BOOLEANS = [
    'colorSchemeInherit',
    'backdrop',
    'footer',
    'drag',
    'contentScroll',
    'smartActions',
    'numeration',
    'fit',
    'nest',
    'inputFocus',
    'bodyLock',
    'downloadLink',
    'shareLink',
    'copyLink',
    'bodyTransitionScale',
    'bodyTransitionBlur',
  ];

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
    $this->installConfig(['neo_modal']);
  }

  /**
   * Every boolean option getValues() can emit survives the constructor.
   *
   * This is the generic form of the defect: `drag` was emittable but its setter
   * is named setDraggable(), so the key resolved to nothing. Asserting the
   * whole set means the next option added with a prettier method name than its
   * key fails here instead of in production.
   */
  public function testEmittableBooleansRoundTripThroughTheOptionsArray(): void {
    $dropped = [];
    foreach (self::EMITTABLE_BOOLEANS as $key) {
      // getValues() is a diff, so only a value differing from the configured
      // default is emitted. Trying both flips means the assertion holds
      // whatever that default is, without restating it here.
      $emitted = FALSE;
      foreach ([TRUE, FALSE] as $value) {
        if (array_key_exists($key, (new Modal(NULL, [$key => $value]))->getValues())) {
          $emitted = TRUE;
          break;
        }
      }
      if (!$emitted) {
        $dropped[] = $key;
      }
    }
    $this->assertSame([], $dropped, 'Every emittable boolean option must be settable from an options array. Dropped: ' . implode(', ', $dropped));
  }

  /**
   * The `drag` option is honoured per instance.
   *
   * Its setter is setDraggable(), which the key `drag` does not name.
   */
  public function testDragIsHonoured(): void {
    $values = (new Modal(NULL, ['drag' => FALSE]))->getValues();
    $this->assertArrayHasKey('drag', $values);
    $this->assertSame('false', $values['drag']);
  }

  /**
   * The `backdropColorBg` option is honoured per instance.
   *
   * Its setter is setBackdropColor(), which the key does not name.
   */
  public function testBackdropColorBgIsHonoured(): void {
    $values = (new Modal(NULL, ['backdropColorBg' => 'primary']))->getValues();
    $this->assertArrayHasKey('backdropColorBg', $values);
    $this->assertSame('primary', $values['backdropColorBg']);
  }

  /**
   * The `colorSchemeInherit` option is settable on its own.
   *
   * It used to be reachable only as setColorScheme()'s second argument, so it
   * could not be expressed from an options array at all, and could never be
   * set without also setting a colour scheme.
   */
  public function testColorSchemeInheritIsSettableIndependently(): void {
    $values = (new Modal(NULL, ['colorSchemeInherit' => TRUE]))->getValues();
    $this->assertArrayHasKey('colorSchemeInherit', $values);
    $this->assertSame('true', $values['colorSchemeInherit']);
  }

  /**
   * The `backdrop` setting exists in config and validates against the schema.
   *
   * The settings form has always rendered a Backdrop checkbox reading
   * getValue('backdrop'), but the key was in neither the shipped defaults nor
   * the schema. neo_settings derives its defaults from the install file and
   * intersects submitted values against the known key set, so the checkbox
   * rendered unchecked whatever the real default was, and the submitted value
   * was discarded on save -- a control that looked functional and did nothing.
   *
   * KernelTestBase validates config against the schema on save, so the save
   * below is what pins the schema half.
   */
  public function testBackdropSettingExistsInConfig(): void {
    $config = $this->config('neo_modal.settings');
    $this->assertArrayHasKey('backdrop', $config->getRawData(), 'The Backdrop checkbox has a setting behind it.');
    $this->assertTrue($config->get('backdrop'), 'Backdrop defaults on, matching the JS default and the serializer default.');

    $config->set('backdrop', FALSE)->save();
    $this->assertFalse($this->config('neo_modal.settings')->get('backdrop'), 'The setting round trips through config.');
  }

  /**
   * Setting both header options emits both.
   *
   * These are two independent options, but were emitted from the two arms of
   * one if/elseif — and the first arm fires whenever `header` merely differs
   * from config, so headerInContent was silently dropped alongside it.
   */
  public function testHeaderAndHeaderInContentAreIndependent(): void {
    $values = (new Modal(NULL, [
      'header' => FALSE,
      'headerInContent' => TRUE,
    ]))->getValues();

    $this->assertSame('false', $values['header'] ?? NULL);
    $this->assertSame('true', $values['headerInContent'] ?? NULL);
  }

  /**
   * An attach placement is emitted on its own.
   *
   * It used to be nested inside the `attach` branch, which tests difference
   * from config rather than whether a value was set — so a modal whose attach
   * selector matched the configured one lost its placement too.
   */
  public function testAttachPlacementIsEmittedIndependently(): void {
    $values = (new Modal(NULL, ['attachPlacement' => 'top-start']))->getValues();
    $this->assertSame('top-start', $values['attachPlacement'] ?? NULL);
  }

  /**
   * An unmodified modal emits nothing.
   *
   * The serializer returns a diff against the active settings, so the
   * no-override case must stay empty -- otherwise every trigger on the site
   * carries redundant data-neo-modal-* attributes.
   */
  public function testUnmodifiedModalEmitsNoOverrides(): void {
    $this->assertSame([], (new Modal())->getValues());
  }

}

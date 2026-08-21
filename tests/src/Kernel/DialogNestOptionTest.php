<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_modal\Kernel;

use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests that an explicit `nest` survives neo_modal_ajax_render_alter().
 *
 * On the AJAX dialog path `nest` carries three meanings, not two: TRUE always
 * stacks, FALSE replaces the modal on top, and *absent* means "fall back to
 * core's same-selector-replaces contract" (see modal-dialog-ajax.ts). Every
 * other option is a two-state default.
 *
 * Modal::getValues() serialises a diff against the configured baseline, so it
 * emits a boolean only when it DIFFERS from config. `nest` ships as TRUE in
 * neo_modal.settings, which means an explicit TRUE looked identical to an
 * untouched default and was dropped from the payload — collapsing the third
 * state and handing the client "nothing was asked for".
 *
 * That is what closed the media library's own opener. Its widget button
 * renders into `#drupal-modal`, the same selector as any other
 * `data-dialog-type="modal"` link, so a media field inside a dialog — a Neo
 * Alchemist component form, a node form in a modal — hit the same-selector
 * rule and replaced the form it was launched from instead of stacking on it.
 * The `nest => TRUE` override that exists to exempt it never left PHP.
 *
 * @see neo_modal_ajax_render_alter()
 */
#[Group('neo_modal')]
class DialogNestOptionTest extends KernelTestBase {

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
   * The baseline this whole test hangs on: config ships `nest` as TRUE.
   *
   * Asserted rather than assumed, because if the default ever flips the bug
   * this file guards inverts too — an explicit FALSE becomes the value that
   * matches config and gets dropped — and the tests below would keep passing
   * while testing the wrong half of the tri-state.
   */
  public function testNestDefaultsToTrueInConfig(): void {
    $this->assertTrue(
      (bool) $this->config('neo_modal.settings')->get('nest'),
      'neo_modal.settings ships nest as TRUE.'
    );
  }

  /**
   * The media library's override reaches the client.
   *
   * TRUE here equals the configured default, which is exactly the case
   * getValues() drops.
   */
  public function testMediaLibraryClassStacksOverItsOpener(): void {
    $settings = $this->alterOpenDialog([
      'classes' => ['ui-dialog' => 'media-library-widget-modal'],
    ]);

    $this->assertArrayHasKey('nest', $settings, 'The media library asks to stack.');
    $this->assertTrue($settings['nest'], 'It stacks rather than replacing its opener.');
  }

  /**
   * CKEditor 5 appends its own class beside the media library's.
   *
   * The override is matched on tokens for this reason; a whole-value match
   * missed the media library opened from a Drupal Media button.
   */
  public function testMediaLibraryClassIsTokenMatched(): void {
    $settings = $this->alterOpenDialog([
      'classes' => ['ui-dialog' => 'media-library-widget-modal ui-dialog--narrow'],
    ]);

    $this->assertTrue($settings['nest'] ?? NULL, 'A neighbouring class does not hide the override.');
  }

  /**
   * A caller's explicit `nest` beats the per-integration override.
   */
  public function testCallerCanForceReplacement(): void {
    $settings = $this->alterOpenDialog([
      'classes' => ['ui-dialog' => 'media-library-widget-modal'],
      'neo' => ['nest' => FALSE],
    ]);

    $this->assertArrayHasKey('nest', $settings, 'An explicit FALSE is stated, not implied.');
    $this->assertFalse($settings['nest'], "The caller's choice wins over the override.");
  }

  /**
   * An attribute-style 'false' is not the truthy string it literally is.
   */
  public function testStringFalseIsNormalised(): void {
    $settings = $this->alterOpenDialog(['neo' => ['nest' => 'false']]);

    $this->assertFalse($settings['nest'] ?? NULL, "The string 'false' means FALSE.");
  }

  /**
   * A dialog that asked for nothing stays silent.
   *
   * The absent third state is the whole reason `nest` is special-cased, so a
   * fix that simply always emitted it would be just as wrong: every
   * same-selector dialog would start stacking instead of replacing.
   */
  public function testAnOrdinaryDialogOmitsNest(): void {
    $settings = $this->alterOpenDialog(['width' => 800]);

    $this->assertArrayNotHasKey('nest', $settings, 'Nothing was asked for, so nothing is said.');
  }

  /**
   * A sizing-only override stays silent too.
   *
   * Views UI and webform sit beside the media library in the same override
   * map, and they rely on being replaced rather than stacked. This pins the
   * boundary: `nest` travels only for the entry that actually asks for it.
   */
  public function testSizingOnlyOverridesOmitNest(): void {
    $settings = $this->alterOpenDialog([
      'classes' => ['ui-dialog' => 'views-ui-dialog'],
    ]);

    $this->assertSame('100%', $settings['width'] ?? NULL, 'The sizing override still applies.');
    $this->assertArrayNotHasKey('nest', $settings, 'Replacing remains the default for it.');
  }

  /**
   * Runs an openDialog command through the hook and returns its settings.
   *
   * @param array $dialogOptions
   *   The command's jQuery-UI dialog options.
   *
   * @return array
   *   The settings the client would receive.
   */
  protected function alterOpenDialog(array $dialogOptions): array {
    $data = [
      [
        'command' => 'openDialog',
        'selector' => '#drupal-modal',
        'data' => '<p>Body</p>',
        'dialogOptions' => $dialogOptions,
      ],
    ];
    neo_modal_ajax_render_alter($data);

    return $data[0]['settings'] ?? [];
  }

}

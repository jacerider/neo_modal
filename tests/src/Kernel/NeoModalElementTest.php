<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_modal\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\neo_modal\Element\NeoModal;
use Drupal\neo_modal\Element\NeoModalConfirm;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the neo_modal render element's option handling.
 *
 * A modal inside a form defaults to smart actions. That default used to be
 * applied by calling setSmartActions() on the Modal *after* constructing it
 * with the caller's options — and since that setter defaults to TRUE, it
 * overwrote whatever the caller asked for. The result was that
 * '#modal' => ['smartActions' => FALSE] was impossible to express on any form
 * modal, despite smartActions being a documented, settable option.
 */
#[Group('neo_modal')]
class NeoModalElementTest extends KernelTestBase {

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
   * A form modal turns smart actions on by default.
   */
  public function testFormModalDefaultsToSmartActions(): void {
    $attributes = $this->triggerAttributes(['#parents' => ['thing']]);
    $this->assertArrayHasKey('data-neo-modal-smartActions', $attributes);
    $this->assertSame('true', $attributes['data-neo-modal-smartActions']);
  }

  /**
   * A form modal honours an explicit smartActions of FALSE.
   */
  public function testFormModalHonoursExplicitSmartActionsFalse(): void {
    $attributes = $this->triggerAttributes([
      '#parents' => ['thing'],
      '#modal' => ['smartActions' => FALSE],
    ]);
    // FALSE matches the shipped default, so the serializer emits nothing —
    // the point is that TRUE is no longer forced on top of the caller.
    $this->assertArrayNotHasKey('data-neo-modal-smartActions', $attributes);
  }

  /**
   * A modal outside a form does not turn smart actions on.
   */
  public function testNonFormModalDoesNotSetSmartActions(): void {
    $attributes = $this->triggerAttributes([]);
    $this->assertArrayNotHasKey('data-neo-modal-smartActions', $attributes);
  }

  /**
   * A subclass default survives a caller passing its own #modal.
   *
   * The neo_modal_confirm element wants scope on, which is what puts
   * neo-modal--scoped on the modal. That default used to live inside #modal in
   * getInfo(), and Drupal applies element info with a shallow union — so a
   * caller passing any #modal of their own replaced the whole array and
   * silently dropped it. The shelf_top preset it uses ships scope: false, so
   * nothing downstream restored it.
   */
  public function testSubclassModalDefaultSurvivesCallerOptions(): void {
    // Real element info, so the shallow union Drupal performs is the one under
    // test rather than something reconstructed here.
    $info = $this->container->get('plugin.manager.element_info')
      ->getInfo('neo_modal_confirm');
    $element = [
      '#title' => 'Delete',
      '#parents' => ['thing'],
      // The caller cares about the title and says nothing about scope.
      '#modal' => ['title' => 'Are you sure?'],
      '#description' => NULL,
      '#modal_title' => NULL,
      '#children' => '',
    ] + $info;

    $result = NeoModalConfirm::preRenderModal($element);
    // Scope becomes a class on the modal, carried to the client through the
    // modalClasses data attribute rather than applied to the trigger.
    $modalClasses = $result['trigger']['#attributes']['data-neo-modal-modalClasses'] ?? '';
    $this->assertStringContainsString('neo-modal--scoped', $modalClasses);
  }

  /**
   * Renders the element and returns its trigger's attributes.
   *
   * @param array $overrides
   *   Element properties to set on top of the defaults.
   *
   * @return array
   *   The trigger's HTML attributes.
   */
  protected function triggerAttributes(array $overrides): array {
    $element = $overrides + [
      '#title' => 'Open',
      '#close' => '',
      '#modal' => [],
      '#modal_preset' => NULL,
      '#optional' => FALSE,
      '#attributes' => [],
      '#wrapper_attributes' => [],
      '#description' => NULL,
      '#children' => '',
    ];
    $result = NeoModal::preRenderModal($element);
    return $result['trigger']['#attributes'] ?? [];
  }

}

<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_modal\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\neo_modal\Element\NeoModal;
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

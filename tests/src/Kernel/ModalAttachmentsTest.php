<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_modal\Kernel;

use Drupal\Core\Url;
use Drupal\KernelTests\KernelTestBase;
use Drupal\neo_modal\Modal;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests that applying a modal preserves the caller's own #attached.
 *
 * The modal's own libraries used to be written in key by key:
 * $build['#attached'][$type][$key] = $attachment. `library` is a LIST, so that
 * assigned indexes 0 and 1 — overwriting whatever libraries the caller had
 * already attached there — and drupalSettings was replaced two levels deep
 * rather than merged.
 *
 * Most callers never hit it, because buildTrigger() wraps builds that have no
 * #type (or a markup-ish one) and the wrapper carries the new attachments
 * while the original keeps its own. The clobber needed a build with a real
 * #type, which is what these use.
 */
#[Group('neo_modal')]
class ModalAttachmentsTest extends KernelTestBase {

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
   * A caller's libraries survive alongside the modal's own.
   */
  public function testCallerLibrariesArePreserved(): void {
    $build = $this->linkBuild();
    (new Modal())->applyTo($build);

    // applyTo() nests the trigger, so the attachments live under it.
    $libraries = $build['trigger']['#attached']['library'] ?? [];
    $this->assertContains('system/base', $libraries, "The caller's library survives.");
    $this->assertContains('neo_modal/modal', $libraries, "The modal's library is added.");
  }

  /**
   * A caller's drupalSettings survive alongside the modal's own.
   */
  public function testCallerDrupalSettingsArePreserved(): void {
    $build = $this->linkBuild();
    (new Modal())->applyTo($build);

    $settings = $build['trigger']['#attached']['drupalSettings'] ?? [];
    $this->assertSame(1, $settings['neoModal']['callerKey'] ?? NULL, 'A neoModal settings bucket is merged, not replaced.');
  }

  /**
   * A build with a real #type carrying its own attachments.
   *
   * @return array
   *   The render array.
   */
  protected function linkBuild(): array {
    return [
      '#type' => 'link',
      '#title' => 'Open',
      '#url' => Url::fromRoute('<front>'),
      '#attached' => [
        'library' => ['system/base'],
        'drupalSettings' => ['neoModal' => ['callerKey' => 1]],
      ],
    ];
  }

}

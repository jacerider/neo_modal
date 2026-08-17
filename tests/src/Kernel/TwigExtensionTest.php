<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_modal\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\neo_modal\TwigExtension;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the twig neo_modal function and filter.
 *
 * The neo_modal() *function* builds a modal whose body comes from its options
 * — an image, a video, an iframe, an attached element — so it has no inline
 * content. It used to pass the literal string 'Modal Content' as the body,
 * which meant applyTo() emitted a <template> carrying those words next to
 * every trigger. For the media options the JS discards it, but for any other
 * option set the modal rendered the words "Modal Content" as its body.
 */
#[Group('neo_modal')]
class TwigExtensionTest extends KernelTestBase {

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
   * The function emits no placeholder body.
   */
  public function testRenderModalEmitsNoPlaceholderContent(): void {
    $trigger = ['#markup' => 'Open'];
    $build = TwigExtension::renderModal($trigger, ['video' => 'https://youtu.be/abc123']);

    $this->assertStringNotContainsString(
      'Modal Content',
      print_r($build, TRUE),
      'No placeholder text is rendered as the modal body.'
    );
  }

  /**
   * The function builds no content branch when it has no content.
   *
   * Modal already models "no inline content" as NULL, and applyTo() then emits
   * the trigger alone rather than a trigger plus an empty content template.
   */
  public function testRenderModalBuildsNoContentBranch(): void {
    $trigger = ['#markup' => 'Open'];
    $build = TwigExtension::renderModal($trigger, ['video' => 'https://youtu.be/abc123']);

    $this->assertArrayNotHasKey('modal', $build);
  }

  /**
   * The filter still carries the piped content through as the modal body.
   *
   * The filter's content is real, so removing the function's placeholder must
   * not have disturbed it.
   */
  public function testFilterModalKeepsItsContent(): void {
    $trigger = ['#markup' => 'Open'];
    $build = TwigExtension::filterModal('Some body copy', $trigger);

    $this->assertStringContainsString(
      'Some body copy',
      print_r($build, TRUE),
      'The filter still renders the content it was piped.'
    );
  }

}

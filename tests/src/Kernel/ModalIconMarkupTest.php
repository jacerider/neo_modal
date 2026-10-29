<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_modal\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\neo_icon\IconInterface;
use Drupal\neo_icon\IconLibraryInterface;
use Drupal\neo_modal\Modal;
use PHPUnit\Framework\Attributes\Group;

/**
 * A modal's icon reaches the browser rendered, not only as a class.
 *
 * The browser used to build the title icon itself, an `<i>` carrying the
 * icon's class, which only draws while the icon's library renders as a font
 * and its stylesheet is on the page. A library rendering inline SVG has no
 * per-icon class rules at all. So a modal whose icon came from the icon
 * repository now carries the icon as the icon renderer rendered it, and what
 * that rendering depended on travels with the trigger.
 *
 * The icon is a double whose render array is plain markup: what is under test
 * is that the modal renders whatever the icon renders and hands it on, not how
 * any icon library renders.
 */
#[Group('neo_modal')]
class ModalIconMarkupTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   *
   * `path_alias` and `linkit` because `neo.linkit_resolver` takes
   * `path_alias.manager`, and the container refuses to compile without it.
   */
  protected static $modules = [
    'system',
    'user',
    'path_alias',
    'linkit',
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
   * An icon from the repository is shipped rendered, beside its class.
   */
  public function testIconFromTheRepositoryIsShippedRendered(): void {
    $modal = new Modal();
    $this->setIcon($modal, $this->icon());

    $values = $modal->getValues();
    $this->assertSame('icon-test-home', $values['icon'], 'The class is still shipped.');
    $this->assertSame('<i class="neo-icon icon-test-home"><svg></svg></i>', $values['iconMarkup']);

    // On a trigger it is an attribute the script reads, and what the icon
    // depended on goes with the trigger.
    $build = ['#markup' => 'Open'];
    $modal->applyTo($build);
    $this->assertSame('<i class="neo-icon icon-test-home"><svg></svg></i>', $build['trigger']['#attributes']['data-neo-modal-iconMarkup']);
    $this->assertContains('config:neo_icon.neo_icon_library.test', $build['trigger']['#cache']['tags']);
    $this->assertContains('neo_icon/library.test', $build['trigger']['#attached']['library']);
  }

  /**
   * A class set by hand drops markup rendered for an earlier icon.
   */
  public function testRawIconClearsTheRenderedIcon(): void {
    $modal = new Modal();
    $this->setIcon($modal, $this->icon());
    $modal->setRawIcon('icon-test-other');

    $values = $modal->getValues();
    $this->assertSame('icon-test-other', $values['icon']);
    $this->assertArrayNotHasKey('iconMarkup', $values, 'The browser builds from the class alone.');
  }

  /**
   * Sets a modal's icon from an icon instance.
   *
   * @param \Drupal\neo_modal\Modal $modal
   *   The modal.
   * @param \Drupal\neo_icon\IconInterface $icon
   *   The icon.
   */
  protected function setIcon(Modal $modal, IconInterface $icon): void {
    $method = new \ReflectionMethod($modal, 'setIconFromInstance');
    $method->invoke($modal, $icon);
  }

  /**
   * Builds an icon double that renders as fixed markup.
   *
   * @return \Drupal\neo_icon\IconInterface
   *   The icon.
   */
  protected function icon(): IconInterface {
    $library = $this->createMock(IconLibraryInterface::class);
    $library->method('getLibraryName')->willReturn('library.test');
    $icon = $this->createMock(IconInterface::class);
    $icon->method('getLibrary')->willReturn($library);
    $icon->method('getSelector')->willReturn('icon-test-home');
    $icon->method('render')->willReturn([
      '#markup' => '<i class="neo-icon icon-test-home"><svg></svg></i>',
      '#allowed_tags' => ['i', 'svg'],
      '#cache' => ['tags' => ['config:neo_icon.neo_icon_library.test']],
    ]);
    return $icon;
  }

}

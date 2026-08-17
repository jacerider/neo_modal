<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_modal\Kernel;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\file\Entity\File;
use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests that the gallery formatter's title options resolve to real values.
 *
 * The formatter offers four title sources, but two of them used to read
 * $elements[$delta]['image']['#alt'] and ['#title'] -- from a render array the
 * same loop does not build until several lines later. So they read an
 * undefined index:
 *
 * - As a thumbnail title, the result was NULL, the caption silently never
 *   rendered, and the NULL was then passed on as the image's alt text.
 * - As a modal title, the NULL reached Modal::setTitle(), whose parameter is a
 *   non-nullable string -- a TypeError, i.e. a UI-selectable formatter setting
 *   that fatals the render.
 *
 * The alt and title properties live on the field item, which is where these
 * tests assert they are now read from.
 */
#[Group('neo_modal')]
class GalleryTitleResolutionTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'file',
    'image',
    'breakpoint',
    'entity_test',
    'neo',
    'neo_settings',
    'neo_tooltip',
    'neo_image',
    'neo_modal',
  ];

  /**
   * The file entity being displayed.
   */
  protected File $file;

  /**
   * The host entity carrying the image field.
   */
  protected EntityTest $host;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('file');
    $this->installEntitySchema('entity_test');
    $this->installSchema('file', ['file_usage']);
    $this->installConfig(['neo_modal']);

    FieldStorageConfig::create([
      'field_name' => 'field_gallery',
      'entity_type' => 'entity_test',
      'type' => 'image',
    ])->save();
    FieldConfig::create([
      'field_name' => 'field_gallery',
      'entity_type' => 'entity_test',
      'bundle' => 'entity_test',
    ])->save();

    file_put_contents('public://gallery.png', 'x');
    $this->file = File::create([
      'uri' => 'public://gallery.png',
      'filename' => 'gallery.png',
      'status' => 1,
    ]);
    $this->file->save();

    $this->host = EntityTest::create([
      'name' => 'Host',
      'field_gallery' => [
        'target_id' => $this->file->id(),
        'alt' => 'The alt text',
        'title' => 'The title text',
      ],
    ]);
    $this->host->save();
  }

  /**
   * Each title option resolves to the value it names.
   *
   * @param string $setting
   *   The formatter's title setting.
   * @param string $expected
   *   The title it must resolve to.
   */
  #[\PHPUnit\Framework\Attributes\DataProvider('titleSettings')]
  public function testTitleOptionsResolve(string $setting, string $expected): void {
    $this->assertSame($expected, $this->resolve($setting));
  }

  /**
   * The title settings and what each must produce.
   */
  public static function titleSettings(): array {
    return [
      'none' => ['', ''],
      'alt from the field item' => ['image_alt', 'The alt text'],
      'title from the field item' => ['image_title', 'The title text'],
    ];
  }

  /**
   * Every option resolves to a string, so setTitle() can never see NULL.
   *
   * Modal::setTitle() takes a non-nullable string, so a resolver returning
   * NULL is a fatal rather than a missing title.
   */
  public function testEveryOptionResolvesToString(): void {
    foreach (['', 'entity_title', 'image_alt', 'image_title', 'nonsense'] as $setting) {
      $this->assertIsString($this->resolve($setting), sprintf('The "%s" option resolves to a string.', $setting));
    }
  }

  /**
   * Resolves a title setting through the formatter.
   *
   * @param string $setting
   *   The formatter's title setting.
   *
   * @return mixed
   *   Whatever the formatter resolved.
   */
  protected function resolve(string $setting) {
    $formatter = $this->container->get('plugin.manager.field.formatter')->createInstance('neo_modal_image_gallery', [
      'field_definition' => $this->host->getFieldDefinition('field_gallery'),
      'settings' => [],
      'label' => 'hidden',
      'view_mode' => 'default',
      'third_party_settings' => [],
    ]);
    // EntityReferenceFormatterBase::getEntitiesToView() stamps the referring
    // item onto the entity; that item is where an image field keeps alt/title.
    $file = $this->file;
    $file->_referringItem = $this->host->get('field_gallery')->first();

    $method = new \ReflectionMethod($formatter, 'resolveTitle');
    $method->setAccessible(TRUE);
    return $method->invoke($formatter, $setting, $file);
  }

}

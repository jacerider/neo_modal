<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_modal\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests that modals open when the modal code has not loaded yet.
 *
 * The modal code proper (`neo_modal/modal.core`) is fetched on demand by the
 * stub every modal attaches. Each case here makes the first interaction with
 * the page the one that opens a modal, with no hover or focus beforehand, so
 * the code is not on the page when the request to open arrives. Clicks are
 * dispatched from script for the same reason: a webdriver click moves the
 * pointer over the element first, which would start the load early.
 *
 * Runs on stark, where neo's animation keyframes are missing and a modal
 * never finishes opening, so each page gets a 1ms animation shim.
 */
#[Group('neo_modal')]
class ModalLazyLoadTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['neo_modal_test'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * A click on a trigger before the code has loaded opens its modal.
   */
  public function testFirstClickOpensModal(): void {
    $this->visit('neo-modal-test/page');
    $this->assertCoreLoaded(FALSE);

    $this->clickTrigger('Open single');
    $this->assertNotNull($this->assertSession()->waitForElementVisible('css', '.neo-modal #single-content'));
    $this->assertCoreLoaded(TRUE);
    $this->assertSame(1, $this->countModals(), 'The held click opened the modal once.');

    // The core's own handler answers from now on: close and open again.
    $this->getSession()->executeScript('Drupal.neoModal.close();');
    $this->assertTrue($this->assertSession()->waitForElementRemoved('css', '.neo-modal'));
    $this->clickTrigger('Open single');
    $this->assertNotNull($this->assertSession()->waitForElementVisible('css', '.neo-modal #single-content'));
    $this->assertSame(1, $this->countModals());
  }

  /**
   * A gallery opened by the first click walks its whole group.
   */
  public function testGalleryNavigation(): void {
    $this->visit('neo-modal-test/page');
    $this->assertCoreLoaded(FALSE);

    $this->clickTrigger('Gallery 1');
    $assert = $this->assertSession();
    $this->assertNotNull($assert->waitForElementVisible('css', '.neo-modal .neo-modal--next a'));
    $this->assertTrue($this->waitForGalleryItem(1));

    $this->getSession()->executeScript("document.querySelector('.neo-modal .neo-modal--next a').click();");
    $this->assertTrue($this->waitForGalleryItem(2), 'Next shows the second item.');
    $this->getSession()->executeScript("document.querySelector('.neo-modal .neo-modal--next a').click();");
    $this->assertTrue($this->waitForGalleryItem(3), 'Next shows the third item.');
    $this->getSession()->executeScript("document.querySelector('.neo-modal .neo-modal--prev a').click();");
    $this->assertTrue($this->waitForGalleryItem(2), 'Prev goes back.');
  }

  /**
   * The neoModal AJAX command opens a modal before the code has loaded.
   */
  public function testAjaxCommandBeforeLoad(): void {
    $this->visit('neo-modal-test/page');
    $this->assertCoreLoaded(FALSE);
    $this->getSession()->executeScript("document.getElementById('command').click();");
    $this->assertNotNull($this->assertSession()->waitForElementVisible('css', '.neo-modal #command-content'));
    $this->assertCoreLoaded(TRUE);
  }

  /**
   * The stub can itself arrive in the AJAX response that opens the modal.
   */
  public function testAjaxCommandBringsTheStub(): void {
    $this->visit('neo-modal-test/bare');
    $this->assertFalse($this->getSession()->evaluateScript('!!Drupal.neoModal'), 'The page has no modal stub.');
    $this->getSession()->executeScript("document.getElementById('command').click();");
    $this->assertNotNull($this->assertSession()->waitForElementVisible('css', '.neo-modal #command-content'));
    $this->assertCoreLoaded(TRUE);
  }

  /**
   * With the escape hatch on, the code is on the page from the start.
   */
  public function testPreload(): void {
    $this->writeSettings([
      'settings' => [
        'neo_modal_preload' => (object) ['value' => TRUE, 'required' => TRUE],
      ],
    ]);
    $this->rebuildAll();
    $this->visit('neo-modal-test/page');
    $this->assertCoreLoaded(TRUE);
    $this->assertFalse($this->getSession()->evaluateScript('!!(drupalSettings.neoModal && drupalSettings.neoModal.core)'), 'Nothing is left to load on demand.');
    $this->assertTrue($this->getSession()->evaluateScript('!(Drupal.neoModal.open({content: "<p id=\'sync\'>x</p>"}) instanceof Promise)'), 'open() returns the modal itself.');
    $this->assertNotNull($this->assertSession()->waitForElementVisible('css', '.neo-modal #sync'));
  }

  /**
   * Visits a page and shims the animations.
   */
  private function visit(string $path): void {
    $this->drupalGet($path);
    $this->getSession()->executeScript(<<<'JS'
      const style = document.createElement('style');
      style.textContent = '@keyframes neo-test-shim { to { opacity: 1; } } [class*="neo-animate--"] { animation: neo-test-shim 1ms !important; }';
      document.head.appendChild(style);
      JS);
  }

  /**
   * Clicks a modal trigger from script, by its text.
   */
  private function clickTrigger(string $text): void {
    $this->getSession()->executeScript(sprintf(
      "Array.from(document.querySelectorAll('.use-neo-modal')).find((el) => el.textContent.trim() === %s).click();",
      json_encode($text),
    ));
  }

  /**
   * Asserts whether the modal code is on the page.
   */
  private function assertCoreLoaded(bool $loaded): void {
    $this->assertSame($loaded, $this->getSession()->evaluateScript('typeof window.NeoModal === "function"'));
  }

  /**
   * Counts the modals on the page.
   */
  private function countModals(): int {
    return (int) $this->getSession()->evaluateScript('document.querySelectorAll(".neo-modal").length');
  }

  /**
   * Waits for the top modal to show a gallery item.
   */
  private function waitForGalleryItem(int $delta): bool {
    return $this->getSession()->getPage()->waitFor(10, fn () => $this->getSession()->evaluateScript(sprintf(
      '(() => { const modal = Drupal.neoModal.getTop(); const content = modal && modal.getContent(); return !!content && content.textContent.includes("Gallery item %d"); })()',
      $delta,
    )));
  }

}

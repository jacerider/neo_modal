/**
 * @file
 * Smoke tests for Neo modals against the running site.
 *
 * These deliberately do NOT call drupalInstall(). Nightwatch's install path
 * builds a throwaway prefixed site from the minimal `nightwatch_testing`
 * profile, which would need a setup file to install neo, neo_settings and the
 * modal presets before any of this exists. Running against the site as it
 * stands tests the configuration people actually use, and is the fastest way
 * to prove the whole path works.
 *
 * The trade-off is the usual one for install-free tests: no drupalCreateUser,
 * no drupalUninstall, and the assertions have to hold for anonymous visitors.
 * They target the mobile slide-menu modal, which is the one modal trigger the
 * front page renders without a login.
 *
 * Each case re-opens from a fresh page load rather than inheriting the previous
 * one's state, so a failure points at the behaviour it names instead of
 * cascading into the rest of the file.
 */
const TRIGGER = '.use-neo-modal';
const MODAL = '.neo-modal';

/**
 * Load the front page at a mobile width and open the modal.
 *
 * @param {object} browser
 *   The Nightwatch browser object.
 *
 * @return {object}
 *   The browser object.
 */
function openModal(browser) {
  return browser
    .drupalRelativeURL('/')
    .waitForElementVisible(TRIGGER, 5000)
    .click(TRIGGER)
    .waitForElementVisible(MODAL, 5000)
    // The modal is visible well before it has finished opening, and it binds
    // its keyboard handlers in finishOpen() — which runs from the open
    // animation's callback. Pressing Escape before that lands does nothing.
    .neoWaitForAnimations(MODAL);
}

module.exports = {
  '@tags': ['neo_modal', 'neo'],

  before(browser) {
    // The slide-menu trigger is the mobile navigation, hidden at the 1920px
    // default width the webdriver args set.
    browser.resizeWindow(420, 900);
  },

  after(browser) {
    browser.end();
  },

  'the page serves compiled assets, not the dev server': (browser) => {
    browser
      .drupalRelativeURL('/')
      .waitForElementPresent('body', 5000)
      // Shipped by neo_build and discovered automatically; if this fails, the
      // run is testing HMR output rather than what deploys.
      .assert.neoAssetsBuilt();
  },

  'a trigger opens a modal': (browser) => {
    openModal(browser).assert.visible(
      '.neo-modal--content',
      'The modal rendered its content.',
    );
  },

  'escape closes the modal': (browser) => {
    openModal(browser)
      .neoPressKey(browser.Keys.ESCAPE)
      .waitForElementNotPresent(MODAL, 5000);
  },

  /**
   * Regression guard for the bound/unbound listener leak.
   *
   * finishOpen() attached its keydown handlers with .bind(this) but
   * finishClose() removed them by the unbound method reference, so the
   * references never matched and every modal ever opened kept a live
   * document.body listener. Escape then re-ran close() on already-dead
   * instances, which steal focus back to their old trigger and fire their
   * signals again.
   *
   * There is no DOM API to enumerate listeners, so this asserts the observable
   * consequence: after closing, further Escapes must be inert, and the modal
   * must still open cleanly afterwards.
   */
  'escape after close is inert and the modal still reopens': (browser) => {
    openModal(browser)
      .neoPressKey(browser.Keys.ESCAPE)
      .waitForElementNotPresent(MODAL, 5000)
      .neoPressKey(browser.Keys.ESCAPE)
      .neoPressKey(browser.Keys.ESCAPE)
      .assert.not.elementPresent(
        MODAL,
        'Stray Escapes did not resurrect a closed modal.',
      )
      .click(TRIGGER)
      .waitForElementVisible(MODAL, 5000)
      .neoWaitForAnimations(MODAL)
      .assert.visible(
        '.neo-modal--content',
        'The modal still opens after repeated Escapes.',
      )
      .neoPressKey(browser.Keys.ESCAPE)
      .waitForElementNotPresent(MODAL, 5000);
  },

  /**
   * Regression guard for reopening during the out-animation.
   *
   * close() marks the modal `neo-modal--closing` and only removes it once the
   * animation finishes. Clicking the trigger inside that window reuses the
   * element that is still being torn down — and that class was never removed,
   * so the modal was excluded from its own stack: depth came up short and the
   * backdrop teardown it gates never ran, leaving a dead backdrop over the
   * page.
   *
   * close() and open() are driven back to back in one tick through the
   * instance itself rather than through two WebDriver clicks. A click pair
   * cannot reach this: the round trip between them is long enough for the
   * animation to finish and the element to be removed and rebuilt, so the test
   * passes whether or not the bug is present.
   */
  'reopening during the close animation clears the closing state': (browser) => {
    openModal(browser).execute(
      function () {
        var el = document.querySelector('.neo-modal');
        if (!el || !el.neoModal) {
          return -1;
        }
        el.neoModal.close();
        // Same tick: the element is still mid-teardown here.
        el.neoModal.open();
        return document.querySelectorAll('.neo-modal--closing').length;
      },
      [],
      (result) => {
        browser.assert.strictEqual(
          result.value,
          0,
          'A modal reopened mid-close is no longer marked as closing.',
        );
      },
    );
  },

  /**
   * Regression guard for closing the upper half of a nested pair.
   *
   * A stacked modal pushes the one under it down a level: it takes a
   * `data-neo-modal--depth` the stylesheet fades and shrinks it by, and its
   * chrome is animated away behind `neo-modal--focus`. Closing the modal on top
   * has to hand both back, or what is left on screen is a half-transparent,
   * scaled-down panel that nothing can be done with — and no way out of it,
   * because the close control went with the chrome.
   *
   * The stack is read from `.neo-modal:not(.neo-modal--closing)`, so a modal
   * counts itself while opening and not while closing. Depth arithmetic written
   * for one of those is off by one in the other, and this is the case that
   * catches it: with a single modal left, the wrong offset skips it entirely
   * and its depth is never cleared.
   *
   * The nested modal is opened through the JS API rather than a second trigger
   * because the front page renders exactly one modal trigger to anonymous
   * visitors, and this file does not install a site to add another.
   */
  'closing a nested modal restores the one underneath': (browser) => {
    const NESTED = '.neo-modal--nested-probe';

    openModal(browser)
      .execute(
        function () {
          Drupal.neoModal.open({
            modalClasses: 'neo-modal--nested-probe',
            content: '<p>Nested</p>',
          });
        },
        [],
      )
      .waitForElementVisible(NESTED, 5000)
      .neoWaitForAnimations(NESTED)
      .execute(
        function () {
          const below = document.querySelector(
            '.neo-modal:not(.neo-modal--nested-probe)',
          );
          return below ? below.getAttribute('data-neo-modal--depth') : null;
        },
        [],
        (result) => {
          browser.assert.strictEqual(
            result.value,
            '1',
            'The modal underneath dropped a level while one was stacked on it.',
          );
        },
      )
      // closeTop(), the same path the close control and Escape take.
      .execute(function () {
        Drupal.neoModal.close();
      }, [])
      .waitForElementNotPresent(NESTED, 5000)
      .execute(
        function () {
          const below = document.querySelector('.neo-modal');
          return {
            depth: below ? below.getAttribute('data-neo-modal--depth') : 'gone',
            focus: below ? below.classList.contains('neo-modal--focus') : true,
          };
        },
        [],
        (result) => {
          browser.assert.strictEqual(
            result.value.depth,
            null,
            'The modal left behind is back at the top of the stack.',
          );
          browser.assert.strictEqual(
            result.value.focus,
            false,
            'The modal left behind got its chrome back.',
          );
        },
      )
      // It has to be usable again, which is the point of all of the above.
      .neoPressKey(browser.Keys.ESCAPE)
      .waitForElementNotPresent(MODAL, 5000);
  },

  'repeated open/close leaves a single wrapper behind': (browser) => {
    openModal(browser)
      .neoPressKey(browser.Keys.ESCAPE)
      .waitForElementNotPresent(MODAL, 5000)
      .click(TRIGGER)
      .waitForElementVisible(MODAL, 5000)
      .neoWaitForAnimations(MODAL)
      .neoPressKey(browser.Keys.ESCAPE)
      .waitForElementNotPresent(MODAL, 5000)
      // removeWrapper() drops the .neo-modals wrapper once it holds no modals,
      // so a fully closed stack leaves nothing behind at all. Wrappers or
      // modals accumulating across cycles is how this class's lifecycle bugs
      // surface in the DOM.
      .assert.elementCount('.neo-modals', 0)
      .assert.elementCount(MODAL, 0);
  },
};

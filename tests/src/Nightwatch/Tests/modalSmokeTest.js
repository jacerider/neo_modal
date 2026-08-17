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

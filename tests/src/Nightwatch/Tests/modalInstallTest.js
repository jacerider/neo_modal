/**
 * @file
 * Hermetic counterpart to modalSmokeTest.
 *
 * The smoke tests drive the site as it stands, which is fast and matches how
 * the module is developed. This one builds a throwaway site from scratch, so it
 * holds on a machine that has never seen this project — the shape CI needs.
 *
 * It asserts only what a clean install uniquely proves: that drupalInstallNeo()
 * produces a working Neo site with the requested theme's compiled assets on the
 * page. Behavioural coverage belongs in the smoke tests, where it costs a page
 * load rather than a site build, and a minimal site has no anonymous page that
 * opens a modal to exercise anyway.
 *
 * It targets **neo_base**, not `front`. Installing `front` hermetically pulls in
 * most of the site: its module dependencies live two base themes up on
 * neo_base; it ships block.block.* config so `block` must be present though
 * nothing declares it; its SDC components type their props with Alchemist
 * shapes, so neo_alchemist is needed just to render the header; and installing
 * that brings editor.editor.neo, whose linkit_profile does not resolve on a
 * minimal site. At that point "hermetic" costs most of what it buys. The modal
 * is theme-agnostic, so the lighter theme tests the same thing.
 */
module.exports = {
  '@tags': ['neo_modal', 'neo', 'neo_install'],

  before(browser) {
    // A failure here fails the suite, so the install succeeding is itself the
    // assertion that this module set installs cleanly from nothing.
    browser.drupalInstallNeo({
      modules: ['neo_modal'],
      theme: 'neo_base',
    });
  },

  after(browser) {
    browser.drupalUninstall().end();
  },

  'the installed site serves the requested theme': (browser) => {
    browser
      .drupalRelativeURL('/')
      .waitForElementPresent('body', 10000)
      // The reason drupalInstallNeo installs a theme at all: Neo compiles its
      // assets into the theme that owns the build scope, so a site left on the
      // install profile's default would serve none of them.
      .execute(
        function () {
          return Array.from(
            document.querySelectorAll('link[href], script[src]'),
          ).some(function (el) {
            var url = el.getAttribute('href') || el.getAttribute('src') || '';
            return url.indexOf('/neo_base/') > -1;
          });
        },
        [],
        (result) => {
          browser.assert.ok(
            result.value,
            'The requested theme is installed and serving its assets.',
          );
        },
      );
  },
};

/**
 * @file
 * Hermetic counterpart to modalSmokeTest. NOT YET PASSING — see below.
 *
 * The smoke tests run against the site as it stands. This one builds a
 * throwaway site from scratch, which is the shape a CI run needs.
 *
 * STATUS: drupalInstallNeo() itself works — it resolves the theme's base-theme
 * chain, installs the modules those themes require and the modules providing
 * their shipped config, and produces a rendering Neo site. Getting to that
 * point surfaced a chain of undeclared requirements:
 *
 *   1. front's module dependencies live on neo_base, two base themes up.
 *   2. front ships block.block.* config, so `block` must be installed even
 *      though nothing declares it.
 *   3. front's SDC components type their props with Alchemist shapes
 *      ("image", "menu"), so neo_alchemist is required to render the header —
 *      declared by neither the theme nor neo_modal.
 *   4. CURRENT BLOCKER: installing neo_alchemist brings editor.editor.neo,
 *      whose linkit_profile references a profile that does not exist on a
 *      minimal site, and the test installer's ConfigSchemaChecker rejects it.
 *
 * The honest read is that a hermetic install of the `front` theme is close to
 * installing the whole site, which costs much of what hermeticity buys. The
 * likely resolutions are to install linkit's profile config too, or to run
 * these against a lighter theme where the modal (which is theme-agnostic) does
 * not need the full component stack.
 *
 * Tagged `neo_install` only, deliberately: `ddev nightwatch neo_modal` must
 * stay green, so this does not run unless asked for by name.
 *
 * WARNING when picking this up: a failed drupalInstall leaves the browser
 * pointed at the ORIGINAL site rather than the test site, so these assertions
 * pass against the dev site while the install is broken. Confirm the install
 * succeeded before trusting a green run.
 */
module.exports = {
  '@tags': ['neo_install'],

  before(browser) {
    browser.drupalInstallNeo({
      // neo_alchemist is not a declared dependency of either neo_modal or the
      // front theme, but the theme's SDC components type their props with
      // Alchemist shapes ("image", "menu"). Without it the component
      // definitions are invalid and rendering the header throws, so a theme's
      // *runtime* requirements can exceed what its info.yml declares.
      modules: ['neo_modal', 'neo_alchemist'],
      theme: 'front',
    });
  },

  after(browser) {
    browser.drupalUninstall().end();
  },

  'the installed site serves Neo theme assets': (browser) => {
    browser
      .drupalRelativeURL('/')
      .waitForElementPresent('body', 10000)
      // The reason drupalInstallNeo installs a theme at all: Neo's compiled
      // assets live in the theme that owns the build scope, so a site left on
      // the profile default would serve none of them and every later assertion
      // would fail for the wrong reason.
      .execute(
        function () {
          return (
            document.querySelectorAll(
              'link[href*="/themes/front/"], script[src*="/themes/front/"]',
            ).length > 0
          );
        },
        [],
        (result) => {
          browser.assert.ok(
            result.value,
            'The front theme is installed and serving its assets.',
          );
        },
      );
  },

  'neo_modal is installed and its API is available': (browser) => {
    browser
      .drupalRelativeURL('/')
      .waitForElementPresent('body', 10000)
      .execute(
        function () {
          return {
            drupal: typeof Drupal !== 'undefined',
            // neo_modal replaces core's dialog system, so on a site where it is
            // installed this global is the observable proof it took over.
            neoModal:
              typeof Drupal !== 'undefined' &&
              typeof Drupal.neoModal !== 'undefined',
          };
        },
        [],
        (result) => {
          browser.assert.ok(result.value.drupal, 'Drupal bootstrapped.');
          browser.assert.ok(
            result.value.neoModal,
            'Drupal.neoModal is present, so neo_modal installed and its library loaded.',
          );
        },
      );
  },
};

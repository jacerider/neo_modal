# Changelog

## Tests no longer install Linkit and Path alias just to start

The kernel and FunctionalJavascript tests list only the modules they use, now
that `neo` installs without Linkit and Path alias. The Nightwatch install test
checks that the page is rendered by the requested theme and serves compiled
Neo stylesheets. It used to look for the theme's name in asset URLs, but Neo
compiles assets into the `dist/` of the theme that owns the scope, so that
does not match.

## The modal code loads on demand

**Breaking.** `neo_modal/modal`, the library every modal attaches, is now a
small stub, and the modal code — the `NeoModal` class with popper, tippy and
body-scroll-lock, about 100 KB raw and 28 KB gzipped — moved to a new
`neo_modal/modal.core` that no page attaches. The stub fetches it when a
visitor points at, focuses, touches or clicks a modal trigger, or when anything
calls `Drupal.neoModal`; a click that arrives first is held and replayed once
the code is in. Pages that can open a modal no longer pay for one that never
opens.

What changes for code outside this module:

- **`window.NeoModal` exists only once the code has loaded.** A script that
  calls `NeoModal.*` at load time finds it undefined. Use `Drupal.neoModal`, or
  wait on the new `Drupal.neoModal.ready()`.
- **`Drupal.neoModal.open()` returns a Promise for the instance until the code
  has loaded**, and the instance itself after. Code that used the return value
  synchronously must wait on `ready()` first, as the core dialog commands now
  do.
- Before the code has loaded, `getTop()` returns `null` and `close()` does
  nothing, since nothing can be open.
- New `Drupal.neoModal.ready()` and `load()`, both returning a Promise that
  settles once the code is loaded, loading it if need be.
- The `neoModal` and `openDialog` AJAX commands return a promise, which core
  waits on before running the response's next command.

**Escape hatch:** `$settings['neo_modal_preload']` in settings.php makes
`neo_modal/modal` depend on `neo_modal/modal.core`, so the code loads with the
page as it used to and everything is synchronous again. `TRUE` or `FALSE` for
every scope, or a list of Neo scopes. It defaults to `['back']`, so admin pages
and the Alchemist editor are unchanged. Rebuild caches after changing it.

Also: inline scripts in a modal's template content now run in the global scope
(an indirect `eval`), as they would on the page, rather than inside the modal's
own method, which also lets the minifier shorten the modal code's names.

## A modal's title icon reaches the browser rendered

**A modal whose icon is set with `setIcon()` or `setTitleWithDynamicIcon()`
now carries the icon as the icon renderer rendered it, in a new `iconMarkup`
option, and the browser shows that instead of building an `<i>` from the
icon's class.** The class only drew while the icon's library rendered as a font
and its stylesheet was on the page; an icon library rendering inline SVG has no
per-icon class rules, so its icons came out blank. The rendered icon also
brings its own attachments and cache tags onto the trigger.

- `icon` is still set and shipped beside `iconMarkup`. With no `iconMarkup`
  the browser builds the `<i>` from `icon` and `iconClasses` as before.
- `setRawIcon()` clears `iconMarkup`, since a class set by hand names a
  different icon than any markup rendered before it.
- `setIconMarkup()` sets the rendered icon directly. It is inserted as HTML, so
  it must come from the icon renderer or be otherwise trusted.
- In font mode the title icon looks as it did; its `<i>` now also carries
  `aria-hidden="true"`, as every rendered icon does.

## 1.0.85 — 2026-10-22

- A media item's edit modal stacks on the modal that hosts its media library
  widget.

## 1.0.84 — 2026-10-15

- A modal never autofocuses a field's help badge when it opens.

## 1.0.83 — 2026-10-14

- Tab stays inside the top modal without making the rest of the page inert.

## 1.0.82 — 2026-10-01

- New `Modal::setShadow()` and `Modal::setHeaderOutRadius()`, written as
  `--modal-shadow` and `--modal-header-out-radius`, so an outside close button
  can read as a tab of the panel. Both default to the previous rendering.
- The panel shadow sits on the element the content animation slides, so it
  moves with the panel.

## 1.0.81 — 2026-08-21

- Opening the media library from a modal no longer replaces the dialog it was
  opened from.

## 1.0.80 — 2026-08-18

- Closing a nested modal restores the modal underneath it.
- Coding standard pass.

## 1.0.79 — 2026-08-17

A correctness sweep:

- Options that were silently dropped are honoured, `smartActions` among them.
- A formatter fatal is fixed, and listeners are torn down properly.
- Icon libraries a modal's icon needs are attached, and an empty placeholder
  body is no longer rendered.
- Per-open and per-build state each have a single owner.
- The AJAX dialog payload has one precedence order and one encoding.
- The modal block no longer collects configuration it cannot use, and drag
  ordering is restored.
- Touch drag works again, and stale sizing is recalculated.
- Phantom route parameters are dropped, and the TypeScript typings match the
  code.

## 1.0.78 — 2026-08-15

- The close control is a `<button>`, not an anchor, and shows its focus ring
  only for keyboard focus.

## 1.0.77 — 2026-08-14

- A dialog opened with the same selector as an open one replaces it instead of
  stacking, and jQuery dialog events are bridged.

## 1.0.76 — 2026-08-07

- Opening a modal imperatively through `triggerBind` no longer stacks a second
  one.

## 1.0.75 — 2026-08-03

- Focusing a modal as it opens no longer scrolls its content pane.

## 1.0.74 — 2026-07-31

- The backdrop animation setting keys match between the settings form and the
  script.
- Expanded README, and the `neo-modal` agent skill reference.

## 1.0.73 — 2026-07-16

- The modal libraries declare the script dependencies they were relying on.

## 1.0.72 — 2026-07-15

- Slide menus gain an `expand_depth` setting that renders children inline.

## 1.0.71 — 2026-07-13

- The `neo_modal_account` block can be embedded and can show a link to the
  login page, and has a configuration schema.
- The login form's forgot-password link no longer fails when its block is
  missing.

## 1.0.70 — 2026-07-09

- Configuration schema: the heading and footer block sequences are mappings,
  not strings.

## Releases before 1.0.70 are not recorded

This file was started in October 2026. The releases from `1.0.70` on were
recorded afterwards from their commits; earlier ones are in the git history
only.

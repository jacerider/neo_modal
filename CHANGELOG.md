# Changelog

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

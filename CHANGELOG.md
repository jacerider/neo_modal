# Changelog

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

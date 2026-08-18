CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Presets
 * Usage within TWIG
 * Usage within PHP
 * Render elements
 * Closing a modal
 * Groups and galleries
 * Usage with AJAX
 * Blocks
 * Core dialog replacement
 * JavaScript API
 * Options
 * Styling
 * Notes and known quirks


INTRODUCTION
------------

Provides a modal API for elements and fields. One system covers centered
dialogs, left/right/top shelves (off-canvas panels), image/video lightboxes with
group navigation, and popovers anchored to another element.

It also **replaces core's jQuery UI dialog**. The module empties
`core/drupal.dialog` and `core/drupal.dialog.ajax` and re-points them at its own
JavaScript, so existing `data-dialog-type="modal"` links, Views UI, Media
Library and Webform dialogs render as Neo modals with no changes on your side,
and jQuery UI is never loaded.

For an agent-oriented reference of the same material, see
[install/skills/neo-modal/SKILL.md](install/skills/neo-modal/SKILL.md) (copied
into `.claude/skills/` by `drush neo:build:install`).


REQUIREMENTS
------------

This module requires the Neo suite of modules: `neo`, `neo_settings` and
`neo_tooltip`.


INSTALLATION
------------

Install as you would normally install a contributed Drupal module. Visit
https://www.drupal.org/node/1897420 for further information.


CONFIGURATION
-------------

Site-wide defaults live at `/admin/config/neo/modal` (permission:
*Administer Neo Modal*). The form is grouped into Features, Header, Content,
Footer, Group, Size & Placement, Colors and Animations.

Whatever you change here is emitted once per page as
`drupalSettings.neoModal.defaults` and becomes the baseline for every modal on
the site. Presets and per-modal options are diffed against this baseline and
travel as `data-neo-modal-*` attributes on the trigger.

There are four option layers, each overriding the one before it:

1. JavaScript defaults (`src/js/modal/modal.ts`)
2. Site settings (`neo_modal.settings`) → `drupalSettings.neoModal.defaults`
3. A preset (a `neo_settings` variation) → `data-neo-modal-*`
4. Per-modal options → `data-neo-modal-*`


PRESETS
-------

Presets are `neo_settings` variations of the `neo_modal` plugin, managed on the
same settings page. Shipped presets:

| ID            | Effect                                                        |
|---------------|---------------------------------------------------------------|
| `dialog`      | Smart actions, header in content, content scroll. Used automatically for AJAX/core dialogs |
| `media`       | Numeration, fit, no content padding — the lightbox preset      |
| `shelf_left`  | 460px wide, full height, left placement, slide in/out          |
| `shelf_right` | 460px wide, full height, right placement, slide in/out         |
| `shelf_top`   | 1200px wide, top placement, fade down                          |

Reference a preset with or without the `neo_modal_` prefix — `'shelf_right'` and
`'neo_modal_shelf_right'` are equivalent. An unknown preset name silently falls
back to the active settings.

**Scopes.** A preset with *Allow Scope* checked will additionally merge the
preset selected as the site's Frontend or Backend scope (base settings form →
Scopes), depending on whether the current route is an admin route. Pass
`scope: TRUE` in a modal's options to opt that modal in; the twig filter and
function do this by default.


USAGE WITHIN TWIG
-----------------

A twig filter converts a render element into a modal. The subject of the filter
is the modal **content**; the first argument is the **trigger**.

```twig
{{ content|neo_modal(trigger, options, preset, modal_attributes, trigger_attributes) }}
```

In this example the first param is the trigger title, the second is the modal
options (because the breakpoint is set to `md`, the modal is only used when the
screen is below the medium breakpoint), the third is the preset, and the fourth
is the attributes applied to the trigger.

```twig
{{ form|neo_modal('Filter By'|t, {title: 'Filter Products', breakpoint: 'md'}, 'shelf_left', {class: ['btn btn-outline bg-base-0 w-full text-center']}) }}
```

A gallery of images sharing one group:

```twig
{% for item in items %}
  {{ item.image|neo_modal(item.thumbnail, {group: 'gallery'}, 'media') }}
{% endfor %}
```

A `neo_modal()` twig **function** also exists, but it hard-codes its content to
the string `Modal Content` — it is only useful when the content comes from
options (`image`, `video`, `iframe`). Use the filter for real content.


USAGE WITHIN PHP
----------------

```php
use Drupal\neo_modal\Modal;

$build['link'] = [
  '#type' => 'link',
  '#title' => $this->t('Open'),
  '#url' => Url::fromRoute('<front>'),
];

$modal = new Modal($contentRenderArray, ['title' => 'Details'], 'shelf_right');
$modal
  ->setSubtitle($this->t('More information'))
  ->setColorScheme('primary-dark')
  ->setDraggable(FALSE)
  ->setPlacementToRight();
$modal->applyTo($build['link']);
```

`applyTo()` replaces the build with `['trigger' => …, 'modal' => …]`, where the
modal content is wrapped in a `<template class="neo-modal--template">` that the
behavior adopts on click.

Option keys may be camelCase or snake_case — `contentScroll` and
`content_scroll` both resolve to `setContentScroll()`.

Media modals need no content at all:

```php
$modal = new Modal();
$modal->setTitle('Promo video')
  ->setVideo('https://www.youtube.com/watch?v=9bZkp7q19f0')
  ->setGroup('media')
  ->setTriggerOverlay($this->t('Watch Video'), 'play-circle');
$modal->applyTo($build['thumbnail']);
```

Useful builder methods that have no equivalent options key:

| Method                                              | Purpose                                        |
|-----------------------------------------------------|------------------------------------------------|
| `setDraggable(bool)`                                 | The `drag` option (drag between grouped modals) |
| `setBackdropColor(string)`                           | The `backdropColorBg` option                    |
| `setTitleWithDynamicIcon(string $title, array $prefix = [])` | Title plus an auto-matched icon, and attaches the icon library |
| `setTriggerOverlay($title, $icon, $iconOnly, $attributes)` | Hover overlay drawn on top of the trigger  |
| `setBreakpoint('md'\|'lg')`                          | Render inline above the breakpoint, modal below |
| `addModalClass()` / `setModalClasses()`              | Classes on `.neo-modal`                         |
| `addTriggerClass()` / `mergeTriggerAttributes()`     | Trigger markup                                  |
| `applyToForm(&$trigger, &$content)`                  | Form-safe variant of `applyTo()` (div, not template) |
| `getValues()` / `getTriggerAttributes()` / `getAttachments()` | Inspect what will be emitted            |


RENDER ELEMENTS
---------------

### neo_modal

Wraps its children in a modal.

```php
$form['author'] = [
  '#type' => 'neo_modal',
  '#title' => $this->t('Open Modal'),      // the trigger
  '#close' => $this->t('Cancel'),          // optional close button
  '#modal' => [
    'scope' => TRUE,
    'title' => $this->t('Modal Title'),
    'width' => '600px',
  ],
  '#modal_preset' => 'shelf_right',
  '#attributes' => ['class' => ['btn']],           // trigger attributes
  '#wrapper_attributes' => ['class' => ['mt-4']],  // outer wrapper
  '#optional' => TRUE,   // skip rendering when there are no visible children
];

$form['author']['name'] = [
  '#type' => 'textfield',
  '#title' => $this->t('Name'),
];
```

Inside a form the element switches to `applyToForm()`: the content is emitted in
a `<div>` rather than a `<template>`, `smartActions` is forced on, and the
buttons found in the content are cloned into the modal footer so that submits
keep working.

### neo_modal_confirm

A trigger that opens a confirmation modal with confirm/cancel actions. Defaults
to the `shelf_top` preset with `scope` and `smartActions` enabled.

```php
$form['delete'] = [
  '#type' => 'neo_modal_confirm',
  '#title' => $this->icon('Delete this thing', 'trash'),
  '#description' => $this->t('Are you sure? This cannot be undone.'),
  '#modal_title' => $this->t('Confirm'),
  '#confirm_text' => $this->t('Delete'),
  '#cancel_text' => $this->t('Cancel'),
  '#submit' => ['::deleteSubmit'],
  '#submit_element' => [],   // extra properties merged into the confirm button
  '#attributes' => ['class' => ['btn']],
];
```

### neo_modal_link

Loads a route over AJAX and shows the response in a modal.

```php
$build = [
  '#type' => 'neo_modal_link',
  '#title' => $this->t('Edit'),
  '#url' => Url::fromRoute('entity.node.edit_form', ['node' => 1]),
  '#modal' => ['width' => '900px', 'title' => 'Edit'],
  '#modal_preset' => 'dialog',
  '#ajax_url' => $alternateUrl,   // optional: request this instead of #url
  '#ajax_method' => 'GET',        // defaults to POST
];
```

### neo_modal_close

Renders a button that closes the modal it lives in.

```php
$build['close'] = [
  '#type' => 'neo_modal_close',
  '#title' => $this->t('Close'),
  '#check_ajax' => TRUE,   // only render inside an AJAX/dialog request
];
```


CLOSING A MODAL
---------------

| Mechanism | Notes |
|---|---|
| `data-neo-modal-close` on any element | Declarative close link/button in your content |
| `#type => 'neo_modal_close'` | Render element that builds one |
| `#modal_close => TRUE` on a `submit` element | Adds `data-neo-modal-close-submit`; closes on mouseup so the submit still fires |
| `#close` on a `neo_modal` element | Adds a cancel-style close button |
| `NeoModalCloseCommand` | From an AJAX response |
| `Drupal.neoModal.close()` | Closes the top modal |
| Escape key / backdrop click | `closeOnEscape` and `backdropClose` options |

With `nest` enabled (the default) the close handlers close the **topmost**
modal, which is not necessarily the one that owns the button.


GROUPS AND GALLERIES
--------------------

Give several triggers the same `group` string and they become one navigable set:
prev/next arrows, arrow keys (`navKeyboard`), drag (`drag`) and an optional
counter (`numeration`).

Navigation reuses a single modal instance and rebuilds it from the next
trigger's options. Only `title`, `subtitle`, `icon`, `image`, `video`, `iframe`,
`content` and `trigger` change between slides; every other option stays as it
was on the first modal opened.

For fields, the **Neo | Modal Gallery** formatters build the whole thing —
`neo_modal_image_gallery` for `image` fields and `neo_modal_media_gallery` for
entity reference (media) fields. Both expose thumbnail and full image settings,
a modal preset, the title source and the group name.


USAGE WITH AJAX
---------------

```php
use Drupal\neo_modal\Ajax\NeoModalCommand;
use Drupal\neo_modal\Ajax\NeoModalCloseCommand;

$response = new AjaxResponse();
$response->addCommand(new NeoModalCommand($renderArrayOrHtml, [
  'title' => 'Hello',
  'width' => '700px',
  'fit' => TRUE,
]));

// Elsewhere:
$response->addCommand(new NeoModalCloseCommand());
```

The settings array is passed straight through to the JavaScript constructor, so
JavaScript-only options work here as well.

Local action links can be turned into modals by adding a `modal` key to the
plugin definition — the module swaps in `ModalLocalAction` automatically. Core
dialog keys sit at the top level and Neo options go under `neo`:

```yaml
my_module.action:
  route_name: my_module.form
  title: 'Add thing'
  modal:
    width: '900px'
    neo:
      placement: top
      smartActions: true
```


BLOCKS
------

Extend `NeoModalBlockBase` (or apply `NeoModalBlockTrait` to an existing block)
to get a *Modal Preset* selector, the full modal settings subform, a configurable
trigger (text, icon, icon-only, icon position, URL) and an optional **Load modal
content via AJAX** mode that renders the block body at
`/api/modal/block/{block}` instead of inlining it.

Implement `buildModalContent()`; override `neoModalForceConfiguration()` to
hard-lock options that site builders should not change. The module ships
`neo_modal_slide_menu` and `neo_modal_account` blocks built this way.


CORE DIALOG REPLACEMENT
-----------------------

* `hook_library_info_alter()` blanks `core/drupal.dialog` and
  `core/drupal.dialog.ajax` and adds dependencies on `neo_modal/modal` and
  `neo_modal/modal-dialog-ajax`.
* `openDialog`, `closeDialog` and `openModalDialogWithUrl` AJAX commands are
  reimplemented. `setDialogOption` is a no-op that logs a message.
* `hook_ajax_render_alter()` maps core dialog options (`title`, `width`,
  `height`, `classes`, `position.my`) onto Neo options and applies
  per-integration fixes: Media Library and Webform off-canvas dialogs get
  `width`/`height: 100%` and nesting; Views UI gets full size without nesting.
* Views' `setBrowserUrl` command is suppressed inside `.neo-modal` so modal
  query parameters cannot leak into `window.location`.
* A jQuery `.dialog()` shim is provided for legacy callers.


JAVASCRIPT API
--------------

```js
// Open an ad-hoc modal.
Drupal.neoModal.open({
  title: 'Hi',
  content: '<p>Markup, an HTMLElement, or (trigger) => string|HTMLElement</p>',
  width: '600px',
  buttons: { Save: () => doSave(), Cancel: () => Drupal.neoModal.close() },
  onAfterOpen: (modal) => console.log(modal.getContent()),
});

Drupal.neoModal.close();   // close the top modal
Drupal.neoModal.getTop();  // the top NeoModal instance, or null

// Static helpers.
NeoModal.setDefaultOptions(options);
NeoModal.getTop();
NeoModal.closeTop();

// Instance methods.
modal.open();
modal.close();
modal.size();             // re-measure after the content changed
modal.refreshContent();   // rebuild the content footer / smart actions
modal.getModal();
modal.getContent();
modal.getOption('width');
modal.event('onAfterOpen').on(callback);
```

The module registers default `onContentLoaded` and `onAfterClose` handlers that
run `Drupal.attachBehaviors()` and `Drupal.detachBehaviors()` on the modal
content. Supplying your own handler for those options replaces the default —
subscribe with `modal.event(...).on()` instead if you need both.

### `trigger` and `triggerBind`

`trigger` names the element a modal belongs to. It drives focus restore on
close, the argument passed to a `content()` callback, `data-neo-modal-*` option
scraping, group navigation and `appendToClosest` — and, separately, a
click-to-toggle binding controlled by `triggerBind` (default `true`).

`Drupal.neoModal.open()` forces `triggerBind: false`, because it builds a new
instance per call and opens it immediately: keeping the binding would leave a
live toggle behind on every call, so the second click would open two modals and
the third three. Construct a `new NeoModal({trigger})` **without** calling
`open()` when you want the trigger to own the modal — that is what the
declarative `.use-neo-modal` behavior does.

Every live modal element exposes its instance as `element.neoModal`.


OPTIONS
-------

Availability legend:

* **PHP** — works as an options array key (`#modal`, twig filter, `new Modal()`)
* **method** — the array key is ignored; call the named method instead
* **Config** — site settings form or preset config only
* **JS** — JavaScript only (`Drupal.neoModal.open()`, `NeoModalCommand`)

### Content and media

| Option | Default | Available | Notes |
|---|---|---|---|
| `content` | `null` | PHP, JS | Render array, string, HTMLElement, or `(trigger) => …` in JS |
| `image` | `null` | PHP, JS | Image URL; enables share/download/copy and zoom when `fit` |
| `video` | `null` | PHP, JS | YouTube/Vimeo URL, or mp4/ogg/webm |
| `iframe` | `null` | PHP, JS | Arbitrary URL in an iframe |
| `videoAutoplay` | `true` | JS | |
| `videoRatio` | `16x9` | JS | Styled ratios: `16x9`, `4x3`, `1x1`, `21x9` |
| `contentPadding` | `''` | PHP, JS | Bare numbers gain `px` (`'0'` → `0px`) |
| `contentScroll` | `false` | PHP, JS | Scroll inside the content instead of the whole modal |
| `fit` | `false` | PHP, JS | Fit media to the viewport; enables click-to-zoom on images |
| `smartActions` | `false` | PHP, JS | Clone the form's buttons into the modal footer. Add `btn-ignore` to a button to skip it |
| `buttons` | `null` | JS | `{label: callback}` — disables `smartActions` |

Precedence: `image` → `video` → `iframe` → `content`.

### Header, title and close button

| Option | Default | Available | Notes |
|---|---|---|---|
| `header` | `true` | PHP, JS | Master switch for the header row |
| `headerInContent` | `false` | PHP, JS | Header inside the content block instead of floating above it |
| `headerAnimate` | `true` | Config | Allow title/subtitle/icon/close to animate individually |
| `title` | `''` | PHP, JS | Also `setTitleWithDynamicIcon()` |
| `subtitle` | `''` | PHP, JS | |
| `icon` | `''` | PHP, JS | `setIcon($name)` resolves through neo_icon; `setRawIcon($selector)` takes a class |
| `iconClasses` | `neo-icon neo-icon-font` | JS | |
| `titleCallback` | `null` | Config, JS | Name of a `window.*` function `(modal, span) => void` |
| `closeButton` | `end` | PHP, JS | `''`, `start`, `end`, `start-out`, `end-out` |
| `closeButtonSvg` / `closeButtonClasses` | X icon / `''` | JS | |
| `numeration` | `false` | PHP, JS | "3 / 8" counter — requires `group` with more than one trigger |
| `numerationPlacement` | `start` | JS | `start` or `end` |

### Footer

The modal footer only renders when there is a media URL to act on — image, video
or iframe modals, or content whose first child is an `<img>`/`<picture>`. Form
buttons live in the separate content footer built by `buttons`/`smartActions`.

| Option | Default | Available | Notes |
|---|---|---|---|
| `footer` | `true` | PHP, JS | |
| `downloadLink` | `true` | PHP, JS | |
| `shareLink` | `true` | PHP, JS | Requires `navigator.canShare` |
| `copyLink` | `true` | PHP, JS | Copies the media URL to the clipboard |

### Size and placement

| Option | Default | Available | Notes |
|---|---|---|---|
| `width` | `auto` | PHP, JS | `auto`, `full`, `460px`, `100%`, `80vw`, or a bare number |
| `height` | `auto` | PHP, JS | Same forms as width |
| `placement` | `center` | PHP, JS | `center`, `top`, `bottom`, `left`, `right`, each also `-start`/`-end` |
| `attach` | `null` | PHP, JS | CSS selector — turns the modal into a Popper-anchored popover |
| `attachPlacement` | `auto` | PHP, JS | Only emitted when `attach` is also set |
| `appendTo` | `null` | PHP, JS | Parent for the `.neo-modals` wrapper (default `body`) |
| `appendToClosest` | `null` | PHP, JS | Nearest ancestor of the trigger; wins over `appendTo` |
| `zIndex` | `null` | PHP, JS | Sets `--modal-z-index` |
| `displaceTop`/`Right`/`Bottom`/`Left` | `''` | PHP, JS | Insets, e.g. to clear a sticky header |
| `breakpoint` | — | PHP | `md` or `lg`: inline above the breakpoint, modal below |

### Behavior

| Option | Default | Available | Notes |
|---|---|---|---|
| `nest` | `true` | PHP, JS | Stack on an open modal; `false` closes the current top first |
| `drag` | `true` | `setDraggable()`, JS | Drag left/right through a group |
| `inputFocus` | `true` | PHP, JS | Focus the first form input on open |
| `bodyLock` | `true` | PHP, JS | Lock body scroll |
| `backdrop` | `true` | PHP, JS | |
| `backdropClose` | `true` | JS | Click outside to close |
| `closeOnEscape` | `true` | JS | |
| `navKeyboard` | `true` | JS | Arrow keys navigate a group |
| `navPrevLabel` / `navNextLabel` | `Prev` / `Next` | JS | |
| `group` | `null` | PHP, JS | Shared string turns triggers into a gallery |
| `loader` | `true` | Config | Spinner while content loads |
| `modalClasses` | `null` | PHP, JS | Classes on `.neo-modal` |
| `wrapperClasses` | `null` | JS | Classes on the shared `.neo-modals` wrapper |
| `backdropClasses` | `null` | JS | |
| `bodySelector` | `.page-wrapper` | JS | Element scaled/blurred by the body transitions |
| `bodyTransitionScale` | `false` | PHP, JS | Scale the page behind the modal |
| `bodyTransitionBlur` | `false` | PHP, JS | Blur the page behind the modal |
| `scope` | `false` | PHP | Merge the front/back scope preset; adds `.neo-modal--scoped` |

### Colors

Each maps to a `--modal-*` custom property and accepts any CSS color. Available
to PHP and JS: `contentColor`, `contentColorBg`, `contentFooterColor`,
`contentFooterColorBg`, `headerColor`, `headerColorBg`, `footerColor`,
`footerColorBg`, `navColor`, `navColorBg`, `loaderColor`, `loaderColorBg`.

| Option | Default | Available | Notes |
|---|---|---|---|
| `backdropColorBg` | `''` | `setBackdropColor()`, JS | The array key is ignored — the setter is named differently |
| `colorScheme` | `scheme--reset` | PHP, JS | `setColorScheme($scheme, $inherit = TRUE)`; `primary_dark` → `scheme-primary-dark` |
| `colorSchemeInherit` | `false` | `setColorScheme()` 2nd arg, JS | When false the content gets `scheme--reset` |

### Animations

Every animated part accepts `<part>AnimateIn`, `<part>AnimateInSpeed`,
`<part>AnimateInDelay`, `<part>AnimateOut`, `<part>AnimateOutSpeed` and
`<part>AnimateOutDelay` (some parts have no delay). Names come from the Neo
animate catalog — `fadeIn*`, `slideIn*`, `zoomIn*`, `bounceIn*`, `flipIn*`,
`rotateIn*`, `backIn*`, `lightSpeedIn*`, `rollIn`, `comingIn` and the matching
`*Out` variants. Speeds and delays are `slow`, `slower`, `slowest`, `fast`,
`faster`, `fastest` (plus `default`). Invalid values are discarded.

| Part | Default in / out | Available |
|---|---|---|
| `content` | `comingIn` / `comingOut` at `fastest` | PHP, JS |
| `header` | `slideInDown` / `slideOutUp` at `fastest` | PHP, JS |
| `title` | `fadeInDown` (delay `fastest`) / none | Config, JS |
| `subtitle` | `fadeIn` (delay `fast`) / none | Config, JS |
| `icon` | `zoomIn` (delay `fastest`) / none | Config, JS |
| `closeButton` | `fadeIn` (delay `fast`) / none | Config, JS |
| `footer` | `slideInUp` / `slideOutDown` at `fastest` | Config, JS |
| `navPrev` / `navNext` | `slideInLeft` / `slideInRight` … | Config, JS |
| `prev` / `next` (between grouped modals) | `slideInLeft` / `slideOutLeft` … | Config, JS |
| `backdrop` | `fadeIn` / `fadeOut` at `fastest` | Config, JS |
| `loader` | `fadeIn` / `fadeOut` at `fastest` | Config, JS |

### Callbacks

JavaScript only: `onSettings`, `onBeforeOpen`, `onOpen`, `onAfterOpen`,
`onBeforeClose`, `onClose`, `onAfterClose`, `onBeforeNext`, `onNext`,
`onAfterNext`, `onBeforePrev`, `onPrev`, `onAfterPrev`, `onContentLoaded` — all
`(modal, data?) => void`. The footer icon markup can be overridden with
`svgOpen`, `svgClose`, `svgIconDownload`, `svgIconShare` and `svgIconLink`.


STYLING
-------

* Tailwind variant `modal:` (`.neo-modal &`) styles anything only when it is
  inside a modal — `class="p-4 modal:p-8"`.
* Spacing tokens `modal-t|r|b|l` and `modal-content-t|r|b|l` map to the live
  measurements, e.g. `top-modal-t`.
* Custom properties on `.neo-modal`: `--modal-content-color`,
  `--modal-content-bg`, `--modal-content-footer-bg`, `--modal-header-color`,
  `--modal-header-bg`, `--modal-footer-color`, `--modal-footer-bg`,
  `--modal-backdrop-bg`, `--modal-nav-color`, `--modal-nav-bg`,
  `--modal-loader-color`, `--modal-loader-bg`, `--modal-content-padding`,
  `--modal-padding-t`, `--modal-z-index`, `--modal-displace-*`,
  `--modal-max-width`.
* Classes you can add by hand: `neo-modal--btn` (a button smart actions will
  hoist into the footer), `btn-ignore` (exclude a button from smart actions),
  `neo-modal--hide`, `neo-modal--tooltip` (gets a tippy on build),
  `neo-modal--template` (marks content the behavior should adopt).
* State classes: `has-neo-modal`, `neo-modal--body-lock`,
  `neo-modal--body-transition`, `neo-modal--closing`, `[data-neo-modal--depth]`.

Editing `src/js/**` or `src/css/modal.css` requires a Neo build
(`drush neo:build`, or `npm start` while developing).


NOTES AND KNOWN QUIRKS
----------------------

* **Options without a setter are silently dropped.** A key in an options array
  only takes effect if `Modal` has a matching `set*()` method, the property is
  in `Modal::getValues()` and the name is in the JavaScript `optionsAsAttributes`
  list. `drag` and `backdropColorBg` need their setters; `closeOnEscape`,
  `backdropClose`, `videoRatio`, `videoAutoplay`, `iconClasses`, `titleCallback`,
  `numerationPlacement`, `nav*Label`, `wrapperClasses`, `backdropClasses`,
  `bodySelector`, `loader`, `headerAnimate` and the title/subtitle/icon/
  closeButton/footer/nav/backdrop/loader animation keys are settings-form or
  JavaScript territory. Invalid enum values are discarded the same way.
* Config-only options reach the browser through
  `drupalSettings.neoModal.defaults`, which is built from the **base** settings
  diff — putting them in a preset or a `#modal` array has no effect.
* `header` and `headerInContent` are mutually exclusive on the wire: if `header`
  differs from the site default, `headerInContent` is not emitted.
* `appendToClosest` suppresses `appendTo`; `attachPlacement` is only emitted when
  `attach` is set; `numeration` needs a `group` with more than one trigger.
* The `.neo-modals` wrapper and `.neo-modal--backdrop` are shared by the whole
  stack, so `wrapperClasses`, `backdropClasses`, `zIndex`, `displace*` and
  `backdropColorBg` come from whichever modal built the stack first.
* The backdrop animation settings were named `overlayAnimate*` in configuration
  and schema until `neo_modal_update_11001()` renamed them to
  `backdropAnimate*`, which is what the form and the JavaScript have always
  used. Sites that skipped the update keep dead `overlayAnimate*` keys.

Inspect what a modal will actually emit:

```bash
drush php:eval '$m = new \Drupal\neo_modal\Modal(NULL, ["title" => "T"], "shelf_right"); print json_encode($m->getValues());'
drush php:eval 'print json_encode(\Drupal::service("neo_modal.settings")->getActive()->getDiffConfigValues());'
```


MAINTAINERS
-----------

Current maintainers for Drupal 10:

- Cyle Carlson (jacerider) - https://www.drupal.org/u/jacerider

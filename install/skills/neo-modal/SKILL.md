---
name: neo-modal
description: Build modals, dialogs, shelves/off-canvas panels, lightboxes and media galleries with neo_modal — the PHP `Modal` builder, the `|neo_modal` twig filter, the `neo_modal` / `neo_modal_link` / `neo_modal_close` render elements, modal presets, AJAX commands, and the `NeoModal` JS API. Use when asked to open something in a modal/dialog/popup/shelf/lightbox, to pass or debug modal options (title, placement, width, scroll, smart actions, groups…), or when core's `data-dialog-type=modal` output needs adjusting. NOT for authoring the component that lives inside the modal (use neo-component).
allowed-tools: Read, Write, Edit, Glob, Grep, Bash
---

# Neo Modal

Module: [web/modules/contrib/neo_modal/](web/modules/contrib/neo_modal/)

Provides every overlay in a Neo site: centered dialogs, left/right/top shelves
(off-canvas), image/video lightboxes with group navigation, anchored popovers,
and a drop-in replacement for **core's jQuery UI dialog** — `neo_modal` empties
`core/drupal.dialog` and re-points it at its own JS
([neo_modal.module:82](web/modules/contrib/neo_modal/neo_modal.module#L82)), so
`data-dialog-type="modal"` links, Views UI, Media Library and Webform dialogs
all render as Neo modals with no extra work.

## Mental model

A modal is always **a trigger + some content**.

- PHP [`Drupal\neo_modal\Modal`](web/modules/contrib/neo_modal/src/Modal.php)
  renders the trigger with `class="use-neo-modal"` plus one
  `data-neo-modal-<option>` attribute per option that differs from site
  defaults, and (usually) an immediately-following
  `<template class="neo-modal--template">` holding the content.
- `Drupal.behaviors.neoModal`
  ([src/js/modal.ts](web/modules/contrib/neo_modal/src/js/modal.ts)) picks up
  every `.use-neo-modal`, reads the data attributes, resolves the content from
  the sibling template, and constructs a
  [`NeoModal`](web/modules/contrib/neo_modal/src/js/modal/modal.ts) instance.
  Nothing is in the DOM until the trigger is clicked.
- The modal itself is built into a single global `.neo-modals` wrapper appended
  to `<body>` (one shared `.neo-modal--backdrop` for the whole stack).

**Four option layers**, each overriding the previous:

| Layer | Where | Reaches the browser as |
|---|---|---|
| 1. JS defaults | [modal.ts:22-173](web/modules/contrib/neo_modal/src/js/modal/modal.ts#L22) | hard-coded |
| 2. Site settings | `/admin/config/neo/modal` (`neo_modal.settings`) | `drupalSettings.neoModal.defaults` (page-wide) |
| 3. Preset | a `neo_settings` variation, e.g. `shelf_right` | `data-neo-modal-*` on the trigger |
| 4. Per-modal options | `new Modal($content, $options, $preset)` | `data-neo-modal-*` on the trigger |

Layers 3 and 4 are diffed against layer 2 and emitted by
`Modal::getValues()` — **only options on that method's allowlist survive the
trip to the browser** (see [Which options work where](#which-options-work-where)).

## Pick an entry point

| Goal | Use |
|---|---|
| Wrap markup/render content already in a twig template | `|neo_modal` filter |
| Put part of a form in a modal (with working submits) | `#type => 'neo_modal'` |
| Confirm-before-submit button | `#type => 'neo_modal_confirm'` |
| Open a route's content over AJAX | `#type => 'neo_modal_link'` |
| Open a modal from an AJAX response | `NeoModalCommand` |
| Image/video lightbox gallery from a field | `neo_modal_gallery_*` formatters |
| A block whose content opens in a modal | extend `NeoModalBlockBase` |
| Anything else, client-side | `Drupal.neoModal.open({...})` |

## Quick starts

### Twig filter — content is the thing being filtered

```twig
{# form|neo_modal(trigger, options, preset, modal_attributes, trigger_attributes) #}
{{ form|neo_modal('Filter By'|t, {title: 'Filter Products', breakpoint: 'md'}, 'shelf_left', {class: ['btn', 'w-full']}) }}
```

Argument 4 is merged onto the **trigger**; argument 5 is merged on top of it
(rarely needed — prefer 4). `scope: true` is applied by default here.

There is also a `neo_modal()` twig **function**, but it hard-codes its content
to the string `'Modal Content'`
([TwigExtension.php:63](web/modules/contrib/neo_modal/src/TwigExtension.php#L63))
— it is only useful when the content comes from options (`image`, `video`,
`iframe`). For real content use the filter.

### Render element — `neo_modal`

```php
$form['author'] = [
  '#type' => 'neo_modal',
  '#title' => $this->t('Open Modal'),      // the trigger
  '#close' => $this->t('Cancel'),          // optional close button inside
  '#modal' => [
    'scope' => TRUE,
    'title' => $this->t('Modal Title'),
    'width' => '600px',
  ],
  '#modal_preset' => 'shelf_right',
  '#attributes' => ['class' => ['btn']],           // trigger attributes
  '#wrapper_attributes' => ['class' => ['mt-4']],  // outer wrapper
  '#optional' => TRUE,   // don't render if it has no visible children
];
$form['author']['name'] = ['#type' => 'textfield', '#title' => $this->t('Name')];
```

Inside a form (element has `#parents`) it switches to `applyToForm()`: content
is emitted in a `<div>` rather than a `<template>`, `smartActions` is forced on,
and the modal's buttons are cloned into the modal footer so submits work.
Outside a form it uses `applyTo()` and a `<template>`.
[src/Element/NeoModal.php](web/modules/contrib/neo_modal/src/Element/NeoModal.php)

### Render element — `neo_modal_confirm`

```php
$form['delete'] = [
  '#type' => 'neo_modal_confirm',
  '#title' => $this->t('Delete this thing'),        // trigger
  '#description' => $this->t('This cannot be undone.'),
  '#modal_title' => $this->t('Confirm'),
  '#confirm_text' => $this->t('Delete'),
  '#cancel_text' => $this->t('Cancel'),
  '#submit' => ['::deleteSubmit'],
  '#submit_element' => [],   // extra props merged into the confirm button
];
```

Defaults to the `shelf_top` preset with `scope`/`smartActions` on.
[src/Element/NeoModalConfirm.php](web/modules/contrib/neo_modal/src/Element/NeoModalConfirm.php)

### Render element — `neo_modal_link` (AJAX)

```php
$build = [
  '#type' => 'neo_modal_link',
  '#title' => $this->t('Edit'),
  '#url' => Url::fromRoute('entity.node.edit_form', ['node' => 1]),
  '#modal' => ['width' => '900px', 'title' => 'Edit'],
  '#modal_preset' => 'dialog',
  '#ajax_url' => $someOtherUrl,   // optional: request this instead of #url
  '#ajax_method' => 'GET',        // default POST
];
```

Emits a core AJAX dialog link (`data-dialog-type="modal"`) whose
`data-dialog-options` carries `{"neo": {...your options...}}`. On the response,
`neo_modal_ajax_render_alter()` re-runs those through `Modal` (default preset:
`dialog`) and translates core dialog keys (`width`, `height`, `title`,
`classes`, `position.my`) into Neo options.

### From an AJAX response

```php
use Drupal\neo_modal\Ajax\NeoModalCommand;
use Drupal\neo_modal\Ajax\NeoModalCloseCommand;

$response->addCommand(new NeoModalCommand($renderArrayOrHtml, [
  'title' => 'Hello',
  'width' => '700px',
  'fit' => TRUE,
]));
$response->addCommand(new NeoModalCloseCommand());
```

Settings here are **raw JS option names** — they are handed straight to
`Drupal.neoModal.open()`, so JS-only options (`buttons`, `closeOnEscape`,
callbacks are not serialisable but flags are) work too.

### PHP builder directly

```php
use Drupal\neo_modal\Modal;

$modal = new Modal($contentRenderArray, ['title' => 'Gallery'], 'media');
$modal->setColorScheme('primary-dark')
  ->setGroup('gallery')
  ->setDraggable(FALSE)
  ->setTriggerOverlay($this->t('Watch Video'), 'play-circle');
$modal->applyTo($build['thumbnail']);   // $build['thumbnail'] becomes ['trigger' => …, 'modal' => …]
```

Option keys may be camelCase **or** snake_case (`contentScroll` ===
`content_scroll`) — they are run through `Str::camel()` and matched to a
`set*()` method.

### Pure JS

```js
Drupal.neoModal.open({
  title: 'Hi',
  content: '<p>Markup, an HTMLElement, or (trigger) => string|HTMLElement</p>',
  width: '600px',
  buttons: { 'Save': () => doSave(), 'Cancel': () => Drupal.neoModal.close() },
  onAfterOpen: (modal) => console.log(modal.getContent()),
});
Drupal.neoModal.close();     // closes the top modal
Drupal.neoModal.getTop();    // the top NeoModal instance or null
```

## Options reference

Legend for the **PHP** column:

- ✅ — works as an options-array key (`#modal`, twig filter, `new Modal()`)
- `method()` — options-array key is **ignored**; call the method instead
- ⚙️ — site settings form / preset config only (never emitted per-modal)
- 🖥️ — JS only (`Drupal.neoModal.open()`, `NeoModalCommand` settings)

### Content & media

| Option | Default | PHP | Notes |
|---|---|---|---|
| `content` | `null` | ✅ ctor / `setContent()` | Render array, string, HTMLElement, or `(trigger) => …` in JS |
| `image` | `null` | ✅ | Image URL. Content becomes an `<img>`; enables share/download/copy + zoom when `fit` |
| `video` | `null` | ✅ | YouTube/Vimeo URL (embedded via `youtube-nocookie`/`player.vimeo`) or mp4/ogg/webm |
| `iframe` | `null` | ✅ | Arbitrary URL in an `<iframe>` |
| `videoAutoplay` | `true` | 🖥️ | |
| `videoRatio` | `'16x9'` | 🖥️ | Styled ratios: `16x9`, `4x3`, `1x1`, `21x9` |
| `contentPadding` | `''` | ✅ | `--modal-content-padding`. Bare numbers get `px` (`'0'` → `0px`) |
| `contentScroll` | `false` | ✅ | Scroll **inside** the content instead of the whole modal |
| `fit` | `false` | ✅ | Shrink content to fit the viewport (media). Enables click-to-zoom on images |
| `smartActions` | `false` | ✅ | Clone the form's action buttons into the modal footer. Add `btn-ignore` to a button to skip it |
| `buttons` | `null` | 🖥️ | `{label: callback}`. **Disables `smartActions`** |

Precedence when several are set: `image` → `video` → `iframe` → `content`.

### Header, title, close button

| Option | Default | PHP | Notes |
|---|---|---|---|
| `header` | `true` | ✅ | Master switch for the header row |
| `headerInContent` | `false` | ✅ | Render the header inside the content block instead of floating above it |
| `headerAnimate` | `true` | ⚙️ | Allow title/subtitle/icon/close to animate individually |
| `title` | `''` | ✅ `setTitle()` / `setTitleWithDynamicIcon()` | The latter also picks + attaches a matching icon |
| `subtitle` | `''` | ✅ | |
| `icon` | `''` | ✅ `setIcon($name)` / `setRawIcon($selector)` | `setIcon()` resolves through neo_icon but does **not** attach the icon library; `setTitleWithDynamicIcon()` does |
| `iconClasses` | `'neo-icon neo-icon-font'` (set by Drupal) | 🖥️ | |
| `titleCallback` | `null` | ⚙️/🖥️ | Name of a `window.*` function `(modal, span) => void` |
| `closeButton` | `'end'` | ✅ | `''` (none), `start`, `end`, `start-out`, `end-out` (outside the modal box) |
| `closeButtonSvg` / `closeButtonClasses` | X icon / `''` | 🖥️ | |
| `numeration` | `false` | ✅ | "3 / 8" counter — **requires `group`** with >1 trigger |
| `numerationPlacement` | `'start'` | 🖥️ | `start` or `end` |

### Footer

The **modal footer** (`.neo-modal--footer`) only renders when there is a media
URL to act on — i.e. for `image`/`video`/`iframe` modals or content whose first
child is an `<img>`/`<picture>`. The **content footer**
(`.neo-modal--content-footer`) is separate and holds `buttons`/`smartActions`.

| Option | Default | PHP | Notes |
|---|---|---|---|
| `footer` | `true` | ✅ | |
| `downloadLink` | `true` | ✅ | |
| `shareLink` | `true` | ✅ | Requires `navigator.canShare` |
| `copyLink` | `true` | ✅ | Copies the media URL to the clipboard |

### Size, placement, positioning

| Option | Default | PHP | Notes |
|---|---|---|---|
| `width` | `'auto'` | ✅ | `auto`, `full`, `460px`, `100%`, `80vw`, or a bare number (→ px) |
| `height` | `'auto'` | ✅ | Same forms as width |
| `placement` | `'center'` | ✅ | `center`, `top`, `bottom`, `left`, `right`, each also `-start` / `-end`. Invalid values are silently dropped |
| `attach` | `null` | ✅ | CSS selector — turns the modal into a Popper-anchored popover |
| `attachPlacement` | `'auto'` | ✅ | `auto`, `auto-start`, `auto-end`, `top…left-end`. **Only emitted when `attach` is also set** |
| `appendTo` | `null` | ✅ | Selector for the `.neo-modals` wrapper's parent (default `<body>`) |
| `appendToClosest` | `null` | ✅ | Nearest ancestor of the trigger. **Wins over `appendTo`** |
| `zIndex` | `null` | ✅ | Sets `--modal-z-index` |
| `displaceTop` / `Right` / `Bottom` / `Left` | `''` | ✅ | Insets for the whole modal area, e.g. to clear a sticky header (`'4rem'`) |
| `breakpoint` | — | ✅ (PHP-only) | `md` or `lg`: content renders inline above the breakpoint and as a modal below (trigger gets `md:hidden`, content `md:!block`) |

### Behavior

| Option | Default | PHP | Notes |
|---|---|---|---|
| `nest` | `true` | ✅ | Stack on top of an open modal. `false` closes the current top first |
| `drag` | `true` | `setDraggable()` | Drag left/right to move through a `group` |
| `inputFocus` | `true` | ✅ | Focus the first form input on open |
| `bodyLock` | `true` | ✅ | Lock body scroll |
| `backdrop` | `true` | ✅ | |
| `backdropClose` | `true` | 🖥️ | Click outside to close |
| `closeOnEscape` | `true` | 🖥️ | |
| `navKeyboard` | `true` | 🖥️ | Arrow keys navigate a `group` |
| `navPrevLabel` / `navNextLabel` | `'Prev'` / `'Next'` | 🖥️ | |
| `group` | `null` | ✅ | Triggers sharing a group string become a navigable gallery |
| `loader` | `true` | ⚙️ | Spinner while content loads |
| `modalClasses` | `null` | ✅ `setModalClasses()` / `addModalClass()` | Space-separated classes on `.neo-modal` |
| `wrapperClasses` | `null` | 🖥️ | Classes on the shared `.neo-modals` wrapper |
| `backdropClasses` | `null` | 🖥️ | |
| `bodySelector` | `'.page-wrapper'` | 🖥️ | Element scaled/blurred by the body transitions |
| `bodyTransitionScale` | `false` | ✅ | Scale the page behind the modal |
| `bodyTransitionBlur` | `false` | ✅ | Blur the page behind the modal |
| `scope` | `false` | ✅ (PHP-only) | Merge the site's front/back scope preset (see [Presets](#presets)) and add `.neo-modal--scoped` |

### Colors

All accept any CSS color; each maps to a `--modal-*` custom property on
`.neo-modal`. All are ✅ except where noted.

`contentColor`, `contentColorBg`, `contentFooterColor`, `contentFooterColorBg`,
`headerColor`, `headerColorBg`, `footerColor`, `footerColorBg`, `navColor`,
`navColorBg`, `loaderColor`, `loaderColorBg` — plus:

| Option | Default | PHP | Notes |
|---|---|---|---|
| `backdropColorBg` | `''` | `setBackdropColor()` | The array key is ignored — the setter is named differently |
| `colorScheme` | `'scheme--reset'` | ✅ `setColorScheme($scheme, $inherit = TRUE)` | `'primary-dark'` → `scheme-primary-dark`; `_` → `-`; the `scheme-` prefix is added if missing |
| `colorSchemeInherit` | `false` | via `setColorScheme()` 2nd arg | When false the content gets `scheme--reset`; passing a `colorScheme` sets this to true |

### Animations

Every animated part takes `<part>AnimateIn`, `<part>AnimateInSpeed`,
`<part>AnimateInDelay`, `<part>AnimateOut`, `<part>AnimateOutSpeed`,
`<part>AnimateOutDelay` (some parts have no delay). Values come from
`Modal::getAnimationsIn()` / `getAnimationsOut()` (the neo animate catalog:
`fadeIn*`, `slideIn*`, `zoomIn*`, `bounceIn*`, `flipIn*`, `rotateIn*`,
`backIn*`, `lightSpeedIn*`, `rollIn`, `comingIn`, and the matching `*Out`),
speeds/delays from `slow|slower|slowest|fast|faster|fastest` (+ `default`).
**Invalid values are silently discarded.**

| Part | Defaults (in / out) | PHP |
|---|---|---|
| `content` | `comingIn` / `comingOut` @ `fastest` | ✅ (all six) |
| `header` | `slideInDown` / `slideOutUp` @ `fastest` | ✅ (all six) |
| `title` | `fadeInDown` (delay `fastest`) / none | ⚙️ |
| `subtitle` | `fadeIn` (delay `fast`) / none | ⚙️ |
| `icon` | `zoomIn` (delay `fastest`) / none | ⚙️ |
| `closeButton` | `fadeIn` (delay `fast`) / none | ⚙️ |
| `footer` | `slideInUp` / `slideOutDown` @ `fastest` | ⚙️ |
| `navPrev` / `navNext` | `slideInLeft` / `slideInRight` … | ⚙️ |
| `prev` / `next` (modal-to-modal in a group) | `slideInLeft` / `slideOutLeft` … | ⚙️ |
| `backdrop` | `fadeIn` / `fadeOut` @ `fastest` | ⚙️ |
| `loader` | `fadeIn` / `fadeOut` @ `fastest` | ⚙️ |

### Callbacks (🖥️ only)

`onSettings`, `onBeforeOpen`, `onOpen`, `onAfterOpen`, `onBeforeClose`,
`onClose`, `onAfterClose`, `onBeforeNext`, `onNext`, `onAfterNext`,
`onBeforePrev`, `onPrev`, `onAfterPrev`, `onContentLoaded` — all
`(modal, data?) => void`.

Also `svgOpen`, `svgClose`, `svgIconDownload`, `svgIconShare`, `svgIconLink` for
the footer icon markup.

## Which options work where

An options-array key only reaches the browser if **all three** hold:

1. `Modal` has a matching `set*()` method (`Str::camel($key)`),
2. the property is in `Modal::getValues()`'s string/bool allowlist
   ([Modal.php:2061](web/modules/contrib/neo_modal/src/Modal.php#L2061)), and
3. the name is in the JS `optionsAsAttributes` list
   ([modal.ts:219](web/modules/contrib/neo_modal/src/js/modal/modal.ts#L219)).

Anything else is **silently dropped** — no warning. This is the single most
common source of "my option does nothing":

```php
// ❌ silently ignored
new Modal(NULL, ['drag' => FALSE, 'backdropColorBg' => '#000', 'closeOnEscape' => FALSE]);
// ✅
(new Modal())->setDraggable(FALSE)->setBackdropColor('#000');   // closeOnEscape: JS/site-settings only
```

⚙️ options (site-settings-only) reach JS through
`drupalSettings.neoModal.defaults`, which is built from the **base** config diff
only — putting them in a preset or a `#modal` array has no effect.

Verify what a given option set actually emits:

```bash
ddev drush php:eval '$m = new \Drupal\neo_modal\Modal(NULL, ["title" => "T", "drag" => FALSE], "shelf_right"); print json_encode($m->getValues());'
```

## Presets

Presets are `neo_settings` variations of the `neo_modal` plugin. Shipped:

| ID | Effect |
|---|---|
| `dialog` | `smartActions`, `headerInContent`, `contentScroll` — the default for AJAX/core dialogs |
| `media` | `numeration`, `fit`, `contentPadding: 0` — lightbox |
| `shelf_left` / `shelf_right` | 460px, full height, edge placement, slide in/out |
| `shelf_top` | 1200px wide, top placement, fade down |

Reference them with or without the `neo_modal_` prefix (`'shelf_right'` ===
`'neo_modal_shelf_right'`). **An unknown preset name silently falls back to the
active settings** — no error, so typos look like "the preset does nothing".

Manage them at `/admin/config/neo/modal`. New presets are config entities
(`neo_settings.variation.neo_modal_*`); the shipped shelves are unlocked and
editable, `dialog`/`media` are `lock: true`.

**Scope** — a variation with "Allow Scope" checked will additionally merge the
preset chosen as the site's Frontend/Backend scope (base settings form → Scopes)
depending on whether the current route is an admin route. `scope: TRUE` in a
per-modal options array turns this on for that modal (and is the default for the
twig filter/function).

## Closing a modal

| Mechanism | Use |
|---|---|
| `data-neo-modal-close` attribute (any element) | Declarative close button/link inside the content |
| `#type => 'neo_modal_close'` | Render element that builds one (`#title`, `#check_ajax` to only show inside an AJAX/dialog request) |
| `#modal_close => TRUE` on a `submit` element | Adds `data-neo-modal-close-submit`; closes on mouseup so the submit still fires |
| `#close` on a `neo_modal` element | Adds a "Cancel"-style close button |
| `NeoModalCloseCommand` | From an AJAX response |
| `Drupal.neoModal.close()` / `NeoModal.closeTop()` | JS — closes the **top** modal |
| Escape key / backdrop click | `closeOnEscape`, `backdropClose` |

When `nest` is on, close handlers call `NeoModal.closeTop()` — the topmost
modal closes, not necessarily the one owning the button.

## Groups & galleries

Give several triggers the same `group` string; they become one navigable set
(prev/next arrows, arrow keys when `navKeyboard`, drag when `drag`,
counter when `numeration`). Navigation reuses the same `NeoModal` instance and
rebuilds it from the next trigger's options — only `title`, `subtitle`, `icon`,
`image`, `video`, `iframe`, `content`, `trigger` change between slides
(`optionsAsMerge`); everything else stays from the first opened modal.

For fields, the **"Neo | Modal Gallery"** formatters generate the whole thing
(thumbnail + full image settings, modal preset, title source, group name) —
`neo_modal_image_gallery` for `image` fields, `neo_modal_media_gallery` for
`entity_reference` (media) fields.

## Core dialog / AJAX integration

- `hook_library_info_alter()` blanks `core/drupal.dialog` and
  `core/drupal.dialog.ajax` and re-points them at `neo_modal/modal` and
  `neo_modal/modal-dialog-ajax`. jQuery UI dialog is not loaded.
- `Drupal.AjaxCommands.openDialog` / `closeDialog` / `openModalDialogWithUrl`
  are reimplemented; `setDialogOption` is a no-op that logs.
- A jQuery `.dialog()` shim exists for legacy callers
  ([modal.ts:147](web/modules/contrib/neo_modal/src/js/modal.ts#L147)).
- `dialog:beforecreate/aftercreate/beforeclose/afterclose` are dispatched as
  **native events only**, like core. A jQuery special-event bridge
  ([modal.ts:30](web/modules/contrib/neo_modal/src/js/modal.ts#L30), mirroring
  core's `dialog-deprecation.js`, which the library replacement removes) feeds
  legacy `$(window).on('dialog:aftercreate', (e, dialog, $element, settings))`
  listeners (e.g. webform's) their arguments. Never additionally
  `jQuery.trigger()` these events — handlers would fire twice.
- `hook_ajax_render_alter()` translates core dialog options and applies
  per-integration **sizing** fixes (Media Library, Webform off-canvas, Views UI
  get `width/height: 100%`).
- **Replace-vs-stack is decided client-side from the command's selector**
  ([modal-dialog-ajax.ts](web/modules/contrib/neo_modal/src/js/modal-dialog-ajax.ts)),
  mirroring core's same-selector-replaces contract: opening a dialog whose
  `response.selector` (e.g. `#drupal-modal`) already owns an open Neo modal
  closes that modal first; other modals are left alone. An incoming
  `#drupal-off-canvas` dialog additionally closes all open selector-tracked
  dialogs (webform relies on this; its own close handler targets `.ui-dialog`
  markup that doesn't exist here). `closeDialog` also closes by selector. An
  explicit `nest` option overrides: `true` always stacks (Media Library keeps
  this so it survives a selector collision), `false` closes the top modal.
- Views' `setBrowserUrl` command is suppressed inside `.neo-modal` to stop
  modal query parameters leaking into `window.location`.
- Local actions: add a `modal` key to a local action definition and
  `hook_menu_local_actions_alter()` swaps in `ModalLocalAction` (default
  `width: 700px`). Neo options go under a `neo` sub-key:
  `modal: {width: '900px', neo: {placement: 'top'}}`.

## Blocks

Extend
[`NeoModalBlockBase`](web/modules/contrib/neo_modal/src/Plugin/Block/NeoModalBlockBase.php)
(or use `NeoModalBlockTrait` on an existing block) to get a "Modal Preset" +
full modal settings subform, a trigger (text/icon/icon-only/position/URL), and
an optional **Load modal content via AJAX** mode that renders the block body at
`/api/modal/block/{block}/{arg1}/{arg2}` instead of inlining it. Implement
`buildModalContent()`; override `neoModalForceConfiguration()` to hard-lock
options. Shipped blocks: `neo_modal_slide_menu`, `neo_modal_account`.

## JS API

```ts
NeoModal.setDefaultOptions(options)   // page-wide defaults
NeoModal.getTop() / NeoModal.closeTop()

modal.open() / close() / size()       // size() re-measures after content changes
modal.refreshContent()                // rebuild the content footer (smart actions)
modal.getModal() / getContent()       // HTMLElement | null
modal.getOption('width')
modal.event('onAfterOpen').on(cb)     // signal-based subscription
```

Drupal registers default `onContentLoaded` / `onAfterClose` handlers that call
`Drupal.attachBehaviors()` / `detachBehaviors()` on the modal content
([modal.ts:50](web/modules/contrib/neo_modal/src/js/modal.ts#L50)).

## Styling

- Tailwind variant **`modal:`** (`.neo-modal &`) — style anything only when it
  is inside a modal: `class="p-4 modal:p-8"`.
- Spacing tokens `modal-t/r/b/l` and `modal-content-t/r/b/l` map to the live
  `--modal-*` measurements (e.g. `top-modal-t`).
- CSS custom properties on `.neo-modal`: `--modal-content-color`,
  `--modal-content-bg`, `--modal-content-footer-bg`, `--modal-header-color/bg`,
  `--modal-footer-color/bg`, `--modal-backdrop-bg`, `--modal-nav-color/bg`,
  `--modal-loader-color/bg`, `--modal-content-padding`, `--modal-padding-t`,
  `--modal-z-index`, `--modal-displace-*`, `--modal-max-width`.
- Useful classes to write by hand: `neo-modal--btn` (a button smart actions will
  hoist), `btn-ignore` (exclude a button from smart actions),
  `neo-modal--hide`, `neo-modal--tooltip` (tippy-ized on build),
  `neo-modal--template` (marks content the behavior should adopt).
- Body/state classes: `has-neo-modal`, `neo-modal--body-lock`,
  `neo-modal--body-transition`, `.neo-modal--closing`, `[data-neo-modal--depth]`.

Editing this module's `src/js/**` or `src/css/modal.css` requires a Neo build
(`drush neo:build` / `npm start`) — see the **neo-build** skill.

## Gotchas

- **Silent option drops.** Options with no matching setter (`drag`,
  `backdropColorBg`, `closeOnEscape`, `videoRatio`, `iconClasses`,
  `titleCallback`, `numerationPlacement`, `nav*Label`, `wrapperClasses`,
  `backdropClasses`, `loader`, `headerAnimate`, and every title/subtitle/icon/
  closeButton/footer/nav/backdrop/loader animation key) are discarded from an
  options array. Invalid enum values (placement, closeButton, animations) are
  discarded too.
- **`header` and `headerInContent` are mutually exclusive on the wire.**
  `getValues()` uses an `elseif`: if `header` differs from the site default,
  `headerInContent` is not emitted at all.
- **`appendToClosest` suppresses `appendTo`.**
- **`attachPlacement` needs `attach`** or it is not emitted.
- **`numeration` needs `group`** with more than one trigger, or nothing renders.
- **The footer needs a media URL.** `footer: true` alone renders nothing for
  ordinary content — share/download/copy only appear for image/video/iframe
  modals. Form buttons live in the *content* footer instead.
- **Passing a callback in JS replaces the Drupal default.** Supplying your own
  `onContentLoaded` removes `Drupal.attachBehaviors()` from the content; call it
  yourself or subscribe with `modal.event('onContentLoaded').on(cb)` instead.
- **Unknown preset IDs fall back to the active settings** with no error.
- The backdrop animation keys were named `overlayAnimate*` in config/schema
  until `neo_modal_update_11001()` renamed them to `backdropAnimate*` (what the
  form and JS have always used). Sites that skipped the update keep dead
  `overlayAnimate*` keys.
- The `.neo-modals` wrapper and `.neo-modal--backdrop` are **shared** by the
  whole stack — `wrapperClasses`, `backdropClasses`, `zIndex`, `displace*` and
  `backdropColorBg` come from whichever modal built the stack first.

## Verify

```bash
# what a given option set + preset actually emits
ddev drush php:eval '$m = new \Drupal\neo_modal\Modal(NULL, ["title" => "T"], "shelf_right"); print json_encode($m->getValues());'

# the page-wide JS defaults (site settings diff)
ddev drush php:eval 'print json_encode(\Drupal::service("neo_modal.settings")->getActive()->getDiffConfigValues());'

# available presets (last entry is the base settings, not a preset)
ddev drush php:eval 'foreach (\Drupal::service("neo_modal.settings")->getAll() as $id => $s) { print $id . "\n"; }'
```

In the browser: the trigger must carry `class="use-neo-modal"` plus your
`data-neo-modal-*` attributes; `drupalSettings.neoModal.defaults` holds the
site-wide layer; `document.querySelector('.neo-modal').neoModal` is the live
instance.

## Tests

```bash
ddev phpunit --testsuite neo_modal     # PHP: option round trip, gallery titles, animation catalog
ddev nightwatch neo_modal              # browser: open/close/reopen against the running site
```

The PHP suite is where the wire format is pinned — that an option handed in as
`['drag' => FALSE]` actually comes back out of `getValues()`, that every offered
animation names a class the neo catalog defines, and that a form modal's
`smartActions` default stays overridable. Add to it when you add an option; a
setter named more prettily than its option key is silently dropped by the
constructor's name-based dispatch, and `testEmittableBooleansRoundTrip…` is what
catches that.

The Nightwatch suite is install-free: it drives the running site rather than
building a throwaway one, so it exercises the presets actually configured here.
Two things it needs that are easy to get wrong — wait for
`neoWaitForAnimations()` before asserting (a modal is visible well before it has
finished opening, and it binds its keyboard handlers in the open animation's
callback), and use `neoPressKey()` rather than `browser.keys()`, which silently
sends nothing under W3C. Both helpers come from neo_build. `modalInstallTest` is
tagged `neo_install` only and does **not** pass yet — see its docblock.

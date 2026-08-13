import NeoModal from "./modal/modal";

(function (Drupal) {

  // Open dialogs keyed by the selector core would render them into
  // (`response.selector`, e.g. `#drupal-modal` for every data-dialog-type
  // link). Core reuses the DOM element with that id, so opening a dialog whose
  // selector is already on screen replaces it, while dialogs with different
  // selectors (modal vs off-canvas) coexist. openDialog mirrors that contract
  // through this registry instead of stacking unconditionally.
  const dialogModals: {[selector: string]: NeoModal} = {};

  if (Drupal.AjaxCommands) {

    /**
     * Command to open a dialog.
     *
     * @param {Drupal.Ajax} ajax
     *   The Drupal Ajax object.
     * @param {object} response
     *   Object holding the server response.
     * @param {number} [status]
     *   The HTTP status code.
     *
     * @return {boolean|undefined}
     *   Returns false if there was no selector property in the response object.
     */
    Drupal.AjaxCommands.prototype.openDialog = function (_ajax, response, _status) {
      if (Drupal.neoModal) {
        let options:any = {};
        if (typeof response.settings === 'object') {
          options = Object.assign({}, options, response.settings);
        }
        if (response.data) {
          options['content'] = response.data;
        }
        options['onBeforeOpen'] = (modal:NeoModal) => {
          const content = modal.getContent();
          if (content) {
            content.querySelectorAll('.dialog-cancel').forEach((el) => {
              el.addEventListener('click', e => {
                modal.close();
                e.preventDefault();
                e.stopPropagation();
              });
            });
          }
        };
        // `nest` may arrive as a boolean or a data-attribute-style string.
        const nest = options.nest === true || options.nest === 'true' ? true
          : options.nest === false || options.nest === 'false' ? false
          : undefined;
        if (nest !== undefined) {
          options.nest = nest;
        }
        const selector = typeof response.selector === 'string' ? response.selector : '';
        // Same selector already open: replace it, exactly like core reusing
        // the `#drupal-modal` element. Other modals (custom overlays, other
        // selectors) are left alone. An explicit `nest` option still wins:
        // true always stacks, false closes the top modal (NeoModal.open()
        // handles that itself).
        if (nest === undefined && selector) {
          if (dialogModals[selector]) {
            dialogModals[selector].close();
            delete dialogModals[selector];
          }
          // An off-canvas panel replaces any open dialogs: no core flow layers
          // a dialog-modal under an off-canvas (jQuery UI's modal overlay
          // would block the tray), and webform depends on this — its own
          // "close modals on off-canvas open" handler is a no-op here because
          // it targets `.ui-dialog` markup that Neo modals don't render.
          // Dialogs opening OVER an off-canvas (e.g. media library from
          // layout builder) still stack.
          if (selector.indexOf('#drupal-off-canvas') === 0) {
            Object.keys(dialogModals).forEach((key) => {
              dialogModals[key].close();
              delete dialogModals[key];
            });
          }
        }
        const modal = Drupal.neoModal.open(options);
        if (modal && selector) {
          dialogModals[selector] = modal;
          modal.event('onAfterClose').on(() => {
            if (dialogModals[selector] === modal) {
              delete dialogModals[selector];
            }
          });
        }
      }
    } as drupal.Core.IAjaxCommand;

    /**
     * Command to close a dialog.
     *
     * If no selector is given, it defaults to trying to close the modal.
     *
     * @param {Drupal.Ajax} [ajax]
     *   The ajax object.
     * @param {object} response
     *   Object holding the server response.
     * @param {string} response.selector
     *   The selector of the dialog.
     * @param {boolean} response.persist
     *   Whether to persist the dialog element or not.
     * @param {number} [status]
     *   The HTTP status code.
     */
    Drupal.AjaxCommands.prototype.closeDialog = function (_ajax, response, _status) {
      const selector = response && typeof response.selector === 'string' ? response.selector : '';
      if (selector && dialogModals[selector]) {
        dialogModals[selector].close();
        delete dialogModals[selector];
      }
      else if (Drupal.neoModal) {
        Drupal.neoModal.close();
      }
    } as drupal.Core.IAjaxCommand;

    /**
     * Command to set a dialog property.
     *
     * JQuery UI specific way of setting dialog options.
     *
     * @param {Drupal.Ajax} [ajax]
     *   The Drupal Ajax object.
     * @param {object} response
     *   Object holding the server response.
     * @param {string} response.selector
     *   Selector for the dialog element.
     * @param {string} response.optionsName
     *   Name of a key to set.
     * @param {string} response.optionValue
     *   Value to set.
     * @param {number} [status]
     *   The HTTP status code.
     */
    Drupal.AjaxCommands.prototype.setDialogOption = function (_ajax, _response, _status) {
      console.log('Not yet supported in Neo Modal.', 'setDialogOption');
    } as drupal.Core.IAjaxCommand;

    /**
     * Ajax command to open URL in a modal dialog.
     *
     * @param {Drupal.Ajax} [ajax]
     *   An Ajax object.
     * @param {object} response
     *   The Ajax response.
     */
    Drupal.AjaxCommands.prototype.openModalDialogWithUrl = function (_ajax, response, _status) {
      const dialogOptions = response.dialogOptions || {};
      const elementSettings = {
        progress: { type: 'throbber' },
        dialogType: 'modal',
        dialog: dialogOptions,
        url: response.url,
        httpMethod: 'GET',
      };
      Drupal.ajax(elementSettings).execute();
    } as drupal.Core.IAjaxCommand;
  }

  // Prevent views AJAX (exposed filters, pagers) from rewriting the browser URL
  // when the triggering element lives inside a Neo modal.
  //
  // Core's views `setBrowserUrl` command (core/modules/views/js/ajax_view.js)
  // calls window.history.replaceState() with the current request's query string
  // — which, inside a modal, includes that modal's opener parameters and hash.
  // Core suppresses this only when the element is inside a `.ui-dialog-content`
  // wrapper (its jQuery UI dialog). Neo modals replace that dialog and have no
  // such wrapper, so without this the first exposed-filter request inside a
  // modal pollutes window.location. The next request then re-sends and
  // double-encodes those parameters, breaking e.g. the Media Library hash check
  // ("Invalid media library parameters specified.").
  //
  // We can't simply add the `.ui-dialog-content` class to Neo modals: core's
  // dialog.ajax.js also keys off that class and would call jQuery UI `.dialog()`
  // methods on a non-dialog element, throwing on every AJAX attach inside a
  // modal. So we wrap the command instead. This runs in a behavior (rather than
  // at file-eval time) because ajax_view.js assigns setBrowserUrl when its
  // library loads — load order relative to this file is not guaranteed, but
  // behaviors always run afterward.
  (Drupal as any).behaviors.neoModalSuppressBrowserUrl = {
    attach: function () {
      const commands:any = Drupal.AjaxCommands && Drupal.AjaxCommands.prototype;
      if (!commands || typeof commands.setBrowserUrl !== 'function' || commands.setBrowserUrl.neoModalPatched) {
        return;
      }
      const original = commands.setBrowserUrl;
      const patched:any = function (this:any, ajax:any, response:any, status:any) {
        if (ajax && ajax.element && ajax.element.closest && ajax.element.closest('.neo-modal')) {
          return;
        }
        return original.call(this, ajax, response, status);
      };
      patched.neoModalPatched = true;
      commands.setBrowserUrl = patched;
    },
  };

})(Drupal);

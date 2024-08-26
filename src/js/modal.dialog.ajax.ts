import { NeoModal } from "./modal/modal";

(function (Drupal) {

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
        // Close any existing. May need to be reworked. Doing this just for
        // views_ui right now.
        Drupal.neoModal.close();
        Drupal.neoModal.open(options);
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
    Drupal.AjaxCommands.prototype.closeDialog = function (_ajax, _response, _status) {
      if (Drupal.neoModal) {
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
    Drupal.AjaxCommands.prototype.openModalDialogWithUrl = function (_ajax, _response, _status) {
      console.log('Not yet supported in Neo Modal.', 'openModalDialogWithUrl');
    } as drupal.Core.IAjaxCommand;
  }

})(Drupal);

export {};

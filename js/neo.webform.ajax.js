/**
 * @file
 * JavaScript behaviors for Ajax.
 */

(function ($, Drupal, drupalSettings, once, tabbable) {

  /**
   * Command to close a off-canvas and modal dialog.
   *
   * If no selector is given, it defaults to trying to close the modal.
   *
   * @param {Drupal.Ajax} [ajax]
   *   {@link Drupal.Ajax} object created by {@link Drupal.ajax}.
   * @param {object} response
   *   The response from the Ajax request.
   * @param {string} response.selector
   *   Selector to use.
   * @param {bool} response.persist
   *   Whether to persist the dialog element or not.
   * @param {number} [status]
   *   The HTTP status code.
   */
  Drupal.AjaxCommands.prototype.webformCloseDialog = function (ajax, response, status) {
    if (Drupal.neoModal) {
      // Remove all *.off-canvas events
      $(document).off('.off-canvas');
      $(window).off('.off-canvas');
      Drupal.neoModal.close();
    }
  };

})(jQuery, Drupal, drupalSettings, once, tabbable);

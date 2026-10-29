(function (Drupal) {

  if (Drupal.AjaxCommands) {
    // Both commands may arrive before the modal code has loaded (it loads on
    // demand; see modal.ts). Core runs a response's commands in order and waits
    // on a returned promise, so returning the open keeps any later command
    // from overtaking it.
    Drupal.AjaxCommands.prototype.neoModal = function (_ajax, response, _status) {
      const api = Drupal.neoModal;
      if (api) {
        let options:any = {};
        if (typeof response.settings === 'object') {
          options = Object.assign({}, options, response.settings);
        }
        if (response.data) {
          options['content'] = response.data;
        }
        return api.ready().then(() => {
          api.open(options);
        });
      }
      return undefined;
    } as drupal.Core.IAjaxCommand;

    Drupal.AjaxCommands.prototype.neoModalClose = function (_ajax, _response, _status) {
      // Not worth loading the modal code for: if it is not here, no modal is
      // open. close() itself waits on a load already under way.
      if (Drupal.neoModal) {
        Drupal.neoModal.close();
      }
    } as drupal.Core.IAjaxCommand;
  }

})(Drupal);

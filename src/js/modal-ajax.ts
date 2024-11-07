(function (Drupal, _NeoModal) {

  if (Drupal.AjaxCommands) {
    Drupal.AjaxCommands.prototype.neoModal = function (_ajax, response, _status) {
      if (Drupal.neoModal) {
        let options:any = {};
        if (typeof response.settings === 'object') {
          options = Object.assign({}, options, response.settings);
        }
        if (response.data) {
          options['content'] = response.data;
        }
        Drupal.neoModal.open(options);
      }
    } as drupal.Core.IAjaxCommand;

    Drupal.AjaxCommands.prototype.neoModalClose = function (_ajax, _response, _status) {
      if (Drupal.neoModal) {
        Drupal.neoModal.close();
      }
    } as drupal.Core.IAjaxCommand;
  }

})(Drupal, NeoModal);

export {};

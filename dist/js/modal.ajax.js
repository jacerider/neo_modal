(function(o, d) {
  o.AjaxCommands && (o.AjaxCommands.prototype.neoModal = function(n, t, e) {
    if (o.neoModal) {
      let a = {};
      typeof t.settings == "object" && (a = Object.assign({}, a, t.settings)), t.data && (a.content = t.data), o.neoModal.open(a);
    }
  }, o.AjaxCommands.prototype.neoModalClose = function(n, t, e) {
    o.neoModal && o.neoModal.close();
  });
})(Drupal, NeoModal);
//# sourceMappingURL=modal.ajax.js.map

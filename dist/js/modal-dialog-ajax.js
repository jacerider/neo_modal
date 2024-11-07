(function(o) {
  o.AjaxCommands && (o.AjaxCommands.prototype.openDialog = function(n, t, a) {
    if (o.neoModal) {
      let e = {};
      typeof t.settings == "object" && (e = Object.assign({}, e, t.settings)), t.data && (e.content = t.data), e.onBeforeOpen = (s) => {
        const i = s.getContent();
        i && i.querySelectorAll(".dialog-cancel").forEach((c) => {
          c.addEventListener("click", (l) => {
            s.close(), l.preventDefault(), l.stopPropagation();
          });
        });
      }, (typeof e.nest > "u" || e.nest === !1) && o.neoModal.close(), o.neoModal.open(e);
    }
  }, o.AjaxCommands.prototype.closeDialog = function(n, t, a) {
    o.neoModal && o.neoModal.close();
  }, o.AjaxCommands.prototype.setDialogOption = function(n, t, a) {
    console.log("Not yet supported in Neo Modal.", "setDialogOption");
  }, o.AjaxCommands.prototype.openModalDialogWithUrl = function(n, t, a) {
    console.log("Not yet supported in Neo Modal.", "openModalDialogWithUrl");
  });
})(Drupal);
//# sourceMappingURL=modal-dialog-ajax.js.map

(function(o) {
  o.AjaxCommands && (o.AjaxCommands.prototype.openDialog = function(n, e, a) {
    if (o.neoModal) {
      let t = {};
      typeof e.settings == "object" && (t = Object.assign({}, t, e.settings)), e.data && (t.content = e.data), t.onBeforeOpen = (s) => {
        const i = s.getContent();
        i && i.querySelectorAll(".dialog-cancel").forEach((c) => {
          c.addEventListener("click", (l) => {
            s.close(), l.preventDefault(), l.stopPropagation();
          });
        });
      }, (typeof t.nest > "u" || t.nest === !1 || t.nest === "false") && o.neoModal.close(), o.neoModal.open(t);
    }
  }, o.AjaxCommands.prototype.closeDialog = function(n, e, a) {
    o.neoModal && o.neoModal.close();
  }, o.AjaxCommands.prototype.setDialogOption = function(n, e, a) {
    console.log("Not yet supported in Neo Modal.", "setDialogOption");
  }, o.AjaxCommands.prototype.openModalDialogWithUrl = function(n, e, a) {
    console.log("Not yet supported in Neo Modal.", "openModalDialogWithUrl");
  });
})(Drupal);
//# sourceMappingURL=modal-dialog-ajax.js.map

(function(o) {
  o.AjaxCommands && (o.AjaxCommands.prototype.openDialog = function(a, e, s) {
    if (o.neoModal) {
      let t = {};
      typeof e.settings == "object" && (t = Object.assign({}, t, e.settings)), e.data && (t.content = e.data), t.onBeforeOpen = (n) => {
        const i = n.getContent();
        i && i.querySelectorAll(".dialog-cancel").forEach((d) => {
          d.addEventListener("click", (l) => {
            n.close(), l.preventDefault(), l.stopPropagation();
          });
        });
      }, (typeof t.nest > "u" || t.nest === !1 || t.nest === "false") && o.neoModal.close(), o.neoModal.open(t);
    }
  }, o.AjaxCommands.prototype.closeDialog = function(a, e, s) {
    o.neoModal && o.neoModal.close();
  }, o.AjaxCommands.prototype.setDialogOption = function(a, e, s) {
    console.log("Not yet supported in Neo Modal.", "setDialogOption");
  }, o.AjaxCommands.prototype.openModalDialogWithUrl = function(a, e, s) {
    const t = e.dialogOptions || {}, n = {
      progress: { type: "throbber" },
      dialogType: "modal",
      dialog: t,
      url: e.url,
      httpMethod: "GET"
    };
    o.ajax(n).execute();
  });
})(Drupal);
//# sourceMappingURL=modal-dialog-ajax.js.map

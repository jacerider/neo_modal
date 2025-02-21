(function(o, d) {
  o.behaviors.neoModalLink = {
    attach: (g) => {
      d("ajax", ".use-modal-ajax", g).forEach((t) => {
        const e = {
          // Clicked links look better with the throbber than the progress bar.
          progress: { type: "throbber" },
          dialogType: t.getAttribute("data-dialog-type"),
          dialog: JSON.parse(t.getAttribute("data-dialog-options") || "{}"),
          dialogRenderer: t.getAttribute("data-dialog-renderer"),
          base: t.id,
          element: t
        }, r = t.getAttribute("data-ajax-href") || t.getAttribute("href");
        r && (e.url = r, e.event = "click");
        const a = t.getAttribute("data-ajax-http-method");
        a && (e.httpMethod = a), o.ajax(e);
      });
    }
  };
})(Drupal, once);
//# sourceMappingURL=modal-link.js.map

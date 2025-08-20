(function (Drupal, once) {

  Drupal.behaviors.neoModalLink = {
    attach: (context:HTMLElement, ) => {
      // Bind Ajax behaviors to all items showing the class.
      once('ajax', '.use-modal-ajax', context).forEach((ajaxLink) => {
        const elementSettings: any = {
          // Clicked links look better with the throbber than the progress bar.
          progress: { type: 'throbber' },
          dialogType: ajaxLink.getAttribute('data-dialog-type'),
          dialog: JSON.parse(ajaxLink.getAttribute('data-dialog-options') || '{}'),
          dialogRenderer: ajaxLink.getAttribute('data-dialog-renderer'),
          base: ajaxLink.id,
          element: ajaxLink,
        };
        const href = ajaxLink.getAttribute('data-ajax-href') || ajaxLink.getAttribute('href');
        /**
         * For anchor tags, these will go to the target of the anchor rather than
         * the usual location.
         */
        if (href) {
          elementSettings.url = href;
          elementSettings.event = 'click';
        }
        const httpMethod = ajaxLink.getAttribute('data-ajax-http-method');
        /**
         * In case of setting a custom AJAX HTTP method for the link, we rewrite ajax.httpMethod.
         */
        if (httpMethod) {
          elementSettings.httpMethod = httpMethod;
        }
        Drupal.ajax(elementSettings);
      });
    }
  };

})(Drupal, once);

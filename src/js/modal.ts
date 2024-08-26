import { NeoModal } from './modal/modal';

(function (Drupal, drupalSettings, once) {

  const defaultOptions = {
    iconClasses: 'neo-icon neo-icon-font',
  };
  if (typeof drupalSettings.neoModal !== 'undefined' && typeof drupalSettings.neoModal.defaults !== 'undefined') {
    Object.assign(defaultOptions, drupalSettings.neoModal.defaults);
  }
  NeoModal.setDefaultOptions(defaultOptions);

  Drupal.behaviors.neoModal = {
    attach: (context:HTMLElement) => {
      once('neo.modal', '.use-neo-modal', context).forEach(el => {
        const options:any = {};
        options['trigger'] = el;
        options['content'] = (ref:HTMLElement) => {
          let template = ref.nextElementSibling;
          if (template && template.tagName === 'TEMPLATE') {
            return template.innerHTML;
          }
          if (template && template.classList.contains('neo-modal-template')) {
            return template;
          }
          template = ref.querySelector('.neo-modal-template');
          if (template && template.tagName === 'TEMPLATE') {
            return template.innerHTML;
          }
          return '';
        };
        // if (typeof Drupal.behaviors.neoLoader !== 'undefined') {
        //   options['loader'] = (movement:neoModal.Movement) => {
        //     if (movement === 'in') {
        //       Drupal.behaviors.neoLoader.show();
        //     }
        //     else if (movement === 'out') {
        //       Drupal.behaviors.neoLoader.hide();
        //     }
        //   }
        // }
        // Events as options.
        // options['onBeforeOpen'] = () => {
        //   Drupal.behaviors.neoLoader.show();
        // };
        // options['onOpen'] = () => {
        //   Drupal.behaviors.neoLoader.hide();
        // };
        // options['onContentLoaded'] = (content:HTMLElement) => {
        //   Drupal.attachBehaviors(content, drupalSettings);
        // };
        // options['onAfterOpen'] = () => {
        //   console.log('option: after open');
        // };
        // options['onBeforeClose'] = () => {
        //   console.log('option: before close');
        // };
        // options['onClose'] = () => {
        //   console.log('option: close');
        // };
        // options['onAfterClose'] = () => {
        //   console.log('option: after close');
        // };
        // Events as listeners.

        new NeoModal(options);

        // console.log(drupalSettings);
        // modal.beforeOpenEvent().on(() => {
        //   console.log('event: before open');
        // });
        // modal.openEvent().on(() => {
        //   console.log('event: open');
        // });
        // modal.afterOpenEvent().on(() => {
        //   console.log('event: after open');
        // });
        // modal.beforeCloseEvent().on(() => {
        //   console.log('event: before close');
        // });
        // modal.closeEvent().on(() => {
        //   console.log('event: close');
        // });
        // modal.afterCloseEvent().on(() => {
        //   console.log('event: after close');
        // });
      });
    }
  };

  Drupal.neoModal = {
    open: (options:any) => {
      const modal = new NeoModal(options);
      modal.event('onContentLoaded').on(() => {
        const content = modal.getContent();
        if (content) {
          Drupal.attachBehaviors(content, drupalSettings);
        }
      });
      modal.event('onAfterClose').on(() => {
        const content = modal.getContent();
        console.log('afer close', content);
        if (content) {
          Drupal.detachBehaviors(content, drupalSettings);
        }
      });
      modal.open();
    },
    close: () => {
      NeoModal.closeTop();
    }
  };

  Drupal.behaviors.dialog = {};
  Drupal.behaviors.dialog.prepareDialogButtons = () => {};

})(Drupal, drupalSettings, once);

export {};

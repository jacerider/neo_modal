import { NeoModal } from './modal/modal';

(function (Drupal, drupalSettings, once) {

  class DrupalDialogEvent extends Event {
    dialog:NeoModal;
    settings:drupal.IDrupalSettings|null;
    constructor(type:string, dialog:NeoModal, settings:drupal.IDrupalSettings|null = null) {
      super(`dialog:${type}`, { bubbles: true });
      this.dialog = dialog;
      this.settings = settings;
    }
  }

  const defaultOptions = {
    iconClasses: 'neo-icon neo-icon-font',
    onContentLoaded: (modal:NeoModal) => {
      const content = modal.getContent();
      if (content) {
        content.children[0]?.classList.add('neo-modal--processed');
        Drupal.attachBehaviors(content, drupalSettings);
      }
    },
    onAfterClose: (modal:NeoModal) => {
      const content = modal.getContent();
      if (content) {
        Drupal.detachBehaviors(content, drupalSettings);
      }
    }
  };
  if (typeof drupalSettings.neoModal !== 'undefined' && typeof drupalSettings.neoModal.defaults !== 'undefined') {
    Object.assign(defaultOptions, drupalSettings.neoModal.defaults);
  }
  NeoModal.setDefaultOptions(defaultOptions);

  Drupal.behaviors.neoModal = {
    attach: (context:HTMLElement, ) => {

      // Rebuild content footer for content that may have changed within the
      // top modal.
      const modal = NeoModal.getTop();
      if (modal) {
        const content = modal.getContent();
        if (content && content.children[0] && !content.children[0].classList.contains('neo-modal--processed')) {
          modal.refreshContent();
        }
      }

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

        new NeoModal(options);
      });
    }
  };

  Drupal.neoModal = {
    open: (options:any) => {
      const modal = new NeoModal(options);
      modal.event('onBeforeOpen').on(() => {
        window.dispatchEvent(new DrupalDialogEvent('beforecreate', modal, drupalSettings));
      });
      modal.event('onOpen').on(() => {
        window.dispatchEvent(new DrupalDialogEvent('aftercreate', modal, drupalSettings));
      });
      modal.event('onClose').on(() => {
        window.dispatchEvent(new DrupalDialogEvent('beforeclose', modal, drupalSettings));
      });
      modal.event('onAfterClose').on(() => {
        window.dispatchEvent(new DrupalDialogEvent('afterclose', modal, drupalSettings));
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

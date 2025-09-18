import NeoModal from './modal/modal';

declare global {
  interface JQuery {
    dialog(options?:any): JQuery;
  }
}

(function (Drupal, drupalSettings, once) {

  class DrupalDialogEvent extends Event {
    dialog:NeoModal;
    settings:drupal.IDrupalSettings|null;
    constructor(type:string, dialog:NeoModal, settings:drupal.IDrupalSettings|null = null) {
      super(`dialog:${type}`, { bubbles: true });
      this.dialog = dialog;
      this.settings = settings;
    }

    // Add a method to dispatch with jQuery compatibility
    dispatchOn(element: Element) {
      // Dispatch native event
      element.dispatchEvent(this);
      // Use jQuery for backwards compatibility. Will be removed in Drupal 12.
      if (typeof jQuery !== 'undefined') {
        const eventType = this.type;
        jQuery(element).trigger(eventType, [this.dialog, jQuery(element), this.settings]);
      }
    }
  }

  const defaultOptions = {
    iconClasses: 'neo-icon neo-icon-font',
    onContentLoaded: (modal:NeoModal) => {
      const content = modal.getContent();
      if (content) {
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
      once('neo.modal', '.use-neo-modal', context).forEach(el => {
        const options:any = {};
        options['trigger'] = el;
        options['content'] = (ref:HTMLElement) => {
          let template = ref.nextElementSibling;
          if (template && template.tagName === 'TEMPLATE') {
            return template.innerHTML;
          }
          if (template && template.classList.contains('neo-modal--template')) {
            return template;
          }
          template = ref.querySelector('.neo-modal--template');
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
        const modalElement = modal.getModal();
        if (modalElement) {
          const event = new DrupalDialogEvent('beforecreate', modal, drupalSettings);
          event.dispatchOn(modalElement);
          // modalElement.dispatchEvent(new DrupalDialogEvent('beforecreate', modal, drupalSettings));
        }
      });
      modal.event('onOpen').on(() => {
        const modalElement = modal.getModal();
        if (modalElement) {
          const event = new DrupalDialogEvent('aftercreate', modal, drupalSettings);
          event.dispatchOn(modalElement);
          // modalElement.dispatchEvent(new DrupalDialogEvent('aftercreate', modal, drupalSettings));
        }
      });
      modal.event('onClose').on(() => {
        const modalElement = modal.getModal();
        if (modalElement) {
          const event = new DrupalDialogEvent('beforeclose', modal, drupalSettings);
          event.dispatchOn(modalElement);
          // modalElement.dispatchEvent(new DrupalDialogEvent('beforeclose', modal, drupalSettings));
        }
      });
      modal.event('onAfterClose').on(() => {
        const modalElement = modal.getModal();
        if (modalElement) {
          const event = new DrupalDialogEvent('afterclose', modal, drupalSettings);
          event.dispatchOn(modalElement);
          // modalElement.dispatchEvent(new DrupalDialogEvent('afterclose', modal, drupalSettings));
        }
      });
      modal.open();
    },
    close: () => {
      NeoModal.closeTop();
    },
    getTop: () => {
      return NeoModal.getTop();
    }
  };

  Drupal.behaviors.dialog = {};
  Drupal.behaviors.dialog.prepareDialogButtons = () => {};

})(Drupal, drupalSettings, once);

if (typeof jQuery === 'function' && typeof jQuery.fn.dialog === 'undefined') {
  // Provide a jQuery plugin similar to the jQuery UI dialog.
  jQuery.fn.dialog = function (options?:any): JQuery {
    if (!Drupal.neoModal) {
      return this;
    }
    if (typeof options === 'string') {
      if (options === 'destroy') {
        Drupal.neoModal.close();
      }
      return this;
    }
    return this.each(function () {
      if (!Drupal.neoModal) {
        return;
      }
      if (Drupal.neoModal) {
        options.headerInContent = true;
        if (options.dialogClass) {
          options.modalClasses = options.dialogClass;
        }
        if (typeof options.closeOnEscape !== 'undefined' && options.closeOnEscape === false) {
          options.closeButton = false;
          options.closeOnEscape = false;
          options.backdropClose = false;
        }
        options.content = jQuery(this)[0].outerHTML;
        Drupal.neoModal.open(options);
      }
    });
  };
}

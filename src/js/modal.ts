/**
 * The modal stub: `neo_modal/modal`.
 *
 * Every page that can open a modal attaches this library, but most visitors
 * never open one, so the modal code itself, with popper, tippy and
 * body-scroll-lock, lives in `neo_modal/modal.core` and is fetched only when a
 * visitor shows intent: a pointer over, focus on or touch of a modal trigger,
 * a click on one, or a call to `Drupal.neoModal`. What stays here is what has
 * to exist from the start: the jQuery `dialog:*` bridge, the site's modal
 * defaults, the core dialog replacement and the `Drupal.neoModal` API.
 *
 * The server hands over the core's script URLs, minus whatever the page has
 * already loaded, in `drupalSettings.neoModal.core` (neo_modal_js_settings_alter()).
 * A site or scope that sets the escape hatch gets the core attached up front
 * instead, and then everything here behaves synchronously, exactly as before.
 *
 * The core announces itself by defining `window.NeoModal`. That is checked
 * when this file runs, for the escape hatch, where the core is a dependency and
 * so runs first, and again once the injected scripts have loaded.
 */
import type NeoModal from './modal/modal';

declare global {
  interface JQuery {
    dialog(options?:any): JQuery;
  }
}

(function (Drupal, drupalSettings, once) {

  type NeoModalClass = typeof NeoModal;

  /**
   * One script of the core, as its attributes. `src` is always present.
   */
  type CoreScript = {[attribute: string]: string|boolean};

  /**
   * What the server sends in `drupalSettings.neoModal.core`.
   */
  interface CoreSettings {
    // The core's scripts, in the order they must run.
    js?: CoreScript[];
    // `ajaxPageState.libraries` as the server computed it for this page...
    base?: string;
    // ...and as it reads once the core is loaded.
    libraries?: string;
  }

  class DrupalDialogEvent extends Event {
    dialog:NeoModal;
    settings:drupal.IDrupalSettings|null;
    constructor(type:string, dialog:NeoModal, settings:drupal.IDrupalSettings|null = null) {
      super(`dialog:${type}`, { bubbles: true });
      this.dialog = dialog;
      this.settings = settings;
    }

    dispatchOn(element: Element) {
      element.dispatchEvent(this);
    }
  }

  // Replacing core/drupal.dialog also removes core's dialog-deprecation.js,
  // whose jQuery special-event bridge feeds the legacy
  // (event, dialog, $element, settings) arguments to jQuery-bound listeners
  // (e.g. webform) when the native event bubbles up. Recreate that bridge here;
  // without it those listeners would fire with the extra arguments undefined.
  if (typeof jQuery !== 'undefined') {
    const eventSpecial = {
      handle($event:any, ...args:any[]) {
        const event = $event.originalEvent;
        if (event instanceof DrupalDialogEvent) {
          return $event.handleObj.handler.call(this, $event, event.dialog, jQuery($event.target), event.settings);
        }
        return $event.handleObj.handler.apply(this, [$event, ...args]);
      },
    };
    [
      'dialog:beforecreate',
      'dialog:aftercreate',
      'dialog:beforeclose',
      'dialog:afterclose',
    ].forEach((type) => {
      jQuery.event.special[type] = eventSpecial;
    });
  }

  // Read once, when the page loads, as it always was; handed to the core when
  // it arrives.
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

  // Elements whose interaction means a modal is likely to open: declarative
  // triggers, and the links that open a dialog through an AJAX request.
  const INTENT = '.use-neo-modal, .use-modal-ajax, [data-dialog-type]';
  // Declarative triggers. Their click handler is bound by the core, so a
  // click on one before the core is here has to be held and replayed.
  const TRIGGER = '.use-neo-modal';

  let core:NeoModalClass|null = null;
  let loading:Promise<void>|null = null;
  let heldTrigger:HTMLElement|null = null;

  /**
   * Adopts the core if it has run. Returns whether it is available.
   */
  const install = ():boolean => {
    if (core) {
      return true;
    }
    if (typeof window.NeoModal === 'undefined') {
      return false;
    }
    core = window.NeoModal as unknown as NeoModalClass;
    core.setDefaultOptions(defaultOptions);
    stopListening();
    return true;
  };

  /**
   * Injects the core's scripts, to run in order.
   */
  const inject = (scripts:CoreScript[]):Promise<void> => {
    return Promise.all(scripts.map((attributes) => new Promise<void>((resolve, reject) => {
      const script = document.createElement('script');
      Object.keys(attributes).forEach((name) => {
        const value = attributes[name];
        if (value !== false) {
          script.setAttribute(name, value === true ? '' : value);
        }
      });
      // A script added by script runs as soon as it arrives unless told
      // otherwise, and these depend on each other in the order given.
      script.async = false;
      script.onload = () => resolve();
      script.onerror = () => reject(new Error(`neo_modal: could not load ${script.src}`));
      document.body.appendChild(script);
    }))).then(() => undefined);
  };

  /**
   * Loads the core, once. Resolves when it is ready to use.
   */
  const load = ():Promise<void> => {
    if (install()) {
      return Promise.resolve();
    }
    if (!loading) {
      const settings:CoreSettings = (drupalSettings.neoModal && drupalSettings.neoModal.core) || {};
      loading = inject(settings.js || []).then(() => {
        if (!install()) {
          throw new Error('neo_modal: the modal code did not load.');
        }
        // Record the core's libraries as loaded, so a later AJAX response
        // does not send them again. Only while the page state is still the
        // one the server described: if an AJAX response has moved it on since,
        // the server's figure would drop that response's libraries.
        const pageState = drupalSettings.ajaxPageState;
        if (pageState && settings.libraries && pageState.libraries === settings.base) {
          pageState.libraries = settings.libraries;
        }
        // Everything on the page at once, not just what was touched: a
        // gallery walks its whole group, and each member is read from its own
        // trigger.
        attachTriggers(document);
      });
      loading.catch(() => {
        // Let the next intent try again.
        loading = null;
      });
    }
    return loading;
  };

  const report = (error:unknown) => {
    console.error(error);
  };

  const onIntent = (event:Event) => {
    if (event.target instanceof Element && event.target.closest(INTENT)) {
      load().catch(report);
    }
  };

  /**
   * Holds a click on a declarative trigger until the core can answer it.
   *
   * Capture phase on the document, so it runs before anything bound to the
   * trigger. The replay is a click on the trigger itself, which by then has
   * the core's handler and passes through here untouched.
   */
  const onClick = (event:MouseEvent) => {
    if (core || !(event.target instanceof Element)) {
      return;
    }
    const trigger = event.target.closest<HTMLElement>(TRIGGER);
    if (!trigger) {
      if (event.target.closest(INTENT)) {
        load().catch(report);
      }
      return;
    }
    event.preventDefault();
    event.stopImmediatePropagation();
    const replay = !heldTrigger;
    // Clicks while loading replace one another: the last trigger wins, and
    // opens once.
    heldTrigger = trigger;
    if (replay) {
      load().then(() => {
        const held = heldTrigger;
        heldTrigger = null;
        if (held && held.isConnected) {
          held.click();
        }
      }, (error) => {
        heldTrigger = null;
        report(error);
      });
    }
  };

  const listeners:[string, EventListener, AddEventListenerOptions][] = [
    ['pointerover', onIntent, { capture: true, passive: true }],
    ['focusin', onIntent, { capture: true }],
    ['touchstart', onIntent, { capture: true, passive: true }],
    ['click', onClick as EventListener, { capture: true }],
  ];

  const startListening = () => {
    listeners.forEach(([type, listener, options]) => document.addEventListener(type, listener, options));
  };

  function stopListening() {
    listeners.forEach(([type, listener, options]) => document.removeEventListener(type, listener, options));
  }

  const attachTriggers = (context:Document|Element) => {
    if (!core) {
      return;
    }
    const NeoModalCore = core;
    // once() is typed for an element, but takes a document as core's does.
    once('neo.modal', '.use-neo-modal', context as HTMLElement).forEach(el => {
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

      new NeoModalCore(options);
    });
  };

  Drupal.behaviors.neoModal = {
    attach: (context:HTMLElement) => {
      attachTriggers(context);
    }
  };

  /**
   * Opens a modal now. The core must be loaded.
   */
  const openNow = (NeoModalCore:NeoModalClass, options:any):NeoModal => {
    // Imperative open: this instance is built to be opened right now, not to
    // sit on the trigger waiting for clicks. Leaving the click binding on
    // would stack one more modal onto the trigger per call.
    // @see NeoModal.buildTrigger()
    const modal = new NeoModalCore(Object.assign({}, options, { triggerBind: false }));
    modal.event('onBeforeOpen').on(() => {
      const modalElement = modal.getModal();
      if (modalElement) {
        const event = new DrupalDialogEvent('beforecreate', modal, drupalSettings);
        event.dispatchOn(modalElement);
      }
    });
    modal.event('onOpen').on(() => {
      const modalElement = modal.getModal();
      if (modalElement) {
        const event = new DrupalDialogEvent('aftercreate', modal, drupalSettings);
        event.dispatchOn(modalElement);
      }
    });
    modal.event('onClose').on(() => {
      const modalElement = modal.getModal();
      if (modalElement) {
        const event = new DrupalDialogEvent('beforeclose', modal, drupalSettings);
        event.dispatchOn(modalElement);
      }
    });
    modal.event('onAfterClose').on(() => {
      const modalElement = modal.getModal();
      if (modalElement) {
        const event = new DrupalDialogEvent('afterclose', modal, drupalSettings);
        event.dispatchOn(modalElement);
      }
    });
    modal.open();
    return modal;
  };

  Drupal.neoModal = {
    open: (options:any) => {
      if (core) {
        return openNow(core, options);
      }
      return load().then(() => openNow(core as NeoModalClass, options));
    },
    close: () => {
      if (core) {
        core.closeTop();
      }
      else if (loading) {
        // Nothing can be open before the core is here, but an open may be
        // queued behind the load; close after it, as the calls were made.
        loading.then(() => core?.closeTop(), () => {});
      }
    },
    getTop: () => {
      return core ? core.getTop() : null;
    },
    ready: () => load(),
    load: () => load(),
  };

  Drupal.behaviors.dialog = {};
  Drupal.behaviors.dialog.prepareDialogButtons = () => {};

  if (!install()) {
    startListening();
  }

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

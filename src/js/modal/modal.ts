import { Signal } from './signal';

class NeoModal {

  protected options:neoModal.NeoModalOptions;
  protected originalOptions:neoModal.NeoModalOptions|null = null;
  protected static colorDefaults:neoModal.NeoModalColorOptions = {
    contentColor: '',
    contentColorBg: '',
    contentFooterColor: '',
    contentFooterColorBg: '',
    headerColor: '',
    headerColorBg: '',
    footerColor: '',
    footerColorBg: '',
    loaderColor: '',
    loaderColorBg: '',
    backdropColorBg: '',
    navColor: '',
    navColorBg: '',
  };
  protected static defaults:neoModal.NeoModalOptions = Object.assign({}, {
    appendTo: null,
    appendToClosest: null,
    wrapperClasses: null,
    modalClasses: null,
    colorScheme: 'scheme--reset',
    colorSchemeInherit: false,
    trigger: null,
    placement: 'center',
    width: 'auto',
    height: 'auto',
    image: null,
    video: null,
    iframe: null,
    group: null,
    fit: false,
    drag: true,
    bodyLock: true,
    downloadLink: true,
    shareLink: true,
    copyLink: true,
    videoAutoplay: true,
    videoRatio: '16x9',
    smartActions: false,
    content: null,
    contentPadding: '',
    contentScroll: false,
    contentAnimateIn: 'comingIn',
    contentAnimateInSpeed: null,
    contentAnimateInDelay: null,
    contentAnimateOut: 'comingOut',
    contentAnimateOutSpeed: 'fastest',
    contentAnimateOutDelay: null,
    closeButton: 'end',
    closeButtonSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="neo-modal--close-icon" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z"/><path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z"/></svg>',
    closeButtonClasses: '',
    closeButtonAnimateIn: 'fadeIn',
    closeButtonAnimateInSpeed: null,
    closeButtonAnimateInDelay: 'fast',
    closeButtonAnimateOut: false,
    closeButtonAnimateOutSpeed: null,
    closeButtonAnimateOutDelay: null,
    numeration: false,
    numerationPlacement: 'start',
    header: true,
    headerAnimate: true,
    headerInContent: false,
    headerAnimateIn: 'slideInDown',
    headerAnimateInSpeed: null,
    headerAnimateInDelay: null,
    headerAnimateOut: 'slideOutUp',
    headerAnimateOutSpeed: 'fastest',
    headerAnimateOutDelay: null,
    footer: true,
    footerAnimateIn: 'slideInUp',
    footerAnimateInSpeed: null,
    footerAnimateInDelay: null,
    footerAnimateOut: 'slideOutDown',
    footerAnimateOutSpeed: 'fastest',
    footerAnimateOutDelay: null,
    title: '',
    titleAnimateIn: 'fadeInDown',
    titleAnimateInSpeed: null,
    titleAnimateInDelay: 'fastest',
    titleAnimateOut: false,
    titleAnimateOutSpeed: null,
    titleAnimateOutDelay: null,
    subtitle: '',
    subtitleAnimateIn: 'fadeIn',
    subtitleAnimateInSpeed: null,
    subtitleAnimateInDelay: 'fast',
    subtitleAnimateOut: false,
    subtitleAnimateOutSpeed: null,
    subtitleAnimateOutDelay: null,
    icon: '',
    iconClasses: '',
    iconAnimateIn: 'zoomIn',
    iconAnimateInSpeed: 'default',
    iconAnimateInDelay: 'fastest',
    iconAnimateOut: false,
    iconAnimateOutSpeed: null,
    iconAnimateOutDelay: null,
    backdrop: true,
    backdropClasses: null,
    backdropClose: true,
    backdropAnimateIn: 'fadeIn',
    backdropAnimateInSpeed: 'fastest',
    backdropAnimateOut: 'fadeOut',
    backdropAnimateOutSpeed: 'fastest',
    loader: true,
    loaderAnimateIn: 'fadeIn',
    loaderAnimateInSpeed: 'default',
    loaderAnimateOut: 'fadeOut',
    loaderAnimateOutSpeed: 'fastest',
    nextAnimateIn: 'slideInRight',
    nextAnimateInSpeed: null,
    nextAnimateOut: 'slideOutLeft',
    nextAnimateOutSpeed: null,
    prevAnimateIn: 'slideInLeft',
    prevAnimateInSpeed: null,
    prevAnimateOut: 'slideOutRight',
    prevAnimateOutSpeed: null,
    attach: null,
    attachPlacement: 'auto',
    navKeyboard: true,
    navPrevLabel: 'Prev',
    navPrevAnimateIn: 'slideInLeft',
    navPrevAnimateInSpeed: null,
    navPrevAnimateInDelay: null,
    navPrevAnimateOut: 'slideOutLeft',
    navPrevAnimateOutSpeed: 'fastest',
    navPrevAnimateOutDelay: null,
    navNextLabel: 'Next',
    navNextAnimateIn: 'slideInRight',
    navNextAnimateInSpeed: null,
    navNextAnimateInDelay: null,
    navNextAnimateOut: 'slideOutRight',
    navNextAnimateOutSpeed: 'fastest',
    navNextAnimateOutDelay: null,
    bodySelector: '.page-wrapper',
    bodyTransitionScale: false,
    bodyTransitionBlur: false,
    svgOpen: '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor">',
    svgClose: '</svg>',
    svgIconDownload: '<path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>',
    svgIconShare: '<path fill-rule="evenodd" d="M3.5 6a.5.5 0 0 0-.5.5v8a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 1 0-1h2A1.5 1.5 0 0 1 14 6.5v8a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-8A1.5 1.5 0 0 1 3.5 5h2a.5.5 0 0 1 0 1h-2z"/><path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 1.707V10.5a.5.5 0 0 1-1 0V1.707L5.354 3.854a.5.5 0 1 1-.708-.708l3-3z"/>',
    svgIconLink: '<path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/><path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>',
    onSettings: null,
    onBeforeOpen: null,
    onOpen: null,
    onAfterOpen: null,
    onBeforeClose: null,
    onClose: null,
    onAfterClose: null,
    onBeforeNext: null,
    onNext: null,
    onAfterNext: null,
    onBeforePrev: null,
    onPrev: null,
    onAfterPrev: null,
    onContentLoaded: null,
  }, this.colorDefaults) as neoModal.NeoModalOptions;
  protected eventMap:Map<string, Function> = new Map([
    ['onSettings', ():ISignal<NeoModal, any> => {
      return this.eventSettings.expose();
    }],
    ['onContentLoaded', ():ISignal<NeoModal, any> =>{
      return this.eventContentLoaded.expose();
    }],
    ['onBeforeOpen', ():ISignal<NeoModal, any> => {
      return this.eventBeforeOpen.expose();
    }],
    ['onOpen', ():ISignal<NeoModal, any> => {
      return this.eventOpen.expose();
    }],
    ['onAfterOpen', ():ISignal<NeoModal, any> => {
      return this.eventAfterOpen.expose();
    }],
    ['onBeforeClose', ():ISignal<NeoModal, any> => {
      return this.eventBeforeClose.expose();
    }],
    ['onClose', ():ISignal<NeoModal, any> => {
      return this.eventClose.expose();
    }],
    ['onAfterClose', ():ISignal<NeoModal, any> => {
      return this.eventAfterClose.expose();
    }],
    ['onBeforeNext', ():ISignal<NeoModal, any> => {
      return this.eventBeforeNext.expose();
    }],
    ['onNext', ():ISignal<NeoModal, any> => {
      return this.eventNext.expose();
    }],
    ['onAfterNext', ():ISignal<NeoModal, any> => {
      return this.eventAfterNext.expose();
    }],
    ['onBeforePrev', ():ISignal<NeoModal, any> => {
      return this.eventBeforePrev.expose();
    }],
    ['onPrev', ():ISignal<NeoModal, any> => {
      return this.eventPrev.expose();
    }],
    ['onAfterPrev', ():ISignal<NeoModal, any> => {
      return this.eventAfterPrev.expose();
    }],
  ]);
  // Options that support declaration via [data-neo-modal-*].
  protected optionsAsAttributes:Array<string> = [
    'appendTo',
    'appendToClosest',
    'wrapperClasses',
    'modalClasses',
    'colorScheme',
    'width',
    'height',
    'image',
    'video',
    'iframe',
    'group',
    'smartActions',
    'numeration',
    'fit',
    'drag',
    'bodyLock',
    'backdrop',
    'header',
    'headerInContent',
    'footer',
    'downloadLink',
    'shareLink',
    'copyLink',
    'closeButton',
    'contentScroll',
    'contentPadding',
    'title',
    'subtitle',
    'icon',
    'placement',
    'attach',
    'attachPlacement',
    'bodyTransitionScale',
    'bodyTransitionBlur',
    'contentAnimateIn',
    'contentAnimateInSpeed',
    'contentAnimateInDelay',
    'contentAnimateOut',
    'contentAnimateOutSpeed',
    'contentAnimateOutDelay',
  ];
  protected optionsAsMerge:Array<string> = [
    'title',
    'subtitle',
    'icon',
    'image',
    'video',
    'iframe',
    'content',
    'trigger',
  ];
  // Options that, when boolean, should be set to default.
  protected optionsFromBoolToDefault:Array<string> = [
    'icon',
  ];
  protected loader:HTMLElement|null = null;
  protected loading:boolean = false;
  protected wrapper:HTMLElement|null = null;
  protected modal:neoModal.NeoModalElement|null = null;
  protected backdrop:HTMLElement|null = null;
  protected groupTriggers:NodeListOf<HTMLElement>|null = null;
  protected container:HTMLElement|null = null;
  protected content:HTMLElement|null = null;
  protected contentBlock:HTMLElement|null = null;
  protected contentWrapper:HTMLElement|null = null;
  protected contentInner:HTMLElement|null = null;
  protected contentPlaceholder:HTMLElement|null = null;
  protected header:HTMLElement|null = null;
  protected headerStartOut:HTMLElement|null = null;
  protected headerEndOut:HTMLElement|null = null;
  protected footer:HTMLElement|null = null;
  protected title:HTMLElement|null = null;
  protected subtitle:HTMLElement|null = null;
  protected icon:HTMLElement|null = null;
  protected closeButton:HTMLElement|null = null;
  protected next:HTMLElement|null = null;
  protected prev:HTMLElement|null = null;
  protected shareUrl:string = '';
  protected downloadUrl:string = '';
  protected copyUrl:string = '';
  protected depth:number = 0;
  protected isOpen:boolean = false;
  protected isBuilt:boolean = false;
  protected canZoom:boolean = false;
  protected canClickContent:boolean = true;
  protected throttle:ReturnType<typeof setTimeout>|null = null;
  protected watchInterval:ReturnType<typeof setInterval>|null = null;
  protected popper:Popper.instance|null = null;
  private eventSettings = new Signal<NeoModal, neoModal.NeoModalOptions>();
  private eventBeforeOpen = new Signal<NeoModal, void>();
  private eventOpen = new Signal<NeoModal, void>();
  private eventAfterOpen = new Signal<NeoModal, void>();
  private eventBeforeClose = new Signal<NeoModal, void>();
  private eventClose = new Signal<NeoModal, void>();
  private eventAfterClose = new Signal<NeoModal, void>();
  private eventBeforeNext = new Signal<NeoModal, void>();
  private eventNext = new Signal<NeoModal, void>();
  private eventAfterNext = new Signal<NeoModal, void>();
  private eventBeforePrev = new Signal<NeoModal, void>();
  private eventPrev = new Signal<NeoModal, void>();
  private eventAfterPrev = new Signal<NeoModal, void>();
  private eventContentLoaded = new Signal<NeoModal, void>();

  /**
   * Construct.
   */
  constructor(options:neoModal.NeoModalOptions) {
    // Colors can always be set via [data-neo-modal-*].
    for (let key in NeoModal.colorDefaults) {
      this.optionsAsAttributes.push(key);
    }

    this.options = this.buildOptions(options);
    if (this.options.trigger instanceof HTMLElement) {
      this.options.trigger.neoModalOptions = this.options;
    }

    this.buildTrigger();
  }

  public static setDefaultOptions(options:any):void {
    this.defaults = Object.assign({}, this.defaults, options);
  }

  public static closeTop():void {
    const modal = document.querySelector<HTMLElement>('.neo-modal:last-child') as neoModal.NeoModalElement;
    if (modal && modal.neoModal) {
      modal.neoModal.close();
    }
  }

  /**
   * Get an event by name.
   */
  public event(eventName:string):any|null {
    if (this.eventMap.has(eventName)) {
      const method = this.eventMap.get(eventName);
      if (method) {
        return method();
      }
    }
    return null;
  }

  protected buildOptions(options:neoModal.NeoModalOptions):neoModal.NeoModalOptions {
    options = Object.assign({}, NeoModal.defaults, options);
    // Set boolean options to default.
    const opts:any = {};
    this.optionsFromBoolToDefault.forEach((key) => {
      const val = options[key as keyof neoModal.NeoModalOptions];
      if (typeof val === 'boolean' && val === true) {
        opts[key] = val;
      }
    });
    // Get options from trigger data attributes.
    if (options.trigger instanceof HTMLElement) {
      const trigger = options.trigger;
      this.optionsAsAttributes.forEach((attribute) => {
        const property = 'data-neo-modal-' + attribute;
        if (trigger.hasAttribute(property)) {
          let value:any = trigger.getAttribute(property);
          if (typeof value === 'string') {
            if (value === 'true') {
              value = true;
            }
            if (value === 'false') {
              value = false;
            }
          }
          opts[attribute] = value;
        }
      });
    }
    const builtOptions = Object.assign({}, options, opts);

    // Listen for events passed in as options.
    this.eventMap.forEach((method, event) => {
      if (typeof builtOptions[event as keyof neoModal.NeoModalOptions] === 'function') {
        const expose = method();
        expose.on(builtOptions[event as keyof neoModal.NeoModalOptions] as (modal:NeoModal, data?:any) => void);
      }
    });

    this.eventSettings.trigger(this, builtOptions);
    return builtOptions;
  }

  protected mergeOptions(options:neoModal.NeoModalOptions):neoModal.NeoModalOptions {
    const opts:any = {};
    this.optionsAsMerge.forEach((key) => {
      if (typeof this.options[key as keyof neoModal.NeoModalOptions] !== 'undefined' && typeof options[key as keyof neoModal.NeoModalOptions] !== 'undefined') {
        opts[key] = options[key as keyof neoModal.NeoModalOptions];
      }
    });
    return Object.assign({}, this.options, opts);
  }

  protected buildTrigger():void {
    if (this.options.trigger instanceof HTMLElement) {
      const trigger = this.options.trigger;
      trigger.addEventListener('click', (event) => {
        event.preventDefault();
        this.toggle();
      });
    }
  }

  /**
   * Build.
   */
  protected build():Promise<void> {
    return new Promise((resolve) => {
      if (this.isBuilt) {
        resolve();
      }
      else {
        if (this.wrapper) {
          this.isBuilt = true;
          this.modal = document.createElement('div') as neoModal.NeoModalElement;
          this.modal.classList.add('neo-modal');
          if (this.options.modalClasses) {
            this.options.modalClasses.split(' ').forEach((className) => {
              this.modal?.classList.add(className);
            });
          }
          if (this.options.colorScheme) {
            this.modal?.classList.add(this.options.colorScheme);
          }
          this.modal.neoModal = this;
          // this.modal.setAttribute('neoModal', 'hi');
          this.modal.setAttribute('role', 'dialog');
          this.modal.setAttribute('aria-modal', 'true');
          this.modal.style.setProperty('visibility', 'hidden');
          this.modal.style.setProperty('pointer-events', 'none');
          this.wrapper.appendChild(this.modal);

          const container = this.buildContainer();
          this.modal.appendChild(container);

          this.buildContent(container).then(() => {
            if (this.modal) {
              this.buildNavigation(this.modal);
            }
            this.buildModal();
            this.attachModal();
            resolve();
          });
        }
        else {
          // Something happened. We don't have a wrapper?
          resolve();
        }
      }
    });
  }

  /**
   * Rebuild modal with new options.
   */
  protected rebuild(newOptions:neoModal.NeoModalOptions):Promise<void> {
    return new Promise((resolve) => {
      if (this.modal && this.container) {
        this.container.style.setProperty('visibility', 'hidden');
        // Reset values that may be altered in the content.
        this.canClickContent = true;
        this.canZoom = false;
        this.currentZoom = 1;
        this.shareUrl = '';
        this.copyUrl = '';
        this.downloadUrl = '';
        this.options = this.mergeOptions(newOptions);
        this.buildContent(this.container).then(() => {
          this.buildModal();
          this.buildTooltips();
          resolve();
        });
      }
      else {
        // Something went wrong. We don't have a container?
        resolve();
      }
    });
  }

  /**
   * Build modal.
   */
  protected buildModal():void {
    this.buildHeader();
    this.buildFooter();
    this.buildContentFooter();
    if (this.modal) {
      this.modal.querySelectorAll('[data-neo-modal-close]').forEach((el) => {
        el.addEventListener('click', e => {
          this.close();
          e.preventDefault();
          e.stopPropagation();
        });
      });
    }
    this.setAttributes();
    this.size();
  }

  protected buildTooltips():void {
    if (this.modal) {
      this.modal.querySelectorAll('.neo-modal--tooltip:not(.neo-modal--tooltip-processed)').forEach((el) => {
        el.classList.add('neo-modal--tooltip-processed');
        tippy(el);
      });
    }
  }

  protected attachModal():void {
    if (this.options.attach) {
      const attach = this.options.attach;
      let anchor:HTMLElement|null = null;
      if (attach instanceof HTMLElement) {
        anchor = attach;
      }
      else if (typeof this.options.attach === 'string') {
        anchor = document.querySelector(this.options.attach) as HTMLElement;
      }
      if (anchor && this.content) {
        this.popper = Popper.createPopper(anchor, this.content, {
          placement: this.options.attachPlacement,
        });
      }
    }
  }

  protected setAttributes():void {
    if (this.modal) {
      if (this.options.contentScroll) {
        this.modal.classList.add('neo-modal--content-scroll');
      }
      else {
        this.modal.classList.add('neo-modal--global-scroll');
      }
      for (let key in NeoModal.colorDefaults) {
        if (this.options[key as keyof neoModal.NeoModalOptions]) {
          const value = this.options[key as keyof neoModal.NeoModalOptions] as string;
          const dashed = key.replace(/[A-Z]/g, m => "-" + m.toLowerCase()).replace('color-bg', 'bg');
          this.modal.style.setProperty('--modal-' + dashed, value);
        }
      }
      if (this.header) {
        this.modal.classList.add('neo-modal--header-' + (this.options.headerInContent ? 'content' : 'global'));
        this.modal.style.setProperty('--modal-padding-t', this.getAbsoluteHeight(this.header) + 'px');
      }
      if (this.options.contentPadding) {
        if (typeof this.options.contentPadding === 'string') {
          const isNum = /^\d+$/.test(this.options.contentPadding);
          if (isNum) {
            this.options.contentPadding += 'px';
          }
          this.modal.style.setProperty('--modal-content-padding', this.options.contentPadding);
        }
        else if (typeof this.options.contentPadding === 'number') {
          this.modal.style.setProperty('--modal-content-padding', this.options.contentPadding + 'px');
        }
      }
      if (this.content) {
        if (this.options.attach) {
          this.modal.classList.add('neo-modal--attach');
        }
        if (this.options.fit) {
          this.modal.classList.add('neo-modal--fit');
        }
        this.canClickContent ? this.modal.classList.remove('neo-modal--no-click') : this.modal.classList.add('neo-modal--no-click');
        if (this.options.width) {
          if (['auto', 'full'].includes(this.options.width as string)) {
            this.modal.classList.add('neo-modal--width-' + this.options.width);
          }
          else {
            let width = this.options.width;
            if (typeof this.options.width === 'string') {
              let isnum = /^\d+$/.test(this.options.width);
              if (isnum) {
                width += 'px';
              }
            }
            this.content.style.width = width + '';
          }
        }
        if (this.options.height) {
          if (['auto', 'full'].includes(this.options.height as string)) {
            this.modal.classList.add('neo-modal--height-' + this.options.height);
          }
          else {
            let height = this.options.height;
            if (typeof this.options.height === 'string') {
              let isnum = /^\d+$/.test(this.options.height);
              if (isnum) {
                height += 'px';
              }
            }
            else {
              height += 'px';
            }
            this.content.style.minHeight = height + '';
          }
        }
      }
    }
  }

  public size():void {
    if (this.container && this.modal) {
      if (this.options.placement && !this.options.attach) {
        // We remove placement so scroll can be properly calculated.
        this.modal.classList.remove('neo-modal--placement-' + this.options.placement);
      }
      if (!this.options.fit && this.container.scrollHeight > this.container.clientHeight) {
        this.modal.classList.remove('neo-modal--no-scroll');
        this.modal.classList.add('neo-modal--scroll');
      }
      else {
        this.modal.classList.remove('neo-modal--scroll');
        this.modal.classList.add('neo-modal--no-scroll');
      }
      if (this.options.placement && !this.options.attach) {
        // Add placement after scroll calculation.
        this.modal.classList.add('neo-modal--placement-' + this.options.placement);
      }
      if (this.content) {
        let offsetWidth = this.content.clientWidth;
        if (this.headerStartOut && this.headerEndOut) {
          offsetWidth += this.headerStartOut.clientWidth + this.headerEndOut.clientWidth;
        }
        else if (this.headerStartOut) {
          // We double it because the modal is centered.
          offsetWidth += this.headerStartOut.clientWidth * 2;
        }
        else if (this.headerEndOut) {
          // We double it because the modal is centered.
          offsetWidth += this.headerEndOut.clientWidth * 2;
        }
        if (this.container.clientWidth <= offsetWidth) {
          if (!this.modal.classList.contains('neo-modal--flush')) {
            this.modal.classList.add('neo-modal--flush');
          }
        }
        else if (this.modal.classList.contains('neo-modal--flush')) {
          this.modal.classList.remove('neo-modal--flush');
        }
      }
    }
  }

  public watch():void {
    this.size();
  }

  protected onKeyboardDown(event:KeyboardEvent):void {
    if (this.options.fit && !this.focused) {
      this.focusWatch();
    }
    if (event.key === 'Escape') {
      const modal = document.querySelector<HTMLElement>('.neo-modal:last-child');
      // We may have multiple modals open. Only close the top level.
      if (modal === this.modal) {
        this.close();
      }
    }
    if (!this.throttle) {
      if (event.key === 'ArrowRight') {
        this.navigate('next');
      }
      if (event.key === 'ArrowLeft') {
        this.navigate('prev');
      }
      this.throttle = setTimeout(() => {
        this.throttle = null;
      }, 100);
    }
  }

  protected onKeyboardUp():void {
    if (this.throttle) {
      clearTimeout(this.throttle);
      this.throttle = null;
    }
  }

  protected focused:boolean = false;
  protected focusing:boolean = false;
  protected focusTimeout:ReturnType<typeof setTimeout>|null = null;
  public focusWatch():void {
    if (this.focused) {
      this.focusOut();
    }
    clearTimeout(this.focusTimeout as ReturnType<typeof setTimeout>);
    this.focusTimeout = setTimeout(() => {
      this.focusIn();
    }, 3000);
  }

  public focusIn():void {
    if (this.focusing) {
      return;
    }
    if (this.modal) {
      this.modal.classList.add('neo-modal--focus');
    }
    this.focusing = true;
    let callback:Function|null = () => {
      this.focusing = false;
    };
    if (this.header) {
      if (!this.options.headerInContent) {
        this.animateOut(this.header, 'header', callback, 'default');
        callback = null;
      }
    }
    if (this.prev) {
      this.animateOut(this.prev, 'navPrev', callback, 'default');
      callback = null;
    }
    if (this.next) {
      this.animateOut(this.next, 'navNext', callback, 'default');
      callback = null;
    }
    if (this.footer) {
      this.animateOut(this.footer, 'footer', callback, 'default');
      callback = null;
    }
    this.focused = true;
  }

  protected focusOut():void {
    if (this.focusing) {
      return;
    }
    if (this.modal) {
      this.modal.classList.remove('neo-modal--focus');
    }
    if (this.header) {
      if (!this.options.headerInContent) {
        this.animateIn(this.header, 'header');
      }
    }
    if (this.prev) {
      this.animateIn(this.prev, 'navPrev');
    }
    if (this.next) {
      this.animateIn(this.next, 'navNext');
    }
    if (this.footer) {
      this.animateIn(this.footer, 'footer');
    }
    this.focused = false;
  }

  protected currentZoom:number = 1;
  protected minZoom:number = 1;
  protected maxZoom:number = 3;
  protected stepSize:number = 1;
  protected zooming:boolean = false;
  protected zoom(direction:number):void {
    if (this.contentInner) {
      if (this.zooming === true) {
        return;
      }
      this.zooming = true;
      setTimeout(() => {
        this.zooming = false;
      }, 500);
      let newZoom = this.currentZoom + direction * this.stepSize;
      // Limit the zoom level to the minimum and maximum
      // values
      if (newZoom < this.minZoom || newZoom > this.maxZoom) {
          return;
      }
      const prefix = 'neo-modal--zoom-';
      const pattern = '(' + prefix + '(\\s|(-)?(\\w*)(\\s)?)).*?';
      const regEx = new RegExp(pattern, 'g');
      this.contentInner.className = this.contentInner.className.replace(regEx, '');
      this.contentInner.classList.add('neo-modal--zoom-' + this.currentZoom + 'to' + newZoom);
      this.currentZoom = newZoom;
    }
  }

  protected getGroupTriggers():NodeListOf<HTMLElement>|null {
    if (this.options.group) {
      if (!this.groupTriggers) {
        this.groupTriggers = document.querySelectorAll<HTMLElement>('[data-neo-modal-group="' + this.options.group + '"]');
      }
    }
    return this.groupTriggers;
  }

  protected getToTrigger(direction:neoModal.Direction):neoModal.NeoModalTriggerElement|null {
    const triggers = this.getGroupTriggers();
    if (triggers && triggers.length > 1) {
      let to:HTMLElement|null = null;
      triggers.forEach((trigger, index) => {
        if (trigger === this.options.trigger) {
          if (direction === 'next') {
            if (index < triggers.length - 1) {
              to = triggers[index + 1];
            }
            else {
              to = triggers[0];
            }
          }
          if (direction === 'prev') {
            if (index > 0) {
              to = triggers[index - 1];
            }
            else {
              to = triggers[triggers.length - 1];
            }
          }
        }
      });
      return to;
    }
    return null;
  }

  protected navigate(direction:neoModal.Direction):void {
    const triggers = this.getGroupTriggers();
    if (this.contentBlock && triggers && triggers.length > 1) {
      const to = this.getToTrigger(direction);
      if (to) {
        this.showLoader();
        if (direction === 'prev') {
          this.eventBeforePrev.trigger(this);
        }
        if (direction === 'next') {
          this.eventBeforeNext.trigger(this);
        }
        if (!this.focused && this.header && !this.options.headerInContent) {
          this.animateOut(this.header, 'header', null, 'default');
        }
        if (!this.focused && this.footer) {
          this.animateOut(this.footer, 'footer', null, 'default');
        }
        this.animateOut(this.contentBlock, direction, () => {
          if (direction === 'prev') {
            this.eventPrev.trigger(this);
          }
          if (direction === 'next') {
            this.eventNext.trigger(this);
          }
          this.rebuild(to.neoModalOptions).then(() => {
            if (this.contentBlock) {
              if (direction === 'prev') {
                this.eventAfterPrev.trigger(this);
              }
              if (direction === 'next') {
                this.eventAfterNext.trigger(this);
              }
              if (this.header && !this.options.headerInContent) {
                this.focused ? this.header.style.display = 'none' : this.animateIn(this.header, 'header');
              }
              if (this.footer) {
                this.focused ? this.footer.style.display = 'none' : this.animateIn(this.footer, 'footer');
              }
              this.modal?.style.setProperty('visibility', '');
              this.container?.style.setProperty('visibility', '');
              this.animateIn(this.contentBlock, direction);
              if (direction === 'prev') {
                this.eventPrev.trigger(this);
              }
              if (direction === 'next') {
                this.eventNext.trigger(this);
              }
            }
          });
        });
      }
    }
  }

  protected remove():void {
    if (this.modal) {
      this.modal.remove();
      this.modal = null;
      this.isBuilt = false;
    }
  }

  /**
   * Build the global stack.
   *
   * This may be called multiple times, but will only build the stack once.
   */
  public buildStack():void {
    this.buildWrapper();
    const insert = this.buildBackdrop();
    if (this.wrapper) {
      this.wrapper.removeAttribute('class');
      this.wrapper.removeAttribute('style');
      this.wrapper.classList.add('neo-modals');
      if (this.options.wrapperClasses) {
        this.options.wrapperClasses.split(' ').forEach((className) => {
          this.wrapper?.classList.add(className);
        });
      }
      if (this.options.backdropColorBg) {
        this.wrapper.style.setProperty('--modal-backdrop-bg', this.options.backdropColorBg);
      }
      if (this.options.colorScheme) {
        this.wrapper.classList.add(this.options.colorScheme);
      }
    }
    if (this.backdrop) {
      this.backdrop.removeAttribute('class');
      this.backdrop.removeAttribute('style');
      this.backdrop.classList.add('neo-modal--backdrop');
      if (this.options.backdropClasses) {
        this.options.backdropClasses.split(' ').forEach((className) => {
          this.backdrop?.classList.add(className);
        });
      }
    }
    if (insert) {
      if (this.wrapper && this.backdrop) {
        if (!this.options.backdrop) {
          this.backdrop.style.setProperty('visibility', 'hidden');
        }
        this.wrapper.appendChild(this.backdrop);
        this.animateIn(this.backdrop, 'backdrop');
      }
    }
    else if (this.backdrop) {
      const visibility = window.getComputedStyle(this.backdrop).display !== 'none';
      if (this.options.backdrop && !visibility) {
        this.animateIn(this.backdrop, 'backdrop');
      }
      else if (!this.options.backdrop && visibility) {
        this.animateOut(this.backdrop, 'backdrop');
      }
    }
  }

  protected buildWrapper():HTMLElement {
    if (!this.wrapper) {
      this.wrapper = document.querySelector('.neo-modals') as HTMLElement;
      if (!this.wrapper) {
        this.wrapper = document.createElement('div');
        this.globalInit();
        let appendTo = document.body;
        if (this.options.appendToClosest && this.options.trigger) {
          appendTo = this.options.trigger.closest(this.options.appendToClosest) as HTMLElement;
        }
        else if (this.options.appendTo) {
          if (typeof this.options.appendTo === 'string') {
            appendTo = document.querySelector(this.options.appendTo) as HTMLElement;
          }
          else {
            appendTo = this.options.appendTo as HTMLElement;
          }
        }
        appendTo.appendChild(this.wrapper);
      }
    }
    // if (this.backdrop) {
    //   this.animateIn(this.backdrop, 'backdrop');
    // }
    return this.wrapper;
  }

  protected removeWrapper():void {
    if (this.wrapper && this.wrapper.querySelectorAll('.neo-modal').length === 0) {
      this.wrapper.remove();
      this.wrapper = null;
      this.globalDestroy();

      // if (this.backdrop) {
      //   this.animateOut(this.backdrop, 'backdrop');
      // }
    }
  }

  protected buildBackdrop():boolean {
    let insert = false;
    this.backdrop = document.querySelector('.neo-modal--backdrop') as HTMLElement;
    if (!this.backdrop) {
      insert = true;
      this.backdrop = document.createElement('div');
    }
    return insert;
  }

  protected buildContainer():HTMLElement {
    if (this.container) {
      this.container.remove();
    }
    this.container = document.createElement('div');
    this.container.classList.add('neo-modal--container');
    this.container.addEventListener('click', event => {
      const target = event.target as HTMLElement;
      if (this.options.backdropClose && target.classList.contains('neo-modal--container')) {
        this.close();
      }
    });
    return this.container;
  }

  protected buildNavigation(element:HTMLElement) {
    if (this.options.group) {
      const triggers = this.getGroupTriggers();
      if (triggers && triggers.length > 1) {
        this.prev = document.createElement('div');
        this.prev.classList.add('neo-modal--nav');
        this.prev.classList.add('neo-modal--prev');
        this.prev.innerHTML = `<a class="neo-modal--tooltip" data-tippy-theme="modal" data-tippy-content="Prev" data-tippy-placement="right" data-tippy-delay="[500, 0]"><span>${this.options.navPrevLabel}</span></a>`;
        this.prev.querySelector('a')?.addEventListener('click', event => {
          event.preventDefault();
          this.navigate('prev');
        });
        element.appendChild(this.prev);

        this.next = document.createElement('div');
        this.next.classList.add('neo-modal--nav');
        this.next.classList.add('neo-modal--next');
        this.next.innerHTML = `<a class="neo-modal--tooltip" data-tippy-theme="modal" data-tippy-content="Next" data-tippy-placement="left" data-tippy-delay="[500, 0]"><span>${this.options.navNextLabel}</span></a>`;
        this.next.querySelector('a')?.addEventListener('click', event => {
          event.preventDefault();
          this.navigate('next');
        });
        element.appendChild(this.next);

        this.buildDrag();
      }
    }
    return null;
  }

  protected buildDrag():void {
    if (this.modal) {
      this.modal.classList.add('neo-modal--draggable');
      this.modal.addEventListener('touchstart', this.onDragStart.bind(this), false);
      this.modal.addEventListener('touchend', this.onDragEnd.bind(this), false);
      this.modal.addEventListener('touchmove', this.onDrag.bind(this), false);
      this.modal.addEventListener('mousedown', this.onDragStart.bind(this), false);
      this.modal.addEventListener('mouseup', this.onDragEnd.bind(this), false);
      this.modal.addEventListener('mouseout', this.onDragEnd.bind(this), false);
      this.modal.addEventListener('mousemove', this.onDrag.bind(this), false);
    }
  }

  protected dragStartX:number = 0;
  protected dragStartY:number = 0;
  protected dragEndX:number = 0;
  protected dragEndY:number = 0;
  protected dragThreshold:number = 50;
  protected dragging:boolean = false;
  protected onDragStart(e:MouseEvent|TouchEvent):void {
    if (this.contentWrapper) {
      this.dragging = true;
      if (e.type === 'touchmove') {
        e = e as TouchEvent;
        this.dragStartX = this.dragEndX = e.touches[0].pageX;
        this.dragStartY = this.dragEndY = e.touches[0].pageY;
      }
      else {
        e = e as MouseEvent;
        this.dragStartX = this.dragEndX = e.pageX;
        this.dragStartY = this.dragEndY = e.pageY;
      }
    }
  }

  protected onDragEnd():void {
    if (this.dragging) {
      this.dragging = false;
      if (this.contentWrapper) {
        let direction:neoModal.Direction|null = null;
        const diff = this.dragEndX - this.dragStartX;
        if (diff < 0) {
          direction = 'next';
        }
        if (diff > 0) {
          direction = 'prev';
        }
        if (Math.abs(diff) > this.dragThreshold && direction) {
          this.navigate(direction);
        }
        else {
          this.contentWrapper.style.transform = '';
          this.contentWrapper.style.opacity = '';
        }
      }
    }
  }

  protected onDrag(e:MouseEvent|TouchEvent):void {
    if (this.dragging) {
      if (this.contentWrapper) {
        if (e.type === 'touchmove') {
          e = e as TouchEvent;
          this.dragEndX = e.touches[0].pageX;
          this.dragEndY = e.touches[0].pageY;
        }
        else {
          e = e as MouseEvent;
          this.dragEndX = e.pageX;
          this.dragEndY = e.pageY;
        }
        const diffX = this.dragEndX - this.dragStartX;
        const diffY = this.dragEndY - this.dragStartY;
        const absDiffX = Math.abs(diffX);
        const absDiffY = Math.abs(diffY);

        if (absDiffX > absDiffY && absDiffX <= 180) {
            const diffopac = Math.min(1, (1 - absDiffX / 180) * 1.5);
            e.preventDefault();
            this.contentWrapper.style.transform = 'translateX(' + diffX + 'px)';
            this.contentWrapper.style.opacity = diffopac + '';
        }
      }
    }
  }

  protected buildContent(element:HTMLElement):Promise<void> {
    return new Promise((resolve) => {
      if (!this.container) {
        // Something went wrong. We dont' have a container?
        resolve();
      }

      if (this.content) {
        this.content.remove();
        this.content = null;
      }

      this.showLoader();

      this.content = document.createElement('div');
      this.content.classList.add('neo-modal--content--container');

      this.contentBlock = document.createElement('div');
      this.contentBlock.classList.add('neo-modal--content-block');
      this.content.appendChild(this.contentBlock);

      element.appendChild(this.content);

      this.buildContentByType(this.contentBlock).then(() => {
        this.eventContentLoaded.trigger(this);
        this.hideLoader();
        this.bindContentEvents();
        resolve();
      });
    });
  }

  protected bindContentEvents():void {
    if (this.contentInner && this.canZoom) {
      this.contentInner.classList.add('neo-modal--zoom');
      this.contentInner.addEventListener('wheel', (e) => {
        e.preventDefault();
        const direction = e.deltaY > 0 ? -1 : 1;
        this.zoom(direction);
      }, false);
      this.contentInner.addEventListener('dblclick', () => {
        const direction = this.currentZoom === 1 ? 1 : -1;
        this.zoom(direction);
      });
    }
  }

  protected buildContentByType(element:HTMLElement):Promise<HTMLElement> {
    return new Promise((resolve) => {
      this.contentWrapper = document.createElement('div');
      this.contentWrapper.classList.add('neo-modal--content-wrapper');

      if (this.options.image) {
        this.contentInner = this.getContentImage(resolve);
      }
      else if (this.options.video) {
        this.contentInner = this.getContentVideo(resolve);
      }
      else if (this.options.iframe) {
        this.contentInner = this.getContentIframe(resolve);
      }
      else {
        this.contentInner = this.getContentDefault(resolve);
      }

      if (this.contentInner) {
        this.contentInner.classList.add('neo-modal--content');
        if (!this.options.colorSchemeInherit) {
          this.contentInner.classList.add('scheme--reset');
        }
        this.contentWrapper.appendChild(this.contentInner);
        element.appendChild(this.contentWrapper);
      }
      else {
        // There was an issue with the content.
        element.appendChild(this.contentWrapper);
      }
    });
  }

  protected getContentDefault(resolve:(content:HTMLElement) => void):HTMLElement {
    let element:HTMLElement = document.createElement('div');
    element.classList.add('neo-modal--content-missing');
    element.innerText = 'Content not found.';
    if (typeof this.options.content === 'function') {
      element = document.createElement('div');
      if (this.options.trigger) {
        const content = this.options.content(this.options.trigger);
        if (content instanceof HTMLElement) {
          this.contentPlaceholder = document.createElement('template');
          this.contentPlaceholder.classList.add('neo-modal--content-placeholder');
          content.parentNode?.insertBefore(this.contentPlaceholder, content);
          element.appendChild(content);
        }
        else {
          element.innerHTML = content;
        }
      }
    }
    else if (typeof this.options.content === 'string') {
      element = document.createElement('div');
      element.innerHTML = this.options.content;
    }
    else {
      this.contentInner = this.options.content as HTMLElement;
    }
    if (element.childElementCount) {
      const child = element.firstElementChild as HTMLElement;
      if (child) {
        switch (child.tagName) {
          case 'IMAGE':
            const image = child as HTMLImageElement;
            image.onload = () => {
              resolve(element);
            }
            this.shareUrl = image.src;
            this.downloadUrl = image.src;
            this.copyUrl = image.src;
            this.canZoom = this.options.fit;
            return element;

          case 'PICTURE':
            const pictureImage = child.querySelector('img');
            if (pictureImage) {
              pictureImage.onload = () => {
                this.shareUrl = pictureImage.currentSrc;
                this.downloadUrl = pictureImage.currentSrc;
                this.copyUrl = pictureImage.currentSrc;
                this.canZoom = this.options.fit;
                resolve(element);
              }
              if (this.options.group) {
                // Fix weird drag.
                pictureImage.addEventListener('dragstart', function(e) {
                  e.preventDefault();
                });
              }
              return element;
            }
            break;
        }
      }
    }
    resolve(element);
    return element;
  }

  protected getContentImage(resolve:(content:HTMLElement) => void):HTMLElement {
    const imageUrl = this.options.image || '';
    const content = document.createElement('div');
    const image = document.createElement('img');
    this.canZoom = this.options.fit;
    image.onload = () => {
      resolve(content);
    };
    if (this.options.group) {
      // Fix weird drag.
      image.addEventListener('dragstart', function(e) {
        e.preventDefault();
      });
    }
    image.src = imageUrl;
    content.appendChild(image);
    this.shareUrl = imageUrl;
    this.downloadUrl = imageUrl;
    this.copyUrl = imageUrl;
    return content;
  }

  protected getContentVideo(resolve:(content:HTMLElement) => void):HTMLElement {
    const content = document.createElement('div');
    if (this.options.video) {
      const videoObj = this.parseVideo(this.options.video);
      if (videoObj) {
        const wrapper = document.createElement('div');
        wrapper.classList.add('neo-modal--video');
        wrapper.classList.add('neo-modal--ratio');
        wrapper.classList.add('neo-modal--ratio-' + this.options.videoRatio);
        wrapper.classList.add('bg-black');
        // this.options.contentColorBg = 'transparent';
        this.canClickContent = false;
        let stringAutoplay:string;
        if (videoObj.type == 'vimeo' || videoObj.type == 'youtube') {
          let player;
          // set rel=0 to hide related videos at the end of YT + optional autoplay
          stringAutoplay = this.options.videoAutoplay ? "?rel=0&autoplay=1" : "?rel=0";
          let queryvars = stringAutoplay + this.getUrlParameter(this.options.video);
          if (videoObj.type == 'vimeo') {
            player = 'https://player.vimeo.com/video/';
          } else if (videoObj.type == 'youtube') {
            player = 'https://www.youtube-nocookie.com/embed/';
          }
          const iframe = document.createElement('iframe');
          iframe.onload = () => {
            resolve(content);
          };
          iframe.classList.add('neo-modal--iframe');
          iframe.setAttribute('webkitallowfullscreen', '');
          iframe.setAttribute('mozallowfullscreen', '');
          iframe.setAttribute('allowfullscreen', '');
          iframe.setAttribute('allow', 'autoplay');
          iframe.setAttribute('frameborder', '0');
          iframe.setAttribute('src', player+videoObj.id+queryvars);
          wrapper.appendChild(iframe);
          content.appendChild(wrapper);
          // When set to auto width, default to 100%.
          if (['auto'].includes(this.options.width as string) && this.content) {
            this.content.style.width = 'calc(100% - 6rem)';
          }
          this.shareUrl = this.options.video;
          this.downloadUrl = this.options.video;
          this.copyUrl = this.options.video;
          return content;
        }

        const video = document.createElement('video');
        video.setAttribute('src', this.options.video);
        video.innerText = 'Your browser does not support the video tag.';
        if (this.options.videoAutoplay) {
          video.setAttribute('autoplay', '');
        }
        wrapper.appendChild(video);
        content.appendChild(wrapper);
      }
      else {
        content.innerText = 'Video not found.';
      }
    }
    resolve(content);
    return content;
  }

  protected getContentIframe(resolve:(content:HTMLElement) => void):HTMLElement {
    const content = document.createElement('div');
    if (this.options.iframe) {
      const iframe = document.createElement('iframe');
      iframe.classList.add('neo-modal--iframe');
      iframe.setAttribute('webkitallowfullscreen', '');
      iframe.setAttribute('mozallowfullscreen', '');
      iframe.setAttribute('allowfullscreen', '');
      iframe.setAttribute('frameborder', '0');
      iframe.setAttribute('src', this.options.iframe);
      iframe.onload = () => {
        resolve(content);
      };
      content.appendChild(iframe);
    }
    return content;
  }

  protected buildHeader():HTMLElement|null {
    if (this.header) {
      this.header.remove();
      this.header = null;
    }
    if (this.options.header) {
      const header = document.createElement('div');
      header.classList.add('neo-modal--header');

      const headerStart = document.createElement('div');
      headerStart.classList.add('neo-modal--header-start');
      const headerCenter = document.createElement('div');
      headerCenter.classList.add('neo-modal--header-center');
      const headerEnd = document.createElement('div');
      headerEnd.classList.add('neo-modal--header-end');
      // We store these as they are needed for sizing.
      this.headerStartOut = document.createElement('div');
      this.headerStartOut.classList.add('neo-modal--header-start-out');
      this.headerStartOut.classList.add('neo-modal--header-out');
      this.headerEndOut = document.createElement('div');
      this.headerEndOut.classList.add('neo-modal--header-end-out');
      this.headerEndOut.classList.add('neo-modal--header-out');

      const label = this.buildLabel();
      if (label) {
        headerCenter.appendChild(label);
      }

      this.buildCloseButton();
      if (this.closeButton) {
        if (this.options.closeButton === 'start') {
          headerStart.appendChild(this.closeButton);
        }
        else if (this.options.closeButton === 'start-out') {
          this.headerStartOut.appendChild(this.closeButton);
        }
        else if (this.options.closeButton === 'end') {
          headerEnd.appendChild(this.closeButton);
        }
        else if (this.options.closeButton === 'end-out') {
          this.headerEndOut.appendChild(this.closeButton);
        }
      }

      const numeration = this.buildNumeration();
      if (numeration) {
        if (this.options.numerationPlacement === 'start') {
          headerStart.appendChild(numeration);
        }
        else if (this.options.numerationPlacement === 'end') {
          headerEnd.prepend(numeration);
        }
      }

      const hasHeaderStart = headerStart.childNodes.length > 0;
      const hasHeaderCenter = headerCenter.childNodes.length > 0;
      const hasHeaderEnd = headerEnd.childNodes.length > 0;
      if (hasHeaderStart) {
        header.appendChild(headerStart);
      }
      if (hasHeaderCenter) {
        header.appendChild(headerCenter);
      }
      if (hasHeaderEnd) {
        header.appendChild(headerEnd);
      }
      if (this.headerStartOut.childNodes.length > 0) {
        header.appendChild(this.headerStartOut);
      }
      else {
        this.headerStartOut = null;
      }
      if (this.headerEndOut.childNodes.length > 0) {
        header.appendChild(this.headerEndOut);
      }
      else {
        this.headerEndOut = null;
      }

      if (header.childNodes.length > 0) {
        this.header = header;
        if (this.options.headerInContent) {
          if (this.contentBlock) {
            this.contentBlock.prepend(this.header);
          }
        }
        else if (this.modal) {
          this.modal.appendChild(this.header);
          if (hasHeaderCenter && (hasHeaderStart || hasHeaderEnd)) {
            // We give the elements a moment to render.
            setTimeout(() => {
              if (hasHeaderStart && hasHeaderEnd) {
                const minWidth = Math.max(headerStart.offsetWidth, headerEnd.offsetWidth);
                headerStart.style.minWidth = minWidth + 'px';
                headerEnd.style.minWidth = minWidth + 'px';
              }
              else if (hasHeaderStart) {
                headerCenter.style.marginRight = headerStart.offsetWidth + 'px';
              }
              else {
                headerCenter.style.marginLeft = headerEnd.offsetWidth + 'px';
              }
            }, 100);
          }
        }
        return this.header;
      }
    }
    return null;
  }

  protected buildNumeration():HTMLElement|null {
    if (this.options.numeration) {
      const triggers = this.getGroupTriggers();
      if (triggers && triggers.length > 1) {
        const numeration = document.createElement('div');
        numeration.classList.add('neo-modal--numeration');
        triggers.forEach((trigger, index) => {
          if (numeration && trigger === this.options.trigger) {
            numeration.innerHTML = '<span>' + (index + 1) + '</span> / <span>' + triggers.length + '</span>';
          }
        });
        return numeration;
      }
    }
    return null;
  }

  protected buildFooter():HTMLElement|null {
    if (this.footer) {
      this.footer.remove();
      this.footer = null;
    }
    if (this.options.footer) {
      const footer = document.createElement('div');
      footer.classList.add('neo-modal--footer');

      const inner = document.createElement('div');
      inner.classList.add('neo-modal--footer-inner');

      if (this.options.shareLink && this.shareUrl && typeof navigator.canShare !== 'undefined') {
        inner.insertAdjacentHTML('beforeend', '<a class="neo-modal--tooltip neo-modal--share" data-tippy-theme="modal" data-tippy-content="Share" data-tippy-delay="[200, 0]" target="_blank" href="' + this.shareUrl + '" download>' + this.options.svgOpen + this.options.svgIconShare + this.options.svgClose + '</a>');
        const shareLink = inner.querySelector('.neo-modal--share') as HTMLElement;
        shareLink?.addEventListener('click', (e) => {
          e.preventDefault();
          const share:ShareData = {
            title: this.options.title || '',
            text: this.options.subtitle || '',
            url: this.shareUrl,
          };
          navigator.share(share);
        });
      }

      if (this.options.downloadLink && this.downloadUrl) {
        inner.insertAdjacentHTML('beforeend', '<a class="neo-modal--tooltip neo-modal--download" data-tippy-theme="modal" data-tippy-content="Download" data-tippy-delay="[200, 0]" target="_blank" href="' + this.downloadUrl + '" download>' + this.options.svgOpen + this.options.svgIconDownload + this.options.svgClose + '</a>');
      }

      if (this.options.copyLink && this.copyUrl) {
        const text = 'Copy';
        inner.insertAdjacentHTML('beforeend', '<a class="neo-modal--tooltip neo-modal--copy" data-tippy-theme="modal" data-tippy-content="' + text + '" data-tippy-delay="[200, 0]">'+ this.options.svgOpen + this.options.svgIconLink + this.options.svgClose +'</a>');
        const copyLink = inner.querySelector('.neo-modal--copy') as HTMLTippyElement;
        copyLink?.addEventListener('click', (e) => {
          e.preventDefault();
          const url = this.copyUrl.substring(0, 1) === '/' ? window.location.origin + this.copyUrl : this.copyUrl;
          navigator.clipboard.writeText(url).then(() => {
            if (copyLink.hasOwnProperty('_tippy')) {
              copyLink._tippy.setContent('Copied');
              copyLink._tippy.show();
              setTimeout(() => {
                copyLink._tippy.hide();
                setTimeout(() => {
                  copyLink._tippy.setContent(text);
                }, 1000);
              }, 1000);
            }
          });
        });
      }

      if (inner.childNodes.length > 0) {
        this.footer = footer;
        footer.appendChild(inner);
        if (this.modal) {
          this.modal.appendChild(this.footer);
        }
      }
    }
    return null;
  }

  protected buildContentFooter():HTMLElement|null {
    const footer = document.createElement('div');
    footer.classList.add('neo-modal--content-footer');

    if (this.contentInner && this.options.smartActions) {
      const actions = document.createElement('div');
      actions.classList.add('neo-modal--actions');

      const buttons:Array<HTMLElement> = [];

      const formActions = this.contentInner.querySelectorAll<HTMLElement>('.form-actions');
      if (formActions.length > 0) {
        const lastFormAction = formActions[formActions.length - 1] as HTMLElement;
        lastFormAction.style.display = 'none';
        lastFormAction.querySelectorAll('input, button, a').forEach((button) => {
          buttons.push(button as HTMLElement);
        });
      }
      else {
        this.contentInner.querySelectorAll('input[type=submit], button, .neo-modal--btn').forEach((button) => {
          buttons.push(button as HTMLElement);
        });
      }
      if (buttons.length) {
        buttons.forEach((button) => {
          button.style.display = 'none';
          const clone = document.createElement('button');
          clone.classList.add('neo-modal--btn');
          clone.classList.add('btn');
          if (button.classList.contains('button--primary')) {
            clone.classList.add('btn-primary');
          }
          clone.innerHTML = button.getAttribute('value') || button.innerText;
          clone.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            button.dispatchEvent(new Event('mousedown'));
            button.click();
            button.dispatchEvent(new Event('mouseup'));
          });
          actions.appendChild(clone);
        });
        footer.appendChild(actions);
      }
    }

    if (footer.childNodes.length > 0) {
      if (this.contentBlock) {
        this.contentBlock.append(footer);
      }
    }
    return null;
  }

  protected buildLabel():HTMLElement|null {
    const label = document.createElement('div');
    label.classList.add('neo-modal--label');

    if (this.options.title || this.options.subtitle) {
      const title = document.createElement('div');
      title.classList.add('neo-modal--title');

      if (this.options.title) {
        this.title = document.createElement('h2');
        this.title.innerHTML = '<span>' + this.options.title + '</span>';
        title.appendChild(this.title);
        label.classList.add('has-title');
      }

      if (this.options.subtitle) {
        this.subtitle = document.createElement('h5');
        this.subtitle.innerHTML = '<span>' + this.options.subtitle + '</span>';
        title.appendChild(this.subtitle);
        label.classList.add('has-subtitle');
      }

      label.appendChild(title);
    }

    if (this.options.icon) {
      this.icon = document.createElement('div');
      this.icon.classList.add('neo-modal--icon');
      const icon = document.createElement('i');
      if (this.options.iconClasses) {
        this.options.iconClasses.split(' ').forEach((className) => {
          icon.classList.add(className);
        });
      }
      icon.classList.add(this.options.icon);
      this.icon.appendChild(icon);
      label.classList.add('has-icon');
      label.prepend(this.icon);
    }

    return label.childNodes.length > 0 ? label : null;
  }

  protected buildCloseButton():HTMLElement|null {
    if (this.options.closeButton) {
      this.closeButton = document.createElement('a');
      this.closeButton.classList.add('neo-modal--close');
      this.closeButton.classList.add('neo-modal--tooltip');
      this.closeButton.setAttribute('href', '');
      this.closeButton.setAttribute('data-tippy-theme', 'modal');
      this.closeButton.setAttribute('data-tippy-content', 'Close');
      this.closeButton.setAttribute('data-tippy-placement', 'bottom');
      this.closeButton.setAttribute('data-tippy-delay', '500');
      this.closeButton.setAttribute('data-neo-modal-close', 'true');
      const inner = document.createElement('span');
      if (this.options.closeButtonClasses) {
        this.options.closeButtonClasses.split(' ').forEach((className) => {
          inner.classList.add(className);
        });
      }
      if (this.options.closeButtonSvg) {
        inner.innerHTML = this.options.closeButtonSvg;
      }
      // icon.classList.add(this.options.closeButton);
      this.closeButton.appendChild(inner);
    }
    return this.closeButton;
  }

  protected loaderTimeout:ReturnType<typeof setTimeout>|null = null;
  protected showLoader(delay?:number):void {
    delay = typeof delay === 'number' ? delay : 300;
    if (this.options.loader && !this.loading) {
      this.loading = true;
      if (typeof this.options.loader === 'function') {
        this.options.loader('in');
      }
      else {
        this.loaderTimeout = setTimeout(() => {
          if (this.wrapper) {
            if (typeof bodyScrollLock !== 'undefined') {
              bodyScrollLock.lock();
            }
            else {
              document.body.classList.add('has-neo-modal--loader');
            }
            this.loader = document.createElement('div');
            this.loader.classList.add('neo-modal--loader');
            this.loader.innerHTML = '<div><div class="neo-modal--spinner"><div></div><div></div></div></div>';
            if (this.options.loaderColor) {
              this.loader.style.setProperty('--modal-loader-color', this.options.loaderColor);
            }
            if (this.options.loaderColorBg) {
              this.loader.style.setProperty('--modal-loader-bg', this.options.loaderColorBg);
            }
            this.wrapper.appendChild(this.loader);
            this.animateIn(this.loader, 'loader');
          }
        }, delay || 0);
      }
    }
  }

  protected hideLoader():void {
    clearTimeout(this.loaderTimeout as ReturnType<typeof setTimeout>);
    if (this.loading && this.options.loader) {
      this.loading = false;
      if (typeof this.options.loader === 'function') {
        this.options.loader('out');
      }
      else if (this.loader) {
        if (typeof bodyScrollLock !== 'undefined') {
          bodyScrollLock.unlock();
        }
        else {
          document.body.classList.remove('has-neo-modal--loader');
        }
        this.animateOut(this.loader, 'loader', () => {
          this.loader?.remove();
          this.loader = null;
        });
      }
    }
  }

  protected toggle():void {
    if (this.isOpen) {
      this.close();
    }
    else {
      this.open();
    }
  }

  public open():void {
    this.isOpen = true;
    this.originalOptions = Object.assign({}, this.options);
    this.eventBeforeOpen.trigger(this);
    this.buildStack();
    this.build().then(() => {
      setTimeout(() => {
        this.doOpen();
      });
    });
  }

  protected doOpen():void {
    this.eventOpen.trigger(this);
    this.watchInterval = setInterval(this.watch.bind(this), 200);
    this.modal?.style.setProperty('visibility', '');
    this.modal?.style.setProperty('pointer-events', '');

    this.transitionBodyIn();

    // Nest other modals.
    const modals = document.querySelectorAll<HTMLElement>('.neo-modal');
    this.depth = modals.length;
    for (let i = 0; i < modals.length; i++) {
      const delta = modals.length - (i + 1);
      delta === 0 ? modals[i].removeAttribute('data-neo-modal--depth') : modals[i].setAttribute('data-neo-modal--depth', delta + '');
    }
    if (modals.length > 1) {
      const modal = modals[modals.length - 2] as neoModal.NeoModalElement;
      if (modal.neoModal) {
        modal.neoModal.focusIn();
      }
    }

    if (this.header) {
      if (!this.options.headerInContent) {
        console.log('wtf');
        this.animateIn(this.header, 'header');
      }
      else if (this.headerStartOut || this.headerEndOut) {
        if (this.headerStartOut) {
          this.animateIn(this.headerStartOut, 'header');
        }
        if (this.headerEndOut) {
          this.animateIn(this.headerEndOut, 'header');
        }
      }
      if (this.options.headerAnimate) {
        if (this.title) {
          this.animateIn(this.title, 'title');
        }
        if (this.subtitle) {
          this.animateIn(this.subtitle, 'subtitle');
        }
        if (this.icon) {
          this.animateIn(this.icon, 'icon');
        }
        if (this.closeButton) {
          this.animateIn(this.closeButton, 'closeButton');
        }
      }
    }
    if (this.prev) {
      this.animateIn(this.prev, 'navPrev');
    }
    if (this.next) {
      this.animateIn(this.next, 'navNext');
    }
    if (this.footer) {
      this.animateIn(this.footer, 'footer');
    }
    if (this.contentBlock) {
      this.animateIn(this.contentBlock, 'content', () => {
        this.finishOpen();
      });
    }
    else {
      this.finishOpen();
    }
  }

  protected finishOpen():void {
    this.eventAfterOpen.trigger(this);

    if (this.options.navKeyboard) {
      document.body.addEventListener('keydown', this.onKeyboardDown.bind(this));
      document.body.addEventListener('keyup', this.onKeyboardUp.bind(this));
    }

    if (this.options.fit) {
      document.body.addEventListener('mousemove', this.focusWatch.bind(this), false);
      this.focusWatch();
    }

    const focusableElements = 'a[href], button, input:not([type=hidden]), textarea, select, details, [tabindex]';
    const keyboardfocusableElement = this.contentInner?.querySelector<HTMLElement>(
      focusableElements
    ) || this.modal?.querySelector<HTMLElement>(
      focusableElements
    );
    if (keyboardfocusableElement) {
      keyboardfocusableElement.focus();
    }
    else if (this.options.trigger) {
      this.options.trigger.blur();
    }

    this.buildTooltips();
  }

  public close():void {
    this.isOpen = false;
    this.eventBeforeClose.trigger(this);
    clearInterval(this.watchInterval as ReturnType<typeof setInterval>);
    this.doClose().then(() => {
      this.finishClose();
    });

    if (this.options.trigger) {
      this.options.trigger.focus();
    }
  }

  protected doClose():Promise<void> {
    return new Promise((resolve) => {
      this.eventClose.trigger(this);

      this.transitionBodyOut();

      // Unnest other modals.
      const modals = document.querySelectorAll<HTMLElement>('.neo-modal');
      for (let i = 0; i < modals.length; i++) {
        const delta = modals.length - (i + 2);
        if (delta >= 0) {
          delta === 0 ? modals[i].removeAttribute('data-neo-modal--depth') : modals[i].setAttribute('data-neo-modal--depth', delta + '');
        }
      }
      if (modals.length - 1 > 0) {
        const modal = modals[modals.length - 2] as neoModal.NeoModalElement;
        if (modal.neoModal) {
          modal.neoModal.focusOut();
          modal.neoModal.buildStack();
        }
      }

      if (this.backdrop && this.depth === 1 && window.getComputedStyle(this.backdrop).display !== 'none') {
        this.animateOut(this.backdrop, 'backdrop');
      }
      if (this.header) {
        if (!this.options.headerInContent) {
          this.animateOut(this.header, 'header');
        }
        if (this.options.headerAnimate) {
          if (this.title) {
            this.animateOut(this.title, 'title');
          }
          if (this.subtitle) {
            this.animateOut(this.subtitle, 'title');
          }
          if (this.icon) {
            this.animateOut(this.icon, 'icon');
          }
          if (this.closeButton) {
            this.animateOut(this.closeButton, 'closeButton');
          }
        }
      }
      if (this.prev) {
        this.animateOut(this.prev, 'navPrev');
      }
      if (this.next) {
        this.animateOut(this.next, 'navNext');
      }
      if (this.footer) {
        this.animateOut(this.footer, 'footer');
      }
      if (this.contentBlock) {
        this.animateOut(this.contentBlock, 'content', () => {
          resolve();
        });
      }
      else {
        resolve();
      }
    });
  }

  protected finishClose():void {
    this.eventAfterClose.trigger(this);
    if (this.contentPlaceholder) {
      const content = this.contentInner?.querySelector('.neo-modal-template');
      if (content) {
        this.contentPlaceholder.parentNode?.replaceChild(content, this.contentPlaceholder);
      }
    }
    this.remove();
    this.removeWrapper();
    if (this.popper) {
      this.popper.destroy();
    }

    // Restore original options if modal has been rebuild.
    if (this.originalOptions) {
      this.options = this.originalOptions;
      this.originalOptions = null;
    }

    if (this.options.navKeyboard) {
      document.body.removeEventListener('keydown', this.onKeyboardDown);
      document.body.removeEventListener('keyup', this.onKeyboardUp);
    }
    document.body.removeEventListener('mousemove', this.focusWatch);
  }

  protected globalInit():void {
    document.body.classList.add('has-neo-modal');
    if (this.options.bodyLock) {
      if (typeof bodyScrollLock !== 'undefined') {
        bodyScrollLock.lock();
      }
      else {
        document.body.classList.add('neo-modal--body-lock');
      }
    }
  }

  protected globalDestroy():void {
    document.body.classList.remove('has-neo-modal');
    if (this.options.bodyLock) {
      if (typeof bodyScrollLock !== 'undefined') {
        bodyScrollLock.unlock();
      }
      else {
        document.body.classList.remove('neo-modal--body-lock');
      }
    }
  }

  // GENERIC GETTERS
  // --------------------------------------------------------------------------
  public getModal():HTMLElement|null {
    return this.modal;
  }

  public getContent():HTMLElement|null {
    return this.contentInner;
  }

  // HELPERS
  // --------------------------------------------------------------------------

  /**
   * Parse Youtube or Vimeo videos and get host & ID
   */
  protected parseVideo(url:string):{type:string, id:string}|null {
    let type:string = '';
    let vid:string = '';
    let match;
    let regYt = /(https?:\/\/)?((www\.)?(youtube(-nocookie)?|youtube.googleapis)\.com.*(v\/|v=|vi=|vi\/|e\/|embed\/|user\/.*\/u\/\d+\/)|youtu\.be\/)([_0-9a-z-]+)/i;
    match = url.match(regYt);
    if (match && match[7]) {
      type = 'youtube';
      vid = match[7];
    } else {
      let regVim = /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/;
      match = url.match(regVim);
      if (match && match[5]) {
        type = 'vimeo';
        vid = match[5];
      }
    }
    if (type && vid) {
      return {
        type: type,
        id: vid
      };
    }
    return null;
  }

  /**
   * Get additional url parameters
   */
  protected getUrlParameter(url:string) {
    let result = '';
    let sPageURL = decodeURIComponent(url);
    let firstsplit = sPageURL.split('?');
    if (firstsplit[1] !== undefined) {
      let sURLVariables = firstsplit[1].split('&');
      let sParameterName;
      let i;
      for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        result = result + '&'+ sParameterName[0]+'='+ sParameterName[1];
      }
    }
    return encodeURI(result);
  }

  // ANIMATIONS
  // --------------------------------------------------------------------------

  protected transitionBodyIn():void {
    const modals = document.querySelectorAll<HTMLElement>('.neo-modal');
    if (modals.length > 1) {
      return;
    }
    if (this.options.bodyTransitionScale || this.options.bodyTransitionBlur) {
      const body = document.querySelector(this.options.bodySelector);
      if (body) {
        body.classList.add('neo-modal--body-transition');
        setTimeout(() => {
          body.classList.add(...this.transitionBodyClasses());
        });
      }
    }
  }

  protected transitionBodyOut():void {
    const modals = document.querySelectorAll<HTMLElement>('.neo-modal');
    if (modals.length > 1) {
      return;
    }
    if (this.options.bodyTransitionScale || this.options.bodyTransitionBlur) {
      const body = document.querySelector(this.options.bodySelector);
      if (body) {
        body.classList.remove(...this.transitionBodyClasses());
        const parentCallback = () => {
          body.removeEventListener('transitionend', parentCallback);
          body.classList.remove('neo-modal--body-transition');
        };
        body.addEventListener('transitionend', parentCallback);
      }
    }
  }

  protected transitionBodyClasses():Array<string> {
    const classes = [];
    if (this.options.bodyTransitionScale) {
      classes.push('neo-modal--body-scale');
    }
    if (this.options.bodyTransitionBlur) {
      classes.push('neo-modal--body-blur');
    }
    return classes;
  }

  protected animate(el:HTMLElement, key:string, direction:neoModal.Movement, callback?:Function|null, speed?:string):void {
    const op = direction.charAt(0).toUpperCase() + direction.slice(1);
    const animationKey = key + 'Animate' + op as keyof neoModal.NeoModalOptions;
    if (typeof this.options[animationKey] === 'string') {
      const animation = this.options[animationKey];
      const animationSpeed = key + 'Animate' + op + 'Speed' as keyof neoModal.NeoModalOptions;
      const animationDelay = key + 'Animate' + op + 'Delay' as keyof neoModal.NeoModalOptions;
      const parentCallback = () => {
        el.removeEventListener('animationend', parentCallback);
        el.removeEventListener('animationcancel', parentCallback);
        el.classList.remove('neo-animate--animated');
        el.classList.remove('neo-animate--' + animation);
        if (typeof this.options[animationSpeed] === 'string') {
          el.classList.remove('neo-animate--' + this.options[animationSpeed]);
        }
        if (typeof this.options[animationDelay] === 'string') {
          el.classList.remove('neo-animate--delay-' + this.options[animationDelay]);
        }
        if (direction === 'out') {
          el.style.display = 'none';
        }
        if (callback) {
          callback();
        }
      };
      el.addEventListener('animationend', parentCallback);
      el.addEventListener('animationcancel', parentCallback);
      el.style.display = '';
      el.classList.add('neo-animate--animated');
      el.classList.add('neo-animate--' + animation);
      if (speed || typeof this.options[animationSpeed] === 'string') {
        el.classList.add('neo-animate--' + (speed || this.options[animationSpeed]));
      }
      if (typeof this.options[animationDelay] === 'string') {
        el.classList.add('neo-animate--delay-' + this.options[animationDelay]);
      }
    }
    else if (callback) {
      callback();
    }
  }

  protected animateIn(el:HTMLElement, key:string, callback?:Function|null, speed?:string):void {
    this.animate(el, key, 'in', callback, speed);
  }

  protected animateOut(el:HTMLElement, key:string, callback?:Function|null, speed?:string):void {
    this.animate(el, key, 'out', callback, speed);
  }

  // UTILITIES
  // --------------------------------------------------------------------------

  protected getAbsoluteHeight(el:HTMLElement|string) {
    // Get the DOM Node if you pass in a string
    el = (typeof el === 'string') ? document.querySelector(el) as HTMLElement : el;

    var styles = window.getComputedStyle(el);
    var margin = parseFloat(styles['marginTop']) +
                 parseFloat(styles['marginBottom']);

    return Math.ceil(el.offsetHeight + margin);
  }

}

window.NeoModal = NeoModal;

export {NeoModal};

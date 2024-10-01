var g = Object.defineProperty;
var v = (c, t, e) => t in c ? g(c, t, { enumerable: !0, configurable: !0, writable: !0, value: e }) : c[t] = e;
var s = (c, t, e) => (v(c, typeof t != "symbol" ? t + "" : t, e), e);
class h {
  constructor() {
    s(this, "handlers", []);
  }
  on(t) {
    this.handlers.push(t);
  }
  off(t) {
    this.handlers = this.handlers.filter((e) => e !== t);
  }
  trigger(t, e) {
    this.handlers.slice(0).forEach((i) => i(t, e));
  }
  expose() {
    return this;
  }
}
const u = class u {
  /**
   * Construct.
   */
  constructor(t) {
    s(this, "options");
    s(this, "originalOptions", null);
    s(this, "eventMap", /* @__PURE__ */ new Map([
      ["onSettings", () => this.eventSettings.expose()],
      ["onContentLoaded", () => this.eventContentLoaded.expose()],
      ["onBeforeOpen", () => this.eventBeforeOpen.expose()],
      ["onOpen", () => this.eventOpen.expose()],
      ["onAfterOpen", () => this.eventAfterOpen.expose()],
      ["onBeforeClose", () => this.eventBeforeClose.expose()],
      ["onClose", () => this.eventClose.expose()],
      ["onAfterClose", () => this.eventAfterClose.expose()],
      ["onBeforeNext", () => this.eventBeforeNext.expose()],
      ["onNext", () => this.eventNext.expose()],
      ["onAfterNext", () => this.eventAfterNext.expose()],
      ["onBeforePrev", () => this.eventBeforePrev.expose()],
      ["onPrev", () => this.eventPrev.expose()],
      ["onAfterPrev", () => this.eventAfterPrev.expose()]
    ]));
    // Options that support declaration via [data-neo-modal-*].
    s(this, "optionsAsAttributes", [
      "appendTo",
      "appendToClosest",
      "wrapperClasses",
      "modalClasses",
      "colorScheme",
      "width",
      "height",
      "zIndex",
      "displaceTop",
      "displaceRight",
      "displaceBottom",
      "displaceLeft",
      "image",
      "video",
      "iframe",
      "group",
      "smartActions",
      "numeration",
      "fit",
      "nest",
      "drag",
      "bodyLock",
      "backdrop",
      "header",
      "headerInContent",
      "footer",
      "downloadLink",
      "shareLink",
      "copyLink",
      "closeButton",
      "contentScroll",
      "contentPadding",
      "title",
      "subtitle",
      "icon",
      "placement",
      "attach",
      "attachPlacement",
      "bodyTransitionScale",
      "bodyTransitionBlur",
      "contentAnimateIn",
      "contentAnimateInSpeed",
      "contentAnimateInDelay",
      "contentAnimateOut",
      "contentAnimateOutSpeed",
      "contentAnimateOutDelay"
    ]);
    s(this, "optionsAsMerge", [
      "title",
      "subtitle",
      "icon",
      "image",
      "video",
      "iframe",
      "content",
      "trigger"
    ]);
    // Options that, when boolean, should be set to default.
    s(this, "optionsFromBoolToDefault", [
      "icon"
    ]);
    s(this, "loader", null);
    s(this, "loading", !1);
    s(this, "wrapper", null);
    s(this, "modal", null);
    s(this, "backdrop", null);
    s(this, "groupTriggers", null);
    s(this, "container", null);
    s(this, "content", null);
    s(this, "contentBlock", null);
    s(this, "contentWrapper", null);
    s(this, "contentInner", null);
    s(this, "contentPlaceholder", null);
    s(this, "header", null);
    s(this, "headerStartOut", null);
    s(this, "headerEndOut", null);
    s(this, "footer", null);
    s(this, "title", null);
    s(this, "subtitle", null);
    s(this, "icon", null);
    s(this, "closeButton", null);
    s(this, "next", null);
    s(this, "prev", null);
    s(this, "shareUrl", "");
    s(this, "downloadUrl", "");
    s(this, "copyUrl", "");
    s(this, "depth", 0);
    s(this, "isOpen", !1);
    s(this, "isBuilt", !1);
    s(this, "canZoom", !1);
    s(this, "canClickContent", !0);
    s(this, "throttle", null);
    s(this, "watchInterval", null);
    s(this, "popper", null);
    s(this, "eventSettings", new h());
    s(this, "eventBeforeOpen", new h());
    s(this, "eventOpen", new h());
    s(this, "eventAfterOpen", new h());
    s(this, "eventBeforeClose", new h());
    s(this, "eventClose", new h());
    s(this, "eventAfterClose", new h());
    s(this, "eventBeforeNext", new h());
    s(this, "eventNext", new h());
    s(this, "eventAfterNext", new h());
    s(this, "eventBeforePrev", new h());
    s(this, "eventPrev", new h());
    s(this, "eventAfterPrev", new h());
    s(this, "eventContentLoaded", new h());
    s(this, "focused", !1);
    s(this, "focusing", !1);
    s(this, "focusTimeout", null);
    s(this, "currentZoom", 1);
    s(this, "minZoom", 1);
    s(this, "maxZoom", 3);
    s(this, "stepSize", 1);
    s(this, "zooming", !1);
    s(this, "dragStartX", 0);
    s(this, "dragStartY", 0);
    s(this, "dragEndX", 0);
    s(this, "dragEndY", 0);
    s(this, "dragThreshold", 50);
    s(this, "dragging", !1);
    s(this, "loaderTimeout", null);
    for (let e in u.colorDefaults)
      this.optionsAsAttributes.push(e);
    this.options = this.buildOptions(t), this.options.trigger instanceof HTMLElement && (this.options.trigger.neoModalOptions = this.options), this.buildTrigger();
  }
  static setDefaultOptions(t) {
    this.defaults = Object.assign({}, this.defaults, t);
  }
  static getTop() {
    const t = document.querySelector(".neo-modal:last-child");
    return t && t.neoModal ? t.neoModal : null;
  }
  static closeTop() {
    const t = this.getTop();
    t && t.close();
  }
  /**
   * Get an event by name.
   */
  event(t) {
    if (this.eventMap.has(t)) {
      const e = this.eventMap.get(t);
      if (e)
        return e();
    }
    return null;
  }
  buildOptions(t) {
    t = Object.assign({}, u.defaults, t);
    const e = {};
    if (this.optionsFromBoolToDefault.forEach((o) => {
      const n = t[o];
      typeof n == "boolean" && n === !0 && (e[o] = n);
    }), t.trigger instanceof HTMLElement) {
      const o = t.trigger;
      this.optionsAsAttributes.forEach((n) => {
        const a = "data-neo-modal-" + n;
        if (o.hasAttribute(a)) {
          let l = o.getAttribute(a);
          typeof l == "string" && (l === "true" && (l = !0), l === "false" && (l = !1)), e[n] = l;
        }
      });
    }
    const i = Object.assign({}, t, e);
    return this.eventMap.forEach((o, n) => {
      typeof i[n] == "function" && o().on(i[n]);
    }), this.eventSettings.trigger(this, i), i;
  }
  mergeOptions(t) {
    const e = {};
    return this.optionsAsMerge.forEach((i) => {
      typeof this.options[i] < "u" && typeof t[i] < "u" && (e[i] = t[i]);
    }), Object.assign({}, this.options, e);
  }
  buildTrigger() {
    this.options.trigger instanceof HTMLElement && this.options.trigger.addEventListener("click", (e) => {
      e.preventDefault(), this.toggle();
    });
  }
  /**
   * Build.
   */
  build() {
    return new Promise((t) => {
      var e;
      if (this.isBuilt)
        t();
      else if (this.wrapper) {
        this.isBuilt = !0, this.modal = document.createElement("div"), this.modal.classList.add("neo-modal"), this.options.modalClasses && this.options.modalClasses.split(" ").forEach((o) => {
          var n;
          (n = this.modal) == null || n.classList.add(o);
        }), this.options.colorScheme && ((e = this.modal) == null || e.classList.add(this.options.colorScheme)), this.modal.neoModal = this, this.modal.setAttribute("role", "dialog"), this.modal.setAttribute("aria-modal", "true"), this.modal.style.setProperty("visibility", "hidden"), this.modal.style.setProperty("pointer-events", "none"), this.wrapper.appendChild(this.modal);
        const i = this.buildContainer();
        this.modal.appendChild(i), this.buildContent(i).then(() => {
          this.modal && this.buildNavigation(this.modal), this.buildModal(), this.attachModal(), t();
        });
      } else
        t();
    });
  }
  /**
   * Rebuild modal with new options.
   */
  rebuild(t) {
    return new Promise((e) => {
      this.modal && this.container ? (this.container.style.setProperty("visibility", "hidden"), this.canClickContent = !0, this.canZoom = !1, this.currentZoom = 1, this.shareUrl = "", this.copyUrl = "", this.downloadUrl = "", this.options = this.mergeOptions(t), this.buildContent(this.container).then(() => {
        this.buildModal(), this.buildTooltips(), e();
      })) : e();
    });
  }
  /**
   * Build modal.
   */
  buildModal() {
    this.buildHeader(), this.buildFooter(), this.modal && this.modal.querySelectorAll("[data-neo-modal-close]").forEach((t) => {
      t.addEventListener("click", (e) => {
        this.close(), e.preventDefault(), e.stopPropagation();
      });
    }), this.setAttributes(), this.size();
  }
  buildTooltips() {
    this.modal && this.modal.querySelectorAll(".neo-modal--tooltip:not(.neo-modal--tooltip-processed)").forEach((t) => {
      t.classList.add("neo-modal--tooltip-processed"), tippy(t, { touch: !1 });
    });
  }
  attachModal() {
    if (this.options.attach) {
      const t = this.options.attach;
      let e = null;
      t instanceof HTMLElement ? e = t : typeof this.options.attach == "string" && (e = document.querySelector(this.options.attach)), e && this.content && (this.popper = Popper.createPopper(e, this.content, {
        placement: this.options.attachPlacement
      }));
    }
  }
  setAttributes() {
    if (this.modal) {
      this.options.contentScroll ? this.modal.classList.add("neo-modal--content-scroll") : this.modal.classList.add("neo-modal--global-scroll");
      for (let t in u.colorDefaults)
        if (this.options[t]) {
          const e = this.options[t], i = t.replace(/[A-Z]/g, (o) => "-" + o.toLowerCase()).replace("color-bg", "bg");
          this.modal.style.setProperty("--modal-" + i, e);
        }
      if (this.header && (this.modal.classList.add("neo-modal--header-" + (this.options.headerInContent ? "content" : "global")), this.modal.style.setProperty("--modal-padding-t", this.getAbsoluteHeight(this.header) + "px")), this.options.contentPadding && (typeof this.options.contentPadding == "string" ? (/^\d+$/.test(this.options.contentPadding) && (this.options.contentPadding += "px"), this.modal.style.setProperty("--modal-content-padding", this.options.contentPadding)) : typeof this.options.contentPadding == "number" && this.modal.style.setProperty("--modal-content-padding", this.options.contentPadding + "px")), this.content) {
        if (this.options.attach && this.modal.classList.add("neo-modal--attach"), this.options.fit && this.modal.classList.add("neo-modal--fit"), this.canClickContent ? this.modal.classList.remove("neo-modal--no-click") : this.modal.classList.add("neo-modal--no-click"), this.options.width)
          if (["auto", "full"].includes(this.options.width))
            this.modal.classList.add("neo-modal--width-" + this.options.width);
          else {
            let t = this.options.width;
            typeof this.options.width == "string" && /^\d+$/.test(this.options.width) && (t += "px"), this.content.style.width = t + "";
          }
        if (this.options.height)
          if (["auto", "full"].includes(this.options.height))
            this.modal.classList.add("neo-modal--height-" + this.options.height);
          else {
            let t = this.options.height;
            typeof this.options.height == "string" ? /^\d+$/.test(this.options.height) && (t += "px") : t += "px", this.content.style.minHeight = t + "";
          }
      }
    }
  }
  size() {
    if (this.container && this.modal && (this.options.placement && !this.options.attach && this.modal.classList.remove("neo-modal--placement-" + this.options.placement), !this.options.fit && this.container.scrollHeight > this.container.clientHeight ? (this.modal.classList.remove("neo-modal--no-scroll"), this.modal.classList.add("neo-modal--scroll")) : (this.modal.classList.remove("neo-modal--scroll"), this.modal.classList.add("neo-modal--no-scroll")), this.options.placement && !this.options.attach && this.modal.classList.add("neo-modal--placement-" + this.options.placement), this.content)) {
      let t = this.content.clientWidth;
      this.headerStartOut && this.headerEndOut ? t += this.headerStartOut.clientWidth + this.headerEndOut.clientWidth : this.headerStartOut ? t += this.headerStartOut.clientWidth * 2 : this.headerEndOut && (t += this.headerEndOut.clientWidth * 2), this.container.clientWidth <= t ? this.modal.classList.contains("neo-modal--flush") || this.modal.classList.add("neo-modal--flush") : this.modal.classList.contains("neo-modal--flush") && this.modal.classList.remove("neo-modal--flush");
    }
  }
  watch() {
    this.size();
  }
  onKeyboardDown(t) {
    this.options.fit && !this.focused && this.focusWatch(), t.key === "Escape" && document.querySelector(".neo-modal:last-child") === this.modal && this.close(), this.throttle || (t.key === "ArrowRight" && this.navigate("next"), t.key === "ArrowLeft" && this.navigate("prev"), this.throttle = setTimeout(() => {
      this.throttle = null;
    }, 100));
  }
  onKeyboardUp() {
    this.throttle && (clearTimeout(this.throttle), this.throttle = null);
  }
  focusWatch() {
    this.focused && this.focusOut(), clearTimeout(this.focusTimeout), this.focusTimeout = setTimeout(() => {
      this.focusIn();
    }, 3e3);
  }
  focusIn() {
    if (this.focusing)
      return;
    this.modal && this.modal.classList.add("neo-modal--focus"), this.focusing = !0;
    let t = () => {
      this.focusing = !1;
    };
    this.header && (this.options.headerInContent || (this.animateOut(this.header, "header", t, "default"), t = null)), this.prev && (this.animateOut(this.prev, "navPrev", t, "default"), t = null), this.next && (this.animateOut(this.next, "navNext", t, "default"), t = null), this.footer && (this.animateOut(this.footer, "footer", t, "default"), t = null), this.focused = !0;
  }
  focusOut() {
    this.focusing || (this.modal && this.modal.classList.remove("neo-modal--focus"), this.header && (this.options.headerInContent || this.animateIn(this.header, "header")), this.prev && this.animateIn(this.prev, "navPrev"), this.next && this.animateIn(this.next, "navNext"), this.footer && this.animateIn(this.footer, "footer"), this.focused = !1);
  }
  zoom(t) {
    if (this.contentInner) {
      if (this.zooming === !0)
        return;
      this.zooming = !0, setTimeout(() => {
        this.zooming = !1;
      }, 500);
      let e = this.currentZoom + t * this.stepSize;
      if (e < this.minZoom || e > this.maxZoom)
        return;
      const o = "(" + "neo-modal--zoom-" + "(\\s|(-)?(\\w*)(\\s)?)).*?", n = new RegExp(o, "g");
      this.contentInner.className = this.contentInner.className.replace(n, ""), this.contentInner.classList.add("neo-modal--zoom-" + this.currentZoom + "to" + e), this.currentZoom = e;
    }
  }
  getGroupTriggers() {
    return this.options.group && (this.groupTriggers || (this.groupTriggers = document.querySelectorAll('[data-neo-modal-group="' + this.options.group + '"]'))), this.groupTriggers;
  }
  getToTrigger(t) {
    const e = this.getGroupTriggers();
    if (e && e.length > 1) {
      let i = null;
      return e.forEach((o, n) => {
        o === this.options.trigger && (t === "next" && (n < e.length - 1 ? i = e[n + 1] : i = e[0]), t === "prev" && (n > 0 ? i = e[n - 1] : i = e[e.length - 1]));
      }), i;
    }
    return null;
  }
  navigate(t) {
    const e = this.getGroupTriggers();
    if (this.contentBlock && e && e.length > 1) {
      const i = this.getToTrigger(t);
      i && (this.showLoader(), t === "prev" && this.eventBeforePrev.trigger(this), t === "next" && this.eventBeforeNext.trigger(this), !this.focused && this.header && !this.options.headerInContent && this.animateOut(this.header, "header", null, "default"), !this.focused && this.footer && this.animateOut(this.footer, "footer", null, "default"), this.animateOut(this.contentBlock, t, () => {
        t === "prev" && this.eventPrev.trigger(this), t === "next" && this.eventNext.trigger(this), this.rebuild(i.neoModalOptions).then(() => {
          var o, n;
          this.contentBlock && (t === "prev" && this.eventAfterPrev.trigger(this), t === "next" && this.eventAfterNext.trigger(this), this.header && !this.options.headerInContent && (this.focused ? this.header.style.display = "none" : this.animateIn(this.header, "header")), this.footer && (this.focused ? this.footer.style.display = "none" : this.animateIn(this.footer, "footer")), (o = this.modal) == null || o.style.setProperty("visibility", ""), (n = this.container) == null || n.style.setProperty("visibility", ""), this.animateIn(this.contentBlock, t), t === "prev" && this.eventPrev.trigger(this), t === "next" && this.eventNext.trigger(this));
        });
      }));
    }
  }
  remove() {
    this.modal && (this.modal.remove(), this.modal = null, this.isBuilt = !1);
  }
  /**
   * Build the global stack.
   *
   * This may be called multiple times, but will only build the stack once.
   */
  buildStack() {
    this.buildWrapper();
    const t = this.buildBackdrop();
    if (this.wrapper && (this.wrapper.className = "neo-modals", this.wrapper.removeAttribute("style"), this.options.wrapperClasses && this.options.wrapperClasses.split(" ").forEach((e) => {
      var i;
      (i = this.wrapper) == null || i.classList.add(e);
    }), ["top", "right", "bottom", "left"].forEach((e) => {
      const i = "displace" + e.charAt(0).toUpperCase() + e.slice(1);
      this.wrapper && typeof this.options[i] == "string" && this.wrapper.style.setProperty("--modal-displace-" + e, this.options[i]);
    }), this.options.zIndex && this.wrapper.style.setProperty("--modal-z-index", this.options.zIndex + ""), this.options.backdropColorBg && this.wrapper.style.setProperty("--modal-backdrop-bg", this.options.backdropColorBg), this.options.colorScheme && this.wrapper.classList.add(this.options.colorScheme)), this.backdrop && (this.backdrop.className = "neo-modal--backdrop", this.backdrop.removeAttribute("style"), this.options.backdropClasses && this.options.backdropClasses.split(" ").forEach((e) => {
      var i;
      (i = this.backdrop) == null || i.classList.add(e);
    })), t)
      this.wrapper && this.backdrop && (this.options.backdrop && this.backdrop.style.setProperty("visibility", ""), this.wrapper.appendChild(this.backdrop), this.animateIn(this.backdrop, "backdrop"));
    else if (this.backdrop) {
      const e = window.getComputedStyle(this.backdrop).display !== "none";
      this.options.backdrop && !e ? this.animateIn(this.backdrop, "backdrop") : !this.options.backdrop && e && this.animateOut(this.backdrop, "backdrop");
    }
  }
  buildWrapper() {
    if (!this.wrapper && (this.wrapper = document.querySelector(".neo-modals"), !this.wrapper)) {
      this.wrapper = document.createElement("div"), this.globalInit();
      let t = document.body;
      this.options.appendToClosest && this.options.trigger ? t = this.options.trigger.closest(this.options.appendToClosest) : this.options.appendTo && (typeof this.options.appendTo == "string" ? t = document.querySelector(this.options.appendTo) : t = this.options.appendTo), t.appendChild(this.wrapper);
    }
    return this.wrapper;
  }
  removeWrapper() {
    this.wrapper && this.wrapper.querySelectorAll(".neo-modal").length === 0 && (this.wrapper.remove(), this.wrapper = null, this.globalDestroy());
  }
  buildBackdrop() {
    let t = !1;
    return this.backdrop = document.querySelector(".neo-modal--backdrop"), this.backdrop || (t = !0, this.backdrop = document.createElement("div"), this.backdrop.className = "neo-modal--backdrop"), t;
  }
  buildContainer() {
    return this.container && this.container.remove(), this.container = document.createElement("div"), this.container.classList.add("neo-modal--container"), this.container.addEventListener("click", (t) => {
      const e = t.target;
      this.options.backdropClose && e.classList.contains("neo-modal--container") && this.close();
    }), this.container;
  }
  buildNavigation(t) {
    var e, i;
    if (this.options.group) {
      const o = this.getGroupTriggers();
      o && o.length > 1 && (this.prev = document.createElement("div"), this.prev.classList.add("neo-modal--nav"), this.prev.classList.add("neo-modal--prev"), this.prev.innerHTML = `<a class="neo-modal--tooltip" data-tippy-theme="modal" data-tippy-content="Prev" data-tippy-placement="right" data-tippy-delay="[500, 0]"><span>${this.options.navPrevLabel}</span></a>`, (e = this.prev.querySelector("a")) == null || e.addEventListener("click", (n) => {
        n.preventDefault(), this.navigate("prev");
      }), t.appendChild(this.prev), this.next = document.createElement("div"), this.next.classList.add("neo-modal--nav"), this.next.classList.add("neo-modal--next"), this.next.innerHTML = `<a class="neo-modal--tooltip" data-tippy-theme="modal" data-tippy-content="Next" data-tippy-placement="left" data-tippy-delay="[500, 0]"><span>${this.options.navNextLabel}</span></a>`, (i = this.next.querySelector("a")) == null || i.addEventListener("click", (n) => {
        n.preventDefault(), this.navigate("next");
      }), t.appendChild(this.next), this.buildDrag());
    }
    return null;
  }
  buildDrag() {
    this.modal && (this.modal.classList.add("neo-modal--draggable"), this.modal.addEventListener("touchstart", this.onDragStart.bind(this), !1), this.modal.addEventListener("touchend", this.onDragEnd.bind(this), !1), this.modal.addEventListener("touchmove", this.onDrag.bind(this), !1), this.modal.addEventListener("mousedown", this.onDragStart.bind(this), !1), this.modal.addEventListener("mouseup", this.onDragEnd.bind(this), !1), this.modal.addEventListener("mouseout", this.onDragEnd.bind(this), !1), this.modal.addEventListener("mousemove", this.onDrag.bind(this), !1));
  }
  onDragStart(t) {
    this.contentWrapper && (this.dragging = !0, t.type === "touchmove" ? (t = t, this.dragStartX = this.dragEndX = t.touches[0].pageX, this.dragStartY = this.dragEndY = t.touches[0].pageY) : (t = t, this.dragStartX = this.dragEndX = t.pageX, this.dragStartY = this.dragEndY = t.pageY));
  }
  onDragEnd() {
    if (this.dragging && (this.dragging = !1, this.contentWrapper)) {
      let t = null;
      const e = this.dragEndX - this.dragStartX;
      e < 0 && (t = "next"), e > 0 && (t = "prev"), Math.abs(e) > this.dragThreshold && t ? this.navigate(t) : (this.contentWrapper.style.transform = "", this.contentWrapper.style.opacity = "");
    }
  }
  onDrag(t) {
    if (this.dragging && this.contentWrapper) {
      t.type === "touchmove" ? (t = t, this.dragEndX = t.touches[0].pageX, this.dragEndY = t.touches[0].pageY) : (t = t, this.dragEndX = t.pageX, this.dragEndY = t.pageY);
      const e = this.dragEndX - this.dragStartX, i = this.dragEndY - this.dragStartY, o = Math.abs(e), n = Math.abs(i);
      if (o > n && o <= 180) {
        const a = Math.min(1, (1 - o / 180) * 1.5);
        t.preventDefault(), this.contentWrapper.style.transform = "translateX(" + e + "px)", this.contentWrapper.style.opacity = a + "";
      }
    }
  }
  buildContent(t) {
    return new Promise((e) => {
      this.container || e(), this.content && (this.content.remove(), this.content = null), this.showLoader(), this.content = document.createElement("div"), this.content.classList.add("neo-modal--content--container"), this.contentBlock = document.createElement("div"), this.contentBlock.classList.add("neo-modal--content-block"), this.content.appendChild(this.contentBlock), t.appendChild(this.content), this.buildContentByType(this.contentBlock).then(() => {
        this.eventContentLoaded.trigger(this), this.hideLoader(), this.bindContentEvents(), this.buildContentFooter(), e();
      });
    });
  }
  refreshContent() {
    var t;
    this.contentBlock && ((t = this.contentBlock.querySelector(".neo-modal--content-footer")) == null || t.remove()), this.buildContentFooter();
  }
  bindContentEvents() {
    this.contentInner && this.canZoom && (this.contentInner.classList.add("neo-modal--zoom"), this.contentInner.addEventListener("wheel", (t) => {
      t.preventDefault();
      const e = t.deltaY > 0 ? -1 : 1;
      this.zoom(e);
    }, !1), this.contentInner.addEventListener("dblclick", () => {
      const t = this.currentZoom === 1 ? 1 : -1;
      this.zoom(t);
    }));
  }
  buildContentByType(t) {
    return new Promise((e) => {
      this.contentWrapper = document.createElement("div"), this.contentWrapper.classList.add("neo-modal--content-wrapper"), this.options.image ? this.contentInner = this.getContentImage(e) : this.options.video ? this.contentInner = this.getContentVideo(e) : this.options.iframe ? this.contentInner = this.getContentIframe(e) : this.contentInner = this.getContentDefault(e), this.contentInner ? (this.contentInner.classList.add("neo-modal--content"), this.options.colorSchemeInherit || this.contentInner.classList.add("scheme--reset"), this.contentWrapper.appendChild(this.contentInner), t.appendChild(this.contentWrapper)) : t.appendChild(this.contentWrapper);
    });
  }
  getContentDefault(t) {
    var i;
    let e = document.createElement("div");
    if (e.classList.add("neo-modal--content-missing"), e.innerText = "Content not found.", typeof this.options.content == "function") {
      if (e = document.createElement("div"), this.options.trigger) {
        const o = this.options.content(this.options.trigger);
        o instanceof HTMLElement ? (this.contentPlaceholder = document.createElement("template"), this.contentPlaceholder.classList.add("neo-modal--content-placeholder"), (i = o.parentNode) == null || i.insertBefore(this.contentPlaceholder, o), e.appendChild(o)) : e.innerHTML = o;
      }
    } else
      typeof this.options.content == "string" ? (e = document.createElement("div"), e.innerHTML = this.options.content) : this.contentInner = this.options.content;
    if (e.childElementCount) {
      const o = e.firstElementChild;
      if (o)
        switch (o.tagName) {
          case "IMAGE":
            const n = o;
            return n.onload = () => {
              t(e);
            }, this.shareUrl = n.src, this.downloadUrl = n.src, this.copyUrl = n.src, this.canZoom = this.options.fit, e;
          case "PICTURE":
            const a = o.querySelector("img");
            if (a)
              return a.onload = () => {
                this.shareUrl = a.currentSrc, this.downloadUrl = a.currentSrc, this.copyUrl = a.currentSrc, this.canZoom = this.options.fit, t(e);
              }, this.options.group && a.addEventListener("dragstart", function(l) {
                l.preventDefault();
              }), e;
            break;
        }
    }
    return t(e), e;
  }
  getContentImage(t) {
    const e = this.options.image || "", i = document.createElement("div"), o = document.createElement("img");
    return this.canZoom = this.options.fit, o.onload = () => {
      t(i);
    }, this.options.group && o.addEventListener("dragstart", function(n) {
      n.preventDefault();
    }), o.src = e, i.appendChild(o), this.shareUrl = e, this.downloadUrl = e, this.copyUrl = e, i;
  }
  getContentVideo(t) {
    const e = document.createElement("div");
    if (this.options.video) {
      const i = this.parseVideo(this.options.video);
      if (i) {
        const o = document.createElement("div");
        o.classList.add("neo-modal--video"), o.classList.add("neo-modal--ratio"), o.classList.add("neo-modal--ratio-" + this.options.videoRatio), o.classList.add("bg-black"), this.canClickContent = !1;
        let n;
        if (i.type == "vimeo" || i.type == "youtube") {
          let l;
          n = this.options.videoAutoplay ? "?rel=0&autoplay=1" : "?rel=0";
          let p = n + this.getUrlParameter(this.options.video);
          i.type == "vimeo" ? l = "https://player.vimeo.com/video/" : i.type == "youtube" && (l = "https://www.youtube-nocookie.com/embed/");
          const r = document.createElement("iframe");
          return r.onload = () => {
            t(e);
          }, r.classList.add("neo-modal--iframe"), r.setAttribute("webkitallowfullscreen", ""), r.setAttribute("mozallowfullscreen", ""), r.setAttribute("allowfullscreen", ""), r.setAttribute("allow", "autoplay"), r.setAttribute("frameborder", "0"), r.setAttribute("src", l + i.id + p), o.appendChild(r), e.appendChild(o), ["auto"].includes(this.options.width) && this.content && (this.content.style.width = "calc(100% - 6rem)"), this.shareUrl = this.options.video, this.downloadUrl = this.options.video, this.copyUrl = this.options.video, e;
        }
        const a = document.createElement("video");
        a.setAttribute("src", this.options.video), a.innerText = "Your browser does not support the video tag.", this.options.videoAutoplay && a.setAttribute("autoplay", ""), o.appendChild(a), e.appendChild(o);
      } else
        e.innerText = "Video not found.";
    }
    return t(e), e;
  }
  getContentIframe(t) {
    const e = document.createElement("div");
    if (this.options.iframe) {
      const i = document.createElement("iframe");
      i.classList.add("neo-modal--iframe"), i.setAttribute("webkitallowfullscreen", ""), i.setAttribute("mozallowfullscreen", ""), i.setAttribute("allowfullscreen", ""), i.setAttribute("frameborder", "0"), i.setAttribute("src", this.options.iframe), i.onload = () => {
        t(e);
      }, e.appendChild(i);
    }
    return e;
  }
  buildHeader() {
    if (this.header && (this.header.remove(), this.header = null), this.options.header) {
      const t = document.createElement("div");
      t.classList.add("neo-modal--header");
      const e = document.createElement("div");
      e.classList.add("neo-modal--header-start");
      const i = document.createElement("div");
      i.classList.add("neo-modal--header-center");
      const o = document.createElement("div");
      o.classList.add("neo-modal--header-end"), this.headerStartOut = document.createElement("div"), this.headerStartOut.classList.add("neo-modal--header-start-out"), this.headerStartOut.classList.add("neo-modal--header-out"), this.headerEndOut = document.createElement("div"), this.headerEndOut.classList.add("neo-modal--header-end-out"), this.headerEndOut.classList.add("neo-modal--header-out");
      const n = this.buildLabel();
      n && i.appendChild(n), this.buildCloseButton(), this.closeButton && (this.options.closeButton === "start" ? e.appendChild(this.closeButton) : this.options.closeButton === "start-out" ? this.headerStartOut.appendChild(this.closeButton) : this.options.closeButton === "end" ? o.appendChild(this.closeButton) : this.options.closeButton === "end-out" && this.headerEndOut.appendChild(this.closeButton));
      const a = this.buildNumeration();
      a && (this.options.numerationPlacement === "start" ? e.appendChild(a) : this.options.numerationPlacement === "end" && o.prepend(a));
      const l = e.childNodes.length > 0, p = i.childNodes.length > 0, r = o.childNodes.length > 0;
      if (l && t.appendChild(e), p && t.appendChild(i), r && t.appendChild(o), this.headerStartOut.childNodes.length > 0 ? t.appendChild(this.headerStartOut) : this.headerStartOut = null, this.headerEndOut.childNodes.length > 0 ? t.appendChild(this.headerEndOut) : this.headerEndOut = null, t.childNodes.length > 0)
        return this.header = t, this.options.headerInContent ? this.contentBlock && this.contentBlock.prepend(this.header) : this.modal && (this.modal.appendChild(this.header), p && (l || r) && setTimeout(() => {
          if (l && r) {
            const d = Math.max(e.offsetWidth, o.offsetWidth);
            e.style.minWidth = d + "px", o.style.minWidth = d + "px";
          } else
            l ? i.style.marginRight = e.offsetWidth + "px" : i.style.marginLeft = o.offsetWidth + "px";
        }, 100)), this.header;
    }
    return null;
  }
  buildNumeration() {
    if (this.options.numeration) {
      const t = this.getGroupTriggers();
      if (t && t.length > 1) {
        const e = document.createElement("div");
        return e.classList.add("neo-modal--numeration"), t.forEach((i, o) => {
          e && i === this.options.trigger && (e.innerHTML = "<span>" + (o + 1) + "</span> / <span>" + t.length + "</span>");
        }), e;
      }
    }
    return null;
  }
  buildFooter() {
    if (this.footer && (this.footer.remove(), this.footer = null), this.options.footer) {
      const t = document.createElement("div");
      t.classList.add("neo-modal--footer");
      const e = document.createElement("div");
      if (e.classList.add("neo-modal--footer-inner"), this.options.shareLink && this.shareUrl && typeof navigator.canShare < "u") {
        e.insertAdjacentHTML("beforeend", '<a class="neo-modal--tooltip neo-modal--share" data-tippy-theme="modal" data-tippy-content="Share" data-tippy-delay="[200, 0]" target="_blank" href="' + this.shareUrl + '" download>' + this.options.svgOpen + this.options.svgIconShare + this.options.svgClose + "</a>");
        const i = e.querySelector(".neo-modal--share");
        i == null || i.addEventListener("click", (o) => {
          o.preventDefault();
          const n = {
            title: this.options.title || "",
            text: this.options.subtitle || "",
            url: this.shareUrl
          };
          navigator.share(n);
        });
      }
      if (this.options.downloadLink && this.downloadUrl && e.insertAdjacentHTML("beforeend", '<a class="neo-modal--tooltip neo-modal--download" data-tippy-theme="modal" data-tippy-content="Download" data-tippy-delay="[200, 0]" target="_blank" href="' + this.downloadUrl + '" download>' + this.options.svgOpen + this.options.svgIconDownload + this.options.svgClose + "</a>"), this.options.copyLink && this.copyUrl) {
        const i = "Copy";
        e.insertAdjacentHTML("beforeend", '<a class="neo-modal--tooltip neo-modal--copy" data-tippy-theme="modal" data-tippy-content="' + i + '" data-tippy-delay="[200, 0]">' + this.options.svgOpen + this.options.svgIconLink + this.options.svgClose + "</a>");
        const o = e.querySelector(".neo-modal--copy");
        o == null || o.addEventListener("click", (n) => {
          n.preventDefault();
          const a = this.copyUrl.substring(0, 1) === "/" ? window.location.origin + this.copyUrl : this.copyUrl;
          navigator.clipboard.writeText(a).then(() => {
            o.hasOwnProperty("_tippy") && (o._tippy.setContent("Copied"), o._tippy.show(), setTimeout(() => {
              o._tippy.hide(), setTimeout(() => {
                o._tippy.setContent(i);
              }, 1e3);
            }, 1e3));
          });
        });
      }
      e.childNodes.length > 0 && (this.footer = t, t.appendChild(e), this.modal && this.modal.appendChild(this.footer));
    }
    return null;
  }
  buildContentFooter() {
    const t = document.createElement("div");
    if (t.classList.add("neo-modal--content-footer"), this.contentInner && this.options.smartActions) {
      const e = document.createElement("div");
      e.classList.add("neo-modal--actions");
      const i = [], o = this.contentInner.querySelectorAll(".form-actions");
      if (o.length > 0) {
        const n = o[o.length - 1];
        n.style.display = "none", n.querySelectorAll("input, button, a").forEach((a) => {
          i.push(a);
        });
      } else
        this.contentInner.querySelectorAll("form > input[type=submit], form > button, .neo-modal--btn").forEach((n) => {
          i.push(n);
        });
      i.length && (i.forEach((n) => {
        n.style.display = "none";
        const a = document.createElement("button");
        a.classList.add("neo-modal--btn"), a.classList.add("btn"), n.classList.contains("button--primary") && a.classList.add("btn-primary"), a.innerHTML = n.getAttribute("value") || n.innerText, a.addEventListener("click", (l) => {
          l.preventDefault(), l.stopPropagation(), n.dispatchEvent(new Event("mousedown")), n.click(), n.dispatchEvent(new Event("mouseup"));
        }), e.appendChild(a);
      }), t.appendChild(e));
    }
    return t.childNodes.length > 0 && this.contentBlock && this.contentBlock.append(t), null;
  }
  buildLabel() {
    const t = document.createElement("div");
    if (t.classList.add("neo-modal--label"), this.options.title || this.options.subtitle) {
      const e = document.createElement("div");
      e.classList.add("neo-modal--title"), this.options.title && (this.title = document.createElement("h2"), this.title.innerHTML = "<span>" + this.options.title + "</span>", e.appendChild(this.title), t.classList.add("has-title")), this.options.subtitle && (this.subtitle = document.createElement("h5"), this.subtitle.innerHTML = "<span>" + this.options.subtitle + "</span>", e.appendChild(this.subtitle), t.classList.add("has-subtitle")), t.appendChild(e);
    }
    if (this.options.icon) {
      this.icon = document.createElement("div"), this.icon.classList.add("neo-modal--icon");
      const e = document.createElement("i");
      this.options.iconClasses && this.options.iconClasses.split(" ").forEach((i) => {
        e.classList.add(i);
      }), e.classList.add(this.options.icon), this.icon.appendChild(e), t.classList.add("has-icon"), t.prepend(this.icon);
    }
    return t.childNodes.length > 0 ? t : null;
  }
  buildCloseButton() {
    if (this.options.closeButton) {
      this.closeButton = document.createElement("a"), this.closeButton.classList.add("neo-modal--close"), this.closeButton.classList.add("neo-modal--tooltip"), this.closeButton.setAttribute("href", ""), this.closeButton.setAttribute("data-tippy-theme", "modal"), this.closeButton.setAttribute("data-tippy-content", "Close"), this.closeButton.setAttribute("data-tippy-placement", "bottom"), this.closeButton.setAttribute("data-tippy-delay", "500"), this.closeButton.setAttribute("data-neo-modal-close", "true");
      const t = document.createElement("span");
      this.options.closeButtonClasses && this.options.closeButtonClasses.split(" ").forEach((e) => {
        t.classList.add(e);
      }), this.options.closeButtonSvg && (t.innerHTML = this.options.closeButtonSvg), this.closeButton.appendChild(t);
    }
    return this.closeButton;
  }
  showLoader(t) {
    t = typeof t == "number" ? t : 300, this.options.loader && !this.loading && (this.loading = !0, typeof this.options.loader == "function" ? this.options.loader("in") : this.loaderTimeout = setTimeout(() => {
      this.wrapper && (typeof bodyScrollLock < "u" ? bodyScrollLock.lock() : document.body.classList.add("has-neo-modal--loader"), this.loader = document.createElement("div"), this.loader.classList.add("neo-modal--loader"), this.loader.innerHTML = '<div><div class="neo-modal--spinner"><div></div><div></div></div></div>', this.options.loaderColor && this.loader.style.setProperty("--modal-loader-color", this.options.loaderColor), this.options.loaderColorBg && this.loader.style.setProperty("--modal-loader-bg", this.options.loaderColorBg), this.wrapper.appendChild(this.loader), this.animateIn(this.loader, "loader"));
    }, t || 0));
  }
  hideLoader() {
    clearTimeout(this.loaderTimeout), this.loading && this.options.loader && (this.loading = !1, typeof this.options.loader == "function" ? this.options.loader("out") : this.loader && (typeof bodyScrollLock < "u" ? bodyScrollLock.unlock() : document.body.classList.remove("has-neo-modal--loader"), this.animateOut(this.loader, "loader", () => {
      var t;
      (t = this.loader) == null || t.remove(), this.loader = null;
    })));
  }
  toggle() {
    this.isOpen ? this.close() : this.open();
  }
  open() {
    this.options.nest || u.closeTop(), this.isOpen = !0, this.originalOptions = Object.assign({}, this.options), this.eventBeforeOpen.trigger(this), this.buildStack(), this.build().then(() => {
      setTimeout(() => {
        this.doOpen();
      });
    });
  }
  doOpen() {
    var e, i;
    this.eventOpen.trigger(this), this.watchInterval = setInterval(this.watch.bind(this), 200), (e = this.modal) == null || e.style.setProperty("visibility", ""), (i = this.modal) == null || i.style.setProperty("pointer-events", ""), this.transitionBodyIn();
    const t = document.querySelectorAll(".neo-modal:not(.neo-modal--closing)");
    this.depth = t.length;
    for (let o = 0; o < t.length; o++) {
      const n = t.length - (o + 1);
      n === 0 ? t[o].removeAttribute("data-neo-modal--depth") : t[o].setAttribute("data-neo-modal--depth", n + "");
    }
    if (t.length > 1) {
      const o = t[t.length - 2];
      o.neoModal && o.neoModal.focusIn();
    }
    this.header && (this.options.headerInContent ? (this.headerStartOut || this.headerEndOut) && (this.headerStartOut && this.animateIn(this.headerStartOut, "header"), this.headerEndOut && this.animateIn(this.headerEndOut, "header")) : this.animateIn(this.header, "header"), this.options.headerAnimate && (this.title && this.animateIn(this.title, "title"), this.subtitle && this.animateIn(this.subtitle, "subtitle"), this.icon && this.animateIn(this.icon, "icon"), this.closeButton && this.animateIn(this.closeButton, "closeButton"))), this.prev && this.animateIn(this.prev, "navPrev"), this.next && this.animateIn(this.next, "navNext"), this.footer && this.animateIn(this.footer, "footer"), this.contentBlock ? this.animateIn(this.contentBlock, "content", () => {
      this.finishOpen();
    }) : this.finishOpen();
  }
  finishOpen() {
    var o, n, a;
    this.options.navKeyboard && (document.body.addEventListener("keydown", this.onKeyboardDown.bind(this)), document.body.addEventListener("keyup", this.onKeyboardUp.bind(this))), this.options.fit && (document.body.addEventListener("mousemove", this.focusWatch.bind(this), !1), this.focusWatch());
    const t = "a[href], details, [tabindex]", e = "input:not([type=hidden]), textarea, select, button", i = ((o = this.contentInner) == null ? void 0 : o.querySelector(
      e
    )) || ((n = this.modal) == null ? void 0 : n.querySelector(
      t
    )) || ((a = this.contentInner) == null ? void 0 : a.querySelector(
      t
    ));
    i ? i.focus() : this.options.trigger && this.options.trigger.blur(), this.buildTooltips(), this.eventAfterOpen.trigger(this);
  }
  close() {
    var t;
    this.isOpen = !1, (t = this.modal) == null || t.classList.add("neo-modal--closing"), this.eventBeforeClose.trigger(this), clearInterval(this.watchInterval), this.doClose().then(() => {
      this.finishClose();
    }), this.options.trigger && this.options.trigger.focus();
  }
  doClose() {
    return new Promise((t) => {
      this.eventClose.trigger(this), this.transitionBodyOut();
      const e = document.querySelectorAll(".neo-modal");
      for (let i = 0; i < e.length; i++) {
        const o = e.length - (i + 2);
        o >= 0 && (o === 0 ? e[i].removeAttribute("data-neo-modal--depth") : e[i].setAttribute("data-neo-modal--depth", o + ""));
      }
      if (e.length - 1 > 0) {
        const i = e[e.length - 2];
        i.neoModal && (i.neoModal.focusOut(), i.neoModal.buildStack());
      }
      this.backdrop && this.depth === 1 && window.getComputedStyle(this.backdrop).display !== "none" && this.animateOut(this.backdrop, "backdrop"), this.header && (this.options.headerInContent || this.animateOut(this.header, "header"), this.options.headerAnimate && (this.title && this.animateOut(this.title, "title"), this.subtitle && this.animateOut(this.subtitle, "title"), this.icon && this.animateOut(this.icon, "icon"), this.closeButton && this.animateOut(this.closeButton, "closeButton"))), this.prev && this.animateOut(this.prev, "navPrev"), this.next && this.animateOut(this.next, "navNext"), this.footer && this.animateOut(this.footer, "footer"), this.contentBlock ? this.animateOut(this.contentBlock, "content", () => {
        t();
      }) : t();
    });
  }
  finishClose() {
    var t, e;
    if (this.contentPlaceholder) {
      const i = (t = this.contentInner) == null ? void 0 : t.querySelector(".neo-modal-template");
      i && ((e = this.contentPlaceholder.parentNode) == null || e.replaceChild(i, this.contentPlaceholder));
    }
    this.remove(), this.removeWrapper(), this.popper && this.popper.destroy(), this.originalOptions && (this.options = this.originalOptions, this.originalOptions = null), this.options.navKeyboard && (document.body.removeEventListener("keydown", this.onKeyboardDown), document.body.removeEventListener("keyup", this.onKeyboardUp)), this.wrapper = null, document.body.removeEventListener("mousemove", this.focusWatch), this.eventAfterClose.trigger(this);
  }
  globalInit() {
    document.body.classList.add("has-neo-modal"), this.options.bodyLock && (typeof bodyScrollLock < "u" ? bodyScrollLock.lock() : document.body.classList.add("neo-modal--body-lock"));
  }
  globalDestroy() {
    document.body.classList.remove("has-neo-modal"), this.options.bodyLock && (typeof bodyScrollLock < "u" ? bodyScrollLock.unlock() : document.body.classList.remove("neo-modal--body-lock"));
  }
  // GENERIC GETTERS
  // --------------------------------------------------------------------------
  getModal() {
    return this.modal;
  }
  getContent() {
    return this.contentInner;
  }
  // HELPERS
  // --------------------------------------------------------------------------
  /**
   * Parse Youtube or Vimeo videos and get host & ID
   */
  parseVideo(t) {
    let e = "", i = "", o, n = /(https?:\/\/)?((www\.)?(youtube(-nocookie)?|youtube.googleapis)\.com.*(v\/|v=|vi=|vi\/|e\/|embed\/|user\/.*\/u\/\d+\/)|youtu\.be\/)([_0-9a-z-]+)/i;
    if (o = t.match(n), o && o[7])
      e = "youtube", i = o[7];
    else {
      let a = /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/;
      o = t.match(a), o && o[5] && (e = "vimeo", i = o[5]);
    }
    return e && i ? {
      type: e,
      id: i
    } : null;
  }
  /**
   * Get additional url parameters
   */
  getUrlParameter(t) {
    let e = "", o = decodeURIComponent(t).split("?");
    if (o[1] !== void 0) {
      let n = o[1].split("&"), a, l;
      for (l = 0; l < n.length; l++)
        a = n[l].split("="), e = e + "&" + a[0] + "=" + a[1];
    }
    return encodeURI(e);
  }
  // ANIMATIONS
  // --------------------------------------------------------------------------
  transitionBodyIn() {
    if (!(document.querySelectorAll(".neo-modal").length > 1) && (this.options.bodyTransitionScale || this.options.bodyTransitionBlur)) {
      const e = document.querySelector(this.options.bodySelector);
      e && (e.classList.add("neo-modal--body-transition"), setTimeout(() => {
        e.classList.add(...this.transitionBodyClasses());
      }));
    }
  }
  transitionBodyOut() {
    if (!(document.querySelectorAll(".neo-modal").length > 1) && (this.options.bodyTransitionScale || this.options.bodyTransitionBlur)) {
      const e = document.querySelector(this.options.bodySelector);
      if (e) {
        e.classList.remove(...this.transitionBodyClasses());
        const i = () => {
          e.removeEventListener("transitionend", i), e.classList.remove("neo-modal--body-transition");
        };
        e.addEventListener("transitionend", i);
      }
    }
  }
  transitionBodyClasses() {
    const t = [];
    return this.options.bodyTransitionScale && t.push("neo-modal--body-scale"), this.options.bodyTransitionBlur && t.push("neo-modal--body-blur"), t;
  }
  animate(t, e, i, o, n) {
    const a = i.charAt(0).toUpperCase() + i.slice(1), l = e + "Animate" + a;
    if (typeof this.options[l] == "string") {
      const p = this.options[l], r = e + "Animate" + a + "Speed", d = e + "Animate" + a + "Delay", f = () => {
        t.removeEventListener("animationend", f), t.removeEventListener("animationcancel", f), t.classList.remove("neo-animate--animated"), t.classList.remove("neo-animate--" + p), typeof this.options[r] == "string" && t.classList.remove("neo-animate--" + this.options[r]), typeof this.options[d] == "string" && t.classList.remove("neo-animate--delay-" + this.options[d]), i === "out" && (t.style.display = "none"), o && o();
      };
      t.addEventListener("animationend", f), t.addEventListener("animationcancel", f), t.style.display = "", t.classList.add("neo-animate--" + p), (n || typeof this.options[r] == "string") && t.classList.add("neo-animate--" + (n || this.options[r])), typeof this.options[d] == "string" && t.classList.add("neo-animate--delay-" + this.options[d]), t.classList.add("neo-animate--animated");
    } else
      o && o();
  }
  animateIn(t, e, i, o) {
    this.animate(t, e, "in", i, o);
  }
  animateOut(t, e, i, o) {
    this.animate(t, e, "out", i, o);
  }
  // UTILITIES
  // --------------------------------------------------------------------------
  getAbsoluteHeight(t) {
    t = typeof t == "string" ? document.querySelector(t) : t;
    var e = window.getComputedStyle(t), i = parseFloat(e.marginTop) + parseFloat(e.marginBottom);
    return Math.ceil(t.offsetHeight + i);
  }
};
s(u, "colorDefaults", {
  contentColor: "",
  contentColorBg: "",
  contentFooterColor: "",
  contentFooterColorBg: "",
  headerColor: "",
  headerColorBg: "",
  footerColor: "",
  footerColorBg: "",
  loaderColor: "",
  loaderColorBg: "",
  backdropColorBg: "",
  navColor: "",
  navColorBg: ""
}), s(u, "defaults", Object.assign({}, {
  appendTo: null,
  appendToClosest: null,
  wrapperClasses: null,
  modalClasses: null,
  colorScheme: "scheme--reset",
  colorSchemeInherit: !1,
  trigger: null,
  placement: "center",
  width: "auto",
  height: "auto",
  zIndex: null,
  displaceTop: "",
  displaceRight: "",
  displaceBottom: "",
  displaceLeft: "",
  image: null,
  video: null,
  iframe: null,
  group: null,
  fit: !1,
  nest: !0,
  drag: !0,
  bodyLock: !0,
  downloadLink: !0,
  shareLink: !0,
  copyLink: !0,
  videoAutoplay: !0,
  videoRatio: "16x9",
  smartActions: !1,
  content: null,
  contentPadding: "",
  contentScroll: !1,
  contentAnimateIn: "comingIn",
  contentAnimateInSpeed: null,
  contentAnimateInDelay: null,
  contentAnimateOut: "comingOut",
  contentAnimateOutSpeed: "fastest",
  contentAnimateOutDelay: null,
  closeButton: "end",
  closeButtonSvg: '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="neo-modal--close-icon" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z"/><path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z"/></svg>',
  closeButtonClasses: "",
  closeButtonAnimateIn: "fadeIn",
  closeButtonAnimateInSpeed: null,
  closeButtonAnimateInDelay: "fast",
  closeButtonAnimateOut: !1,
  closeButtonAnimateOutSpeed: null,
  closeButtonAnimateOutDelay: null,
  numeration: !1,
  numerationPlacement: "start",
  header: !0,
  headerAnimate: !0,
  headerInContent: !1,
  headerAnimateIn: "slideInDown",
  headerAnimateInSpeed: null,
  headerAnimateInDelay: null,
  headerAnimateOut: "slideOutUp",
  headerAnimateOutSpeed: "fastest",
  headerAnimateOutDelay: null,
  footer: !0,
  footerAnimateIn: "slideInUp",
  footerAnimateInSpeed: null,
  footerAnimateInDelay: null,
  footerAnimateOut: "slideOutDown",
  footerAnimateOutSpeed: "fastest",
  footerAnimateOutDelay: null,
  title: "",
  titleAnimateIn: "fadeInDown",
  titleAnimateInSpeed: null,
  titleAnimateInDelay: "fastest",
  titleAnimateOut: !1,
  titleAnimateOutSpeed: null,
  titleAnimateOutDelay: null,
  subtitle: "",
  subtitleAnimateIn: "fadeIn",
  subtitleAnimateInSpeed: null,
  subtitleAnimateInDelay: "fast",
  subtitleAnimateOut: !1,
  subtitleAnimateOutSpeed: null,
  subtitleAnimateOutDelay: null,
  icon: "",
  iconClasses: "",
  iconAnimateIn: "zoomIn",
  iconAnimateInSpeed: "default",
  iconAnimateInDelay: "fastest",
  iconAnimateOut: !1,
  iconAnimateOutSpeed: null,
  iconAnimateOutDelay: null,
  backdrop: !0,
  backdropClasses: null,
  backdropClose: !0,
  backdropAnimateIn: "fadeIn",
  backdropAnimateInSpeed: "fastest",
  backdropAnimateOut: "fadeOut",
  backdropAnimateOutSpeed: "fastest",
  loader: !0,
  loaderAnimateIn: "fadeIn",
  loaderAnimateInSpeed: "default",
  loaderAnimateOut: "fadeOut",
  loaderAnimateOutSpeed: "fastest",
  nextAnimateIn: "slideInRight",
  nextAnimateInSpeed: null,
  nextAnimateOut: "slideOutLeft",
  nextAnimateOutSpeed: null,
  prevAnimateIn: "slideInLeft",
  prevAnimateInSpeed: null,
  prevAnimateOut: "slideOutRight",
  prevAnimateOutSpeed: null,
  attach: null,
  attachPlacement: "auto",
  navKeyboard: !0,
  navPrevLabel: "Prev",
  navPrevAnimateIn: "slideInLeft",
  navPrevAnimateInSpeed: null,
  navPrevAnimateInDelay: null,
  navPrevAnimateOut: "slideOutLeft",
  navPrevAnimateOutSpeed: "fastest",
  navPrevAnimateOutDelay: null,
  navNextLabel: "Next",
  navNextAnimateIn: "slideInRight",
  navNextAnimateInSpeed: null,
  navNextAnimateInDelay: null,
  navNextAnimateOut: "slideOutRight",
  navNextAnimateOutSpeed: "fastest",
  navNextAnimateOutDelay: null,
  bodySelector: ".page-wrapper",
  bodyTransitionScale: !1,
  bodyTransitionBlur: !1,
  svgOpen: '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor">',
  svgClose: "</svg>",
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
  onContentLoaded: null
}, u.colorDefaults));
let m = u;
window.NeoModal = m;
(function(c, t, e) {
  class i extends Event {
    constructor(l, p, r = null) {
      super(`dialog:${l}`, { bubbles: !0 });
      s(this, "dialog");
      s(this, "settings");
      this.dialog = p, this.settings = r;
    }
  }
  const o = {
    iconClasses: "neo-icon neo-icon-font",
    onContentLoaded: (n) => {
      var l;
      const a = n.getContent();
      a && ((l = a.children[0]) == null || l.classList.add("neo-modal--processed"), c.attachBehaviors(a, t));
    },
    onAfterClose: (n) => {
      const a = n.getContent();
      a && c.detachBehaviors(a, t);
    }
  };
  typeof t.neoModal < "u" && typeof t.neoModal.defaults < "u" && Object.assign(o, t.neoModal.defaults), m.setDefaultOptions(o), c.behaviors.neoModal = {
    attach: (n) => {
      const a = m.getTop();
      if (a) {
        const l = a.getContent();
        l && l.children[0] && !l.children[0].classList.contains("neo-modal--processed") && a.refreshContent();
      }
      e("neo.modal", ".use-neo-modal", n).forEach((l) => {
        const p = {};
        p.trigger = l, p.content = (r) => {
          let d = r.nextElementSibling;
          return d && d.tagName === "TEMPLATE" ? d.innerHTML : d && d.classList.contains("neo-modal-template") ? d : (d = r.querySelector(".neo-modal-template"), d && d.tagName === "TEMPLATE" ? d.innerHTML : "");
        }, new m(p);
      });
    }
  }, c.neoModal = {
    open: (n) => {
      const a = new m(n);
      a.event("onBeforeOpen").on(() => {
        window.dispatchEvent(new i("beforecreate", a, t));
      }), a.event("onOpen").on(() => {
        window.dispatchEvent(new i("aftercreate", a, t));
      }), a.event("onClose").on(() => {
        window.dispatchEvent(new i("beforeclose", a, t));
      }), a.event("onAfterClose").on(() => {
        window.dispatchEvent(new i("afterclose", a, t));
      }), a.open();
    },
    close: () => {
      m.closeTop();
    }
  }, c.behaviors.dialog = {}, c.behaviors.dialog.prepareDialogButtons = () => {
  };
})(Drupal, drupalSettings, once);
//# sourceMappingURL=modal.js.map

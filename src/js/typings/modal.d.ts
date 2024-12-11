declare namespace neoModal {

  export interface NeoModalColorOptions {
    contentColor: string;
    contentColorBg: string;
    contentFooterColor: string;
    contentFooterColorBg: string;
    headerColor: string;
    headerColorBg: string;
    footerColor: string;
    footerColorBg: string;
    loaderColor: string;
    loaderColorBg: string;
    backdropColorBg: string;
    navColor: string;
    navColorBg: string;
  }

  export interface NeoModalOptions extends NeoModalColorOptions {
    appendTo:HTMLElement|string|null;
    appendToClosest:string|null;
    wrapperClasses:string|null;
    modalClasses:string|null;
    colorScheme:string|null;
    colorSchemeInherit:boolean;
    trigger:NeoModalTriggerElement|null;
    placement:Placement;
    width:number|string|Size;
    height:number|string|Size;
    zIndex:number|string|null;
    displaceTop:string;
    displaceRight:string;
    displaceBottom:string;
    displaceLeft:string;
    image:string|null;
    video:string|null;
    iframe:string|null;
    group:string|null;
    fit:boolean;
    nest:boolean;
    drag:boolean;
    inputFocus:boolean;
    bodyLock:boolean;
    downloadLink:boolean;
    shareLink:boolean;
    copyLink:boolean;
    videoAutoplay:boolean;
    videoRatio:VideoRatio;
    smartActions:boolean;
    content:((ref: HTMLElement) => HTMLElement|string)|HTMLElement|string|null;
    contentPadding:string|number;
    contentScroll:boolean;
    contentAnimateIn:AnimationIn|boolean;
    contentAnimateInSpeed:AnimationSpeed|null;
    contentAnimateInDelay:AnimationDelay|null;
    contentAnimateOut:AnimationOut|boolean;
    contentAnimateOutSpeed:AnimationSpeed|null;
    contentAnimateOutDelay:AnimationDelay|null;
    contentColor:string;
    contentColorBg:string;
    closeButton:ClosePlacement|boolean;
    closeButtonSvg:string|null;
    closeButtonClasses:string|null;
    closeButtonAnimateIn:AnimationIn|boolean;
    closeButtonAnimateInSpeed:AnimationSpeed|null;
    closeButtonAnimateInDelay:AnimationDelay|null;
    closeButtonAnimateOut:AnimationOut|boolean;
    closeButtonAnimateOutSpeed:AnimationSpeed|null;
    closeButtonAnimateOutDelay:AnimationDelay|null;
    numeration:boolean;
    numerationPlacement:ClosePlacement;
    header:boolean;
    headerAnimate:boolean;
    headerInContent:boolean;
    headerAnimateIn:AnimationIn|boolean;
    headerAnimateInSpeed:AnimationSpeed|null;
    headerAnimateInDelay:AnimationDelay|null;
    headerAnimateOut:AnimationOut|boolean;
    headerAnimateOutSpeed:AnimationSpeed|null;
    headerAnimateOutDelay:AnimationDelay|null;
    headerColor:string;
    headerColorBg:string;
    footer:boolean;
    footerAnimateIn:AnimationIn|boolean;
    footerAnimateInSpeed:AnimationSpeed|null;
    footerAnimateInDelay:AnimationDelay|null;
    footerAnimateOut:AnimationOut|boolean;
    footerAnimateOutSpeed:AnimationSpeed|null;
    footerAnimateOutDelay:AnimationDelay|null;
    footerColor:string;
    footerColorBg:string;
    title:string|null;
    titleAnimateIn:AnimationIn|boolean;
    titleAnimateInSpeed:AnimationSpeed|null;
    titleAnimateInDelay:AnimationDelay|null;
    titleAnimateOut:AnimationOut|boolean;
    titleAnimateOutSpeed:AnimationSpeed|null;
    titleAnimateOutDelay:AnimationDelay|null;
    subtitle:string|null;
    subtitleAnimateIn:AnimationIn|boolean;
    subtitleAnimateInSpeed:AnimationSpeed|null;
    subtitleAnimateInDelay:AnimationDelay|null;
    subtitleAnimateOut:AnimationOut|boolean;
    subtitleAnimateOutSpeed:AnimationSpeed|null;
    subtitleAnimateOutDelay:AnimationDelay|null;
    icon:string|null;
    iconClasses:string|null;
    iconAnimateIn:AnimationIn|boolean;
    iconAnimateInSpeed:AnimationSpeed|null;
    iconAnimateInDelay:AnimationDelay|null;
    iconAnimateOut:AnimationOut|boolean;
    iconAnimateOutSpeed:AnimationSpeed|null;
    iconAnimateOutDelay:AnimationDelay|null;
    backdrop:boolean;
    backdropClasses:string|null;
    backdropClose:boolean;
    backdropColorBg:string;
    backdropAnimateIn:AnimationIn|boolean;
    backdropAnimateInSpeed:AnimationSpeed|null;
    backdropAnimateOut:AnimationOut|boolean;
    backdropAnimateOutSpeed:AnimationSpeed|null;
    loader:((movement: Movement) => void)|boolean;
    loaderColor:string;
    loaderColorBg:string;
    loaderAnimateIn:AnimationIn|boolean;
    loaderAnimateInSpeed:AnimationSpeed|null;
    loaderAnimateOut:AnimationOut|boolean;
    loaderAnimateOutSpeed:AnimationSpeed|null;
    nextAnimateIn:AnimationIn|boolean;
    nextAnimateInSpeed:AnimationSpeed|null;
    nextAnimateOut:AnimationOut|boolean;
    nextAnimateOutSpeed:AnimationSpeed|null;
    prevAnimateIn:AnimationIn|boolean;
    prevAnimateInSpeed:AnimationSpeed|null;
    prevAnimateOut:AnimationOut|boolean;
    prevAnimateOutSpeed:AnimationSpeed|null;
    attach:HTMLElement|string|null;
    attachPlacement:AttachPlacement;
    navKeyboard:boolean;
    navPrevLabel:string;
    navPrevAnimateIn:AnimationIn|boolean;
    navPrevAnimateInSpeed:AnimationSpeed|null;
    navPrevAnimateInDelay:AnimationDelay|null;
    navPrevAnimateOut:AnimationOut|boolean;
    navPrevAnimateOutSpeed:AnimationSpeed|null;
    navPrevAnimateOutDelay:AnimationDelay|null;
    navNextLabel:string;
    navNextAnimateIn:AnimationIn|boolean;
    navNextAnimateInSpeed:AnimationSpeed|null;
    navNextAnimateInDelay:AnimationDelay|null;
    navNextAnimateOut:AnimationOut|boolean;
    navNextAnimateOutSpeed:AnimationSpeed|null;
    navNextAnimateOutDelay:AnimationDelay|null;
    navColor:string;
    navColorBg:string;
    bodySelector:string;
    bodyTransitionScale:boolean;
    bodyTransitionBlur:boolean;
    svgOpen:string;
    svgClose:string;
    svgIconDownload:string;
    svgIconShare:string;
    svgIconLink:string;
    onSettings:((modal:NeoModal, data?:any) => void)|null;
    onBeforeOpen:((modal:NeoModal, data?:any) => void)|null;
    onOpen:((modal:NeoModal, data?:any) => void)|null;
    onAfterOpen:((modal:NeoModal, data?:any) => void)|null;
    onBeforeClose:((modal:NeoModal, data?:any) => void)|null;
    onClose:((modal:NeoModal, data?:any) => void)|null;
    onAfterClose:((modal:NeoModal, data?:any) => void)|null;
    onBeforeNext:((modal:NeoModal, data?:any) => void)|null;
    onNext:((modal:NeoModal, data?:any) => void)|null;
    onAfterNext:((modal:NeoModal, data?:any) => void)|null;
    onBeforePrev:((modal:NeoModal, data?:any) => void)|null;
    onPrev:((modal:NeoModal, data?:any) => void)|null;
    onAfterPrev:((modal:NeoModal, data?:any) => void)|null;
    onContentLoaded:((content:HTMLElement, modal:NeoModal) => void)|null;
  };

  interface NeoModalElement extends HTMLDivElement {
    neoModal:NeoModal;
  }

  interface NeoModalTriggerElement extends HTMLDivElement {
    neoModalOptions:NeoModalOptions;
  }

  type Placement =
    | 'center'
    | 'top'
    | 'top-start'
    | 'top-end'
    | 'bottom'
    | 'bottom-start'
    | 'bottom-end'
    | 'right'
    | 'right-start'
    | 'right-end'
    | 'left'
    | 'left-start'
    | 'left-end';

  type Size =
    | 'auto'
    | 'full';

  type Direction =
    | 'next'
    | 'prev';

  type Movement =
    | 'in'
    | 'out';

  type HeaderPlacement =
    | 'start'
    | 'end';

  type ClosePlacement =
    | HeaderPlacement
    | 'start-out'
    | 'end-out';

  type AttachPlacement =
    | 'auto'
    | 'auto-start'
    | 'auto-end'
    | 'top'
    | 'top-start'
    | 'top-end'
    | 'bottom'
    | 'bottom-start'
    | 'bottom-end'
    | 'right'
    | 'right-start'
    | 'right-end'
    | 'left'
    | 'left-start'
    | 'left-end';

  type AnimationIn =
    | 'comingIn'
    | 'backInDown'
    | 'backInLeft'
    | 'backInRight'
    | 'backInUp'
    | 'bounceIn'
    | 'bounceInDown'
    | 'bounceInLeft'
    | 'bounceInRight'
    | 'bounceInUp'
    | 'fadeIn'
    | 'fadeInDownSmall'
    | 'fadeInDown'
    | 'fadeInDownBig'
    | 'fadeInLeftSmall'
    | 'fadeInLeft'
    | 'fadeInLeftBig'
    | 'fadeInRightSmall'
    | 'fadeInRight'
    | 'fadeInRightBig'
    | 'fadeInUpSmall'
    | 'fadeInUp'
    | 'fadeInUpBig'
    | 'fadeInTopLeft'
    | 'fadeInTopRight'
    | 'fadeInBottomLeft'
    | 'fadeInBottomRight'
    | 'flipInX'
    | 'flipInY'
    | 'lightSpeedInRight'
    | 'lightSpeedInLeft'
    | 'rotateIn'
    | 'rotateInDownLeft'
    | 'rotateInDownRight'
    | 'rotateInUpLeft'
    | 'rotateInUpRight'
    | 'rollIn'
    | 'zoomIn'
    | 'zoomInDown'
    | 'zoomInLeft'
    | 'zoomInRight'
    | 'zoomInUp'
    | 'slideInDown'
    | 'slideInLeft'
    | 'slideInRight'
    | 'slideInUp';

  type AnimationOut =
    | 'comingOut'
    | 'backOutDown'
    | 'backOutLeft'
    | 'backOutRight'
    | 'backOutUp'
    | 'bounceOut'
    | 'bounceOutDown'
    | 'bounceOutLeft'
    | 'bounceOutRight'
    | 'bounceOutUp'
    | 'fadeOut'
    | 'fadeOutDownSmall'
    | 'fadeOutDown'
    | 'fadeOutDownBig'
    | 'fadeOutLeftSmall'
    | 'fadeOutLeft'
    | 'fadeOutLeftBig'
    | 'fadeOutRightSmall'
    | 'fadeOutRight'
    | 'fadeOutRightBig'
    | 'fadeOutUpSmall'
    | 'fadeOutUp'
    | 'fadeOutUpBig'
    | 'fadeOutTopLeft'
    | 'fadeOutTopRight'
    | 'fadeOutBottomLeft'
    | 'fadeOutBottomRight'
    | 'flipOutX'
    | 'flipOutY'
    | 'lightSpeedOutRight'
    | 'lightSpeedOutLeft'
    | 'rotateOut'
    | 'rotateOutDownLeft'
    | 'rotateOutDownRight'
    | 'rotateOutUpLeft'
    | 'rotateOutUpRight'
    | 'rollOut'
    | 'zoomOut'
    | 'zoomOutDown'
    | 'zoomOutLeft'
    | 'zoomOutRight'
    | 'zoomOutUp'
    | 'slideOutDown'
    | 'slideOutLeft'
    | 'slideOutRight'
    | 'slideOutUp';

  type AnimationSpeed =
    | 'default'
    | 'slow'
    | 'slower'
    | 'slowest'
    | 'fast'
    | 'faster'
    | 'fastest';

  type AnimationDelay =
    | 'default'
    | 'slow'
    | 'slower'
    | 'slowest'
    | 'fast'
    | 'faster'
    | 'fastest';

  type VideoRatio =
    | '16:9'
    | '4:3'
    | '1:1'
    | '16x9'
    | '21x9';
}

declare var NeoModal: neoModal.NeoModalStatic;

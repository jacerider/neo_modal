declare namespace drupal {

  export namespace Core {

    export interface INeoModal {
      open(settings:neoModal.NeoModalOptions): neoModal;
      close(): void;
    }

  }

  export interface IDrupalStatic {

    neoModal?: Core.INeoModal;

  }
}

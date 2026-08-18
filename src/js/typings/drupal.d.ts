declare namespace drupal {

  export namespace Core {

    export interface INeoModal {
      // `neoModal` is a namespace, not a type; these used it as one, so both
      // return values silently degraded to the error type.
      open(settings:neoModal.NeoModalUserOptions): neoModal.NeoModalInstance;
      close(): void;
      getTop(): neoModal.NeoModalInstance|null;
    }

  }

  export interface IDrupalStatic {

    neoModal?: Core.INeoModal;

  }
}

declare namespace drupal {

  export namespace Core {

    export interface INeoModal {
      // `neoModal` is a namespace, not a type; these used it as one, so both
      // return values silently degraded to the error type.
      // The modal code loads on demand (see modal.ts). Until it has, open()
      // queues the modal and returns a promise for it; once it has, open()
      // returns the instance itself.
      open(settings:neoModal.NeoModalUserOptions): neoModal.NeoModalInstance|Promise<neoModal.NeoModalInstance>;
      // Closes the top modal. Before the code has loaded nothing is open, so
      // this does nothing, unless a load is under way, which it waits for.
      close(): void;
      // The top modal, or null. Always null before the code has loaded.
      getTop(): neoModal.NeoModalInstance|null;
      // Resolves once the modal code is loaded, loading it if need be.
      ready(): Promise<void>;
      // Starts loading the modal code, as on a visitor's intent. The same
      // promise as ready().
      load(): Promise<void>;
    }

  }

  export interface IDrupalStatic {

    neoModal?: Core.INeoModal;

  }
}

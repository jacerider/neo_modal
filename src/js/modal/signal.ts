class Signal<S, T> implements ISignal<S,T> {
  private handlers: Array<(source: S, data: T) => void> = [];

  public on(handler: (source: S, data: T) => void): void {
      this.handlers.push(handler);
  }

  public off(handler: (source: S, data: T) => void): void {
      this.handlers = this.handlers.filter(h => h !== handler);
  }

  public trigger(source: S, data: T): void {
      // Duplicate the array to avoid side effects during iteration.
      this.handlers.slice(0).forEach(h => h(source, data));
  }

  public expose(): ISignal<S,T> {
      return this
  }
}

export {Signal};

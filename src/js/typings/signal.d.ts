interface ISignal<S,T> {
  on(handler: (source: S, data: T) => void): void;
  off(handler: (source: S, data: T) => void): void;
}

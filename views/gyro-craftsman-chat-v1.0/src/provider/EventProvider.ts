export type EventCallback = (...args: any[]) => void;

export abstract class EventProvider {
  callbackList: EventCallback[] = [];

  public addCallback(callback: EventCallback) {
    this.callbackList.push(callback);
    if (this.callbackList.length === 1) {
      this.startListen();
    }
  }

  public removeCallback(callback: EventCallback) {
    this.callbackList = this.callbackList.filter(cb => cb !== callback);
    if (this.callbackList.length === 0) {
      this.stopListen();
    }
  }

  abstract startListen(): void;

  abstract stopListen(): void;

  abstract notify(...args: any[]): void;
}

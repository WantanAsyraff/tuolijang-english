import { throttle } from "@/utils/helper";
import { EventProvider } from "@/provider/EventProvider";

export class ResizeProvider extends EventProvider {
  static instance: ResizeProvider;

  constructor() {
    super();
    if (ResizeProvider.instance) {
      return ResizeProvider.instance;
    }
    ResizeProvider.instance = this;
  }

  startListen() {
    window.addEventListener("resize", this.notify);
  }

  stopListen() {
    window.removeEventListener("resize", this.notify);
  }

  _notify() {
    this.callbackList.forEach(cb => cb());
  }

  notify = throttle(this._notify.bind(this), 100);
}

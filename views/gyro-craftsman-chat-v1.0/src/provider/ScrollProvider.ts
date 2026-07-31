import { throttle } from "@/utils/helper";
import { EventProvider } from "@/provider/EventProvider";

/**
 * 监听滚动事件
 */
export class ScrollProvider extends EventProvider {
  static instance: ScrollProvider;
  scrollElement: HTMLElement;

  constructor(scrollElement: HTMLElement) {
    super();
    this.scrollElement = scrollElement;
  }

  startListen() {
    this.scrollElement.addEventListener("scroll", this.notify);
  }

  stopListen() {
    this.scrollElement.removeEventListener("scroll", this.notify);
  }

  _notify(event: Event) {
    this.callbackList.forEach(cb => cb(event));
  }

  notify = throttle(this._notify.bind(this), 100) as () => void;
}

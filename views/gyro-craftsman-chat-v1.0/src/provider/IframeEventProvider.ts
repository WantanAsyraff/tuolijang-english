import { EventProvider } from "@/provider/EventProvider";
import type { EventData } from "@/types/iframe-event";

/**
 * 提供嵌入模式下的 iframe 事件
 */
export class IframeEventProvider extends EventProvider {
  static instance: IframeEventProvider;

  constructor() {
    super();
    if (IframeEventProvider.instance) {
      return IframeEventProvider.instance;
    }
    IframeEventProvider.instance = this;
    this.notify = this.notify.bind(this);
  }

  startListen() {
    window.addEventListener("message", this.notify);
  }

  stopListen() {
    window.removeEventListener("message", this.notify);
  }

  notify(event: MessageEvent) {
    const data = event.data as EventData;
    if (data?.source !== "ai-chat-parent" || !data?.action) return;
    this.callbackList.forEach(cb => cb(data));
  }
}

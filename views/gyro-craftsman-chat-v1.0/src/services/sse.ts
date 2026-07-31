import { EventStreamContentType, fetchEventSource } from "@microsoft/fetch-event-source";
import { apiPrefix, isNotSaveChat } from "@/config";
import { getLanguage, translate } from "@/locale";
import { useUserStore } from "@/pinia/stores/useUserStore";

const baseUrl = `${apiPrefix}/chat/history/dialog`;

export type ResponseData = {
  id: string;
  object: string;
  created: number;
  model: string;
  choices: Array<{
    index: number;
    delta: {
      content?: unknown;
      reasoning_content?: unknown;
      reasoning?: unknown;
      reasoning_text?: unknown;
      think?: unknown;
      role?: string;
    };
    flag: number;
  }>;
  type?: string;
  message?: unknown;
  stage?: string;
  tool_name?: string;
};

export interface ThinkingPayload {
  type: string;
  stage?: string;
  content: string;
  toolName?: string;
  source?: "model" | "server";
}

interface SSEOptions {
  msgUuid?: string;
  onRecv: (data: string) => void;
  onError: (error: any) => void;
  onComplete: () => void;
  onRecvMeta: (...args: any[]) => void;
  onRecvThinking?: (data: ThinkingPayload) => void;
}

interface SSEBody {
  history_id: number;
  message: string;
  chat_record_uuid?: string;
  is_show?: number;
}

// class RetriableError extends Error { }
class FatalError extends Error { }

export class SSEService {
  private chatId: number;
  private message: string;
  private options: SSEOptions;
  private controller: AbortController;

  constructor(chatId: number, message: string, options: SSEOptions) {
    this.chatId = chatId;
    this.message = message;
    this.options = options;
    this.controller = new AbortController();
    this.init();
  }

  // 取消请求的，并调用响应完成的回调
  public close() {
    this.controller.abort();
    this.options.onComplete();
  }

  private getOptions() {
    const { msgUuid } = this.options;
    const store = useUserStore();

    const body: SSEBody = {
      history_id: this.chatId,
      message: this.message
    };

    if (msgUuid) {
      body.chat_record_uuid = msgUuid;
    }

    if (isNotSaveChat) {
      body.is_show = 0;
    }

    const requestOptions = {
      method: "POST",
      openWhenHidden: true,
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${store.token}`,
        "laravel_lang": getLanguage()
      },
      body: JSON.stringify(body),
      signal: this.controller.signal
    };

    return requestOptions;
  }

  private init() {
    const { onRecv, onError, onComplete, onRecvMeta, onRecvThinking } = this.options;
    const requestOptions = this.getOptions();

    fetchEventSource(baseUrl, {
      ...requestOptions,
      onopen: async (response: Response) => {
        if (!response.ok || !response.headers.get("content-type")?.includes(EventStreamContentType)) {
          throw new FatalError(translate("error.connectionFailed"));
        }
      },
      onmessage: (event) => {
        if (event.data === "[DONE]") {
          onComplete();
          return;
        };
        try {
          const data: ResponseData = JSON.parse(event.data);

          const dataType = data.type;
          const delta = data?.choices?.[0]?.delta;
          const rawContent = delta?.content ?? data?.message ?? "";
          const content = typeof rawContent === "string" ? rawContent : "";
          const rawReasoningContent =
            delta?.reasoning_content ??
            delta?.reasoning ??
            delta?.reasoning_text ??
            delta?.think;
          const reasoningContent = typeof rawReasoningContent === "string" ? rawReasoningContent : "";

          if (dataType === "error") {
            const errorMessage = typeof data.message === "string" ? data.message : content;
            throw new Error(errorMessage || translate("error.connectionFailed"));
          }

          if (dataType === "data") {
            // 接收其他元信息
            onRecvMeta(dataType, delta?.content ?? data?.message ?? data);
            return;
          }

          if (["thinking", "reasoning", "tool"].includes(dataType || "")) {
            const thinkingContent = reasoningContent || content;
            if (thinkingContent !== "") {
              onRecvThinking?.({
                type: dataType || "thinking",
                stage: data.stage,
                content: thinkingContent,
                toolName: data.tool_name,
                source: "server"
              });
            }
            return;
          }

          if (dataType === "info") {
            // only dev
            // console.log("INFO", content);
            return;
          }

          if (!dataType) {
            if (reasoningContent !== "") {
              onRecvThinking?.({
                type: "reasoning",
                stage: "reasoning",
                content: reasoningContent,
                source: "model"
              });
            }

            if (content !== "") {
              onRecv(content);
            }
            return;
          }

          console.warn(translate("error.unknownDataType"), dataType);
        } catch (error: any) {
          throw error;
        }
      },
      onerror: (event) => {
        throw event;
      },
      onclose: () => {
        onComplete();
      }
    }).catch((error) => {
      this.controller.abort();
      onError(error);
    });
  }
}

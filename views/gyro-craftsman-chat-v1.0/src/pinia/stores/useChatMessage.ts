import type { Chat } from "@/types/chat";
import { CHAT_MESSAGE_STATUS, CHAT_STATUS } from "@/constants/chat";
import { chatService } from "@/services/chat";
import { SSEService, type ThinkingPayload } from "@/services/sse";
import { translate } from "@/locale";

interface Recver {
  close: () => void;
}

export const useChatMessage = (chatList: Ref<Chat[]>) => {
  // 对话 ID -> 对话 recver 实例的映射
  const recvMap = ref<Map<number, Recver>>(new Map());

  const createMessage = (chatId: number, message: string, msgUuid?: string) => {
    const chat = chatList.value.find(item => item.id === chatId);
    if (!chat) throw new Error(translate("chat.conversationNotFound"));
    if (chat.msgInfo.status !== CHAT_STATUS.READY) return;
    chat.msgInfo.status = CHAT_STATUS.PENDING;

    // 获取最新的一条消息，即正在等待流式响应的消息
    const getLastMsg = () => {
      const msgLen = chat.msgInfo.messageList.length;
      const lastMsg = chat.msgInfo.messageList[msgLen - 1];
      return lastMsg;
    };

    // 如果传入了 uuid ，则为重新生成对话，需要清空上一次响应的内容，并重置状态
    if (msgUuid) {
      const lastMsg = getLastMsg();
      lastMsg.answerText = "";
      lastMsg.thinkingList = [];
      lastMsg.status = CHAT_MESSAGE_STATUS.PENDING;
    } else {
      // 初始化消息结构
      chat.msgInfo.loadOptions.total++;
      chat.msgInfo.messageList.push({
        status: CHAT_MESSAGE_STATUS.PENDING,
        problemText: message,
        answerText: "",
        chatRecordUuid: "",
        thinkingList: [],
        hasPageTable: false,
      });
    };

    const sseOptions = {
      msgUuid,
      onRecv: (message: string) => {
        // 接收到消息时的回调

        if (!message) return;
        const lastMsg = getLastMsg();
        if (lastMsg.status !== CHAT_MESSAGE_STATUS.LOADING) {
          lastMsg.status = CHAT_MESSAGE_STATUS.LOADING;
        }
        lastMsg.answerText += message;
      },
      onRecvThinking: (thinking: ThinkingPayload) => {
        // 接收到思考过程时的回调

        if (!thinking.content) return;
        const lastMsg = getLastMsg();
        if (lastMsg.status !== CHAT_MESSAGE_STATUS.LOADING) {
          lastMsg.status = CHAT_MESSAGE_STATUS.LOADING;
        }
        const thinkingList = [...(lastMsg.thinkingList || [])];
        const lastThinking = thinkingList[thinkingList.length - 1];
        const shouldMerge =
          thinking.source === "model" &&
          lastThinking?.source === "model" &&
          lastThinking.type === thinking.type &&
          lastThinking.stage === thinking.stage;

        if (shouldMerge) {
          thinkingList[thinkingList.length - 1] = {
            ...lastThinking,
            content: lastThinking.content + thinking.content
          };
        } else {
          thinkingList.push(thinking);
        }

        lastMsg.thinkingList = thinkingList;
      },
      onError: (error: any) => {
        // 流式接收失败时的回调

        const lastMsg = getLastMsg();
        lastMsg.status = CHAT_MESSAGE_STATUS.ERROR;
        lastMsg.errorText = error?.message || translate("error.connectionFailed");
        chat.msgInfo.status = CHAT_STATUS.READY;
        clearRecv();
      },
      onComplete: () => {
        // 流式接收完成时的回调

        const lastMsg = getLastMsg();
        lastMsg.status = CHAT_MESSAGE_STATUS.SUCCESS;
        chat.msgInfo.status = CHAT_STATUS.READY;
        clearRecv();
      },
      onRecvMeta: (type: string, data: any) => {
        // 接收到 meta 数据时的回调

        if (type === "data") {
          const lastMsg = getLastMsg();
          if (data?.chat_record_uuid) {
            // 记录当前会话的 chatRecordUuid
            lastMsg.chatRecordUuid = data.chat_record_uuid;
          }
          if (data?.is_page === 1) {
            lastMsg.hasPageTable = true;
          }
        }
      },
    };

    const sseService = new SSEService(chatId, getLastMsg().problemText, sseOptions);

    const recver = {
      close: () => {
        sseService.close();
        const uuid = getLastMsg().chatRecordUuid;
        if (uuid) {
          // 发起停止流式接收的请求
          chatService.stopChatStreamRecv(uuid);
        }

        // 清空流式接收的实例
        clearRecv();
      }
    };

    function clearRecv() {
      if (chat) {
        chat.msgInfo.status = CHAT_STATUS.READY;
        recvMap.value.delete(chat.id);
      }
    };

    recvMap.value.set(chat.id, recver);
  };

  const stopMessage = (chatId: number) => {
    const recver = recvMap.value.get(chatId);
    if (recver) {
      recver.close();
    }
  };

  return {
    createMessage,
    stopMessage
  } as const;
};

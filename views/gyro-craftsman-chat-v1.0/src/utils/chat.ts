import { CHAT_STATUS } from "@/constants/chat";
import type { ChatItemResponse } from "@/types/chat";
import type { Chat } from "@/types/chat";

export const getInitialChatMsgInfo = () => {
  return {
    status: CHAT_STATUS.READY,
    messageList: [],
    loadOptions: {
      page: 1,
      total: 0,
      loaded: false,
      loading: false,
    }
  };
};

/**
 * 生成 Chat 信息
 */
export const generateChatInfo = (chatResponse: ChatItemResponse): Chat => {
  const { id, chat_application_id, title, top_up } = chatResponse;

  return {
    id,
    appId: chat_application_id,
    title,
    isTopUp: !!top_up,
    msgInfo: getInitialChatMsgInfo()
  };
};

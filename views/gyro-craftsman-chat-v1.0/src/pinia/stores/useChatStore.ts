import { CHAT_STATUS } from "@/constants/chat";
import { STORE_KEY } from "@/constants/store-key";
import { chatService } from "@/services/chat";
import type { ChatMessage } from "@/types/chat";

import { defineStore } from "pinia";
import { useChatList } from "./useChatList";
import { useChatMessage } from "./useChatMessage";
import { translate } from "@/locale";

export const useChatStore = defineStore(STORE_KEY.CHAT_LIST_STORE, () => {
  const { chatList, chatById, createChat, deleteChat, clearChatMessage, ...chatHook } = useChatList();

  const { createMessage: originCreateMessage, stopMessage } = useChatMessage(chatList);

  const createMessage = (chatId: number, message: string, msgUuid?: string) => {
    originCreateMessage(chatId, message, msgUuid);
  };

  const mergeChatMessageList = (msgList: ChatMessage[], newMsgList: ChatMessage[]): ChatMessage[] => {
    if (msgList.length === 0) return newMsgList;

    // 本地对话列表里第一条消息的 uuid
    const firstMsgUuid = msgList[0].chatRecordUuid;

    // 需要合并的聊天记录起始点在新消息列表中的索引
    const waitMergeEndIndex = newMsgList.findIndex(msg => msg.chatRecordUuid === firstMsgUuid);

    // 需要合并的聊天记录
    let waitMergeList = newMsgList;

    // 如果本地对话列表里第一条消息的 uuid 在 msgList 中存在，则剔除交集后再合并
    if (waitMergeEndIndex !== -1) {
      waitMergeList = newMsgList.slice(0, waitMergeEndIndex);
    }

    // 合并聊天记录
    const combinedList = [...waitMergeList, ...msgList];

    return combinedList;
  };

  /**
 * 分页获取对话消息列表
 */
  const getChatMessage = async (chatId: number) => {
    const chat = chatList.value.find(item => item.id === chatId);
    if (!chat) throw new Error(translate("chat.conversationNotFound"));
    const { page, loaded, loading } = chat.msgInfo.loadOptions;

    if (loaded || loading) return;
    if (chat.msgInfo.status === CHAT_STATUS.PENDING) return;

    chat.msgInfo.loadOptions.loading = true;
    try {
      const { msgList, count } = await chatService.getChatMessage(chatId, page);
      chat.msgInfo.messageList = mergeChatMessageList(chat.msgInfo.messageList, msgList);
      chat.msgInfo.loadOptions.page++;
      chat.msgInfo.loadOptions.total = count;
      chat.msgInfo.loadOptions.loaded = chat.msgInfo.messageList.length >= count;
    } catch (error) {
      throw error;
    } finally {
      chat.msgInfo.loadOptions.loading = false;
    }
  };

  /**
   * 创建对话
   */
  const wrapperCreateChat = async (title: string, appId: number) => {
    const chatId = await createChat(title, appId);
    createMessage(chatId, title);
    return chatId;
  };

  /**
   * 删除对话
   */
  const wrapperDeleteChat = (chatId: number) => {
    stopMessage(chatId);
    return deleteChat(chatId);
  };

  /**
   * 清空对话消息
   */
  const wrapperClearChatMessage = (chatId: number) => {
    clearChatMessage(chatId);
    stopMessage(chatId);
  };

  /**
   * 停止对话
   */
  const stopChatMessage = (chatId: number) => {
    stopMessage(chatId);
  };

  return {
    chatList: shallowReadonly(chatList),
    chatById: readonly(chatById),

    getChatMessage,

    createMessage,
    stopChatMessage,
    createChat: wrapperCreateChat,
    clearChatMessage: wrapperClearChatMessage,
    deleteChat: wrapperDeleteChat,
    ...chatHook
  } as const;
});

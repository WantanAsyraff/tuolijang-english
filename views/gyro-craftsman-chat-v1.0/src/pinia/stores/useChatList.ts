import { isAppPreview, isAppPreviewUse } from "@/config";
import { getInitialChatMsgInfo, generateChatInfo } from "@/utils/chat";
import { chatService } from "@/services/chat";
import type { Chat } from "@/types/chat";
import { translate } from "@/locale";

export const useChatList = () => {
  const chatList = ref<Chat[]>([]);

  // 对话 ID -> 对话的映射
  const chatById = computed(() => {
    return chatList.value.reduce((acc, item) => {
      acc[item.id] = item;
      return acc;
    }, {} as Record<number, Chat>);
  });

  /**
 * 合并对话列表，防止对话中存在流式接收响应的实例时，被新对话信息覆盖
 */
  const mergeChatList = (chatList: Chat[], newChatList: Chat[]): Chat[] => {
    if (!chatList.length) return newChatList;
    const chatListMap = chatList.reduce((acc, item) => {
      acc[item.id] = item;
      return acc;
    }, {} as Record<number, Chat>);

    return newChatList.map((newChat) => {
      const oldChat = chatListMap[newChat.id];
      if (oldChat) {
        const { title, isTopUp } = newChat;
        oldChat.title = title;
        oldChat.isTopUp = isTopUp;
        return oldChat;
      }
      return newChat;
    });
  };

  /**
   * 初始化对话列表
   */
  const initializeChatList = async () => {
    try {
      const chatListResp = await chatService.getChatList();
      chatList.value = mergeChatList(chatList.value, chatListResp);
    } catch (error) {
      throw error;
    }
  };

  /**
   * iframe 预览模式下使用指定 AppId 初始化对话列表，
   * 预览模式下，如果用户没有创建对话时所使用 AppId 的权限，使用 initializeChatList 刷新对话列表获取不到该条数据
   * 所以要单独为预览模式初始化对话列表
   */
  const initializeChatListByAppPreview = (id: number, appId: number, title: string) => {
    const chatInfo = generateChatInfo({
      id,
      user_id: 0,
      chat_application_id: appId,
      title,
      top_up: null,
      created_at: "",
      updated_at: "",
      deleted_at: null,
    });

    chatList.value = [chatInfo];
  };

  /**
   * 创建对话
   */
  const createChat = async (title: string, appId: number) => {
    try {
      const createChatResp = await chatService.createChat(title, appId);

      if (isAppPreview || isAppPreviewUse) {
        initializeChatListByAppPreview(createChatResp.id, appId, title);
      } else {
        await initializeChatList();
      }

      return createChatResp.id;
    } catch (error) {
      throw error;
    }
  };

  /**
   * 删除对话
   */
  const deleteChat = async (chatId: number) => {
    try {
      await chatService.deleteChat(chatId);
      chatList.value = chatList.value.filter(item => item.id !== chatId);
    } catch (error) {
      throw error;
    }
  };

  /**
   * 重命名对话
   */
  const renameChat = async (chatId: number, title: string) => {
    const idx = chatList.value.findIndex(item => item.id === chatId);
    if (idx === -1) throw new Error(translate("chat.conversationNotFound"));
    const oldTitle = chatList.value[idx].title;
    // 乐观更新标题
    chatList.value[idx].title = title;

    try {
      await chatService.updateChat(chatId, title);
    } catch (error) {
      // 如果失败则恢复原来的标题
      chatList.value[idx].title = oldTitle;
      throw error;
    }
  };

  /**
   * 设置置顶对话
   */
  const setTopUpChat = async (chatId: number, isTopUp: boolean = false) => {
    try {
      await chatService.setTopUpChat(chatId, isTopUp);
      initializeChatList();
    } catch (error) {
      throw error;
    }
  };

  /**
   * 清空对话消息
   * @param chatId number
   */
  const clearChatMessage = (chatId: number) => {
    const chat = chatList.value.find(item => item.id === chatId);
    if (!chat) throw new Error("对话不存在");
    chatService.clearChatMessage(chatId);
    const msgInfo = getInitialChatMsgInfo();
    msgInfo.loadOptions.loaded = true;
    chat.msgInfo = msgInfo;
  };

  /**
   * 重置对话列表
   */
  const resetChatList = () => {
    chatList.value = [];
  };

  return {
    chatList,
    chatById,

    initializeChatList,
    resetChatList,
    createChat,
    deleteChat,
    renameChat,
    setTopUpChat,
    clearChatMessage
  };
};

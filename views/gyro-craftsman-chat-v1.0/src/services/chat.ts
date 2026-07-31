import { clearChatMessageApi, createChatApi, deleteChatApi, getChatRecordApi, getHistoryChatListApi, stopStreamChatApi, updateChatInfoApi } from "@/api/chat";
import { CHAT_MESSAGE_STATUS } from "@/constants/chat";
import type { ChatItemResponse, Chat, ChatMessage, ChatMessageResponse } from "@/types/chat";
import { generateChatInfo } from "@/utils/chat";

interface CreateChatResponse {
  id: number;
  prologue_list: string[];
  prologue_text: string;
}

interface ChatListResponse {
  count: number;
  list: ChatItemResponse[];
  top_up_list: ChatItemResponse[];
}

class ChatService {
  private static readonly PAGE_SIZE = 10;

  /**
   * 获取应用列表
   */
  async getChatList(): Promise<Chat[]> {
    const res = await getHistoryChatListApi();

    const { list, top_up_list } = res.data as ChatListResponse;

    return [...top_up_list, ...list].map(generateChatInfo);
  }

  /**
   * 创建对话
   */
  async createChat(title: string, appId: number): Promise<CreateChatResponse> {
    const res = await createChatApi(title, appId);
    return res.data;
  }

  /**
   * 删除对话
   */
  async deleteChat(chatId: number): Promise<void> {
    await deleteChatApi(chatId);
  }

  /**
   * 更新对话
   */
  async updateChat(chatId: number, title: string): Promise<void> {
    await updateChatInfoApi(chatId, { title });
  }

  /**
   * 设置置顶对话
   */
  async setTopUpChat(chatId: number, isCancel: boolean = false): Promise<void> {
    await updateChatInfoApi(chatId, { top_up: isCancel ? 0 : 1 });
  }

  // 处理后端返回的聊天记录，格式化为 ChatMessage 类型
  private static processChatMessage(msgList: ChatMessageResponse[]): ChatMessage[] {
    return msgList.map(msg => ({
      status: CHAT_MESSAGE_STATUS.SUCCESS,
      problemText: msg.problem_text,
      answerText: msg.answer_text,
      chatRecordUuid: msg.chat_record_uuid,
      hasPageTable: !!msg.is_page
    })).reverse();
  }

  // 获取历史会话消息
  async getChatMessage(chatId: number, page: number): Promise<{ msgList: ChatMessage[]; count: number }> {
    const PAGE_SIZE = ChatService.PAGE_SIZE;
    const res = await getChatRecordApi(chatId, page, PAGE_SIZE);
    return {
      msgList: ChatService.processChatMessage(res.data.list),
      count: res.data.count,
    };
  }

  // 清除对话消息
  async clearChatMessage(chatId: number): Promise<void> {
    await clearChatMessageApi(chatId);
  }

  // 停止流式接收
  async stopChatStreamRecv(chatRecordUuid: string): Promise<void> {
    await stopStreamChatApi(chatRecordUuid);
  }
}

export const chatService = new ChatService();

import { http } from "@/utils/http";
import { isNotSaveChat } from "@/config";

/**
 * 获取历史聊天列表
 */
export const getHistoryChatListApi = () => {
  return http.get("/chat/history/list");
};

/**
 * 创建聊天
 * @param title 标题
 * @param chatApplicationId 应用 ID
 * @returns Promise<any>
 */
export const createChatApi = (title: string, chatApplicationId: number) => {
  return http.post("/chat/history/save", {
    title,
    chat_application_id: chatApplicationId,
    is_show: isNotSaveChat ? 0 : 1
  });
};

/**
 * 获取历史聊天消息
 * @param message 消息
 * @param historyId 历史 ID
 * @returns Promise<any>
 */
export const getHistoryChatMsgApi = (message?: string, historyId?: number) => {
  return http.post("/chat/history/dialog", {
    message,
    history_id: historyId
  });
};

/**
 * 获取聊天记录
 * @param chatId 聊天 ID
 * @param page 页码
 * @param pageSize 每页条数
 * @returns Promise<any>
 */
export const getChatRecordApi = (chatId: number, page: number, pageSize: number) => {
  return http.get("/chat/record/list", {
    chat_history_id: chatId,
    page,
    limit: pageSize
  });
};

/**
 * 停止流式聊天
 * @param chatRecordUuid 聊天记录 UUID
 * @returns Promise<any>
 */
export const stopStreamChatApi = (chatRecordUuid: string) => {
  return http.post("/chat/history/interrupt", {
    chat_record_uuid: chatRecordUuid
  });
};

/**
 * 删除聊天记录
 * @param chatId 聊天 ID
 * @returns Promise<any>
 */
export const deleteChatApi = (chatId: number) => {
  return http.delete(`/chat/history/delete/${chatId}`);
};

type UpdateChatInfoParams = {
  title?: string;
  top_up?: never;
} | {
  title?: never;
  top_up?: number;
};

/**
 * 更新聊天信息
 * @param chatId 聊天 ID
 * @param params 更新参数
 * @returns Promise<any>
 */
export const updateChatInfoApi = (chatId: number, params: UpdateChatInfoParams) => {
  return http.put(`/chat/history/update/${chatId}`, params);
};

/**
 * 获取对话记录表格数据
 * @param uuid 对话记录 UUID
 * @param page 页码
 * @param pageSize 每页条数
 * @returns Promise<any>
 */
export const getChatRecordTableDataApi = (uuid: string, page: number, pageSize: number) => {
  return http.get(`/chat/history/record/${uuid}`, {
    page,
    limit: pageSize
  });
};

/**
 * 清除对话消息
 * @param chatId 聊天 ID
 * @returns Promise<any>
 */
export const clearChatMessageApi = (chatId: number) => {
  return http.post(`/chat/history/clean_up_dialog/${chatId}`);
};

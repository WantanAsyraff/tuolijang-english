import type { CHAT_STATUS, CHAT_MESSAGE_STATUS } from "@/constants/chat";

// 对话响应
export type ChatItemResponse = {
  id: number;
  user_id: number;
  chat_application_id: number;
  title: string;
  created_at: string;
  updated_at: string;
  top_up: string | null;
  deleted_at: any;
};

export type ChatMessage = {
  status: CHAT_MESSAGE_STATUS;
  problemText: string;
  answerText: string;
  chatRecordUuid: string;
  thinkingList?: readonly ChatMessageThinking[];
  errorText?: string;
  hasPageTable?: boolean;
  isSuggest?: boolean;
};

export type ChatMessageThinking = {
  type: string;
  stage?: string;
  content: string;
  toolName?: string;
  source?: "model" | "server";
};

export type ChatMsgInfo = {
  status: CHAT_STATUS;
  messageList: ChatMessage[];
  loadOptions: {
    page: number;
    total: number;
    loaded: boolean;
    loading: boolean;
  };
};

// 对话
export interface Chat {
  id: number;
  appId: number;
  title: string;
  isTopUp: boolean;
  msgInfo: ChatMsgInfo;
}

// 对话消息工具
export interface ChatMessageTool {
  type: string; // 工具类型
  event?: string; // 全局事件
  icon: string; // 工具图标
  text: string; // 工具文本
  handler?: () => void; // 工具点击事件
}

// 对话消息响应
export interface ChatMessageResponse {
  id: number;
  chat_history_id: number;
  chat_record_uuid: string;
  answer_text: string;
  problem_text: string;
  is_page?: boolean;
}

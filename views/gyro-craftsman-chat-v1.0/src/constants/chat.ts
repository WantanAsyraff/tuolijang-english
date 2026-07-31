export const enum CHAT_STATUS {
  READY = "ready", // 准备好发送消息
  PENDING = "pending", // 正在发送 & 接收消息
}

export const enum CHAT_MESSAGE_STATUS {
  PENDING = "pending", // 问题已发出，尚未收到任何报文
  LOADING = "loading", // 正在接收报文
  SUCCESS = "success", // 报文接收完毕
  ERROR = "error", // 报文接收出错
}

export const enum IFRAME_SCREEN_STATE {
  FULL_SCREEN = "full-screen", // 全屏
  MEDIUM_SCREEN = "medium-screen", // 中等屏幕
  MINI_SCREEN = "mini-screen", // 小悬浮球状态
};

// 来自 iframe 的父窗口的事件类型
export const enum IFRAME_EVENT_TYPE {
  UPDATE_APP_PREVIEW_STATE = "update-app-preview-state", // 更新应用预览状态，开场白文案等
  REFRESH_APP_LIST = "refresh-app-list", // 刷新应用列表
  SHOW_IFRAME = "show-iframe", // 显示 iframe 窗口
  SET_MINIMIZE = "set-minimize", // 设置最小化
  OPEN_APP = "open-app", // 打开应用
}

// 发送给父 iframe 的事件类型
export const enum IFRAME_ACTION {
  SET_IFRAME_SCREEN_STATE = "set-iframe-screen-state", // 设置 iframe 窗口状态
  IFRAME_READY = "iframe-ready", // app 已挂载消息监听，通知父窗口可补发积压消息
}

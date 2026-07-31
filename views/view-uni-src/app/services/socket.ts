import WebSocketClass from "@/utils/webSocket";
import { getActiveWsUrl } from "@/utils/serverConfig";

/**
 * WebSocket 单例服务
 * 幂等调用安全：ensureConnected 可重复调用，不会重复创建连接
 */
export const socketService = (() => {
  let socket: WebSocketClass | null = null;
  let connectedToken = "";

  const buildUrl = (token: string) => {
    return `${getActiveWsUrl()}/ws?type=ent&token=${token}`;
  };

  /**
   * 确保 WebSocket 已连接
   * 如果已有连接且处于连接状态，则跳过
   * 如果已有实例但未连接，则重新初始化
   * 如果无实例，则创建新实例并连接
   */
  const ensureConnected = (token: string) => {
    if (!token) return;

    if (socket && connectedToken && connectedToken !== token) {
      disconnect();
    }

    if (!socket) {
      socket = new WebSocketClass(buildUrl(token), 10);
      connectedToken = token;
    }

    if (!socket.isCreate) {
      socket.initSocket();
    }
  };

  /**
   * 断开 WebSocket 连接并销毁实例
   */
  const disconnect = () => {
    socket?.closeSocket();
    socket = null;
    connectedToken = "";
  };

  return {
    ensureConnected,
    disconnect,
  };
})();

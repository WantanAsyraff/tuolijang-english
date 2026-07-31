import { getUserInfoApi } from "@/api/user";
import type { UserInfo, EnterpriseInfo } from "@/types/user";
import { STORAGE_KEY } from "@/constants/storage-key";

type UserInfoResponse = {
  userInfo: UserInfo;
  enterprise: EnterpriseInfo;
};

class UserService {
  getLocalUserInfo(): UserInfo | null {
    const userInfoStr = localStorage.getItem(STORAGE_KEY.USER_INFO);
    if (userInfoStr) {
      const userInfo = JSON.parse(userInfoStr) as UserInfo;
      if (userInfo.avatar && userInfo.name) {
        return userInfo;
      }
    }
    return null;
  }

  getLocalEnterpriseInfo(): EnterpriseInfo | null {
    const enterpriseInfoStr = localStorage.getItem(STORAGE_KEY.ENTERPRISE_INFO);
    if (enterpriseInfoStr) {
      const enterpriseInfo = JSON.parse(enterpriseInfoStr) as EnterpriseInfo;
      if (enterpriseInfo.enterprise_name) {
        return enterpriseInfo;
      }
    }
    return null;
  }

  saveLocalEnterpriseInfo(enterpriseInfo: EnterpriseInfo) {
    localStorage.setItem(STORAGE_KEY.ENTERPRISE_INFO, JSON.stringify(enterpriseInfo));
  }

  saveLocalUserInfo(userInfo: UserInfo) {
    localStorage.setItem(STORAGE_KEY.USER_INFO, JSON.stringify(userInfo));
  }

  clearLocalEnterpriseInfo() {
    localStorage.removeItem(STORAGE_KEY.ENTERPRISE_INFO);
  }

  clearLocalUserInfo() {
    localStorage.removeItem(STORAGE_KEY.USER_INFO);
  }

  async getUserInfo(): Promise<UserInfoResponse> {
    const res = await getUserInfoApi();
    return res.data;
  }

  saveUserToken(token: string) {
    localStorage.setItem(STORAGE_KEY.CHAT_TOKEN, token);
  }

  /**
  * 首先从 url 中获取嵌入模式下传来的 token
  * 不存在时则读取 chat 本身的 token，
  * 如果 chat 本身的 token 不存在，则读取 OA 系统的 token
  * @returns token
  */
  getUserToken(): string {
    const urlParams = new URLSearchParams(window.location.search);
    const urlToken = urlParams.get("token");
    if (urlToken) {
      localStorage.setItem(STORAGE_KEY.CHAT_TOKEN, urlToken);
      return urlToken;
    }

    const chatToken = localStorage.getItem(STORAGE_KEY.CHAT_TOKEN);
    if (chatToken) {
      return chatToken;
    }

    const oaToken = localStorage.getItem(STORAGE_KEY.OA_SYSTEM_TOKEN);
    if (oaToken) {
      localStorage.setItem(STORAGE_KEY.CHAT_TOKEN, oaToken);
      return oaToken;
    }

    return "";
  }

  clearUserToken() {
    localStorage.removeItem(STORAGE_KEY.CHAT_TOKEN);
  }
}

export const userService = new UserService();

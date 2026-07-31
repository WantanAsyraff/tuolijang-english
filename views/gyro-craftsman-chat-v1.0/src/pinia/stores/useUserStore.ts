import { STORE_KEY } from "@/constants/store-key";
import { defineStore } from "pinia";
import type { UserInfo, EnterpriseInfo } from "@/types/user";
import { userService } from "@/services/user";

export const useUserStore = defineStore(STORE_KEY.USER_STORE, () => {
  const enterpriseInfo = ref<EnterpriseInfo | null>(userService.getLocalEnterpriseInfo());
  const userInfo = ref<UserInfo | null>(userService.getLocalUserInfo());
  const token = ref<string>("");

  const isLogin = computed(() => token.value !== "");

  /**
   * 保存用户 token
   */
  const saveUserToken = (token: string) => {
    userService.saveUserToken(token);
  };

  /**
   * 初始化用户 token
   */
  const initializeUserToken = () => {
    token.value = userService.getUserToken();
  };

  /**
   * 初始化用户信息
   */
  const initializeUserInfo = async () => {
    try {
      const userInfoResp = await userService.getUserInfo();
      userInfo.value = userInfoResp.userInfo;
      enterpriseInfo.value = userInfoResp.enterprise;
      userService.saveLocalUserInfo(userInfo.value);
      userService.saveLocalEnterpriseInfo(enterpriseInfo.value);
    } catch (error: any) {
      throw error;
    }
  };

  /**
   * 退出登录
   */
  const logout = () => {
    userService.clearUserToken();
    userService.clearLocalUserInfo();
    userService.clearLocalEnterpriseInfo();
    token.value = "";
    userInfo.value = null;
  };

  return {
    enterpriseInfo: readonly(enterpriseInfo),
    userInfo: readonly(userInfo),
    token: readonly(token),
    isLogin: readonly(isLogin),

    initializeUserToken,
    initializeUserInfo,
    saveUserToken,
    logout,
  } as const;
});

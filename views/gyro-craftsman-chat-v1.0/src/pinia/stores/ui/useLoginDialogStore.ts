import { defineStore } from "pinia";
import { STORE_KEY } from "@/constants/store-key";

/**
 * 提供登录弹窗状态
 */
export const useLoginDialogStore = defineStore(STORE_KEY.LOGIN_DIALOG_STORE, () => {
  const isLoginDialogOpen = ref(false);

  const handleSetLoginDialogOpen = () => {
    isLoginDialogOpen.value = true;
  };

  const handleCloseLoginDialog = () => {
    isLoginDialogOpen.value = false;
  };

  return {
    isLoginDialogOpen,
    handleSetLoginDialogOpen,
    handleCloseLoginDialog,
  };
});

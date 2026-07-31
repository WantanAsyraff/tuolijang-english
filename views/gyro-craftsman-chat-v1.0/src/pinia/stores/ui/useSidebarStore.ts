import { STORE_KEY } from "@/constants/store-key";
import { defineStore } from "pinia";

/**
 * 侧边栏状态
 */
export const useSidebarStore = defineStore(STORE_KEY.SIDEBAR_STORE, () => {
  const isSidebarVisible = ref(false);

  const handleOpenSidebar = () => {
    isSidebarVisible.value = true;
  };

  const handleCloseSidebar = () => {
    isSidebarVisible.value = false;
  };

  return {
    isSidebarVisible,
    handleOpenSidebar,
    handleCloseSidebar,
  };
});

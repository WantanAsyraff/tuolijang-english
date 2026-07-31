import { useUserStore } from "@/pinia/stores/useUserStore";

/**
 * 设置网页标题
 */
export const useWebTitle = () => {
  const userStore = useUserStore();
  const { enterpriseInfo } = toRefs(userStore);

  watch(enterpriseInfo, (newVal) => {
    if (newVal) {
      if (enterpriseInfo.value?.enterprise_name) {
        document.title = enterpriseInfo.value?.enterprise_name;
      }
    }
  }, { immediate: true });
};

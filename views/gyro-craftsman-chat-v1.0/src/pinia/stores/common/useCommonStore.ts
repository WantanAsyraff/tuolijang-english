import { STORE_KEY } from "@/constants/store-key";
import { defineStore } from "pinia";
import { defaultLogo, defaultSiteName } from "@/config";
import type { SiteConfig } from "@/types/site";
import { siteService } from "@/services/site";

/**
 * 通用状态，站点配置 logo 等
 */
export const useCommonStore = defineStore(STORE_KEY.COMMON_STORE, () => {
  const commonConfig = ref<SiteConfig>({
    address: "",
    logo: defaultLogo,
    siteName: defaultSiteName,
    aiImage: ""
  });

  const initializeSiteConfig = async () => {
    try {
      const siteConfig = await siteService.getSiteConfig();
      commonConfig.value = siteConfig;
    } catch (error: any) {
      console.error(error.message);
      throw error;
    }
  };

  return {
    commonConfig,

    initializeSiteConfig
  };
});

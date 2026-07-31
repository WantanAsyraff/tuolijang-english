import { getCommonConfigApi } from "@/api/common";
import { defaultLogo, defaultSiteName } from "@/config";
import type { SiteConfig } from "@/types/site";

class SiteService {
  /**
   * 获取站点配置
   */
  async getSiteConfig(): Promise<SiteConfig> {
    const res = await getCommonConfigApi();
    const { address, logo, site_name, ai_image } = res.data;
    return {
      address,
      logo: logo || defaultLogo,
      siteName: site_name || defaultSiteName,
      aiImage: ai_image
    };
  }
}

export const siteService = new SiteService();

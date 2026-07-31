import * as ww from "@wecom/jssdk";
import {
  clearWxWorkAgentConfigCache,
  getWxWorkSignUrl,
  loadWxWorkAgentConfig,
  type WxWorkAgentConfig
} from "@/libs/wxwork-config";

export const isWxWorkEnv = /wxwork/i.test(typeof navigator !== "undefined" ? navigator.userAgent : "");

export class WxWork {
  private static instance: WxWork;
  private static instancePromise: Promise<WxWork> | null = null;
  private static signatureCache = new Map<string, WxWorkAgentConfig>();
  private agentConfig: WxWorkAgentConfig;
  public ww: typeof ww;

  /**
   * 通过后端返回的 agentConfig 创建 JS-SDK 实例。
   * 构造函数仅保存配置，不触发 SDK 初始化。
   */
  private constructor(agentConfig: WxWorkAgentConfig) {
    this.agentConfig = agentConfig;
    this.ww = ww;
  }

  // 暴露 corpid 供外部使用（如发送跨企业小程序消息）
  get corpId(): string {
    return this.agentConfig.corpid;
  }

  // 暴露完整 agentConfig 供外部使用
  getConfig(): WxWorkAgentConfig {
    return this.agentConfig;
  }

  /**
   * 注册企业微信 JS-SDK，并在 SDK 需要时动态向后端请求最新签名。
   */
  private async init() {
    const agentConfigForRegister = this.agentConfig;
    return new Promise((resolve, reject) => {
      ww.register({
        corpId: agentConfigForRegister.corpid,
        jsApiList: agentConfigForRegister.jsApiList,
        agentId: agentConfigForRegister.agentid,
        // SDK 在当前页面需要校验 agentConfig 时会回调这里获取签名。
        async getAgentConfigSignature(url: string) {
          const signUrl = getWxWorkSignUrl(url);
          const cacheKey = `${agentConfigForRegister.corpid}:${agentConfigForRegister.agentid}:${signUrl}`;
          const cachedAgentConfig = WxWork.signatureCache.get(cacheKey);
          if (cachedAgentConfig) {
            agentConfigForRegister.url = cachedAgentConfig.url;
            const { timestamp, nonceStr, signature } = cachedAgentConfig;
            return {
              timestamp,
              nonceStr,
              signature
            };
          }

          const agentConfig = await loadWxWorkAgentConfig(signUrl, true);
          Object.assign(agentConfigForRegister, agentConfig);
          WxWork.signatureCache.set(cacheKey, agentConfig);
          const { timestamp, nonceStr, signature } = agentConfig;

          return {
            timestamp,
            nonceStr,
            signature
          };
        },
        // agentConfig 校验成功后，标记实例可用。
        onAgentConfigSuccess() {
          resolve(null);
        },
        // 将企微侧的验签错误统一抛回业务层处理。
        onAgentConfigFail(err) {
          clearWxWorkAgentConfigCache(agentConfigForRegister.url || getWxWorkSignUrl());
          WxWork.signatureCache.clear();
          reject({
            message: err instanceof Error ? err.message : err.errMsg
          });
        }
      });
    });
  }

  /**
   * 获取当前页面可复用的企业微信 JS-SDK 单例。
   * 首次调用时会先向后端获取当前页面的 agentConfig，并完成 SDK 初始化。
   */
  static async getInstance() {
    if (this.instance) {
      return this.instance;
    }
    if (this.instancePromise) {
      return this.instancePromise;
    }

    this.instancePromise = (async () => {
      const currentUrl = getWxWorkSignUrl();
      const agentConfig = await loadWxWorkAgentConfig(currentUrl, true);

      const instance = new WxWork(agentConfig);
      const cacheKey = `${agentConfig.corpid}:${agentConfig.agentid}:${currentUrl}`;
      this.signatureCache.set(cacheKey, agentConfig);
      await instance.init();
      this.instance = instance;
      return instance;
    })().finally(() => {
      this.instancePromise = null;
    });

    return this.instancePromise;
  }
}

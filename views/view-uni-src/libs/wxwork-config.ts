import { getWxworkAgentConfigApi } from "@/api/wxwork";

export type WxWorkAgentConfig = {
  agentid: number;
  corpid: string;
  jsApiList: string[];
  nonceStr: string;
  openTagList: string[];
  signature: string;
  timestamp: number;
  url: string;
};

export type WxWorkAuthConfig = Pick<WxWorkAgentConfig, "agentid" | "corpid">;

type CachedWxWorkAgentConfig = {
  cachedAt: number;
  value: Partial<WxWorkAgentConfig> & WxWorkAuthConfig;
};

const CACHE_KEY_PREFIX = "wxwork:agent-config:";
const AUTH_CACHE_KEY_PREFIX = "wxwork:auth-config:";
const FRESH_CACHE_TTL = 55 * 60 * 1000;
const STALE_CACHE_TTL = 115 * 60 * 1000;
const pendingAgentConfigRequests = new Map<string, Promise<WxWorkAgentConfig>>();
const pendingAuthConfigRequests = new Map<string, Promise<WxWorkAuthConfig>>();

export function getWxWorkSignUrl(url: string = window.location.href) {
  return url.split("#")[0];
}

function assertWxWorkAgentConfig(config: Partial<WxWorkAgentConfig>): asserts config is WxWorkAgentConfig {
  assertWxWorkAuthConfig(config);

  if (!config.signature || !config.nonceStr || !config.timestamp) {
    throw new Error("企业微信应用签名配置无效");
  }
}

export function assertWxWorkAuthConfig(
  config: Partial<WxWorkAgentConfig>
): asserts config is Partial<WxWorkAgentConfig> & WxWorkAuthConfig {
  if (!config.corpid) {
    throw new Error("请先配置企业微信企业ID");
  }

  if (!/^ww/i.test(config.corpid)) {
    throw new Error("企业微信企业ID配置无效，应填写ww开头的CorpID");
  }

  if (!config.agentid) {
    throw new Error("请先配置企业微信自建应用AgentId");
  }
}

function buildCacheKey(url: string) {
  return `${CACHE_KEY_PREFIX}${encodeURIComponent(url)}`;
}

function buildAuthCacheKey(url: string) {
  return `${AUTH_CACHE_KEY_PREFIX}${encodeURIComponent(url)}`;
}

function readCachedEntry(cacheKey: string): CachedWxWorkAgentConfig | null {
  const rawValue = uni.getStorageSync(cacheKey);
  if (!rawValue) return null;

  let cachedValue = rawValue;
  if (typeof rawValue === "string") {
    try {
      cachedValue = JSON.parse(rawValue);
    } catch {
      return null;
    }
  }

  if (!cachedValue || typeof cachedValue !== "object") return null;
  if (typeof cachedValue.cachedAt !== "number") return null;
  if (!cachedValue.value || typeof cachedValue.value !== "object") return null;

  return cachedValue as CachedWxWorkAgentConfig;
}

function isCacheFresh(cachedEntry: CachedWxWorkAgentConfig) {
  return Date.now() - cachedEntry.cachedAt <= FRESH_CACHE_TTL;
}

function isCacheStaleUsable(cachedEntry: CachedWxWorkAgentConfig) {
  return Date.now() - cachedEntry.cachedAt <= STALE_CACHE_TTL;
}

function saveCachedEntry(cacheKey: string, value: Partial<WxWorkAgentConfig> & WxWorkAuthConfig) {
  const cachedEntry: CachedWxWorkAgentConfig = {
    cachedAt: Date.now(),
    value
  };
  uni.setStorageSync(cacheKey, cachedEntry);
}

export function clearWxWorkAgentConfigCache(url: string) {
  uni.removeStorageSync(buildCacheKey(getWxWorkSignUrl(url)));
}

export async function loadWxWorkAgentConfig(url: string, forceRefresh: boolean = false) {
  const signUrl = getWxWorkSignUrl(url);
  const cacheKey = buildCacheKey(signUrl);
  const cachedEntry = readCachedEntry(cacheKey);

  if (!forceRefresh && cachedEntry && isCacheFresh(cachedEntry)) {
    const normalizedCachedConfig = {
      ...cachedEntry.value,
      url: signUrl
    };
    assertWxWorkAgentConfig(normalizedCachedConfig);
    return normalizedCachedConfig;
  }

  const pendingRequest = pendingAgentConfigRequests.get(cacheKey);
  if (pendingRequest) {
    return pendingRequest;
  }

  const requestPromise = (async () => {
    try {
      const agentConfigRes = await getWxworkAgentConfigApi(encodeURIComponent(signUrl));
      if (agentConfigRes.status !== 200) {
        throw new Error(agentConfigRes.message);
      }

      const agentConfig = agentConfigRes.data as Partial<WxWorkAgentConfig>;
      assertWxWorkAgentConfig(agentConfig);

      const normalizedAgentConfig = {
        ...agentConfig,
        url: signUrl
      };
      saveCachedEntry(cacheKey, normalizedAgentConfig);
      return normalizedAgentConfig;
    } catch (error) {
      if (cachedEntry && isCacheStaleUsable(cachedEntry)) {
        const normalizedCachedConfig = {
          ...cachedEntry.value,
          url: signUrl
        };
        assertWxWorkAgentConfig(normalizedCachedConfig);
        return normalizedCachedConfig;
      }

      throw error;
    } finally {
      pendingAgentConfigRequests.delete(cacheKey);
    }
  })();

  pendingAgentConfigRequests.set(cacheKey, requestPromise);
  return requestPromise;
}

export async function loadWxWorkAuthConfig(url: string, forceRefresh: boolean = false) {
  const signUrl = getWxWorkSignUrl(url);
  const authCacheKey = buildAuthCacheKey(signUrl);
  const agentCacheKey = buildCacheKey(signUrl);
  const cachedAuthEntry = readCachedEntry(authCacheKey);
  const cachedAgentEntry = readCachedEntry(agentCacheKey);
  const cachedEntries = [cachedAuthEntry, cachedAgentEntry].filter(Boolean) as CachedWxWorkAgentConfig[];
  const freshCachedEntry = cachedEntries.find(isCacheFresh);
  const staleCachedEntry = cachedEntries.find(isCacheStaleUsable);

  if (!forceRefresh && freshCachedEntry) {
    assertWxWorkAuthConfig(freshCachedEntry.value);
    return {
      agentid: freshCachedEntry.value.agentid,
      corpid: freshCachedEntry.value.corpid
    };
  }

  const pendingAgentConfigRequest = pendingAgentConfigRequests.get(agentCacheKey);
  if (pendingAgentConfigRequest) {
    const agentConfig = await pendingAgentConfigRequest;
    return {
      agentid: agentConfig.agentid,
      corpid: agentConfig.corpid
    };
  }

  const pendingAuthConfigRequest = pendingAuthConfigRequests.get(authCacheKey);
  if (pendingAuthConfigRequest) {
    return pendingAuthConfigRequest;
  }

  const requestPromise = (async () => {
    try {
      const authConfigRes = await getWxworkAgentConfigApi(encodeURIComponent(signUrl));
      if (authConfigRes.status !== 200) {
        throw new Error(authConfigRes.message);
      }

      const authConfig = authConfigRes.data as Partial<WxWorkAgentConfig>;
      assertWxWorkAuthConfig(authConfig);
      const normalizedAuthConfig = {
        agentid: authConfig.agentid,
        corpid: authConfig.corpid
      };
      saveCachedEntry(authCacheKey, normalizedAuthConfig);
      return normalizedAuthConfig;
    } catch (error) {
      if (staleCachedEntry) {
        assertWxWorkAuthConfig(staleCachedEntry.value);
        return {
          agentid: staleCachedEntry.value.agentid,
          corpid: staleCachedEntry.value.corpid
        };
      }

      throw error;
    } finally {
      pendingAuthConfigRequests.delete(authCacheKey);
    }
  })();

  pendingAuthConfigRequests.set(authCacheKey, requestPromise);
  return requestPromise;
}

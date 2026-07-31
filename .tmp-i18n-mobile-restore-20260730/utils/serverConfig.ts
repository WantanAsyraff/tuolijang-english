export interface ServerConfigItem {
  address: string;
  isDefault?: boolean;
  [key: string]: any;
}

const SERVER_CONFIG_KEY = "serverConfigInfo";
const CHOOSE_API_URL_KEY = "chooseApiUrl";
const CHOOSE_ENTERPRISE_KEY = "chooseEnterprise";

const getDefaultApiUrl = (): string => {
  let defaultApiUrl = "";
  // #ifdef H5
  defaultApiUrl = `${window.location.protocol}//${window.location.host}`;
  // #endif
  return defaultApiUrl;
};

const normalizeServerConfigList = (value: unknown): ServerConfigItem[] => {
  if (Array.isArray(value)) return value.filter(item => item?.address);
  if (typeof value === "string" && value) {
    try {
      const parsed = JSON.parse(value);
      return Array.isArray(parsed) ? parsed.filter(item => item?.address) : [];
    } catch {
      return [];
    }
  }
  return [];
};

export const getServerConfigList = (): ServerConfigItem[] => {
  return normalizeServerConfigList(uni.getStorageSync(SERVER_CONFIG_KEY));
};

export const setActiveServerConfig = (selected: ServerConfigItem) => {
  if (!selected?.address) return;

  const exists = getServerConfigList().some(item => item.address === selected.address);
  const configList = exists ? getServerConfigList() : [...getServerConfigList(), selected];
  const list = configList.map(item => ({
    ...item,
    ...(item.address === selected.address ? selected : {}),
    isDefault: item.address === selected.address
  }));

  uni.setStorageSync(CHOOSE_API_URL_KEY, selected.address);
  uni.setStorageSync(CHOOSE_ENTERPRISE_KEY, { ...selected, isDefault: true });
  uni.setStorageSync(SERVER_CONFIG_KEY, list);
};

export const syncActiveServerConfig = (): ServerConfigItem | null => {
  const list = getServerConfigList();
  if (!list.length) return null;

  const storedEnterprise = uni.getStorageSync(CHOOSE_ENTERPRISE_KEY) as ServerConfigItem | "";
  const storedApiUrl = uni.getStorageSync(CHOOSE_API_URL_KEY);
  const selected = list.find(item => storedEnterprise && item.address === storedEnterprise.address)
    || list.find(item => item.address === storedApiUrl)
    || list.find(item => item.isDefault)
    || list[0];

  setActiveServerConfig(selected);
  return selected;
};

export const getActiveApiUrl = (): string => {
  const selected = syncActiveServerConfig();
  return selected?.address || uni.getStorageSync(CHOOSE_API_URL_KEY) || getDefaultApiUrl();
};

export const buildWsUrl = (apiUrl: string): string => {
  if (!apiUrl) return "";
  const match = apiUrl.match(/https?:\/\/(?:www\.)?([^/]+)/);
  if (!match) return "";
  const protocol = apiUrl.startsWith("https") ? "wss:" : "ws:";
  return `${protocol}//${match[1]}`;
};

export const getActiveWsUrl = () => buildWsUrl(getActiveApiUrl());

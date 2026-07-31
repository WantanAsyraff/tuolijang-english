const isDev = process.env.NODE_ENV === "development";
const devServerUrl = "http://dev.oa.crmeb.net";
const httpResourceReg = /\bhttp:\/\/[^\s"'<>\\)]+/gi;

const getCurrentProtocol = () => {
  if (typeof window === "undefined" || !window.location) {
    return "";
  }

  return window.location.protocol;
};

const escapeRegExp = (value) => value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

export const shouldUpgradeInsecureResource = (protocol = getCurrentProtocol()) => protocol === "https:";

/**
 * 统一处理后端返回的资源地址：
 * 1. 开发模式下移除固定测试域名，使资源走本地代理
 * 2. HTTPS 页面下将 http 资源升级为 https，避免 Mixed Content
 * @param {string} url 远端资源的 url 或包含资源链接的富文本内容
 * @param {Object} options 可选配置，便于单元测试覆盖协议分支
 * @returns {string} 经过处理后的 url
 */
export const processResourceUrl = (url, options = {}) => {
  if (typeof url !== "string" || !url) {
    return url;
  }

  const devMode = typeof options.isDev === "boolean" ? options.isDev : isDev;
  const proxyHost = options.devServerUrl || devServerUrl;
  const protocol = options.protocol || getCurrentProtocol();
  let normalizedUrl = url;

  if (devMode && proxyHost) {
    normalizedUrl = normalizedUrl.replace(new RegExp(escapeRegExp(proxyHost), "g"), "");
  }

  if (!shouldUpgradeInsecureResource(protocol)) {
    return normalizedUrl;
  }

  return normalizedUrl.replace(httpResourceReg, (matchedUrl) => matchedUrl.replace(/^http:/i, "https:"));
};

/**
 * 递归处理接口响应或缓存数据中的资源地址，覆盖头像、logo、富文本 img 等深层字段。
 * @param {*} data 任意接口响应数据
 * @param {Object} options processResourceUrl 透传配置
 * @param {WeakSet<object>} seen 循环引用保护
 * @returns {*} 处理后的数据
 */
export const processResourceData = (data, options = {}, seen = new WeakSet()) => {
  if (typeof data === "string") {
    return processResourceUrl(data, options);
  }

  if (!data || typeof data !== "object") {
    return data;
  }

  if (seen.has(data)) {
    return data;
  }

  if (Array.isArray(data)) {
    seen.add(data);
    data.forEach((item, index) => {
      data[index] = processResourceData(item, options, seen);
    });
    return data;
  }

  if (Object.prototype.toString.call(data) !== "[object Object]") {
    return data;
  }

  seen.add(data);
  Object.keys(data).forEach((key) => {
    data[key] = processResourceData(data[key], options, seen);
  });

  return data;
};

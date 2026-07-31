import defaultLogo from "@/assets/images/logo.png";
import { translate } from "@/locale";

const urlParams = new URL(location.href).searchParams;

// 是否在 uniapp 的 webview 中
export const isInWebview = /uni-app/.test(navigator.userAgent);

// 是否在iframe中
// 移动端使用 webview 加载时，userAgent 会包含 uni-app
export const isInIframe = window.self !== window.top || isInWebview;

// iframe 的随机前缀
export const iframePrefix = urlParams.get("prefix");

// 是否在预览调试模式下
export const isAppPreview = isInIframe && urlParams.get("app-preview") === "1";

// 是否在预览使用模式下
export const isAppPreviewUse = isInIframe && urlParams.get("scene") === "app-preview-use";

// 是否不保存聊天记录
export const isNotSaveChat = isAppPreview || isAppPreviewUse;

// 是否是移动端
export const isMobile = /Mobi|Android|iPhone/i.test(navigator.userAgent);

// 路由的前缀
export const routePrefix = import.meta.env.VITE_ROUTE_PREFIX;

// 后端接口的基地址
export const apiBaseUrl = import.meta.env.VITE_API_URL;

// 后端接口的前缀
export const apiPrefix = "/api/ent";

// 默认的站点名称
export const defaultSiteName = translate("common.siteName");

// 默认的logo
export { defaultLogo };

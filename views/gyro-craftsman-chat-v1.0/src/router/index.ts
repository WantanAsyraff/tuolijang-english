import { createRouter, createWebHistory, createMemoryHistory } from "vue-router";
import { routePrefix, isInIframe, isAppPreview, isAppPreviewUse } from "@/config";
import { routes } from "./routes";

/**
 * 在 iframe 中使用时，使用 history 或者 hash 模式都会导致 parent window 的 history 发生变化
 * 导致在移动端侧滑失效
 * 使用 memory 模式则不会导致 parent window 的 history 发生变化
 */

const isUseMemoryRouter = isInIframe && !isAppPreview && !isAppPreviewUse;

const history = isUseMemoryRouter ? createMemoryHistory(routePrefix) : createWebHistory(routePrefix);

export const router = createRouter({
  history,
  routes,
});

// 使用 memory router 时，必须手动执行一次初始化路由，因为 memory router 的默认路由永远是 /
if (isUseMemoryRouter && location.pathname.startsWith(routePrefix)) {
  const initialRoute = location.pathname.replace(routePrefix, "");
  // 必须等待 router 安装时的初始导航（memory history 默认导航到 "/"）完成后再 replace，
  // 否则 .use(router) 触发的初始导航会成为最新的 pendingLocation，取消掉这里的手动跳转
  router.isReady().then(() => {
    router.replace(initialRoute);
  });
}

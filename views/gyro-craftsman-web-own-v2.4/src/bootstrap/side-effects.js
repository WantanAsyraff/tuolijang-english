import { setupPermissionGuard } from "@/permission";
import { AuthDialogPlugin } from "@/utils/authDialog";
import { registerDialogDirectives } from "@/utils/directive";
import { setupGlobalAvatarFallback } from "@/utils/avatar";
import { setupWatermark } from "@/utils/watermark.util";
import { getMenus } from "@/utils/auth";
import { setupMenuCacheSync } from "@/utils/menu-cache";

export function registerSideEffects(Vue, router, store) {
  setupPermissionGuard(router, store);
  setupMenuCacheSync(getMenus);
  registerDialogDirectives(Vue);
  Vue.use(AuthDialogPlugin);
  setupGlobalAvatarFallback();
  setupWatermark(router, store);
}

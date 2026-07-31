import type { App } from "vue";
import { installLocalizedNavigation } from "./navigation";
import { localizeSystemObject, translateSystemText } from "./index";

let interceptorsInstalled = false;

function translateArgument(value: unknown): unknown {
  return typeof value === "string" ? translateSystemText(value) : value;
}

function installUniInterceptors(): void {
  if (interceptorsInstalled) return;
  interceptorsInstalled = true;

  uni.addInterceptor("showToast", {
    invoke(args) {
      if (args) args.title = String(translateArgument(args.title));
    }
  });

  uni.addInterceptor("showModal", {
    invoke(args) {
      if (!args) return;
      args.title = String(translateArgument(args.title));
      args.content = String(translateArgument(args.content));
      if (args.confirmText) args.confirmText = String(translateArgument(args.confirmText));
      if (args.cancelText) args.cancelText = String(translateArgument(args.cancelText));
    }
  });

  uni.addInterceptor("setNavigationBarTitle", {
    invoke(args) {
      if (args) args.title = String(translateArgument(args.title));
    }
  });

  uni.addInterceptor("setTabBarItem", {
    invoke(args) {
      if (args?.text) args.text = String(translateArgument(args.text));
    }
  });
}

export function installLocalization(app: App): void {
  app.config.globalProperties.$ts = translateSystemText;
  app.config.globalProperties.$localize = localizeSystemObject;
  installUniInterceptors();
  installLocalizedNavigation(app);
}

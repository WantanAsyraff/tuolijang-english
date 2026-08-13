import { $ } from '@/lang'
import Cookies from "js-cookie";
import VueLazyload from "vue-lazyload";
import Element from "element-ui";
import VueSmartWidget from "vue-smart-widget";
import vResize from "@theshy/v-resize";
import uploadPicture from "@/components/uploadPicture/uploadFrom";
import height from "@/directive/height";
import directive from "@/directive";
import { normalizeNotificationInput } from "@/lang/notification-text";

import errorImg from "@/assets/images/no.png";
import loadingImg from "@/assets/images/moren.jpg";

export function registerPlugins(Vue) {
  Vue.use(Element, {
    size: Cookies.get("size") || "medium",
    zIndex: 1000,
  });

  const normalizeElementOptions = (options) =>
    normalizeNotificationInput(options, $);

  const wrapElementService = (service) => {
    if (!service || service.__i18nWrapped) return service;
    const wrapped = (options) => service(normalizeElementOptions(options));
    ["success", "warning", "info", "error"].forEach((type) => {
      if (typeof service[type] === "function") {
        wrapped[type] = (options) => service[type](normalizeElementOptions(options));
      }
    });
    ["close", "closeAll"].forEach((method) => {
      if (typeof service[method] === "function") {
        wrapped[method] = (...args) => service[method](...args);
      }
    });
    wrapped.__i18nWrapped = true;
    return wrapped;
  };

  Vue.prototype.$message = wrapElementService(Vue.prototype.$message);
  Vue.prototype.$notify = wrapElementService(Vue.prototype.$notify);

  Vue.use(VueLazyload, {
    preLoad: 1.3,
    error: errorImg,
    loading: loadingImg,
    attempt: 1,
    listenEvents: ["scroll", "wheel", "mousewheel", "resize", "animationend", "transitionend", "touchmove"],
  });

  Vue.use(VueSmartWidget);
  Vue.use(vResize);
  Vue.use(uploadPicture);
  Vue.use(height);
  Vue.use(directive);
}

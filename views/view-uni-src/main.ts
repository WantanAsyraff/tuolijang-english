import App from "./App.vue";
import store from "./store";
import i18n from "./locale";
import { installLocalization } from "./locale/install";

import { createSSRApp } from "vue";
export function createApp() {
  const app = createSSRApp(App);
  app.use(store);
  app.use(i18n);
  installLocalization(app);
  return {
    app,
  };
}
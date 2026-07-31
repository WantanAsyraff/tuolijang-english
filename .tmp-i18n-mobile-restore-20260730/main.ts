import App from "./App.vue";
import store from "./store";

import { createSSRApp } from "vue";
export function createApp() {
  const app = createSSRApp(App);
  app.use(store);
  return {
    app
  };
}

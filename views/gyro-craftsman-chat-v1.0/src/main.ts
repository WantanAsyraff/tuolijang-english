import "virtual:uno.css";
import "@unocss/reset/tailwind.css";
import { createApp } from "vue";
import App from "./App.vue";
import { router } from "./router";
import { pinia } from "./pinia";
import i18n from "./locale";

createApp(App)
  .use(pinia)
  .use(router)
  .use(i18n)
  .mount("#app");
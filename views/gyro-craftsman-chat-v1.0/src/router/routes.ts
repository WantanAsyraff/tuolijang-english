import type { RouteRecordRaw } from "vue-router";
import { ROUTE_KEY } from "@/constants/route-key";
export const routes: RouteRecordRaw[] = [
  {
    path: "/",
    name: ROUTE_KEY.CHAT_INDEX,
    component: () => import("@/views/chat-index/chat-index.vue"),
  },
  {
    path: "/app/:appId",
    name: ROUTE_KEY.CHAT_APP,
    component: () => import("@/views/chat-app/chat-app.vue"),
    props: true,
  },
  {
    path: "/chat/:id",
    name: ROUTE_KEY.CHAT_MAIN,
    component: () => import("@/views/chat-main/chat-main.vue"),
    props: true,
  },
  {
    path: "/:pathMatch(.*)*",
    redirect: {
      name: ROUTE_KEY.CHAT_INDEX,
    },
  }
];

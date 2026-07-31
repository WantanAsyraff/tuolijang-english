<template>
  <ul class="mt-14px mb-15px mx-14px list-none overflow-y-auto app-list" @click="handleCloseSidebar">
    <li v-for="(app) in appList" :key="app.id" :class="{ active: currentAppId === app.id }"
      class="app-item rounded-6px">
      <router-link :to="{ name: ROUTE_KEY.CHAT_APP, params: { appId: app.id } }"
        class="flex items-center px-8px h-40px cursor-pointer single-line">
        <img :src="app.pic" class="w-30px h-30px mr-8px rounded-50%" />
        <span class="text-14px flex-1 leading-18px app-item-name single-line">{{ app.name }}</span>
      </router-link>
    </li>
  </ul>
</template>

<script setup lang="ts">
import { storeToRefs } from "pinia";
import { useSidebarStore } from "@/pinia/stores/ui/useSidebarStore";
import { useAppListStore } from "@/pinia/stores/useAppListStore";
import { ROUTE_KEY } from "@/constants/route-key";
import { useRootStore } from "@/pinia/stores/useRootStore";

const { handleCloseSidebar } = useSidebarStore();
const appListStore = useAppListStore();
const { appList } = storeToRefs(appListStore);
const rootStore = useRootStore();
const { currentAppId } = storeToRefs(rootStore);

</script>

<style scoped lang="scss">
.app-list {
  // 应用项的间距
  --app-item-margin-top: 2px;
  // 应用项的显示数量
  --app-item-show-count: 6;
  // 应用项列表的最大高度
  max-height: calc(var(--app-item-margin-top) * (var(--app-item-show-count) - 1) + 40px * var(--app-item-show-count));
}

.app-item {
  &+.app-item {
    margin-top: var(--app-item-margin-top);
  }

  &:hover,
  &.active {
    background-color: rgba(24, 144, 255, 0.05);

    .app-item-name {
      color: var(--color-primary);
      font-weight: bold;
    }
  }
}
</style>

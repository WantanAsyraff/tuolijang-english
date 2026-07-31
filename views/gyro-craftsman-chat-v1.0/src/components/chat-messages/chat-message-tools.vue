<template>
  <div class="text-14px leading-20px text-#606266 mt-12px flex gap-10px h-40px" @click="handleToolsClick">
    <button class="inline-flex items-center cursor-pointer h-24px px-6px rounded-4px hover:bg-#EDEFF3 msg-tool-btn"
      v-for="tool in tools" :key="tool.type" :data-type="tool.type" :data-event="tool.event">
      <i :class="tool.icon" class="mr-4px ai-icon text-16px" />
      {{ tool.text }}
    </button>
    <slot />
  </div>
</template>

<script setup lang="ts">
import { getDataSet } from "@/utils/helper";
import type { ChatMessageTool } from "@/types/chat";

const props = defineProps<{
  tools: ChatMessageTool[];
}>();

const handleToolsClick = (e: Event) => {
  if (!(e.target instanceof Element)) return;
  const type = getDataSet(e.target, ".msg-tool-btn", "type", false);
  if (!type) return;
  const tool = props.tools.find(tool => tool.type === type);
  if (!tool) return;
  tool.handler?.();
};

</script>

<style scoped lang="scss"></style>

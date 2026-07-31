<template>
  <ul class="pt-20px pb-39px" @click="handleSuggestionClick">
    <li v-for="(suggestion, index) in props.config" class="mb-8px" :key="index" :class="{ 'mb-0px!': index === props.config.length - 1 }">
      <button class="py-13px px-16px flex items-center bg-#F3F4F5 rounded-12px msg-suggestion-btn" :data-index="index">
        <span class="text-left flex-1 break-all">{{ suggestion }}</span>
        <span class="w-20px h-20px flex items-center justify-center ml-16px bg-white rounded-full">
          <i-ep-arrow-right class="text-8px" />
        </span>
      </button>
    </li>
  </ul>
</template>

<script setup lang="ts">
import { getDataSet } from "@/utils/helper";

interface Props {
  config: string[] | readonly string[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: "suggestion-click", suggestion: string): void;
}>();

const handleSuggestionClick = (e: Event) => {
  if (!(e.target instanceof Element)) return;
  const index = getDataSet(e.target, ".msg-suggestion-btn", "index", false);
  if (!index) return;
  emit("suggestion-click", props.config[Number(index)]);
};
</script>

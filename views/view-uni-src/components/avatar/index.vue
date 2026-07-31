<template>
  <view :class="autoSize ? 'avatar' : ''" :style="avatarWrapStyle" @click="clickAvatar">
    <image :style="avatarInnerStyle" class="avatar-image" @error="avatarError"
      :src="imageSrc" mode="aspectFill"></image>
  </view>
</template>

<script setup lang="ts">
import { computed, ref, watch } from "vue";
import defaultAvatar from "/static/image/default-avatar.png";
const props = withDefaults(
  defineProps<{
    src?: string;
    radius?: number;
    autoSize?: boolean;
    width?: number;
    height?: number;
  }>(),
  {
    src: "/static/image/default-avatar.png",
    radius: 12,
    autoSize: true,
    width: 90,
    height: 90
  }
);

const emit = defineEmits(["change"]);
const isImageError = ref(false);
const imageSrc = computed(() => (isImageError.value ? defaultAvatar : props.src || defaultAvatar));
const avatarInnerStyle = computed(() => ({
  borderRadius: props.radius + "rpx",
  width: props.autoSize ? "100%" : props.width + "rpx",
  height: props.autoSize ? "100%" : props.height + "rpx"
}));
const avatarWrapStyle = computed(() => props.autoSize ? {} : {
  width: props.width + "rpx",
  height: props.height + "rpx"
});

watch(
  () => props.src,
  () => {
    isImageError.value = false;
  }
);

const clickAvatar = (): void => {
  emit("change");
};

const avatarError = (): void => {
  isImageError.value = true;
};
</script>

<style scoped lang="scss">
.avatar {
  width: 100%;
  height: 100%;

  .avatar-image {
    width: 100%;
    height: 100%;
  }
}

.avatar-image {
  display: block;
}
</style>

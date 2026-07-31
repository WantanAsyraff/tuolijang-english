<template>
  <view class="main">
    <view class="title">{{ $t('ui.attendanceApplyForMenuModalSelectRequest') }}{{ titleDateStr }})</view>
    <view class="menu">
      <view v-for="item in listData" :key="item.id || item.type" class="item">
        <view class="item-logo">
          <view
            class="iconfont"
            :class="getIconClass(item.icon)"
            :style="{ color: item.color }"
            @click="handleExamineItem(item)"
          ></view>
        </view>
        <view class="name over-text">{{ item.name }}</view>
      </view>
    </view>
    <text class="iconfont icon-guanbi" @click="emit('cancel')"></text>
  </view>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";
import moment from "moment";
import { clickNavigateTo } from "@/utils/helper";
import { attendanceApproveApi } from "@/api/attendance";
import message from "@/utils/message";

interface ApplyMenuItem {
  id?: number | string;
  type?: number | string;
  name?: string;
  icon?: string;
  color?: string;
}

const props = withDefaults(defineProps<{
  dateVal?: string;
}>(), {
  dateVal: "",
});

const emit = defineEmits<{
  (e: "cancel"): void;
}>();

const listData = ref<ApplyMenuItem[]>([]);
const titleDateStr = computed(() => moment(props.dateVal).format("M月D日"));

onMounted(() => {
  getAttendanceApprove();
});

watch(() => props.dateVal, () => {
  getAttendanceApprove();
});

function getIconClass(icon = "") {
  if (!icon) return "";
  return icon.includes("-") ? icon : `icon-${icon.slice(4)}`;
}

function handleExamineItem(item: ApplyMenuItem) {
  clickNavigateTo(`/pages/users/examine/default?id=${item.id}&name=${item.name}`);
  emit("cancel");
}

function getAttendanceApprove() {
  attendanceApproveApi({
    date: props.dateVal,
  }).then((res) => {
    listData.value = res.data || [];
  }).catch((error) => {
    message.error(error.message);
  });
}
</script>

<style lang="scss" scoped>
.main {
  background-color: #fff;
  border-radius: 12rpx 12rpx 0 0;
  padding: 36rpx 52rpx 60rpx 52rpx;
  position: relative;

  .icon-guanbi {
    position: absolute;
    right: 30rpx;
    top: 36rpx;
    color: #c0c4cc;
    font-size: 36rpx;
  }

  .title {
    font-size: 30rpx;
    font-weight: 600;
    color: #303133;
    line-height: 30rpx;
    text-align: center;
    margin-bottom: 60rpx;
  }

  .menu {
    display: flex;
    justify-content: flex-start;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 10px;

    .item {
      text-align: center;
      margin-bottom: 48rpx;

      .item-logo {
        display: inline-block;
        width: 100rpx;
        height: 100rpx;
        margin-bottom: 20rpx;

        .iconfont {
          font-size: 90rpx;
          color: #fff;
        }
      }

      .name {
        width: 130rpx;
        font-size: 28rpx;
        font-weight: 400;
        color: #606266;
        line-height: 28rpx;
      }
    }
  }
}
</style>

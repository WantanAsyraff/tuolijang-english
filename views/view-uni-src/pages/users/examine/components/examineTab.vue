<template>
  <view class="examine-search plr10">
    <view class="examine-search-list">
      <view class="examine-search-list-item" :class="index === data.tabIndex ? 'active': ''"
        v-for="(item,index) in examineTabData" :key="'tab'+item.id" @click="examineSearchItem(item, index)">
        <view class="name">{{item.name}}</view>
      </view>
    </view>
  </view>
</template>

<script setup>
  import { reactive, toRefs, watch } from "vue";
  const props = defineProps({
    examineTabData: {
      type: Array,
      default: () => {
        return [];
      }
    },
    index: {
      type: Number,
      default: -1
    }
  });
  const { examineTabData, index } = toRefs(props);

  const data = reactive({
    tabIndex: 0
  });

  const emit = defineEmits(["change"]);

  const examineSearchItem = (item, index) => {
    data.tabIndex = index;
    item.index = index;
    emit("change", item);
  };
  // 数据监听
  watch(() => index, (newvalue) => {
    if (newvalue.value > -1) {
      data.tabIndex = newvalue.value;
    }
  }, { immediate: true });
</script>

<style scoped lang="scss">
  .examine-search {
    height: 80rpx;
    line-height: 80rpx;

    .examine-search-list {
      display: flex;
      justify-content: space-around;
      align-items: center;

      .examine-search-list-item {
        position: relative;
        text-align: center;
      font-family: PingFang SC, PingFang SC;
font-weight: 400;
font-size: 30rpx;
color: #606266;
        border-bottom: 2px solid rgba(0, 0, 0, 0);

        &.active {
          font-weight: 500;
font-size: 30rpx;
color: #303133;
          border-bottom: 2px solid $uni-color-primary;
        }

        .name {
          font-size: 28rpx;
        }

        .examine-search-badge {
          position: absolute;
          top: -10px;
          right: 0;
        }
      }
    }
  }
</style>
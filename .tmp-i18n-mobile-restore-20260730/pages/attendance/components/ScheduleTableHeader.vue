<template>
  <view class="table-header">
    <view class="header-cell header-cell-first">姓名</view>
    <view class="table-header-scroll">
      <scroll-view scroll-x class="scroll-view" @scroll="handleScroll" :scroll-left="scrollLeft">
        <view class="header-cell" v-for="item of dateList" :key="item.date" :data-event-type="SELECT_COLUMN_EVENT"
          :data-date="item.fullDate" :data-is-rest="item.isRest">
          <view :data-event-type="SELECT_COLUMN_EVENT" :data-date="item.fullDate">{{ weekCycleList[item.weekIndex] }}</view>
          <view :data-event-type="SELECT_COLUMN_EVENT" :data-date="item.fullDate" class="header-cell-date">{{ item.date }}
          </view>
        </view>
      </scroll-view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { SELECT_COLUMN_EVENT } from '../constants/schedule';

const { dateList } = inject("scheduleMixin") as any;
const { header } = inject("tableScrollInfo") as any;
const { scrollLeft, handleScroll } = header;

const weekCycleList = ["日", "一", "二", "三", "四", "五", "六"];

</script>

<style lang="scss" scoped>
.table-header {
  height: 104rpx;
  background: rgba(24, 144, 255, 0.05);
  display: flex;
}

.header-cell {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  height: 100%;
  width: var(--table-cell-width);
  border-right: 1px solid var(--border-color);

  &:last-child {
    border-right: none;
  }

  &.header-cell-first {
    width: var(--first-cell-width);
  }

  .header-cell-date {
    margin-top: 10rpx;
  }

  &[data-is-rest="1"] {
    color: #FF2626;
  }
}

.table-header-scroll {
  width: var(--table-body-width);
}

.scroll-view {
  height: 100%;
  width: 100%;
  white-space: nowrap;
}
</style>
<template>
  <view class="schedule-table-popup" :class="{ active: isShow }" @click="handlePopupClick">
    <view class="popup-title-wrapper">
      <view class="popup-title" :class="{ active: activeTab === 'by-shift' }" @click="activeTab = 'by-shift'">按班次排班
      </view>
      <view class="popup-title" :class="{ active: activeTab === 'by-cycle' }" @click="activeTab = 'by-cycle'">按周期排班
      </view>
    </view>
    <view class="scroll-container">
      <view class="scroll-inner-container">
        <scroll-view class="scroll-view" scroll-y>
          <template v-if="activeTab === 'by-shift'">
            <view v-if="!combineShiftData.length" class="empty-placeholder">
              <image src="@/static/image/empty07.png" mode="aspectFit" />
              <view>暂无班次</view>
            </view>
            <view class="shift-item" v-for="item of combineShiftData" :key="item.id">
              <view class="shift-dots" :style="{ '--color': item.color }"></view>
              <view class="shift-name over-text">{{ item.name }}</view>
              <view class="shift-time-range">
                <view v-for="time of item.times" :key="time">{{ time.work_hours }}~{{ time.off_hours
                  }}
                </view>
              </view>
              <view class="cover-mask" :data-event-type="SELECT_SHIFT_EVENT" :data-id="item.id"></view>
            </view>
          </template>
          <view v-else class="cycle-container">
            <view v-if="!combineCycleData.length" class="empty-placeholder">
              <image src="@/static/image/empty07.png" mode="aspectFit" />
              <view>暂无周期</view>
            </view>
            <view class="cycle-item" v-for="item of combineCycleData" :key="item.id">
              <view class="cycle-name over-text">{{ item.name }}（{{ item.cycle }}天）</view>
              <view class="cycle-shift-list over-text">{{ item.shiftNameList }}</view>
              <view class="cover-mask" :data-event-type="SELECT_CYCLE_EVENT" :data-id="item.id"></view>
            </view>
          </view>
        </scroll-view>
      </view>
    </view>
    <view class="action-bar" v-if="activeTab === 'by-shift'">
      <view class="action-item" :data-event-type="SELECT_SHIFT_EVENT" :data-id="0">
        <image class="action-icon" :data-event-type="SELECT_SHIFT_EVENT" :data-id="0"
          src="@/static/image/attendance/schedule-action1.png" />
        清空
      </view>
      <view class="action-item" :data-event-type="SELECT_SHIFT_EVENT" :data-id="1">
        <image class="action-icon" :data-event-type="SELECT_SHIFT_EVENT" :data-id="1"
          src="@/static/image/attendance/schedule-action2.png" />
        休息
      </view>
    </view>

    <view class="iconfont icon-guanbi" @click="closePopup"></view>
  </view>
</template>

<script setup lang="ts">
import { SELECT_SHIFT_EVENT, SELECT_CYCLE_EVENT } from '../constants/schedule';
type TabType = "by-shift" | "by-cycle";

const activeTab = ref<TabType>("by-shift");
const isShow = ref(false);

const props = defineProps<{
  shiftData: any;
}>();

const { cycleList } = inject("scheduleMixin") as any;

const { handleSetScheduleInfo } = inject("scheduleEditInfo") as any;

const { shiftData } = toRefs(props);

const combineShiftData = computed(() => shiftData.value.filter((item: any) => item.id !== 1));

const combineCycleData = computed(() => {
  return cycleList.value.map((item: any) => {
    return {
      ...item,
      shiftNameList: item.shifts.map((shift: any) => shift.name).join("-")
    }
  })
});

const openPopup = () => {
  isShow.value = true;
}

const closePopup = () => {
  isShow.value = false;
}

const handlePopupClick = (e: any) => {
  const { eventType, id } = e.target.dataset;
  if (!eventType) return;

  handleSetScheduleInfo(eventType, id);
  closePopup();
}

defineExpose({
  openPopup
});

onLoad(() => {
  uni.$on("scheduleTablePopupClose", closePopup);
});

onUnload(() => {
  uni.$off("scheduleTablePopupClose", closePopup);
});

</script>

<style scoped lang="scss">
.schedule-table-popup {
  position: fixed;
  left: 0;
  right: 0;
  bottom: 0;
  height: 774rpx;
  background: #FFFFFF;
  padding: 0 30rpx 0;
  font-size: 14px;

  border-top-left-radius: 20rpx;
  border-top-right-radius: 20rpx;
  box-shadow: 0px -4rpx 16rpx 0px rgba(0, 0, 0, 0.08);
  transform: translateY(100%);
  transition: transform 0.2s cubic-bezier(0.25, 0.10, 0.25, 1.00);
  --title-height: 102rpx;

  display: flex;
  flex-flow: column nowrap;

  &.active {
    transform: translateY(0);
  }
}

.popup-title-wrapper {
  display: flex;
  justify-content: center;
  height: var(--title-height);
  gap: 128rpx;

  .popup-title {
    font-size: 30rpx;
    color: #303133;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 2rpx solid transparent;

    &.active {
      color: #308BF8;
      border-color: #308BF8;
    }
  }
}

.scroll-container {
  margin-top: 40rpx;
  flex: 1;
  position: relative;

  .scroll-inner-container {
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
  }
}

.cover-mask {
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
}

.scroll-view {
  height: 100%;
}

.shift-item {
  height: 80rpx;
  background: #F0F1F5;
  border-radius: 8rpx;
  padding: 0 32rpx;
  display: flex;
  align-items: center;
  position: relative;

  &+& {
    margin-top: 20rpx;
  }

  .shift-dots {
    width: 16rpx;
    height: 16rpx;
    background: var(--color);
    border-radius: 50%;
    flex-shrink: 0;
  }

  .shift-name {
    color: #303133;
    margin: 0 16rpx 0 24rpx;
  }

  .shift-time-range {
    color: #606266;
    flex: 1 0 auto;
    display: flex;

    view+view {
      &::before {
        content: ", ";
      }
    }
  }


}

.cycle-container {
  padding-bottom: 20rpx;
}

.cycle-item {
  height: 130rpx;
  background: #F0F1F5;
  border-radius: 8rpx;
  padding: 26rpx 28rpx;
  display: flex;
  flex-flow: column nowrap;
  justify-content: space-between;
  position: relative;

  .cycle-shift-list {
    font-size: 12px;
    color: #606266;
  }

  &+& {
    margin-top: 20rpx;
  }
}

.action-bar {
  background-color: #fff;
  height: 96rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 280rpx;
}

.action-item {
  display: flex;
  align-items: center;

  .action-icon {
    width: 30rpx;
    height: 30rpx;
    margin-right: 10rpx;
  }
}

.icon-guanbi {
  position: absolute;
  right: 30rpx;
  top: 40rpx;
  font-size: 30rpx;
  color: #C0C4CC;
}

.empty-placeholder {
  padding-top: 100rpx;
  display: flex;
  flex-flow: column nowrap;
  align-items: center;

  image {
    width: 264rpx;
    height: 264rpx;
  }

  .empty-text {
    color: #999999;
    font-size: 14px;
    margin-top: 0.5rem;
    text-align: center;
  }
}
</style>

<template>
  <view class="content">
    <view class="cr-position-header calendar-header">
      <view class="status_bar"></view>
      <view class="flex-header">
        <view class="view-tabs">
          <view class="tab-item" :class="{ active: data.viewType === 'calendar' }" @click="data.viewType = 'calendar'">{{ $t('ui.usersScheduleIndexSchedule') }}</view>
          <view class="tab-item" :class="{ active: data.viewType === 'todo' }" @click="switchToTodo">{{ $t('ui.usersScheduleIndexToDo') }}</view>
        </view>
        <view v-if="data.viewType === 'calendar'" class="set-schedule iconfont icon-richengshezhi" @click="clickSetsChedule"></view>
      </view>
    </view>
    <view class="calendar-header-placeholder"></view>

    <!-- 代办日程 -->
    <view v-if="data.viewType === 'todo'">
      <todo></todo>
    </view>

    <template v-else>
      <!-- 日程 -->
      <view v-if="data.styleType == 4">
        <schedule :checkedTypes="data.checkedTypes" :typeData="data.typeData" @scheduleRecord="scheduleRecord"> </schedule>
      </view>

      <!-- 日 -->
      <view v-if="data.styleType == 1">
        <day :checkedTypes="data.checkedTypes" :typeData="data.typeData" @scheduleRecord="scheduleRecord"> </day>
      </view>
      <!-- 三日 -->
      <view v-if="data.styleType == 2">
        <selectThreeDays :checkedTypes="data.checkedTypes" :typeData="data.typeData" @scheduleRecord="scheduleRecord"></selectThreeDays>
      </view>
      <!-- 周 -->
      <view v-if="data.styleType == 3">
        <selectWeekDays :checkedTypes="data.checkedTypes" :typeData="data.typeData" @scheduleRecord="scheduleRecord"></selectWeekDays>
      </view>
    </template>

    <global-index></global-index>
    <set-schedule ref="setScheduleRef" :checked-types="data.checkedTypes" :type-data="data.typeData" @handleItem="handleItem"></set-schedule>
  </view>
</template>

<script setup lang="ts">
import day from './components/day.vue'
import setSchedule from './components/setSchedule.vue'
import schedule from './components/schedule.vue'
import todo from './components/todo.vue'
import selectThreeDays from './components/selectThreeDays.vue'
import selectWeekDays from './components/selectWeekDays.vue'
import globalIndex from '@/components/globalIndex/index.vue'
import { onLoad } from '@dcloudio/uni-app'
import message from '@/utils/message'
import { scheduleTypesApi, scheduleRecordApi } from '@/api/user'
import { clickNavigateTo } from '@/utils/helper'
import { clientRemindDetailApi, configApproveApi } from '@/api/customer'
import { ref, reactive } from 'vue'
const setScheduleRef = ref<InstanceType<typeof setSchedule> | null>(null)
const data = reactive({
  checkedTypes: [],
  typeData: [],
  styleType: 4,
  viewType: 'calendar',
  buildData: {
    contract_refund_switch: null,
    contract_renew_switch: null,
  },
  cidData: [2, 3, 4],
})

onLoad(() => {
  getScheduleTypes()
  getConfigApprove()
})
onMounted(() => {
  data.styleType = uni.getStorageSync('scheduleTypes') || 4
})

const switchToTodo = () => {
  data.viewType = 'todo'
}

// 获取类型
const getScheduleTypes = () => {
  scheduleTypesApi()
    .then((res: any) => {
      data.typeData = res.data
      if (res.data.length > 0) {
        data.checkedTypes.push(...res.data.map((value: any) => value.id))
      }
    })
    .catch((error: any) => {
      message.error(error.message)
    })
}

// 修改日程状态
const scheduleRecord = (item: any) => {
  if (item.finish === 2 || (data.cidData.includes(item.cid) && item.finish === 3)) return false
  if (!data.cidData.includes(item.cid)) {
    const data_s = {
      end: item.end_time,
      start: item.start_time,
      status: item.finish === 3 ? 1 : 3,
    }
    scheduleRecordApi(item.id, data_s)
      .then((res: any) => {
        message.success(res.message, 'none')
      })
      .catch((error: any) => {
        message.error(error.message)
      })
  } else {
    if (item.cid === 3 || item.cid === 4) {
      // 获取提醒详情
      const datas = {
        status: 3,
        end: item.end_time,
        start: item.start_time,
        id: item.id,
      }
      uni.setStorageSync('scheduleData', JSON.stringify(datas))
      const id = item.relation.remind_id
      clientRemindDetailApi(id)
        .then((res: any) => {
          const config = res.data
          if (config.types == 0) {
            clickNavigateTo(
              `/pages/users/examine/default?id=${data.buildData.contract_refund_switch}&eid=${config.eid}&cid=${config.cid}&types=schedule`,
            )
          } else {
            clickNavigateTo(
              `/pages/users/examine/default?id=${data.buildData.contract_renew_switch}&eid=${config.eid}&cid=${config.cid}&types=schedule`,
            )
          }
        })
        .catch((error: any) => {
          message.error(error.message)
        })
    } else if (item.cid === 2) {
      const query = `/pages/customer/list/addFollow?kid=${item.link_id}&type=1&fid=${item.relation.follow_id}`
      clickNavigateTo(query)
    }
  }
}
const getConfigApprove = () => {
  configApproveApi().then((res: any) => {
    data.buildData = res.data
  })
}

const handleItem = (e: any) => {
  data.styleType = e.type
  data.checkedTypes = e.data
}
const clickSetsChedule = () => {
  setScheduleRef.value.popupOpen()
}
</script>
<style>
page {
  background-color: #fff;
}
</style>
<style scoped lang="scss">
.content {
  width: 100%;
  --schedule-header-height: calc(var(--status-bar-height) + 44px);
}
.calendar-header {
  width: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 100;
  background-color: #fff;
  box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.04);
  .flex-header {
    height: 44px;
    width: 100%;
    position: relative;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 500;
    font-size: 28rpx;
    background-color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0 24rpx 0 30rpx;
    border-bottom: 1px solid #eeeeee;

    .icon-richengshezhi {
      cursor: pointer;
      font-size: 36rpx;
    }
  }
}
.calendar-header-placeholder {
  height: var(--schedule-header-height);
  width: 100%;
}

.set-schedule {
  position: absolute;
  right: 24rpx;
  top: 50%;
  transform: translateY(-50%);

  .iconfont {
    font-size: 34rpx;
    color: #333333;
  }
}
.view-tabs {
  display: flex;
  align-items: center;
  justify-content: center;

  .tab-item {
    cursor: pointer;
    font-size: 28rpx;
    color: #666;
    position: relative;
    margin: 0 24rpx;

    &.active {
      color: #1890ff;
      font-weight: 500;

      &::after {
        content: '';
        position: absolute;
        bottom: -18rpx;
        left: 50%;
        transform: translateX(-50%);
        width: 40rpx;
        height: 4rpx;
        background: #1890ff;
        border-radius: 2rpx;
      }
    }
  }
}
</style>

<template>
  <view class="content">
    <view class="schedule-box">
      <view class="oa-calendar box-shadow">
        <view class="calendar-title">{{ selectTime }} </view>
        <oa-calendar ref="oaCalendarRef" @onClickDay="onClickDay" @getselectTime="getselectTime" :countData="data.countData"></oa-calendar>
      </view>
    </view>

    <!-- 日 -->
    <view class="schedule m10" :style="{ '--height': cHeight + 'px' }">
      <scroll-view
        class="schedule-scroll"
        :scroll-y="true"
        @scroll="scrollFn"
        :show-scrollbar="false"
        @scrolltoupper="scrolltoupper"
        @scrolltolower="scrolltolower"
      >
        <uni-list :border="false" v-if="data.listData.length">
          <view v-for="(val, index) in data.listData" :key="index" :id="moment(val.date).format('YYYY-M-D')">
            <view class="mb20" :id="val.date">
              <view v-if="val.date.split('-')[2] == '01'" class="month-text"> {{ data.monthList[val.date.split('-')[1]] }} </view>
              <view v-if="val.list.length == 0" class="add-box">
                <view class="left">
                  <view class="bottom" :class="val.date == moment(new Date()).format('YYYY-MM-DD') ? 'add-text' : ''">
                    <text> {{ moment(val.date).format('YYYY-MM-DD').split('-')[2] }}</text>
                    <text class="week-text"> {{ dayCycleArray1[moment(val.date).isoWeekday() - 1] }}</text>
                  </view>
                </view>
                <view class="no-schedule"
                  >暂无日程安排
                  <template v-if="moment(val.date).isSameOrAfter(moment().format('YYYY-MM-DD'))"
                    ><text> ,</text> <text class="add-text" @click="clickCreate(val)">点击创建</text></template
                  >
                </view>
              </view>

              <view v-else>
                <uni-list-item v-for="(item, indexI) in val.list" :key="indexI">
                  <!-- 自定义 body -->
                  <template v-slot:body>
                    <view class="left" :class="val.date == moment(new Date()).format('YYYY-MM-DD') ? 'active' : ''">
                      <view class="bottom">
                        <text v-if="item.dayIsShow"> {{ val.date.split('-')[2] }} </text>
                        <text class="week-text" v-if="item.dayIsShow"> {{ dayCycleArray1[moment(val.date).isoWeekday() - 1] }}</text>
                      </view>
                    </view>
                    <view
                      class="list-item"
                      :style="{ borderLeftColor: item.color, background: getColor(item.color, '0.1') }"
                      :class="item.finish == 3 ? 'active-text' : ''"
                    >
                      <uni-row>
                        <uni-col :span="21" @click="scheduleItem(item)">
                          <view class="list-item-left over-text" style="width: 270px">{{ item.title }}</view>
                        </uni-col>
                        <template v-if="userInfo.userId === item.master.id">
                          <uni-col :span="3" @click.stop="scheduleRecord(item)" v-if="isIdInObjectArray(item.user, item.master.id)">
                            <view class="list-item-right text-right">
                              <template v-if="item.finish != 2">
                                <text class="icon-no-check" v-if="item.finish === 0 || item.finish === 1 || item.finish === -1"></text>
                                <text class="iconfont icon-denglu-tongyi" :style="{ color: item.color }" v-if="item.finish === 3"></text>
                              </template>
                              <template v-else>
                                <image class="image-item" src="@/static/image/schedule03.png" mode=""></image>
                              </template>
                            </view>
                          </uni-col>
                        </template>
                        <template v-else>
                          <uni-col :span="3" @click.stop="scheduleRecord(item)">
                            <view class="list-item-right text-right">
                              <template v-if="item.finish != 2">
                                <text class="icon-no-check" v-if="item.finish === 0 || item.finish === 1 || item.finish === -1"></text>
                                <text class="iconfont icon-denglu-tongyi" :style="{ color: item.color }" v-if="item.finish === 3"></text>
                              </template>
                              <template v-else>
                                <image class="image-item" src="@/static/image/schedule03.png" mode=""></image>
                              </template>
                            </view>
                          </uni-col>
                        </template>
                      </uni-row>
                      <view class="list-item-time" @click="scheduleItem(item)">
                        <view>{{ getScheduleTime(item.start_time, item.end_time) }} ・ {{ item.type ? item.type.name : '--' }} </view>
                      </view>
                    </view>
                  </template>
                </uni-list-item>
              </view>
            </view>
          </view>
        </uni-list>
      </scroll-view>

      <empty v-if="data.listData.length <= 0 || (!complete && !data.allFinish)" :index="3" title="暂无待办任务～"></empty>
    </view>
    <!-- 新增 -->
    <view class="add">
      <text class="iconfont icon-xuanfuanniu-jia" @click="clickCreate"></text>
    </view>
  </view>
</template>

<script setup>
import oaCalendar from '@/components/oaCalendar/index'
import moment from 'moment'

import empty from '@/components/empty/index'
import message from '@/utils/message'
import { getScheduleTime } from '@/utils/schedule'
import { scheduleCountApi, scheduleListApi, getScheduleListApi } from '@/api/user'
import { useStore } from 'vuex'
import { useBarHeight } from '@/utils/useVerifyCode'
import { ref, reactive, onMounted, getCurrentInstance, computed } from 'vue'
import { getColor, getZeroNumber, dayCycleArray1, clickNavigateTo, clickSwitchTab, isIdInObjectArray } from '@/utils/helper'
const store = useStore()
const userInfo = computed(() => store.state.app.userInfo)
const { height, getBarHeight } = useBarHeight()
const instance = getCurrentInstance() // 获取组件实例
const props = defineProps({
  // 自定义导航栏列表与defaultType为1时，同时使用
  checkedTypes: {
    type: Object,
    default: () => {
      return []
    },
  },
  typeData: {
    type: Object,
    default: () => {
      return []
    },
  },
})

const { checkedTypes, typeData } = toRefs(props)

watch(
  checkedTypes.value,
  (newVal, oldVal) => {
    getScheduleList()
  },
  { deep: true },
)

onMounted(() => {
  getScheduleCount()
  setTimeout(() => {
    getScheduleList()
  }, 300)
})
const targetId = ref('')
const selectTime = ref(moment(new Date()).format('YYYY年MM月'))
const cHeight = ref(55)
const oaCalendarRef = ref(null)
const time = ref(moment(new Date()).format('YYYY-MM-DD'))
const complete = ref(true)
const data = reactive({
  isShow: false,
  styleType: 1,
  toView: '',
  selected: [],
  countData: [],
  listData: [],
  where: {
    startTime: moment(new Date()).startOf('week').format('YYYY-MM-DD'),
    endTime: moment(new Date()).endOf('week').format('YYYY-MM-DD'),
  },
  allFinish: false,
  monthList: {
    '01': '1月',
    '02': '2月',
    '03': '3月',
    '04': '4月',
    '05': '5月',
    '06': '6月',
    '07': '7月',
    '08': '8月',
    '09': '9月',
    10: '10月',
    11: '11月',
    12: '12月',
  },
})
const cancel = () => {
  if (store.state.app.isNoticeJumpPage) {
    uni.navigateBack({
      delta: 1,
    })
    setTimeout(() => {
      // 设置消息跳转按钮
      store.commit('setiINoticeJumpPage', false)
    }, 200)
  } else {
    let pages = getCurrentPages()
    let url = '/pages/workbench/index'
    if (pages.length > 1) {
      url = pages[0].route
    }
    if (clickSwitchTab(url)) {
      clickSwitchTab(url)
    } else {
      uni.navigateBack({
        delta: 1,
      })
    }
  }
}
const emit = defineEmits(['clickSetsCheduleFn', 'scheduleRecord'])
const clickSetsChedule = () => {
  emit('clickSetsCheduleFn')
}

const scrollFn = (e) => {
  console.log('滚动事件', e.detail)
  if (e.detail.scrollTop >= 600) {
    //  oaCalendarRef.value.handleSwiper(1, 1);
  }
}

// 数据滚动到最顶部加载数据
// const scrolltoupper = (e) => {
//    if (!oaCalendarRef.value) return;
//   oaCalendarRef.value.handleSwiper(1, 0);
// };

// // 数据滚动到最底部加载数据
// const scrolltolower = (e) => {
//   if (!oaCalendarRef.value) return;

//   oaCalendarRef.value.handleSwiper(1, 1);
// };

const clickCreate = (item) => {
  let obj = null
  if (item) {
    let date = moment(item.date).format('YYYY-MM-DD')
    obj = {
      start_time: date + ' ' + moment().format('HH:mm:ss'),
      end_time: date + ' ' + moment().add(1, 'hours').format('HH:mm:ss'),
    }
    clickNavigateTo(`/pages/users/schedule/create?time=${JSON.stringify(obj)}`)
  } else {
    clickNavigateTo('/pages/users/schedule/create')
  }
}

const scheduleItem = (item) => {
  clickNavigateTo(`/pages/users/schedule/detail?id=${item.id}&start=${item.start_time}&end=${item.end_time}`)
}

// const change = (e) => {
//   time.value = e.fulldate;
//   getScheduleList();
// };

// 切换月份
const monthSwitch = (e) => {
  const date = e.year + '-' + getZeroNumber(e.month)
  data.where.startTime = moment(date).startOf('month').format('YYYY-MM-DD')
  data.where.endTime = moment(date).endOf('month').format('YYYY-MM-DD')
  getScheduleCount()
}
const onClickDay = async (item, type) => {
  time.value = item.year + '-' + item.month + '-' + item.day

  // setTimeout(() => {
  //   document.getElementById(time.value).scrollIntoView({ block: 'start', behavior: "smooth" });

  // }, 300);
  try {
    // 等待DOM渲染完成
    await nextTick()

    // 获取元素（兼容uniapp移动端）
    const targetEl = document.getElementById(time.value)

    if (!targetEl) {
      console.warn('未找到指定ID的元素：', time.value)
      return
    }

    // 方案1：基础滚动（兼容所有场景）
    targetEl.scrollIntoView({
      block: 'start', // 滚动到顶部
      inline: 'nearest',
      behavior: 'smooth', // 平滑滚动
    })

    // 方案2：uniapp专属滚动（推荐，兼容性更好）
    // #ifdef H5 || APP-PLUS
    uni.pageScrollTo({
      scrollTop: targetEl.offsetTop - 10, // 偏移10px避免贴顶
      duration: 300, // 平滑滚动时长（毫秒）
    })
    // #endif
  } catch (error) {
    console.error('滚动失败：', error)
    // 降级方案：无平滑效果的滚动
    const targetEl = document.getElementById(time.value)
    if (targetEl) {
      targetEl.scrollIntoView(true)
    }
  }
}

const getselectTime = (val, date) => {
  let isLoadData = data.listData.find((item) => item.date == date.endTime)
  if (isLoadData) return false
  selectTime.value = val
  // 判断是0左滑还是右滑1
  let type = 0
  if (data.where.startTime > date.startTime) {
    type = 0
  } else {
    type = 1
  }
  data.where.startTime = date.startTime
  data.where.endTime = date.endTime
  getScheduleCount()
  getScheduleList(type)
}
// 获取数量
const getScheduleCount = () => {
  const data_s = {
    cid: checkedTypes.value,
    start_time: data.where.startTime,
    end_time: data.where.endTime,
    period: 3,
  }
  scheduleCountApi(data_s)
    .then((res) => {
      data.countData = res.data
    })
    .catch((error) => {
      message.error(error.message)
    })
}

// const handleItem = (e) => {
//   data.styleType = e.type;
//   if (e.type === 1) {
//     checkedTypes.value = e.data;
//   }
//   getScheduleCount();
//   getScheduleList();
// };

// 获取列表
const getScheduleList = (type) => {
  uni.showLoading({
    title: '加载中',
  })
  const data_s = {
    cid: checkedTypes.value,
    start_time: `${data.where.startTime} 00:00:00`,
    end_time: `${data.where.endTime} 23:59:59`,
    period: 2,
  }

  getScheduleListApi(data_s)
    .then((res) => {
      if (type === 0) {
        data.listData = [...res.data, ...data.listData]
      } else if (type === 1) {
        data.listData = [...data.listData, ...res.data]
      } else {
        data.listData = res.data
      }
      for (let i = 0; i < data.listData.length; i++) {
        let value = data.listData[i]

        let obj = data.listData[i].list

        if (typeof obj === 'object' && obj !== null && Object.keys(obj).length > 0) {
          // 获取第一个键名
          const firstKey = Object.keys(obj)[0]
          if (firstKey) {
            // 给第一个对象添加dayIsShow属性
            obj[firstKey].dayIsShow = true
          }
        }

        data.listData[i].date = moment(value.date).format('YYYY-MM-DD')

        if (value.finish !== 3) {
          data.allFinish = true
          // break;
        }
        data.allFinish = false
      }

      uni.hideLoading()
    })
    .catch((error) => {
      message.error(error.message)
      uni.hideLoading()
    })
}

const getKeyName = (key) => {
  let color = ''
  for (let i = 0; i < typeData.value.length; i++) {
    if (typeData.value[i].key === key) {
      color = typeData.value[i].name
      break
    }
  }
  return color
}

// 修改日程状态
const scheduleRecord = (item) => {
  emit('scheduleRecord', item)
  setTimeout(() => {
    getScheduleList()
  }, 300)
}

const clickComplete = () => {
  complete.value = !complete.value
}

// 隐藏已完成待办
const showItem = (item) => {
  if (complete.value) {
    return true
  } else {
    return !(item.finish === 3)
  }
}

// 过滤没有日程的数据
data.selected = computed(() => {
  let res = []
  if (data.countData.length > 0) {
    data.countData.forEach((value) => {
      if (value.no_submit > -1) {
        res.push({
          date: value.time,
          info: value.no_submit === 0 ? '已完成' : '未完成',
          color: value.no_submit === 0 ? '#1890FF' : '#dd524d',
        })
      }
    })
  }
  return res
})
</script>
<style scoped lang="scss">
.content {
  position: relative;
  width: 100%;
  background-color: #ffffff;

  .left {
    width: 68rpx;
  }

  .date {
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 500;
    font-size: 36rpx;
    margin-bottom: 38rpx;
    margin-top: 20rpx;
  }

  .schedule {
    height: calc(100vh - var(--schedule-header-height));
    padding-top: calc(var(--schedule-calendar-offset) + var(--height));
    box-sizing: border-box;

    .schedule-scroll {
      height: 100%;
    }

    ::v-deep .uni-list {
      .uni-list-item {
        height: 102rpx;
        margin-bottom: 6px;
        background-color: #f2f6fc;
        border-radius: 8rpx;

        .uni-list-item__container {
          height: 100%;
          padding: 0;
        }
      }

      .uni-list-item:last-child {
        margin-bottom: 0;
      }

      .uni-list--border {
        left: auto;
      }
    }

    ::v-deep .empty {
      padding-bottom: 120px;
    }

    .list-item {
      padding: 16rpx;
      width: 100%;
      font-size: $uni-font-size-default;
      color: #333333;
      border-left-style: solid;
      border-left-width: 2px;
      border-radius: 8rpx;

      .list-item-left {
        font-family:
          PingFang SC,
          PingFang SC;
        font-weight: 500;
        font-size: 24rpx;
        color: #333333;
      }

      .list-item-time {
        display: block;
        font-size: 20rpx;
        color: #606266;

        .pb8 {
          padding-bottom: 16rpx;
        }
      }

      .list-item-right {
        .iconfont {
          font-size: 36rpx;
          // color: $uni-color-primary;
        }

        .icon-denglu-tongyi {
          font-size: 16px;
        }

        .icon-no-check {
          display: inline-block;
          width: 15px;
          height: 15px;
          border-radius: 50%;
          background: #ffffff;
          border: 1px solid #eeeeee;
        }
      }

      .image-item {
        width: 36rpx;
        height: 36rpx;
      }
    }
  }
}

.mb20 {
  margin-bottom: 40rpx;
}

.month-text {
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 500;
  font-size: 40rpx;
  color: #333333;
  margin-bottom: 20rpx;
}

.schedule-box {
  position: relative;
  --schedule-calendar-offset: 54px;

  .oa-calendar {
    width: 100%;
    position: fixed;
    top: var(--schedule-header-height);
    z-index: 99;
    background: #fff;
    padding-bottom: 12rpx;
  }
  .calendar-title {
    text-align: center;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 500;
    font-size: 32rpx;
    color: #333333;
    padding-top: 16rpx;
  }

  .nl-schedule {
    padding-top: var(--schedule-calendar-offset);
  }
}

.box-shadow {
  box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.06) !important;
}

.add-text {
  color: #1890ff !important;
}

::v-deep .schedule-content .add-wrap {
  z-index: 2;
}

.add {
  position: fixed;
  right: 20rpx;
  cursor: pointer;
  bottom: 145rpx;
  width: 42px;
  height: 42px;
  background: linear-gradient(135deg, #47b5ff 0%, #0f86f5 100%);
  box-shadow: 0px 4px 4px 0px rgba(28, 146, 248, 0.1145);
  border-radius: 50%;
  text-align: center;
  line-height: 42px;
  color: #fff;

  .icon-xuanfuanniu-jia {
    font-size: 15px;
  }
}

.no-schedule {
  width: 100%;
  font-weight: 400;
  font-size: 12px;
  color: #9e9e9e;
}

.bottom {
  width: 35px;
  flex-shrink: 0; // flex布局下图片挤压变形
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 18px;
  color: #606266;
  display: flex;
  flex-direction: column;

  .week-text {
    font-size: 12px !important;
  }
}

.add-box {
  display: flex;
  height: 38px;
  align-items: center;
  margin-top: 0px;
  // margin-bottom: 20px
}

.active-text {
  .list-item-left {
    color: rgba(51, 51, 51, 0.5) !important;
  }

  .list-item-time {
    color: rgba(96, 98, 102, 0.5) !important;
  }
}
</style>

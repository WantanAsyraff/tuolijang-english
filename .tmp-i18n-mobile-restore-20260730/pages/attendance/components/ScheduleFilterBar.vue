<template>
  <view class="bar-wrapper">
    <view class="bar-item">
      <picker :range="cycleConfig" @change="handleCycleChange" range-key="label" :value="configData.cycleIndex">
        <view class="bar-item-content">
          周期： {{ cycleText }}
          <view class="iconfont icon-zhankai"></view>
        </view>
      </picker>
    </view>
    <view class="bar-item">
      <picker :range="[yearList, monthList]" mode="multiSelector" @change="handleDateByMonthChange"
        v-if="cycleType === 'month'" :value="configData.dateIndexByMonth">
        <view class="bar-item-content">
          {{ dateRangeText }}
          <view class="iconfont icon-zhankai"></view>
        </view>
      </picker>
      <view class="bar-item-content" @click="handleWeekRangeClick" v-else>
        {{ weekDateRangeText }}
        <view class="iconfont icon-zhankai"></view>
      </view>
    </view>

    <uni-calendar :date="configData.weekDate" :insert="false" ref="weekCalenderRef" @confirm="handleWeekRangeChange"
      weekRange />

  </view>
</template>

<script setup lang="ts">
  import message from '@/utils/message';
  import moment from 'moment';

  const props = defineProps<{
    dateByMonth : string;
    dateByWeek : string;
    groupId : string;
    cycleType : string;
  }>();

  const { dateByMonth, dateByWeek, cycleType } = toRefs(props);

  const emit = defineEmits(["changeCycleType", "changeDateByMonth", "changeDateByWeek"]);

  const cycleConfig = [
    {
      label: "月",
      type: "month"
    },
    {
      label: "周",
      type: "week"
    }
  ];

  const configData = reactive({
    cycleText: "",
    cycleIndex: 0,
    dateIndexByMonth: [0, 0],

    monthDate: "",
    weekDate: ""
  });

  const weekCalenderRef = ref();

  const momentInstance = moment();
  const currentYear = momentInstance.year();
  const currentMonth = momentInstance.month() + 1;

  const monthList = Array.from({ length: 12 }, (_, index) => index + 1);
  const yearList = Array.from({ length: 100 }, (_, index) => 1970 + index);

  const dateRangeText = computed(() => {
    return moment(configData.monthDate).startOf("month").format("MM/DD") + "-" + moment(configData.monthDate).endOf("month").format("MM/DD");
  });

  const cycleText = computed(() => {
    return cycleConfig.find(item => item.type === cycleType.value)?.label || "";
  });

  const weekDateRangeText = computed(() => {
    return moment(configData.weekDate).startOf("week").format("MM/DD") + "-" + moment(configData.weekDate).endOf("week").format("MM/DD");
  });

  const handleDateByMonthChange = (e : any) => {
    const [yearIndex, monthIndex] = e.detail.value;
    const year = yearList[yearIndex];
    const month = monthList[monthIndex];
    const isPrevYear = year < currentYear;
    const isEqualYear = year === currentYear;
    const isPrevMonth = month < currentMonth;
    if (isPrevYear || isEqualYear && isPrevMonth) {
      message.error("不能选择之前的日期", 'none');
      return;
    }

    const date = `${year}-${month.toString().padStart(2, '0')}`;
    emit("changeDateByMonth", date);
  }


  const handleCycleChange = (e : any) => {
    emit("changeCycleType", cycleConfig[e.detail.value].type);
  }


  const handleWeekRangeClick = () => {
    weekCalenderRef.value.open();
  }

  const handleWeekRangeChange = (e : any) => {
    const instance = moment(e.fulldate);
    const isPrevDate = instance.endOf("week").isBefore(moment(), "day");
    if (isPrevDate) {
      message.error("不能选择之前的日期", 'none');
      return;
    }

    const weekStartDay = instance.startOf("week").format("YYYY-MM-DD");
    emit("changeDateByWeek", weekStartDay);
  }


  const updateInnerWeekDate = (date : string) => {
    const instance = moment(date);
    configData.weekDate = instance.startOf("week").format("YYYY-MM-DD");
  }

  const updateInnerMonthDate = (date : string) => {
    const instance = moment(date);
    const yearIndex = instance.year() - 1970;
    const monthIndex = instance.month();
    configData.monthDate = date;
    configData.dateIndexByMonth = [yearIndex, monthIndex];
  }

  const updateInnerDate = (date : string) => {
    updateInnerWeekDate(date);
    updateInnerMonthDate(date);
  }

  watch(cycleType, () => {
    configData.cycleIndex = cycleConfig.findIndex(item => item.type === cycleType.value);
  }, { immediate: true });

  watch(
    [dateByMonth, dateByWeek],
    () => {
      if (cycleType.value === "month") {
        updateInnerDate(dateByMonth.value);
      } else {
        updateInnerDate(dateByWeek.value);
      }
    },
    { immediate: true }
  )
</script>

<style lang="scss" scoped>
  .bar-wrapper {
    height: 80rpx;
    background-color: #fff;

    font-size: 28rpx;
    color: #606266;

    display: flex;
  }

  .bar-item {
    flex: 1;
    display: flex;
    align-items: center;

    .bar-item-content,
    picker {
      width: 100%;
    }

    .bar-item-content {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8rpx;
    }

    .iconfont {
      font-size: 16rpx;
      color: #c0c4cc;
    }
  }
</style>
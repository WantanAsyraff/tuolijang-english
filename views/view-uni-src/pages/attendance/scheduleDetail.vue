<template>
  <view class="base-container" :style="styles">
    <view>
      <uni-nav-bar background-color="transparent" :border="false" status-bar :title="$t('ui.attendanceScheduleDetailSchedule')" left-icon="left"
        @clickLeft="handleBack" dark class="custom-nav-bar" />
      <ScheduleFilterBar :dateByMonth="configData.dateByMonth" :dateByWeek="configData.dateByWeek" :groupId="groupId"
        :cycleType="cycleType" @changeCycleType="handleChangeCycleType" @changeDateByMonth="handleChangeDateByMonth"
        @changeDateByWeek="handleChangeDateByWeek" />
    </view>
    <ScheduleTable :cycleType="cycleType" :date="realDate" />
    <ScheduleToolbar @submit="handleSaveSchedult" :groupId="groupId" />
  </view>
</template>

<script setup lang="ts">
// @ts-ignore
import moment from 'moment';
import ScheduleFilterBar from './components/ScheduleFilterBar.vue';
// @ts-ignore
import ScheduleTable from './components/ScheduleTable.vue';
// @ts-ignore
import ScheduleToolbar from './components/ScheduleToolbar.vue';

import { attendanceScheduleCycleListApi, attendanceScheduleInfoApi, attendanceScheduleSaveApi } from '@/api/attendance';
import { useScheduleEdit } from './composables/useScheduleEdit';
import { useScheduleDate } from './composables/useScheduleDate';
import message from '@/utils/message';

const { windowHeight } = uni.getSystemInfoSync();
const styles = {
  "--full-h": windowHeight + 'px'
};

const cycleType = ref("month"); // month or week
const scheduleInfoMapByMonth = ref<Record<string, any>>({});
const groupId = ref("");
const cycleList = ref([]);

const configData = reactive({
  dateByMonth: "",
  dateByWeek: "",
});

const members = computed(() => {
  const monthKeys = Object.keys(scheduleInfoMapByMonth.value);
  if (!monthKeys.length) return [];
  return scheduleInfoMapByMonth.value[monthKeys[0]].members;
});

const { realDate, dateList, updateRealDate } = useScheduleDate(cycleType, scheduleInfoMapByMonth);

const { cleanEditData } = useScheduleEdit(dateList, members, cycleList, scheduleInfoMapByMonth);

const handleChangeCycleType = (_cycleType: string) => {
  cycleType.value = _cycleType;
}


const handleChangeDateByMonth = (date: string) => {
  configData.dateByMonth = date;
  updateRealDate(date);
}

const handleChangeDateByWeek = (date: string) => {
  configData.dateByWeek = date;
  updateRealDate(date);
}

const handleBack = () => {
  uni.navigateBack();
}

const handleGetScheduleInfo = async (date: string) => {
  try {
    const result = await attendanceScheduleInfoApi(groupId.value, { date });
    return result;
  } catch (err) {
    message.error(err.message, 'none');
    throw err;
  }
};

const handleProcessScheduleInfo = async (nextVal: any[], prevVal: any[]) => {
  const [cycleType, date] = nextVal;
  const [prevCycleType, prevDate] = prevVal;
  const isEqualCycleType = cycleType === prevCycleType;
  const isEqualDate = date === prevDate;
  const isWeekCycleType = cycleType === 'week';

  // 前后类型同为月周期，且日期相同，不做处理
  if (!isWeekCycleType && isEqualCycleType && isEqualDate) return;

  const dateInstance = moment(date);

  // 周类型
  if (isWeekCycleType) {
    const monthOfWeekStart = dateInstance.startOf("week").format("YYYY-MM");
    const monthOfWeekEnd = dateInstance.endOf("week").format("YYYY-MM");
    const isEqualMonth = monthOfWeekStart === monthOfWeekEnd;

    const taskList = [
      handleGetScheduleInfo(monthOfWeekStart),
    ];

    // 新选择的周和上一次所选周所处月份不同时，则要请求下个月的数据
    if (!isEqualMonth) {
      taskList.push(handleGetScheduleInfo(monthOfWeekEnd))
    }

    const results = await Promise.all(taskList);

    const nextData = {
      [monthOfWeekStart]: results[0].data,
    };


    if (results.length > 1) {
      nextData[monthOfWeekEnd] = results[1].data;
    }

    scheduleInfoMapByMonth.value = nextData;
  } else {
    const month = dateInstance.format("YYYY-MM");
    const result = await handleGetScheduleInfo(month);
    scheduleInfoMapByMonth.value = {
      [month]: result.data
    };
  }
}

const handleSaveSchedult = async () => {
  if (Object.keys(scheduleInfoMapByMonth.value).length === 0) return;

  uni.showLoading({
    mask: true
  });
  
  const waitSaveDataList = Object.entries(scheduleInfoMapByMonth.value).map(([date, data]) => {
    return {
      date,
      data: data.arrange
    }
  });

  const tasks = waitSaveDataList.map(data => attendanceScheduleSaveApi(groupId.value, data));

  try {
    const results = await Promise.all(tasks);
    uni.hideLoading();
    message.success("保存成功", "success");
  } catch (err) {
    uni.hideLoading();
    message.error(err.message, 'none');
  }
}

const getCycleList = async () => {
  const result = await attendanceScheduleCycleListApi(groupId.value);
  cycleList.value = result.data;
}

provide("scheduleMixin", {
  scheduleInfoMapByMonth,
  realDate,
  cycleType,
  groupId,
  dateList,
  members,
  cycleList
});

watch(
  [cycleType, realDate],
  (newVal, oldVal) => {
    handleProcessScheduleInfo(newVal, oldVal);
    cleanEditData();
    uni.$emit("scheduleTablePopupClose");
  }
)

onLoad((options) => {
  groupId.value = options.group_id;
  realDate.value = configData.dateByMonth = configData.dateByWeek = options.date;
  getCycleList();
});

</script>

<style scoped lang="scss">
.base-container {
  height: var(--full-h);
  display: flex;
  flex-flow: column nowrap;
  gap: 20rpx;
}

.custom-nav-bar {
  background: linear-gradient(90deg, #459FFF 0%, #388AEF 100%, #3384E7 100%);
}
</style>

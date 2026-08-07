<template>
  <view class="main">
    <view class="header-fixed">
      <uni-nav-bar background-color="transparent" :border="false" status-bar left-icon="left" :title="$t('ui.attendanceShiftAddShiftSettings')" dark
        class="custom-nav-bar" @clickLeft="handleBack" />
    </view>
  </view>
  <view class="page-body">
    <view class="page-card">
      <view class="form-item" style="margin-top: 6rpx">
        <view class="form-item-label" data-required>{{ $t('ui.attendanceShiftAddShiftName') }}</view>
        <input type="text" :placeholder="$t('ui.attendanceShiftAddEnter')" class="form-item-input" v-model="shiftForm.name" />
      </view>
      <view class="form-item" style="margin-top: 48rpx">
        <view class="form-item-label" data-required>{{ $t('ui.attendanceShiftAddOneWorkPeriod') }}</view>
        <PopupListPicker style="flex: 1;" :title="$t('ui.attendanceShiftAddWorkPeriods')" :list="commutesNumberConfig"
          :activeIndex="shiftFormIndex.commutesNumberActiveIndex" @selectItem="handleSelectCommutesNumber">
          <view class="form-item-content">
            {{ commutesNumberConfig[shiftFormIndex.commutesNumberActiveIndex] }}
            <view class="iconfont icon-jinru-copy" />
          </view>
        </PopupListPicker>
      </view>
    </view>

    <view class="page-card">
      <view class="card-tips">
        {{ shiftFormIndex.commutesNumberActiveIndex === 0 ? '' : $t('ui.attendanceShiftAddFirst') }}{{ $t('ui.attendanceShiftAddStartTimeSettings') }}
      </view>
      <ShiftAddForm @change="handleFormChange" prefix="number1" :form="shiftForm.number1" type="work" />
      <view class="card-tips">
        {{ shiftFormIndex.commutesNumberActiveIndex === 0 ? '' : $t('ui.attendanceShiftAddFirst') }}{{ $t('ui.attendanceShiftAddEndTimeSettings') }}
      </view>
      <ShiftAddForm @change="handleFormChange" prefix="number1" :form="shiftForm.number1" type="off-work" />
    </view>

    <view class="page-card" v-if="shiftFormIndex.commutesNumberActiveIndex === 1">
      <view class="card-tips">
        {{ $t('ui.attendanceShiftAddSecondStartTimeSettings') }}
      </view>
      <ShiftAddForm @change="handleFormChange" prefix="number2" :form="shiftForm.number2" type="work" />
      <view class="card-tips">
        {{ $t('ui.attendanceShiftAddSecondEndTimeSettings') }}
      </view>
      <ShiftAddForm @change="handleFormChange" prefix="number2" :form="shiftForm.number2" type="off-work" />
    </view>

    <view class="page-card" v-if="shiftFormIndex.commutesNumberActiveIndex === 0">
      <view class="card-action-box">
        <view>
          <view>{{ $t('ui.attendanceShiftAddBreakTime') }}</view>
          <view class="card-tips" style="margin-top: 8rpx; font-size: 12px;">
            {{ $t('ui.attendanceShiftAddBreakTimeIsExcludedFromWorkingHours') }}
          </view>
        </view>
        <switch :checked="shiftForm.rest_time === 1" @change="handleRestTimeChange" />
      </view>
      <ShiftAddForm @change="handleFormChange" prefix="" :form="shiftForm" type="rest"
        :allowNextDay="shiftForm.number1.second_day_after === 1" />
    </view>
    <view class="overtime-title">{{ $t('ui.attendanceShiftAddOvertimeStart') }}</view>
    <view class="page-card">
      <view class="form-item">
        <view class="form-item-label" data-required>{{ $t('ui.attendanceShiftAddTimeAfterFinalClockOutBeforeOvertimeStarts') }}</view>
        <picker mode="multiSelector" style="flex: 1;" :range="hourTimeRange" :value="shiftFormIndex.overtimeIndex"
          @change="handleOvertimeChange">
          <view class="form-item-content">
            {{ formatHourTime(shiftFormIndex.overtimeIndex) }}
            <view class="iconfont icon-jinru-copy" />
          </view>
        </picker>
      </view>
    </view>

    <view class="work-time-total-box">
      <view class="work-time-total-title">
        {{ $t('ui.attendanceShiftAddTotalWorkingHours') }}&nbsp;&nbsp;<text>{{ formatTotalTime(shiftForm.work_time) }}</text>
      </view>
      <view class="work-time-total-btn" @click="handleSave">{{ $t('ui.attendanceShiftAddSave') }}</view>
    </view>
  </view>
</template>

<script setup lang="ts">import appI18n from '@/locale';

import PopupListPicker from "@/components/PopupListPicker/index.vue";
import ShiftAddForm from "./components/ShiftAddForm.vue";
import { hourTimeRange, formatHourTime } from '@/utils/date';
import message from '@/utils/message';
import { attendanceScheduleShiftAddApi, attendanceScheduleShiftEditApi, attendanceScheduleShiftInfoApi } from '@/api/attendance';
import moment from "moment";

const props = withDefaults(defineProps<{
  id: string
}>(), {
  id: ''
});

const generateInitData = () => {
  return {
    first_day_after: 0, // 上班时间 0 => 当日, 1 => 次日
    work_hours: '09:00', // 上班时间
    late: 600, // 达到该时长即为迟到
    extreme_late: 1800, // 达到该时长即为严重迟到
    late_lack_card: 3600, // 达到该时长即为半天缺卡
    early_card: 1800, // 最多提前多久打卡

    second_day_after: 0, // 下班时间 0 => 当日, 1 => 次日
    off_hours: '18:00', // 下班时间
    early_leave: 600, // 提前多久打卡为早退
    early_lack_card: 1800, // 提前多久打卡为半天缺卡
    delay_card: 1800, // 最多延后多久打卡
    free_clock: 1 // 下班可免打卡
  };
}

const shiftForm = reactive<Record<string, any>>({
  color: "#1890ff", // 班次颜色

  name: '', // 班次名称
  number: 1, // 上下班次数
  work_time: '', // 工作总时间

  number1: generateInitData(),
  number2: generateInitData(),

  rest_time: 0, // 是否包含中途休息时间
  rest_start_after: 0, // 休息开始时间 0 => 当日, 1 => 次日
  rest_start: '12:00', // 休息开始时间
  rest_end_after: 0, // 休息结束时间 0 => 当日, 1 => 次日
  rest_end: '13:30', // 休息结束时间
  overtime: 0, // 多久下班后开始计算加班
});

const shiftFormIndex = ref({
  overtimeIndex: [0, 0],

  commutesNumberActiveIndex: 0, // 上下班次数列表索引
});

const commutesNumberConfig = [
  '一次上下班',
  '两次上下班'
];

// 选择上下班次数
const handleSelectCommutesNumber = (index: number) => {
  shiftFormIndex.value.commutesNumberActiveIndex = index;
  shiftForm.number = index === 0 ? 1 : 2;
  if (shiftForm.number === 1) {
    // 一次上下班
    shiftForm.number1.second_day_after = 0;
    shiftForm.number1.work_hours = "09:00";
    shiftForm.number1.off_hours = "18:00";
  } else {
    // 两次上下班
    shiftForm.number1.second_day_after = 0;
    shiftForm.number1.work_hours = "09:00";
    shiftForm.number1.off_hours = "12:00";
    shiftForm.number2.first_day_after = 0;
    shiftForm.number2.second_day_after = 0;
    shiftForm.number2.work_hours = "14:00";
    shiftForm.number2.off_hours = "18:00";
  }
  updateWorkTime(shiftForm);
};

const handleBack = () => {
  uni.navigateBack();
}

// 格式化合计工作时长
const formatTotalTime = (time: string) => {
  return time.replace('小时', ' 小时 ').replace('分钟', ' 分钟');
}

// 统一更新表单信息，如果是上下班时间设置，需要携带前缀
const handleFormChange = (prefix: string, value: Record<string, any>) => {
  if (prefix) {
    shiftForm[prefix] = {
      ...shiftForm[prefix],
      ...value
    };
  } else {
    Object.entries(value).forEach(([k, v]) => {
      shiftForm[k] = v;
    });
  }
  updateWorkTime(shiftForm);
}

// 选择是否包含中途休息时间
const handleRestTimeChange = (e: any) => {
  shiftForm.rest_time = e.detail.value ? 1 : 0;
  updateWorkTime(shiftForm);
}

// 选择加班起算时间
const handleOvertimeChange = (e: any) => {
  shiftFormIndex.value.overtimeIndex = e.detail.value;
  shiftForm.overtime = e.detail.value[0] * 3600 + e.detail.value[1] * 60;
}

// 保存
const handleSave = async () => {
  uni.showLoading({
    title: appI18n.global.t('ui.attendanceShiftAddSaving'),
    mask: true
  });

  const fn = props.id ? () => attendanceScheduleShiftEditApi(props.id, shiftForm) : () => attendanceScheduleShiftAddApi(shiftForm);

  try {
    const res = await fn();
    uni.hideLoading();
    message.success(res.message, 'success');
    setTimeout(() => {
      uni.navigateBack();
    }, 1000);
  } catch (err) {
    uni.hideLoading();
    message.error(err.message, 'error');
  }
}

const MINUTES_PER_DAY = 24 * 60;

// 统一换算为“从首日 00:00 开始的绝对分钟数”后再求差值，避免跨天时先拆小时和分钟导致负分钟借位。
const getAbsoluteMinutes = (time: string, dayAfter: number = 0): number => {
  const [hour = '0', minute = '0'] = time.split(':');
  return Number(dayAfter) * MINUTES_PER_DAY + Number(hour) * 60 + Number(minute);
}

const formatWorkTime = (totalMinutes: number): string => {
  const normalizedMinutes = Math.max(totalMinutes, 0);
  const hours = Math.floor(normalizedMinutes / 60);
  const minutes = normalizedMinutes % 60;
  return `${hours}小时${minutes}分钟`;
}

const showWorkTimeError = (text: string): null => {
  message.error(text, 'error');
  return null;
}

const getShiftRangeMinutes = (
  startTime: string,
  startDayAfter: number,
  endTime: string,
  endDayAfter: number
): { startMinutes: number; endMinutes: number; durationMinutes: number } => {
  const startMinutes = getAbsoluteMinutes(startTime, startDayAfter);
  const endMinutes = getAbsoluteMinutes(endTime, endDayAfter);
  return {
    startMinutes,
    endMinutes,
    durationMinutes: endMinutes - startMinutes
  };
}

// 第二次上班的“是否次日”依赖第一次下班的结果，这里集中同步，避免字段残留旧状态。
const syncSecondCommuteDayAfter = (form: Record<string, any>): void => {
  form.number2.first_day_after = form.number1.second_day_after === 1 ? 1 : 0;
  if (form.number2.first_day_after === 1) {
    form.number2.second_day_after = 1;
  }
}

const getSingleShiftDurationMinutes = (form: Record<string, any>): number | null => {
  const workRange = getShiftRangeMinutes(
    form.number1.work_hours,
    form.number1.first_day_after,
    form.number1.off_hours,
    form.number1.second_day_after
  );

  if (workRange.durationMinutes < 0) {
    return showWorkTimeError('下班时间要大于上班时间');
  }

  if (form.number1.second_day_after === 0) {
    form.rest_start_after = 0;
    form.rest_end_after = 0;
  } else if (form.rest_start_after === 1) {
    form.rest_end_after = 1;
  }

  if (form.rest_time !== 1) {
    return workRange.durationMinutes;
  }

  const restRange = getShiftRangeMinutes(
    form.rest_start,
    form.rest_start_after,
    form.rest_end,
    form.rest_end_after
  );

  if (restRange.startMinutes < workRange.startMinutes) {
    return showWorkTimeError('中途休息开始时间要大于上班时间');
  }

  if (restRange.endMinutes > workRange.endMinutes) {
    return showWorkTimeError('中途休息结束时间要小于下班时间');
  }

  if (restRange.durationMinutes < 0) {
    return showWorkTimeError('中途休息结束时间要大于开始时间');
  }

  return workRange.durationMinutes - restRange.durationMinutes;
}

const getDoubleShiftDurationMinutes = (form: Record<string, any>): number | null => {
  syncSecondCommuteDayAfter(form);

  const firstRange = getShiftRangeMinutes(
    form.number1.work_hours,
    form.number1.first_day_after,
    form.number1.off_hours,
    form.number1.second_day_after
  );

  if (firstRange.durationMinutes < 0) {
    return showWorkTimeError('下班时间要大于上班时间');
  }

  const secondRange = getShiftRangeMinutes(
    form.number2.work_hours,
    form.number2.first_day_after,
    form.number2.off_hours,
    form.number2.second_day_after
  );

  if (secondRange.durationMinutes < 0) {
    return showWorkTimeError('下班时间要大于上班时间');
  }

  return firstRange.durationMinutes + secondRange.durationMinutes;
}

// 更新合计工作时长
function updateWorkTime(newVal: any): void {
  const totalMinutes = newVal.number == 1
    ? getSingleShiftDurationMinutes(newVal)
    : getDoubleShiftDurationMinutes(newVal);

  if (totalMinutes === null) return;
  shiftForm.work_time = formatWorkTime(totalMinutes);
}

updateWorkTime(shiftForm);

// 获取班次信息
const getShiftInfo = () => {
  attendanceScheduleShiftInfoApi(props.id).then(({ data }: any) => {
    for (const key in shiftForm) {
      if (data[key] !== undefined && data[key] !== null && !Array.isArray(data[key])) {
        if (key === 'number1' || key === 'number2') {
          for (const subKey in shiftForm[key]) {
            if (data[key][subKey] !== undefined && data[key][subKey] !== null) {
              shiftForm[key][subKey] = data[key][subKey];
            }
          }
        } else {
          shiftForm[key] = data[key];
        }
      }
    }

    const duration = moment.duration(shiftForm.overtime, 'seconds');

    // 提取小时和分钟
    const hours = duration.hours();
    const minutes = duration.minutes();

    shiftFormIndex.value.overtimeIndex = [hours, minutes];
    shiftFormIndex.value.commutesNumberActiveIndex = shiftForm.number === 2 ? 1 : 0;
    updateWorkTime(shiftForm);
  });
}


props.id && getShiftInfo();


</script>

<style scoped lang="scss">
.main {
  padding-top: calc(44px + var(--status-bar-height));
  padding-bottom: 24rpx;
}

.header-fixed {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1;
}

.custom-nav-bar {
  background: linear-gradient(90deg, #459FFF 0%, #388AEF 100%, #3384E7 100%);
}

.page-body {
  padding: 20rpx;
  padding-bottom: 260rpx;
  font-weight: 400;
  font-size: 15px;
  color: #303133;
  line-height: 42rpx;
}

.page-card {
  background-color: #fff;
  border-radius: 12rpx;
  padding: 24rpx;

  &+& {
    margin-top: 20rpx;
  }
}

.form-item {
  display: flex;
  align-items: center;

  .form-item-label {
    &[data-required] {
      &::after {
        content: '*';
        color: #FF2529;
        margin-left: 8rpx;
        vertical-align: middle;
      }
    }
  }

  .form-item-content,
  .form-item-input {
    flex: 1;
    text-align: right;
    margin-left: 20rpx;
  }

  .form-item-content {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    color: #606266;

    .iconfont {
      font-size: 12px;
      color: #C0C4CC;
      margin-left: 14rpx;
    }
  }
}

:deep(.input-placeholder) {
  color: #C0C4CC;
}

.card-tips {
  font-size: 26rpx;
  color: #909399;
  line-height: 36rpx;
  margin-bottom: 20rpx;

  &:not(:first-child) {
    margin-top: 34rpx;
  }
}

.overtime-title {
  padding: 30rpx 0 20rpx 24rpx;
}

.card-action-box {
  display: flex;
  justify-content: space-between;
  align-items: center;

  switch {
    transform: scale(0.75);
  }
}

.work-time-total-box {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #fff;
  padding: 24rpx 20rpx;
}

.work-time-total-title {
  margin-bottom: 24rpx;
  font-size: 26rpx;
  color: #909399;

  text {
    color: #303133;
  }
}

.work-time-total-btn {
  height: 86rpx;
  background: #1890FF;
  border-radius: 12rpx;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>

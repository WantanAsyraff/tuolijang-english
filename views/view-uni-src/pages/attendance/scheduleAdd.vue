<template>
  <view class="base-container">
    <uni-nav-bar background-color="transparent" :border="false" status-bar :title="$t('ui.attendanceScheduleAddAddSchedule')" left-icon="left"
      @clickLeft="handleBack" dark class="custom-nav-bar" />
    <view class="form-wrapper">
      <view class="form-item">
        <view class="form-item-label">
          {{ $t('ui.attendanceRulesAttendanceTime') }}
        </view>
        <view class="form-item-content over-text">
          <picker mode="multiSelector" :range="scheduleDateRange" @change="handleScheduleDateChange"
            :value="[0, month - 1]">
            <view class="picker-content">
              {{ scheduleDateText }}
              <view class="iconfont icon-jinru-copy" />
            </view>
          </picker>
        </view>
      </view>
      <view class="form-item">
        <view class="form-item-label">
          {{ $t('ui.attendanceScheduleAddAttendanceGroupName') }}
        </view>
        <view class="form-item-content over-text">
          <view class="picker-content" @click="handleMultiplePickerShow">
            {{ selectGroupText }}
            <view class="iconfont icon-jinru-copy" />
          </view>
          <multiplePicker :show="scheduleData.multiplePickerShow" :columns="scheduleData.groupList"
            :defaultIndex="scheduleData.selectGroupIDs" @change="handleMultiplePickerChange"
            @cancel="handleMultiplePickerCancel"></multiplePicker>
        </view>
      </view>
    </view>

    <view class="submit-btn-wrapper">
      <view class="submit-btn" @click="handleSubmit">
        {{ $t('ui.replyComponentIndexSubmit') }}
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { attendanceGroupListApi, attendanceScheduleAddApi } from '@/api/attendance';
import multiplePicker from '@/components/multiplePicker/index.vue'
import message from '@/utils/message';


const date = new Date();
const year = date.getFullYear();
const month = date.getMonth() + 1;

const yearRange = Array.from({ length: 10 }, (_, index) => year + index);
const monthRange = Array.from({ length: 12 }, (_, index) => index + 1);

const scheduleData = reactive({
  yearIndex: -1,
  monthIndex: -1,
  groupList: [],
  selectGroupIDs: [],
  selectGroupNames: [],
  multiplePickerShow: false
});

const scheduleDateRange = computed(() => {
  return [yearRange, monthRange];
});

const scheduleDateText = computed(() => {
  if (scheduleData.yearIndex === -1 || scheduleData.monthIndex === -1) {
    return '请选择月份';
  }
  return `${yearRange[scheduleData.yearIndex]}年${monthRange[scheduleData.monthIndex]}月`;
});

const selectGroupText = computed(() => {
  if (scheduleData.selectGroupNames.length === 0) {
    return '请选择考勤组';
  }
  return scheduleData.selectGroupNames.join('/');
});

const handleMultiplePickerShow = () => {
  scheduleData.multiplePickerShow = true;
};

const handleMultiplePickerCancel = () => {
  scheduleData.multiplePickerShow = false;
};

const handleMultiplePickerChange = (e: any) => {
  scheduleData.selectGroupIDs = [...e.value];
  scheduleData.selectGroupNames = e.selected.map((i: any) => i.text);
  handleMultiplePickerCancel();
};

const handleBack = () => {
  uni.navigateBack();
};

const handleScheduleDateChange = (e: any) => {
  const [yearIndex, monthIndex] = e.detail.value;
  scheduleData.yearIndex = yearIndex;
  scheduleData.monthIndex = monthIndex;
};

const handleSubmit = async () => {
  if (scheduleData.yearIndex === -1 || scheduleData.monthIndex === -1) {
    message.error("请选择考勤时间!", "none");
    return;
  }

  const _year = yearRange[scheduleData.yearIndex];
  const _month = monthRange[scheduleData.monthIndex];

  if (_year === year && _month < month) {
    message.error("不能选择历史月份!", "none");
    return;
  }

  if (scheduleData.selectGroupIDs.length === 0) {
    message.error("请选择考勤组!", "none");
    return;
  }

  uni.showLoading({
    mask: true
  });


  const data = {
    date: `${_year}-${_month.toString().padStart(2, '0')}`,
    groups: scheduleData.selectGroupIDs
  }

  try {
    await attendanceScheduleAddApi(data);
    uni.hideLoading();
    message.success("新增排班成功!", "none");

    setTimeout(() => {
      uni.navigateBack();
    }, 800);
    try {
      const pages = getCurrentPages();
      const prevPage = pages.at(-2);
      prevPage?.$vm.$.exposed.refreshList();
    } catch {}
  } catch (error) {
    message.error("新增排班失败!", "none");
    uni.hideLoading();
  }

};



onLoad(async () => {
  const res = await attendanceGroupListApi({
    page: 1
  });

  scheduleData.groupList = res.data.list.map((i: any) => {
    return {
      ...i,
      value: i.id
    }
  });
});

</script>
<style lang="scss" scoped>
.custom-nav-bar {
  background: linear-gradient(90deg, #459FFF 0%, #388AEF 100%, #3384E7 100%);
}

.form-wrapper {
  margin: 20rpx;
  background-color: #fff;
  border-radius: 12rpx;
  padding: 8rpx 24rpx;
  font-size: 30rpx;
  color: #303133;
}


.form-item {
  display: flex;
  height: 90rpx;
  align-items: center;
}

.form-item-label {
  width: 180rpx;

  &::after {
    content: '*';
    color: red;
  }
}

.form-item-content {
  flex: 1;
}

.picker-content {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  font-size: 30rpx;
  color: #C0C4CC;

  .iconfont {
    font-size: 20rpx;
    margin-left: 10rpx;
  }
}

.submit-btn-wrapper {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 20rpx;
  background-color: #fff;
}

.submit-btn {
  background-color: #1890ff;
  color: #fff;
  height: 86rpx;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 12rpx;
  font-weight: 400;
  font-size: 30rpx;
  color: #FFFFFF;
}
</style>

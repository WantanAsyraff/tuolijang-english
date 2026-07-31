<template>
  <view class="main">
    <view class="header-fixed">
      <uni-nav-bar background-color="transparent" :border="false" status-bar left-icon="left" title="排班管理" dark
        class="custom-nav-bar" right-icon="plusempty" @clickRight="handleAdd" />
      <view class="search-bar">
        <view class="search-bar-body">
          <view class="iconfont icon-sousuo"></view>
          <input type="text" placeholder="请输入考勤组名称" placeholder-class="placeholder" @confirm="handleSearch" />
        </view>
      </view>
      <picker mode="multiSelector" :value="currentSelectDateRangeByIndex" class="date-picker-content"
        :range="monthPicker" @change="handlePickerChange">
        <view class="month-range">
          {{ scheduleData.monthRange }}
          <view class="iconfont icon-zhankai1"></view>
        </view>
      </picker>
    </view>
    <view class="schedule-list">
      <navigator class="schedule-item" v-for="item of scheduleData.list" :key="item.id"
        :url="`/pages/attendance/scheduleDetail?group_id=${item.group_id}&date=${formatDate(item.date)}`"
        hover-class="none">
        <view class="schedule-title">{{ formatScheduleMonth(item.date) }}排班表</view>
        <view class="schedule-info">
          <view class="schuedling-info-label">考勤组名</view>
          <view class="schuedling-info-value">{{ item.group.name }}</view>
        </view>
        <view class="schedule-info">
          <view class="schuedling-info-label">考勤成员</view>
          <view class="schuedling-info-value flex">
            <view class="over-text avatar-list flex">
              <image v-for="member of item.group.members.slice(0, 6)" :key="member.id" :src="member.avatar"
                class="member-avatar" />
            </view>
            <view class="member-count">等{{ item.group.members.length }}人</view>
          </view>
        </view>
      </navigator>
    </view>
    <view class="empty-container" v-if="scheduleData.list.length === 0">
      <image src="@/static/image/empty06.png" mode="aspectFit" />
      <view class="empty-text">暂无排班信息~</view>
    </view>
    <BottomNavigation :type="4" page-path="/pages/attendance/schedule" />
  </view>
</template>

<script setup lang="ts">
  import BottomNavigation from '@/components/bottomNavigation/index.vue'
  import { attendanceScheduleListApi } from '@/api/attendance';
  import moment from 'moment';
  import message from '@/utils/message';

  const scheduleData = reactive({
    list: [],
    count: 0,
    loaded: false,
    loading: false,
    monthRange: "考勤时间",
  });

  const queryParams = reactive({
    refreshFlag: false,
    page: 1,
    limit: 10,
    name: '',
    time: ''
  });

  const currentYear = (new Date).getFullYear();
  const currentMonth = (new Date).getMonth() + 1;
  const currentSelectDateRangeByIndex = ref([
    10,
    currentMonth - 1,
    10,
    currentMonth - 1
  ]);

  const yearRange = (() => {
    const year = currentYear;
    const start = year - 10;
    const end = year + 10;
    return Array.from({ length: end - start + 1 }, (_, i) => start + i);
  })();

  const monthRange = (() => {
    return Array.from({ length: 12 }, (_, i) => i + 1);
  })();

  const monthPicker = [
    yearRange,
    monthRange,
    yearRange,
    monthRange
  ];

  const formatDate = (date : string) => {
    return moment(date).format("YYYY-MM");
  }

  const formatScheduleMonth = (time : string) => {
    time = time.replace(/-/g, "/"); // ios fix
    const instance = new Date(time);
    const year = instance.getFullYear();
    const month = instance.getMonth() + 1;
    return `${year}年${month.toString().padStart(2, '0')}月`;
  }

  const handlePickerChange = (e : any) => {
    const [startYearIndex, startMonthIndex, endYearIndex, endMonthIndex] = e.detail.value;

    if (endYearIndex < startYearIndex || (endYearIndex === startYearIndex && endMonthIndex < startMonthIndex)) {
      message.error('结束时间不能小于开始时间', 'none');
      currentSelectDateRangeByIndex.value = [...currentSelectDateRangeByIndex.value];
      return;
    }

    currentSelectDateRangeByIndex.value = [
      startYearIndex,
      startMonthIndex,
      endYearIndex,
      endMonthIndex
    ];
    const startYear = yearRange[startYearIndex];
    const startMonth = monthRange[startMonthIndex];
    const endYear = yearRange[endYearIndex];
    const endMonth = monthRange[endMonthIndex];
    scheduleData.monthRange = `${startYear - 2000}年${startMonth}月-${endYear - 2000}年${endMonth}月`;

    queryParams.time = `${startYear}/${startMonth.toString().padStart(2, '0')}-${endYear}/${endMonth.toString().padStart(2, '0')}`;
    queryParams.page = 1;
    scheduleData.loaded = false;
  }

  const getScheduleList = async () => {
    if (scheduleData.loading || scheduleData.loaded) return;
    scheduleData.loading = true;
    const { refreshFlag, ...params } = queryParams;
    const res = await attendanceScheduleListApi(params);
    scheduleData.count = res.data.count;
    if (queryParams.page === 1) {
      scheduleData.list = res.data.list;
    } else {
      scheduleData.list = [...scheduleData.list, ...res.data.list];
    }
    scheduleData.loaded = res.data.list.length < queryParams.limit;
    scheduleData.loading = false;
  }

  const handleAdd = () => {
    uni.navigateTo({
      url: '/pages/attendance/scheduleAdd'
    })
  }

  const handleSearch = (e : any) => {
    scheduleData.loaded = false;
    queryParams.page = 1;
    queryParams.name = e.detail.value;
  }

  const refreshList = () => {
    scheduleData.loaded = false;
    queryParams.page = 1;
    queryParams.refreshFlag = !queryParams.refreshFlag;
  }

  defineExpose({
    refreshList
  });

  watch(
    queryParams,
    () => {
      getScheduleList();
    },
    {
      immediate: true
    }
  );

  onReachBottom(() => {
    if (scheduleData.loaded || scheduleData.loading) return;
    queryParams.page++;
  });
</script>

<style scoped lang="scss">
  .main {
    padding-top: calc(44px + var(--status-bar-height) + 96rpx + 74rpx);
    padding-bottom: calc(54px + 24rpx);
  }

  .month-range {
    display: flex;
    align-items: center;
    height: 74rpx;
    padding: 0 30rpx;
    font-size: 24rpx;
    color: #606266;
    background-color: #f5f5f5;

    .iconfont {
      font-size: 16rpx;
      margin-left: 8rpx;
      color: #C0C4CC;
    }
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

  .search-bar {
    padding: 16rpx 30rpx;
    background-color: #fff;
  }

  .search-bar-body {
    height: 64rpx;
    background: #F5F5F5;
    border-radius: 12rpx;

    display: flex;
    align-items: center;
    padding: 0 16rpx;

    .iconfont {
      font-size: 30rpx;
      color: #999;
      margin-right: 14rpx;
    }

    input {
      flex: 1;
    }
  }

  :deep(.placeholder) {
    font-size: 26rpx;
    color: #909399;
  }

  .schedule-item {
    width: 700rpx;
    margin: 0 auto;
    background-color: #fff;
    border-radius: 16rpx;
    padding: 24rpx 32rpx;
    display: block;
    box-sizing: border-box;

    &+& {
      margin-top: 20rpx;
    }
  }

  .schedule-title {
    font-weight: bold;
    font-size: 32rpx;
    color: #303133;
    line-height: 44rpx;
    margin-bottom: 32rpx;
  }

  .schedule-info {
    display: flex;
    font-size: 28rpx;
    line-height: 40rpx;
    align-items: center;

    .schuedling-info-label {
      color: #909399;
      margin-right: 24rpx;
    }

    .schuedling-info-value {
      color: #303133;
      flex: 1;
      min-width: 0;
      align-items: center;
    }

    &+& {
      margin-top: 32rpx;
    }
  }

  .avatar-list {

    // flex: 1;
    .member-avatar {
      width: 48rpx;
      height: 48rpx;
      border: 1rpx solid #EBEEF5;
      border-radius: 50%;

      &+.member-avatar {
        margin-left: -16rpx;
      }
    }
  }

  .member-count {
    min-width: 3.5em;
    margin-left: 16rpx;
  }

  .flex {
    display: flex;
  }

  .empty-container {
    margin-top: 100rpx;
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
<template>
  <view class="main" @click="handleGlobalClick" :class="{ 'has-bottom-action': !!props.groupId }">
    <view class="header-fixed">
      <uni-nav-bar background-color="transparent" :border="false" status-bar left-icon="left" :title="$t('ui.attendanceShiftShiftManagement')" dark
        class="custom-nav-bar" right-icon="plusempty" @clickRight="handleAdd" @clickLeft="handleBack" />

      <view class="search-bar">
        <view class="search-bar-body">
          <view class="iconfont icon-sousuo"></view>
          <input type="text" :placeholder="$t('ui.attendanceShiftSearchShiftNameOrCreator')" placeholder-class="placeholder" @confirm="handleSearch" />
        </view>
      </view>
    </view>

    <view class="shift-list" @click="handleListClick">
      <view class="shift-item" v-for="item of shiftData.list" :key="item.id">
        <view class="select-btn iconfont icon-xuanzhong" v-if="!!props.groupId"
          :class="{ 'selected': shiftData.selectItemIdSet.has(item.id) }" data-event="select" :data-id="item.id">
        </view>
        <view class="over-text">
          <view class="shift-item-title over-text">{{ item.name }}</view>
          <view class="shift-time-range">
            <view class="shift-time-range-item" v-for="(timeItem, idx) of item.times" :key="idx">{{
              timeItem.work_hours }} ~ {{ timeItem.off_hours }}</view>
          </view>
        </view>
        <view class="more-btn iconfont icon-yunwenjian-gengduo" data-event="show-menu" :data-id="item.id"></view>
      </view>
    </view>

    <drop-down ref="dropDownRef" :list-data="dropDownMenu" @btn-click="handleDropMenuClick"></drop-down>

    <view class="empty-container" v-if="shiftData.list.length === 0">
      <image src="@/static/image/empty06.png" mode="aspectFit" />
      <view class="empty-text">{{ $t('ui.attendanceShiftNoShiftInformation') }}</view>
    </view>

    <view class="bottom-action-wrap" v-if="!!props.groupId">
      <view class="select-shift-count">{{ $t('ui.attendanceShiftSelected') }}{{ shiftData.selectItemIdSet.size }}{{ $t('ui.attendanceShiftShifts') }}</view>
      <view class="confirm-btn" @click="handleSaveScheduleByShift">{{ $t('ui.baTreePickerIndexOk') }}</view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { saveAttendanceGroupShiftApi, attendanceScheduleShiftApi, attendanceScheduleShiftDelApi } from '@/api/attendance';
import DropDown from "@/components/DropDown/index.vue";
import message from '@/utils/message';

const props = withDefaults(defineProps<{
  groupId: string;
}>(), {
  groupId: ''
});

const dropDownRef = ref();
const scrollTop = ref(0);

const shiftData = reactive({
  list: [],
  limit: 10,
  page: 1,
  name: '',
  count: 0,
  loaded: false,
  loading: false,

  selectItemId: null, // 列表项右侧悬浮菜单对应的班次id

  selectItemIdSet: new Set() // 列表项左侧多选选中的班次id列表
});

const handleBack = () => {
  uni.navigateBack();
}

const handleDelShift = async () => {
  const res = await uni.showModal({
    title: '删除提示',
    content: '确认删除该班次吗？班次将无法恢复'
  });
  if (!res.confirm) return;
  uni.showLoading({ mask: true });
  try {
    const res = await attendanceScheduleShiftDelApi(shiftData.selectItemId);
    uni.hideLoading();
    message.success(res.message, "none");
    handleSearch(null);
  } catch (err) {
    uni.hideLoading();
    message.error(err.message, "none");
  }
}

const handleGlobalClick = (e: MouseEvent) => {
  const { id } = (e.target as HTMLElement).dataset;
  if (!id && shiftData.selectItemId) {
    dropDownRef.value.closeDropdown();
  }
}

const handleGoEditPage = () => {
  uni.navigateTo({
    url: `/pages/attendance/shiftAdd?id=${shiftData.selectItemId}`
  })
}

const handleSaveScheduleByShift = async () => {
  if (!props.groupId) return;
  if (shiftData.selectItemIdSet.size === 0) return message.error('请选择班次', 'none');
  uni.showLoading({ mask: true });
  try {
    const res = await saveAttendanceGroupShiftApi(props.groupId, {
      shift_id: Array.from(shiftData.selectItemIdSet)
    });
    uni.hideLoading();
    message.success(res.message, "none");
    uni.navigateBack();
  } catch (err) {
    uni.hideLoading();
    message.error(err.message, "none");
  }
}

const dropDownMenu: ShiftDropMenuItem[] = [
  {
    icon: 'icon-bianji2',
    name: '编辑',
    handler: handleGoEditPage
  },
  {
    icon: 'icon-shanchu',
    name: '删除',
    handler: handleDelShift
  }
];

const handleAdd = () => {
  uni.navigateTo({
    url: '/pages/attendance/shiftAdd'
  })
}

const handleDropMenuClick = (config: ShiftDropMenuItem) => {
  config.handler();
}

const handleSearch = (e: any) => {
  e && (shiftData.name = e.detail.value);
  shiftData.page = 1;
  shiftData.loaded = false;
  getShiftList(true);
}

const handleListClick = (e: MouseEvent) => {
  const { id, event } = (e.target as HTMLElement).dataset;
  if (!id || !event) return;

  if (event === 'select') {
    shiftData.selectItemIdSet.has(id) ? shiftData.selectItemIdSet.delete(id) : shiftData.selectItemIdSet.add(id);
  } else if (event === 'show-menu') {
    shiftData.selectItemId = id;
    uni.createSelectorQuery()
      .select(`.more-btn[data-id="${id}"]`)
      .boundingClientRect()
      .exec(([data]) => {
        dropDownRef.value.openDropdown(data.left, scrollTop.value + data.top + data.height);
      });
  }

}

const getShiftList = async (refresh = false) => {
  if (shiftData.loading || shiftData.loaded) return;
  shiftData.loading = true;
  const res = await attendanceScheduleShiftApi({
    page: shiftData.page,
    limit: shiftData.limit,
    name: shiftData.name
  });
  if (refresh) {
    shiftData.list = res.data.list;
    shiftData.selectItemIdSet.clear();
  } else {
    shiftData.list = [...shiftData.list, ...res.data.list];
  }
  shiftData.count = res.data.count;
  shiftData.page++;
  shiftData.loaded = res.data.list.length < shiftData.limit;
  shiftData.loading = false;
}

const getGroupShiftList = async () => {
  const res = await attendanceScheduleShiftApi({
    group_id: props.groupId,
    page: 1,
  });

  res.data.list.forEach((i: any) => {
    if (i.id !== 1) {
      shiftData.selectItemIdSet.add(i.id);
    }
  });
}

getShiftList();

props.groupId && getGroupShiftList();

onPageScroll(e => {
  scrollTop.value = e.scrollTop;
});

onReachBottom(() => {
  getShiftList();
})

</script>

<style scoped lang="scss">
.main {
  padding-top: calc(44px + var(--status-bar-height) + 96rpx);
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

.shift-list {
  margin: 20rpx;
  background-color: #fff;
  border-radius: 12rpx;
  padding-left: 24rpx;

  font-weight: 400;
  font-size: 15px;
  line-height: 30rpx;


}

.has-bottom-action {
  .shift-list {
    margin-bottom: 122rpx;
  }
}

.shift-item {
  padding: 26rpx 30rpx 24rpx 0;
  display: flex;
  align-items: center;

  &+& {
    border-top: 1px solid #EBEEF5;
  }

  .select-btn {
    width: 32rpx;
    height: 32rpx;
    margin-right: 20rpx;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: #fff;
    border: 1px solid #999;

    &.selected {
      background: #1890FF;
      border: none;
    }
  }
}

.more-btn {
  margin-left: auto;
}

.shift-item-title {
  color: #303133;
  margin-bottom: 20rpx;
}

.shift-time-range {
  color: #909399;
  margin-top: 16rpx;
  display: flex;
  gap: 10rpx;
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

.bottom-action-wrap {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #fff;
  padding: 12rpx 30rpx 24rpx;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 15px;
}

.select-shift-count {
  color: #303133;
}

.confirm-btn {
  width: 344rpx;
  height: 86rpx;
  background: #1890FF;
  border-radius: 12rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
}
</style>

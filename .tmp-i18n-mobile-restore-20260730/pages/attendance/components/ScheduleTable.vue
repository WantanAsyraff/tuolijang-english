<template>
  <view class="schedule-table" @click="handleTableClick">
    <ScheduleTableHeader :cycleType="cycleType" :date="date" />
    <view class="table-slide-container">
      <view class="table-slide-inner-container">
        <ScheduleTableAside />
        <ScheduleTableBody :shiftIdMap="shiftIdMap" />
      </view>
    </view>

    <ScheduleTablePopup ref="scheduleTablePopupRef" :shiftData="shiftData" />
  </view>
</template>

<script setup lang="ts">
import ScheduleTableHeader from './ScheduleTableHeader.vue';
import ScheduleTableAside from './ScheduleTableAside.vue';
import ScheduleTableBody from './ScheduleTableBody.vue';
import ScheduleTablePopup from './ScheduleTablePopup.vue';
import { useSyncScroll } from '../composables/useSyncScroll';
import { attendanceScheduleShiftApi } from '@/api/attendance';
import moment from 'moment';
import message from '@/utils/message';
import { SELECT_CELL_EVENT, SELECT_COLUMN_EVENT, SELECT_ROW_EVENT } from '../constants/schedule';

useSyncScroll();

const scheduleTablePopupRef = ref<any>(null);

const { handleSelectSchedule } = inject("scheduleEditInfo") as any;

const props = defineProps<{
  cycleType: string;
  date: string;
}>();

const { cycleType, date } = toRefs(props);

const { groupId, dateList } = inject("scheduleMixin") as any;

const shiftData = ref([]);

const shiftIdMap = computed(() => {
  return shiftData.value.reduce((result: any, curr: any) => {
    result[curr.id] = curr;
    return result;
  }, {});
});

const handleTableClick = (e: any) => {
  const { date, uid, eventType } = e.target.dataset;
  if (!eventType) return;

  const allowEventTypes = [SELECT_CELL_EVENT, SELECT_ROW_EVENT, SELECT_COLUMN_EVENT];
  if (!allowEventTypes.includes(eventType)) return;

  if (eventType !== SELECT_ROW_EVENT) {
    let selectMinFullDate;

    if (eventType === SELECT_CELL_EVENT) {
      selectMinFullDate = date;
    } else if (eventType === SELECT_COLUMN_EVENT) {
      selectMinFullDate = date;
    }

    const isPrevDate = moment(selectMinFullDate).isBefore(moment(), "day");
    if (isPrevDate) {
      message.error("不能选择之前的日期", 'none');
      return;
    }
  }

  handleSelectSchedule(eventType, { date, uid });
  scheduleTablePopupRef.value.openPopup();
}

onShow(async () => {
  const task1 = attendanceScheduleShiftApi({
    group_id: groupId.value,
    page: 1,
  });

  const res1 = await task1;
  shiftData.value = res1.data.list;
});

</script>

<style lang="scss" scoped>
.schedule-table {
  --border-color: rgba(235, 238, 245, .44);
  --first-cell-width: 120rpx;
  --table-body-width: calc(750rpx - var(--first-cell-width));
  --table-cell-width: calc(var(--table-body-width) / 7);
  --table-cell-height: 80rpx;

  flex: 1;
  background-color: #fff;
  display: flex;
  flex-flow: column nowrap;

  font-weight: 400;
  font-size: 14px;
}

.table-slide-container {
  flex: 1;
  position: relative;
}

.table-slide-inner-container {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
}
</style>
<template>
  <view class="table-body">
    <scroll-view class="table-body-scroll" scroll-y scroll-x @scroll="handleScroll" :scroll-top="scrollTop"
      :scroll-left="scrollLeft">
      <view class="table-row" v-for="userData of tableData" :key="userData.uid">
        <view class="table-cell" v-for="(shift, index) of userData.shifts" :key="index" :data-date="shift.date"
          :data-uid="userData.uid" :data-event-type="SELECT_CELL_EVENT" :class="shift.class">
          <view class="shift-info over-text" v-if="shift.value && shiftIdMap[shift.value]"
            :style="computedStyles(shiftIdMap[shift.value])" :data-date="shift.date" :data-uid="userData.uid"
            :data-event-type="SELECT_CELL_EVENT">
            {{ shiftIdMap[shift.value]?.name?.slice(0, 2) ?? '' }}
          </view>
        </view>
      </view>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
  import { useScheduleTableData } from '../composables/useScheduleTableData';
  import { SELECT_CELL_EVENT } from '../constants/schedule';

  const props = defineProps<{
    shiftIdMap : any;
  }>();

  const { shiftIdMap } = toRefs(props);

  const { body } = inject("tableScrollInfo") as any;
  const { scrollLeft, scrollTop, handleScroll } = body;

  const { tableData } = useScheduleTableData();

  const computedStyles = (config : any) => {

    function hexToRgb(hex : string) {
      hex = hex.replace('#', '');
      if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
      }
      const r = parseInt(hex.substring(0, 2), 16);
      const g = parseInt(hex.substring(2, 4), 16);
      const b = parseInt(hex.substring(4, 6), 16);
      return { r, g, b };
    }

    function setTextColorByBackground(hexColor : string) {
      const { r, g, b } = hexToRgb(hexColor);
      const brightness = 0.299 * r + 0.587 * g + 0.114 * b;
      const textColor = brightness > 155 ? '#303133' : '#fff';
      return textColor;
    }

    return {
      '--bg-color': config.color,
      '--text-color': setTextColorByBackground(config.color),
    }
  }
</script>

<style lang="scss" scoped>
  .table-body {
    width: var(--table-body-width);
  }

  .table-body-scroll {
    height: 100%;
  }

  .table-row {
    height: var(--table-cell-height);
    white-space: nowrap;
    width: fit-content;
  }

  .table-cell {
    width: var(--table-cell-width);
    height: 100%;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    vertical-align: bottom;

    &:not(.active-cell) {
      border-bottom: 1px solid var(--border-color);
      border-right: 1px solid var(--border-color);
    }

    &.active-cell {
      background: rgba(24, 144, 255, 0.08);
    }

    &.selected-cell {
      border: 1px solid #1890FF;
    }

    &.row-cell {
      border-top: 1px solid #1890FF;
      border-bottom: 1px solid #1890FF;

      &.last-cell {
        border-right: 1px solid #1890FF;
      }
    }

    &.column-cell {
      border-left: 1px solid #1890FF;
      border-right: 1px solid #1890FF;

      &.first-cell {
        border-top: 1px solid #1890FF;
      }

      &.last-cell {
        border-bottom: 1px solid #1890FF;
      }
    }
  }

  .shift-info {
    background-color: var(--bg-color);
    height: 50rpx;
    color: var(--text-color, #fff);
    width: calc(100% - 8rpx);
    border-radius: 4rpx;
    font-size: 14px;
    display: flex;
    justify-content: center;
    align-items: center;
  }
</style>
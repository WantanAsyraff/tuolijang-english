<template>
  <scroll-view class="table-aside-scroll" scroll-y @scroll="handleScroll" :scroll-top="scrollTop">
    <view class="table-aside-item" v-for="member of combineMembers" :key="member.id" :data-uid="member.id"
      :data-event-type="SELECT_ROW_EVENT" :class="{ 'selected-cell': member.isSelected }">
      {{ member.name }}
    </view>
  </scroll-view>
</template>

<script setup lang="ts">
import { SELECT_ROW_EVENT } from '../constants/schedule';

const { members } = inject("scheduleMixin") as any;
const { aside } = inject("tableScrollInfo") as any;
const { selectType, selectInfo } = inject("scheduleEditInfo") as any;
const { scrollTop, handleScroll } = aside;

const combineMembers = computed(() => {
  return members.value.map((member: any) => {

    const isSelected = selectType.value === SELECT_ROW_EVENT && selectInfo.value.uid === member.id;

    return {
      ...member,
      isSelected
    };
  });
});

</script>

<style lang="scss" scoped>
.table-aside-scroll {
  width: var(--first-cell-width);
  height: 100%;
}

.table-aside-item {
  height: var(--table-cell-height);
  border-bottom: 1px solid var(--border-color);
  border-right: 1px solid var(--border-color);

  display: flex;
  align-items: center;
  justify-content: center;

  &.selected-cell {
    background: rgba(24, 144, 255, 0.08);
    border: 1px solid #1890FF;
    border-right: none;
  }
}
</style>
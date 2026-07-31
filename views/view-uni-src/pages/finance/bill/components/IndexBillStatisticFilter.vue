<template>
  <view class="statistic-filter-wrapper">
    <view class="filter-item-wrapper income-type">
      <picker mode="selector" :range="inComeAndExpendTypeConfig" range-key="name"
        @change="handleIncomeAndExpendTypeChange">
        <view class="filter-item">
          <text class="iconfont icon-yeji-shijianduan" />
          {{ inComeAndExpendTypeText }}
          <text class="iconfont icon-zhankai1" />
        </view>
      </picker>
    </view>
    <view class="filter-item-wrapper bill-cate" @click="handleShowBillCatePicker">
      <view class="filter-item">
        <text class="iconfont icon-yeji-shijianduan" />
        <view class="over-text">
          {{ billCateNames || DEFAULT_CATE_TEXT }}
        </view>
        <text class="iconfont icon-zhankai1" />
      </view>
    </view>
    <view class="filter-item-wrapper bill-date-range ">
        <view class="form-item search-default-date over-text" @click="handleShowTimePopup">
       
           {{ dateRange || $t('ui.financeBillIndexBillListFilterTransactionDate') }}
            <text v-if="!dateRange" class="date-open-icon iconfont icon-zhankai1"></text>
            <!-- <text v-else class="iconfont date-clear icon-shenpizhongxin-jujue" @click.stop="clearDateRange"></text> -->
          </view>
      <!-- <uni-datetime-picker type="daterange" :clear-icon="false" :border="false"
        @change="handleSelectDateRange" :modelValue="currentMonthRange">
        <view class="filter-item">
          <text class="iconfont icon-yeji-shijianduan" />
          <view class="over-text">
            {{ dateRange || $t('ui.financeBillIndexBillListFilterTransactionDate') }}
          </view> 
          <text class="iconfont icon-zhankai1" />
        </view>
      </uni-datetime-picker> -->
    </view>

    <BaTreePicker ref="billCateRef" :localdata="billCateList" @select-change="handleBillCateChange" selectShow
      :clearTree="false" allowEmpty />
         <timePopup ref="timePopupRef" @change="handleSelectDateRange"></timePopup>
  </view>
</template>

<script setup lang="ts">
import BaTreePicker from "@/components/baTreePicker/index.vue";
import timePopup from "@/components/timePopup/index.vue";
import { useBillFilterIncomeAndExpend } from "../hooks/useBillFilterIncomeAndExpend";
import { useBillCate } from "../hooks/useBillCate";
import { useBillFilterDate } from "../hooks/useBillFilterDate";

import { FilterContent } from "../types/filter";
import { getCurrentMonthRange } from "@/utils/date";

const DEFAULT_CATE_TEXT = "账目分类";
const currentMonthRange = getCurrentMonthRange();
const billCateRef = ref();

const [
  inComeAndExpendType,
  inComeAndExpendTypeText,
  inComeAndExpendTypeConfig,
  handleIncomeAndExpendTypeChange
] = useBillFilterIncomeAndExpend();

const [
  billCateList,
  billCateIds,
  billCateNames,
  handleBillCateChange
] = useBillCate(true, DEFAULT_CATE_TEXT, inComeAndExpendType);

const [dateRange, handleSelectDateRange] = useBillFilterDate(currentMonthRange.join("-"));
const timePopupRef = ref(null);

const clearDateRange = () => {
  dateRange.value = "";
  
}

const handleShowBillCatePicker = () => billCateRef.value.show();
const handleShowTimePopup = () => timePopupRef.value?.popupOpen?.();

const emit = defineEmits<{
  change: [content: FilterContent];
}>();

watch(
  [
    inComeAndExpendType,
    billCateIds,
    dateRange
  ],
  () => {
    emit("change", {
      type: inComeAndExpendType.value,
      cateId: billCateIds.value as number[][],
      time: dateRange.value
    });
  },
  {
    immediate: true
  }
);

</script>

<style scoped lang="scss">
.statistic-filter-wrapper {
  width: 100%;
  background-color: rgba($color: #fff, $alpha: .12);
  height: 70rpx;
  border-radius: 8rpx;
  display: flex;
  align-items: center;
  display: flex;
}

.filter-item-wrapper {
  width: 100%;
  display: flex;
  justify-content: center;
  color: #fff;
  font-size: 26rpx;

  .icon-yeji-shijianduan {
    font-size: 28rpx;
    margin: 0 6rpx;
  }

  .icon-zhankai1 {
    font-size: 16rpx;
    margin: 0 8rpx;
  }

 

  &.bill-cate {

    border-left: 1px solid rgba($color: #fff, $alpha: .4);
    border-right: 1px solid rgba($color: #fff, $alpha: .4);
  }

 
}

.filter-item {
  display: flex;
  align-items: center;

}
</style>
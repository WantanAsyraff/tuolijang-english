<template>
  <view class="bill-list-filter">
    <view class="filter-input-wrapper">
      <view class="iconfont icon-sousuo" />
      <input :placeholder="$t('ui.financeBillIndexBillListFilterEnterAmountOrRemarks')" class="filter-input" confirm-type="search" @confirm="handleConfirmSearch" />
    </view>
    <view class="filter-selector-wrapper">
      <picker mode="selector" :range="inComeAndExpendTypeConfig" range-key="name" @change="handleIncomeAndExpendTypeChange">
        <view class="selector-item-wrapper">
          <view class="over-text">
            {{ inComeAndExpendTypeText }}
          </view>
          <text class="iconfont icon-zhankai1" />
        </view>
      </picker>
      <view class="selector-item-wrapper" @click="handleShowBillCatePicker">
        <view class="over-text">
          {{ billCateNames || $t('ui.financeBillAddAccountCategory') }}
        </view>
        <text class="iconfont icon-zhankai1" />
      </view>
      <picker mode="selector" :range="payTypeConfig" range-key="name" @change="handlePayTypeChange">
        <view class="selector-item-wrapper">
          <view class="over-text">
            {{ payTypeText }}
          </view>
          <text class="iconfont icon-zhankai1" />
        </view>
      </picker>
      <view class="selector-item-wrapper">
        <view class="form-item search-default-date over-text" @click="handleDaterange">
          {{ dateRange || $t('ui.financeBillIndexBillListFilterTransactionDate') }}
          <text v-if="!dateRange" class="date-open-icon iconfont icon-zhankai1"></text>
          <text v-else class="iconfont date-clear icon-shenpizhongxin-jujue" @click.stop="clearDateRange"></text>
        </view>
      </view>
    </view>

    <BaTreePicker ref="billCateRef" :localdata="billCateList" @select-change="handleBillCateChange" selectShow :clearTree="false" allowEmpty />
    <timePopup ref="timePopupRef" @change="handleSelectDateRange"></timePopup>
  </view>
</template>

<script lang="ts" setup>
import timePopup from '@/components/timePopup/index.vue'
import BaTreePicker from '@/components/baTreePicker/index.vue'
import { useFilterPayType } from '../hooks/useIndexBillListFilterPayType'
import { useBillFilterIncomeAndExpend } from '../hooks/useBillFilterIncomeAndExpend'
import { useBillFilterDate } from '../hooks/useBillFilterDate'
import { useBillCate } from '../hooks/useBillCate'

const emit = defineEmits<{
  change: [filterContent: BillListFilterContent]
}>()

const billCateRef = ref()

const DEFAULT_CATE_TEXT = '账目分类'
const searchText = ref<string>('')
const [payType, payTypeText, payTypeConfig, handlePayTypeChange] = useFilterPayType()
const [inComeAndExpendType, inComeAndExpendTypeText, inComeAndExpendTypeConfig, handleIncomeAndExpendTypeChange] = useBillFilterIncomeAndExpend()
const [dateRange, handleSelectDateRange] = useBillFilterDate()
const [billCateList, billCateIds, billCateNames, handleBillCateChange] = useBillCate(true, DEFAULT_CATE_TEXT, inComeAndExpendType)

const handleShowBillCatePicker = () => {
  billCateRef.value.show()
}

const handleConfirmSearch: UniEventValueHandler = (e) => {
  searchText.value = e.detail.value
}
const timePopupRef = ref(null)
const handleDaterange = () => {
  timePopupRef.value?.popupOpen?.()
}
const clearDateRange = () => {
  dateRange.value = ''
}

watch([payType, inComeAndExpendType, searchText, dateRange, billCateIds], () => {
  emit('change', {
    payType: payType.value,
    inComeAndExpendType: inComeAndExpendType.value,
    searchText: searchText.value,
    dateRange: dateRange.value,
    billCateIds: billCateIds.value as number[][],
  })
})
</script>

<style scoped lang="scss">
.bill-list-filter {
  position: fixed;
  left: 0;
  right: 0;
  top: var(--nav-height);
  height: var(--filter-height);
  background-color: #fff;
  z-index: 1;

  padding: {
    top: 18rpx;
    left: 30rpx;
    right: 30rpx;
  }
}

.filter-input-wrapper {
  background: $uni-default-bg;
  border-radius: 12rpx;
  padding: 0 20rpx;
  height: 64rpx;

  display: flex;
  align-items: center;

  .iconfont {
    font-size: 24rpx;
    color: #999;
    margin-right: 14rpx;
  }

  .filter-input {
    flex: 1;
    font-size: 26rpx;
    color: $nui-text-color-four;
  }
}

.filter-selector-wrapper {
  margin-top: 20rpx;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10rpx;
  color: #606266;

  picker {
    display: flex;

    &:not(:first-child):not(:last-child) {
      justify-content: center;
    }
  }

  & > .selector-item-wrapper,
  & > picker {
    flex: 1;
  }

  .selector-item-wrapper {
    display: flex;
    align-items: center;
    line-height: 34rpx;
    font-size: 24rpx;

    min-width: 0;

    .iconfont {
      font-size: 14rpx;
      color: $uni-text-color-five;
      margin-left: 8rpx;
    }
  }

  .date-range-wrapper {
    display: flex;
    justify-content: flex-end;
  }
}
</style>

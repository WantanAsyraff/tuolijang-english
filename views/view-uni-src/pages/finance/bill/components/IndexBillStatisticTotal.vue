<template>
  <view class="total-wrapper">
    <view class="total-item" v-for="item of config" :key="item.key">
      <view class="iconfont" :class="item.icon"></view>
      <view class="total-item-right">
        <view class="total-item-value over-text">
          {{ item.key === "count" ? census[item.key as keyof BillStatisticCensus] : formatToTwoDecimal(census[item.key as keyof BillStatisticCensus]) }}
        </view>
        <view class="total-item-label">{{ item.label }}</view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">import appI18n from '@/locale';

const props = defineProps<{
  census: BillStatisticCensus;
}>();

const { census } = toRefs(props);

const formatToTwoDecimal = (value: number | string) => {
  if (typeof value === "string") {
    value = Number(value);
  }

  return value.toFixed(2);
};

const config = [
  {
    icon: "icon-yeji-zongshouru",
    label: appI18n.global.t('ui.financeBillIndexBillStatisticTotalInflowCny'),
    key: "income"
  },
  {
    icon: "icon-yeji-xinzengkehu",
    label: appI18n.global.t('ui.financeBillIndexBillStatisticTotalOutflowCny'),
    key: "expend"
  },
  {
    icon: "icon-yeji-zongshouru",
    label: appI18n.global.t('ui.financeBillIndexBillStatisticTotalNetAmountCny'),
    key: "profit"
  },
  {
    icon: "icon-yeji-xinzenghetong",
    label: appI18n.global.t('ui.financeBillIndexBillStatisticTotalTransactionCount'),
    key: "count"
  }
];
</script>

<style scoped lang="scss">
  .total-wrapper {
    padding: 40rpx 32rpx;
  }

  .total-wrapper {
    display: flex;
    flex-flow: row wrap;
    gap: 56rpx 0;
  }

  .total-item {
    width: 50%;
    display: flex;

    .iconfont {
      color: #308BF8;
      font-size: 52rpx;
      margin-right: 20rpx;
    }

    .total-item-right {
      overflow: hidden;
    }

    .total-item-value {
      font-size: 38rpx;
      font-weight: 500;
      line-height: 54rpx;
      margin-bottom: 2rpx;
    }

    .total-item-label {
      font-size: 24rpx;
      color: $nui-text-color-four;
      line-height: 24rpx;
    }
  }
</style>

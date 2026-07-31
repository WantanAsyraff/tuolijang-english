<template>
  <view class="float-product-total">
    <view class="total-box">
      <text class="product-count">{{ $t('ui.customerOpportunityAddProductTotal') }}{{ listData.length }}{{ $t('ui.customerOpportunityAddProductItems') }}</text>
      <text class="product-total">{{ $t('ui.customerOpportunityProductTotalTotalPrice') }}</text>
      <view class="product-total-price">
        <BasePriceFormat :price="totalPrice" />
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import BasePriceFormat from "@/components/BasePriceFormat/index.vue";
import { computed } from "vue";
import { ProductItemWithOppo } from "./product";

const props = defineProps<{
  listData: ProductItemWithOppo[];
}>();
const { listData } = toRefs(props);

const totalPrice = computed(() => {
  return listData.value.reduce((acc: number, item: any) => acc + Number(item.total_price), 0);
});
</script>

<style lang="scss" scoped>
.float-product-total {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #fff;
  padding-bottom: calc(var(--bottom-area-height));
  box-shadow: inset 0rpx 1rpx 0rpx 0rpx #EEEEEE;

  .total-box {
    height: 96rpx;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0 28rpx;
    font-size: 24rpx;
    color: #606266;

    .product-total {
      font-size: 26rpx;
      margin-inline: 6rpx;
    }

    .product-total-price {
      color: #303133;
      font-weight: 600;
      --symbol-size: 30rpx;
      --int-part-size: 40rpx;
      --decimal-part-size: 40rpx;
    }
  }
}
</style>

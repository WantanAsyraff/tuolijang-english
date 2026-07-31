<template>
  <view class="form-card" :class="{ empty: productListData.length === 0 }">
    <view class="form-title">
      {{ $t('ui.customerOpportunityProductPanelProductList') }}
      <navigator hover-class="none" :url="addProductUrl" class="add-btn">{{ $t('ui.examineFormApprovalBillAdd') }}</navigator>
    </view>
    <view class="product-list-box">
      <ProductList :list-data="productListData" :moreShow="false">
        <template #action="{ index }">
          <view class="action-box">
            <view class="action-item" @click="handleDelProduct(index)">{{ $t('ui.examineFormApprovalBillDelete') }}</view>
            <view class="action-item edit" @click="handleGoEditProductPage(index)">{{ $t('ui.customerQuickReplyIndexEdit') }}</view>
          </view>
        </template>
      </ProductList>
    </view>
  </view>
</template>

<script setup lang="ts">
import { useProductReducer } from '../composition/useProduct'
import { type ProductItemWithOppo } from './product'
import ProductList from './product-list.vue'

const props = defineProps<{
  list: ProductItemWithOppo[]
}>()

const { productListData, addProductUrl, handleDelProduct, handleGoEditProductPage, totalPrice, totalCount } = useProductReducer()

watch(
  () => props.list,
  (newVal) => {
    if (productListData.value) {
      productListData.value = newVal
    }
  },
  { immediate: true },
)

defineExpose({
  totalPrice,
  totalCount,
  productListData,
})
</script>

<style scoped lang="scss">
.form-card {
  background-color: #fff;
  // border-radius: 12rpx;
  padding-top: 30rpx;
  padding-left: 24rpx;

  &.empty {
    padding-bottom: 30rpx;
  }

  &:last-child {
    // margin-bottom: calc(var(--bottom-area-height) + 120rpx);
  }
}

.form-title {
  font-weight: 500;
  font-size: 32rpx;
  color: #303133;
  line-height: 44rpx;

  display: flex;
  align-items: center;

  .add-btn {
    font-weight: 400;
    margin-left: auto;
    font-size: 28rpx;
    color: #2a7efb;
    line-height: 40rpx;
    margin-right: 24rpx;
  }
}

.product-list-box {
  padding-right: 24rpx;
}

.action-box {
  display: flex;
  justify-content: flex-end;
  gap: 16rpx;
  margin-top: 26rpx;

  .action-item {
    width: 144rpx;
    height: 56rpx;
    background: #f7f7f7;
    border-radius: 8rpx;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24rpx;
    color: #333333;

    &.edit {
      background: rgba(48, 139, 248, 0.1);
      color: #308bf8;
    }
  }
}
</style>

<template>
  <view :class="listData.length > 2 ? 'pb60' : ''">
    <view class="product-list-wrap">
      <template v-if="listData.length > 0">
        <view v-for="(item, idx) in listData" :key="item.id" class="product-item">
          <view class="product-main">
            <image :src="item.image" class="product-img" v-if="item.image" />
            <view class="product-img default" v-else />
            <view class="product-info-box">
              <view class="product-info-top">
                <view class="product-info">
                  <view class="product-title line1">{{ item.product_name || item.name }}</view>
                  <view class="product-spec line1">{{ item.sku }} {{ $t('ui.customerOpportunityAddProductTotal') }}{{ item.count }}{{ $t('ui.customerOpportunityAddProductItems') }}</view>
                </view>
                <view class="product-right">
                  <view class="product-price">
                    <BasePriceFormat :price="getTotal(item)" />
                  </view>
                  <view class="product-count">{{ $t('ui.customerOpportunityProductListDealTotal') }}</view>
                </view>
              </view>
              <view class="product-meta">
                <text>{{ $t('ui.customerOpportunityProductListSalePrice') }} {{ item.ot_price }}</text>
                <text>{{ $t('ui.customerOpportunityProductListDiscount') }} {{ item.discount }}%</text>
                <text>{{ $t('ui.customerOpportunityProductListDealUnitPrice') }} {{ item.price }}</text>
              </view>
            </view>
          </view>
          <view class="product-remark" v-if="item.remark">
            <text class="remark-title">{{ $t('ui.customerContractPayDetailRemarks') }}</text>
            <text class="remark-content line1">{{ item.remark }}</text>
          </view>
          <slot name="action" :product="item" :index="idx" />
        </view>
      </template>

      <empty v-else :index="7" :title="$t('ui.customerOpportunityProductListNoProductData')"></empty>
    </view>
    <view v-if="listData.length > 0 && moreShow" class="footer-text">{{ $t('ui.customerListFollowRecordNoMore') }}</view>
  </view>
</template>

<script setup lang="ts">
import empty from '@/components/empty/index.vue'
import BasePriceFormat from '@/components/BasePriceFormat/index.vue'
import type { ProductItemWithOppo } from './product'

const props = defineProps<{
  listData: ProductItemWithOppo[]
  moreShow?: boolean
}>()
const { listData, moreShow } = toRefs(props)
const getTotal = (item: ProductItemWithOppo) => {
  if (item.total_price) {
    return Number(item.total_price)
  } else {
    return Number(item.ot_price) * Number(item.count)
  }
}
</script>

<style scoped lang="scss">
.product-list-wrap {
  // padding: 30rpx;
  padding-top: 0;
  background-color: #fff;
  // border-radius: 16rpx;
  .product-item {
    padding-top: 32rpx;
    padding-bottom: 40rpx;
    border-bottom: 1px solid #f5f5f5;

    &:last-child {
      border-bottom: none;
    }

    .product-main {
      display: flex;
      flex-direction: row;
      align-items: flex-start;

      .product-img {
        width: 128rpx;
        height: 128rpx;
        border-radius: 16rpx;
        margin-right: 16rpx;
        object-fit: cover;

        &.default {
          background: url('@/static/image/shan.png') no-repeat center / 60% auto #f3f9ff;
        }
      }

      .product-info-box {
        flex: 1;
        overflow: hidden;
      }

      .product-info-top {
        display: flex;
        margin-bottom: 16rpx;
      }

      .product-info {
        flex: 1;
        overflow: hidden;

        .product-title {
          font-size: 28rpx;
          color: #333;
          line-height: 40rpx;
          margin-bottom: 8rpx;
        }

        .product-spec {
          font-size: 24rpx;
          color: #999;
          margin-bottom: 8rpx;
        }
      }

      .product-meta {
        font-size: 22rpx;
        color: #666;
        display: flex;
        line-height: 30rpx;
        gap: 30px;
      }

      .product-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;

        .product-price {
          font-family: D-DIN-PRO, D-DIN-PRO;
          font-size: 32rpx;
          color: #222;
          font-weight: bold;
        }

        .product-count {
          font-size: 24rpx;
          color: #999;
          margin-top: 12rpx;
        }
      }
    }

    .product-remark {
      margin-top: 26rpx;
      background: #f5f5f5;
      border-radius: 8rpx;
      padding: 18rpx 20rpx;
      font-size: 26rpx;
      color: #333;
      display: flex;
      align-items: center;

      .remark-title {
        margin-right: 80rpx;
      }

      .remark-content {
        margin-left: auto;
        color: #606266;
        flex: 1;
      }
    }
  }
}
</style>

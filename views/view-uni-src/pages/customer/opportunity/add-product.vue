<template>
  <BaseContainer class="base-container">
    <view class="head-wrap">
      <NavBar :is-right="true" />
      <view class="search-box">
        <view class="search-content">
          <i class="iconfont icon-sousuo" />
          <input type="text" placeholder-class="placeholder" :placeholder="$t('ui.customerOpportunityAddProductSearchByProductNameOrSpecification')" v-model="searchValue"
            @confirm="handleSearch" confirm-type="search" />
        </view>
      </view>
    </view>
    <view class="add-card">
      <view class="add-card-title">
        {{ $t('ui.customerOpportunityAddProductTotal') }} {{ productList.length }} {{ $t('ui.customerOpportunityAddProductItemsSelected') }} {{ selectedCount }} {{ $t('ui.customerOpportunityAddProductItems') }}
      </view>
      <view class="product-item" v-for="product in productList" :key="product.id">
        <view class="select-btn" @click="handleToggleSelect(product)" :class="{ 'selected': product.selected }">
          <i class="iconfont icon-xuanzhong" v-if="product.selected" />
        </view>
        <image :src="product.image" mode="aspectFill" class="product-img" v-if="product.image" />
        <view class="product-img default" v-else />
        <view class="product-info">
          <view class="product-name line2">{{ product.name }}</view>
          <view class="product-intro line1">{{ product.sku }}</view>
        </view>
        <view class="product-price">
          <BasePriceFormat :price="Number(product.price)" />
        </view>
      </view>
    </view>

    <BaseBottomBtn :text="$t('ui.baTreePickerIndexOk')" @click="handleSubmit" />
  </BaseContainer>
</template>

<script setup lang="ts">
import BaseContainer from "@/components/BaseContainer/index.vue";
import NavBar from "@/components/defaultNavBar/index.vue";
import BaseBottomBtn from "@/components/BaseBottomBtn/index.vue";
import { opportunityGetProductApi } from "@/api/customer";
import message from "@/utils/message";
import type { ProductItem } from "./components/product";
import BasePriceFormat from "@/components/BasePriceFormat/index.vue";

const searchValue = ref("");

const eventName = ref("");

const productList = ref<ProductItem[]>([]);

const selectedCount = computed(() => {
  return productList.value.filter(item => item.selected).length;
});

const handleToggleSelect = (product: ProductItem) => {
  product.selected = !product.selected;
};

const getProductList = async () => {
  uni.showLoading({ mask: true });
  try {

    const res = await opportunityGetProductApi({
      name: searchValue.value
    });
    productList.value = res.data.list.map((item: Exclude<ProductItem, "selected">) => ({
      ...item,
      selected: false
    }));
    uni.hideLoading();
  } catch (error) {
    uni.hideLoading();
    message.error(error.message);
  }
};

const handleSearch = () => {
   productList.value =[]
  getProductList();
};

const handleSubmit = () => {
  if (selectedCount.value === 0) {
    return message.error("请选择商品");
  }
  
  uni.$emit(eventName.value, toRaw(productList.value).filter(item => item.selected));
  uni.navigateBack();
};

onLoad((options: Record<string, any>) => {
  eventName.value = options.event_name;
  const selectUnique: string[] = options.unique ? options.unique.split(",") : [];
  getProductList()
    .then(() => {
      const selectUniqueMap = selectUnique.reduce((map, unique) => {
        map.set(unique, true);
        return map;
      }, new Map<string, boolean>());
      productList.value.forEach((item) => {
        item.selected = selectUniqueMap.has(item.unique);
      });
    });
});

</script>
<style scoped lang="scss">
.head-wrap {
  padding-top: var(--status-bar-height);
  background-color: #fff;
  position: sticky;
  top: 0;
  z-index: 1;
}

.search-box {
  padding: 20rpx 24rpx;

  .search-content {
    height: 64rpx;
    background: #F5F5F5;
    border-radius: 12rpx;
    display: flex;
    align-items: center;
    padding: 0 24rpx;

    .icon-sousuo {
      font-size: 30rpx;
      color: #999;
      margin-right: 16rpx;
    }

    input {
      flex: 1;
      font-size: 26rpx;
    }

    ::v-deep .placeholder {
      color: #ccc;
      font-size: 26rpx;
    }
  }
}

.add-card {
  background-color: #fff;
  // margin: 20rpx;
  margin-top: 16rpx;
  margin-bottom: calc(var(--bottom-area-height) + 140rpx);
  border-radius: 12rpx;
  padding: 30rpx 0 12rpx;
}

.add-card-title {
  font-size: 24rpx;
  color: #333333;
  line-height: 34rpx;
  margin-bottom: 20rpx;
  padding-left: 24rpx;
}

.product-item {
  padding: 28rpx 26rpx;
  display: flex;
  align-items: center;

  .select-btn {
    width: 34rpx;
    height: 34rpx;
    border: 1px solid #909399;
    border-radius: 50%;

    &.selected {
      border: none;
      background-color: #0a91ff;
      display: flex;
      align-items: center;
      justify-content: center;

      .icon-xuanzhong {
        font-size: 17rpx;
        color: #fff;
      }
    }
  }

  .product-img {
    width: 136rpx;
    height: 136rpx;
    border-radius: 16rpx;
    margin-inline: 24rpx 20rpx;

    &.default {
      background: url('@/static/image/shan.png') no-repeat center / 60% auto #f3f9ff;
    }
  }

  .product-info {
    flex: 1;
    overflow: hidden;

    .product-name {
      font-size: 28rpx;
      color: #333333;
      line-height: 40rpx;
      margin-bottom: 12rpx;
    }

    .product-intro {
      font-size: 24rpx;
      color: #999999;
      line-height: 34rpx;
    }
  }
}

.product-price {
  font-weight: bold;
}
</style>

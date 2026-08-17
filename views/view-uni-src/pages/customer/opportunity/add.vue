<template>
  <BaseContainer class="base-container">
    <view class="head-wrap">
      <NavBar :is-right="true" :defaultTitle="pageData.id ? $t('mobile.ui.navigation.editOpportunity') : $t('mobile.navigation.pages/customer/opportunity/add')" />
    </view>
    <view class="form-box" v-if="formConfig.length">
      <oaForm :listData="formConfig" ref="formRef" @submitOk="handleSaveData" immediate>
        <template v-slot:product>
          <ProductPanel :list="productList" ref="productPanelRef" />
        </template>
      </oaForm>
    </view>
    <view class="placeholder-box"></view>
    <view class="product-total-panel">
      <view class="left">
        <view class="price-info">
          {{ $t('ui.customerOpportunityProductTotalTotalPrice') }}
          <view class="price-format">
            <BasePriceFormat :price="totalPrice" />
          </view>
        </view>
        {{ $t('ui.customerOpportunityAddProductTotal') }} {{ totalCount }} {{ $t('ui.customerOpportunityAddProductItems') }}
      </view>
      <view class="submit-btn" @click="handleSubmit"> {{ $t('ui.replyComponentIndexSubmit') }} </view>
    </view>
  </BaseContainer>
</template>

<script setup lang="ts">
import BaseContainer from '@/components/BaseContainer/index.vue'
import NavBar from '@/components/defaultNavBar/index.vue'
import { fillProductInfo, processSubmitProductData, type ProductItemWithOppo } from './components/product'
import oaForm from '@/components/oaForm/index.vue'
import { opportunityAddFormApi, opportunityEditFormApi, opportunityEditApi, opportunitySaveApi } from '@/api/customer'
import message from '@/utils/message'
import { delayedReLaunch } from '@/utils/helper'
import ProductPanel from './components/product-panel.vue'
import BasePriceFormat from '@/components/BasePriceFormat/index.vue'

const formRef = ref<InstanceType<typeof oaForm>>()
const pageData = ref<any>({
  id: 0,
  eid: 0,
})
const formConfig = ref([])
const productList = ref<ProductItemWithOppo[]>([])
const productPanelRef = ref<InstanceType<typeof ProductPanel>>()

const totalPrice = computed(() => {
  return productPanelRef.value?.totalPrice || 0
})
const totalCount = computed(() => {
  return productPanelRef.value?.totalCount || 0
})

const productListData = computed(() => {
  return productPanelRef.value?.productListData || []
})

const getFormConfig = async () => {
  const api = pageData.value.id ? opportunityEditFormApi(pageData.value.id, { edit: 1 }) : opportunityAddFormApi({ eid: pageData.value.eid })
  try {
    const res = await api
    if (pageData.value.id) {
      formConfig.value = res.data.form
      productList.value = res.data.product
    } else {
      res.data.forEach((item) => {
        item.data.forEach((el) => {
          if (el.key === 'eid') {
            el.value = pageData.value.eid || ''
          }
        })
      })

      formConfig.value = res.data
    }
  } catch (error) {
    message.error(error.message)
  }
}

const handleSubmit = () => formRef.value.submit()

const handleSaveData = async (value: any) => {
  const formData = {
    ...value,
    products: processSubmitProductData(productListData.value),
  }

  const api = pageData.value.id ? opportunityEditApi(pageData.value.id, formData) : opportunitySaveApi(formData)

  uni.showLoading({ mask: true })
  try {
    const res = await api
    uni.hideLoading()
    message.success(res.message)
    if (pageData.value.eid && !pageData.value.id) {
      delayedReLaunch(`/pages/customer/list/details?id=${pageData.value.eid}&&types=customer`)
    } else {
      setTimeout(() => {
        uni.navigateBack()
      }, 800)
    }
  } catch (error) {
    uni.hideLoading()
    message.error(error.message)
  }
}

onLoad((options) => {
  const { id, eid } = options
  if (id) {
    pageData.value.id = id
  }
  if (eid) {
    pageData.value.eid = eid
  }
  getFormConfig()
})
</script>

<style scoped lang="scss">
.head-wrap {
  padding-top: var(--status-bar-height);
  background-color: #fff;
  position: sticky;
  top: 0;
  z-index: 1;
}

.placeholder-box {
  height: calc(var(--bottom-area-height) + 120rpx);
}

.product-total-panel {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #fff;
  padding: 16rpx 20rpx calc(var(--bottom-area-height) + 16rpx) 24rpx;

  display: flex;
  align-items: center;

  .left {
    font-size: 24rpx;
    color: #909399;
    line-height: 34rpx;
  }

  .price-info {
    display: flex;
    align-items: center;
    font-size: 26rpx;
    line-height: 36rpx;
    color: #606266;

    .price-format {
      margin-left: 2rpx;
      color: #303133;
      font-weight: 600;
      --symbol-size: 30rpx;
      --int-part-size: 40rpx;
      --decimal-part-size: 40rpx;
    }
  }

  .submit-btn {
    width: 248rpx;
    height: 86rpx;
    background: #308bf8;
    border-radius: 12rpx;
    font-size: 30rpx;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: auto;
  }
}

::v-deep .form-card {
  margin: 0 16rpx;
}
</style>

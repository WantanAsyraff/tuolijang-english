<template>
  <view class="order-item-container">
    <!-- 选择器 -->
    <picker
      v-if="isShow"
      class="picker-selector"
      mode="selector"
      @change="handlePickerChange"
      :value="selectedIndex"
      :range="recordStatusList"
      range-key="label"
    >
      <view class="search-default-label">
        {{ currentStatusText }}
        <text class="iconfont icon-jinru"></text>
      </view>
    </picker>

    <!-- 数据列表 -->
    <view class="examine-content">
      <template v-if="link_type == 5 && isShow">
        <view v-for="(item, index) in oddslist" :key="`odds-${item.id}`" class="item">
          <view class="iconfont" :class="isSelected(item.id) ? 'icon-denglu-tongyi' : 'icon-xuanzeanniu-weixuan'" @click="toggleSelection(item)" />
          <view class="list-item">
            <view class="name">{{ $t('ui.customerListBusinessFollowOpportunityNo') }}</view>
            <view class="list-text">{{ item.odds_no || '--' }}</view>
          </view>

          <view class="list-item">
            <view class="name">{{ $t('ui.customerSigningOrderItemOpportunityType') }}</view>
            <view class="list-text">{{ item.types?.name || '--' }}</view>
          </view>

          <view class="list-item">
            <view class="name">{{ $t('ui.customerSigningOrderItemOpportunityAmount') }}</view>
            <view class="list-text">{{ item.total_amount || '--' }}</view>
          </view>
          <view class="list-item">
            <view class="name">{{ $t('ui.customerSigningOrderItemOpportunityStatus') }}</view>
            <view class="list-text">{{ $ts(item.status?.name || '--') }}</view>
          </view>
          <view class="list-item">
            <view class="name">{{ $t('ui.customerContractPayDetailSalesperson') }}</view>
            <view class="list-text">{{ item.salesman?.name || '--' }}</view>
          </view>
          <view class="list-item">
            <view class="name">{{ $t('ui.customerInvoiceCheckPaymentCreatedTime') }}</view>
            <view class="list-text">{{ item.created_at || '--' }}</view>
          </view>
        </view>
        <view v-if="oddslist.length == 0" class="no-data"> {{ $t('ui.customerSigningOrderItemNoOpportunityData') }} </view>
      </template>

      <!-- 订单列表 -->
      <template v-if="link_type == 2 && isShow">
        <view v-for="(item, index) in contractList" :key="`contract-${item.id}`" class="item">
          <view class="iconfont" :class="isSelected(item.id) ? 'icon-denglu-tongyi' : 'icon-xuanzeanniu-weixuan'" @click="toggleSelection(item)" />
          <view class="list-item">
            <view class="name">{{ $t('ui.customerContractPayDetailOrderNo') }}</view>
            <view class="list-text">{{ item.contract_no || '--' }}</view>
          </view>
          <view class="list-item">
            <view class="name">{{ $t('ui.customerContractIndexOrderName') }}</view>
            <view class="list-text">{{ item.contract_name || '--' }}</view>
          </view>

          <view class="list-item">
            <view class="name">{{ $t('ui.customerSigningOrderListOrderAmount') }}</view>
            <view class="list-text">{{ item.contract_price || '--' }}</view>
          </view>
          <!-- <view class="list-item">
          <view class="name">客户名称</view>
          <view class="list-text">{{ item.contract_customer || '--' }}</view>
        </view> -->
          <view class="list-item">
            <view class="name">{{ $t('ui.customerSigningOrderListPaymentStatus') }}</view>
            <view class="list-text">{{ item.payment_status == 1 ? $t('ui.customerSigningOrderListSettled') : $t('ui.customerSigningOrderListUnsettled') }}</view>
          </view>
          <view class="list-item">
            <view class="name">{{ $t('ui.customerSigningOrderListOrderStatus') }}</view>
            <view class="list-text">{{ $ts(item.contract_status?.name || '--') }}</view>
          </view>
          <view class="list-item">
            <view class="name">{{ $t('ui.customerContractPayDetailSalesperson') }}</view>
            <view class="list-text">{{ item.salesman?.name || '--' }}</view>
          </view>
          <view class="list-item">
            <view class="name">{{ $t('ui.customerInvoiceCheckPaymentCreatedTime') }}</view>
            <view class="list-text">{{ item.created_at || '--' }}</view>
          </view>
        </view>
        <view v-if="contractList.length == 0" class="no-data"> {{ $t('ui.customerSigningOrderListNoOrderData') }} </view>
      </template>
    </view>

    <!-- 产品清单 -->
    <view class="product-box">
      <ProductPanel :list="productList" ref="productPanelRef" />
    </view>
  </view>
</template>

<script setup lang="ts">
import empty from '@/components/empty/index.vue'
import { ref, reactive, computed, onMounted, watch } from 'vue'
import ProductPanel from '@/pages/customer/opportunity/components/product-panel.vue'
import { clientContractListApi, opportunityListApi } from '@/api/customer'

// Props
const props = defineProps({
  eid: {
    type: String,
    default: '',
  },
})

// 响应式数据
const isShow = ref<boolean>(true)
const filterValue = ref<string>('')
const selectedIds = ref<number[]>([]) // 选中的cid列表
const oddslist = ref<any[]>([])
const contractList = ref<any[]>([])
const productList = ref<any[]>([]) //
const link_type = ref<number>(5) // 默认关联商机

// 选择器配置
const recordStatusList = [
  { label: '关联商机', value: 5 },
  { label: '关联订单', value: 2 },
]

const getProductForm = () => {
  return { list: productList.value, link_type: link_type.value, selectedIds: selectedIds.value }
}

// 计算属性
const selectedIndex = computed(() => {
  return recordStatusList.findIndex((item) => item.value == link_type.value)
})

const currentStatusText = computed(() => {
  const current = recordStatusList.find((item) => item.value === link_type.value)
  return current?.label || '关联商机'
})

// 方法
const isSelected = (id: number): boolean => {
  return selectedIds.value.includes(id)
}

// 从商机、订单设置产品清单
const setProductList = (ids: any[], type: number, show: boolean, filterType?: string) => {
  ids = ids.map((item) => Number(item))
  isShow.value = !show
  link_type.value = type
  selectedIds.value = ids
  filterValue.value = filterType || ''
  loadData(ids)
}

defineExpose({
  getProductForm,
  setProductList,
})

const toggleSelection = (item: any): void => {
  const id = item.id
  if (isSelected(id)) {
    selectedIds.value = selectedIds.value.filter((selectedId) => selectedId !== id)
  } else {
    selectedIds.value.push(id)
    productList.value = [...productList.value, ...(item.product || [])]
  }
}

const handlePickerChange = (e: any): void => {
  const index = e.detail.value
  link_type.value = recordStatusList[index].value
  loadData()
  productList.value = []
}

const loadData = async (ids: any[]): Promise<void> => {
  if (!props.eid) return

  try {
    if (link_type.value === 5) {
      await getOddsList(ids)
    } else {
      await getContractList(ids)
    }
  } catch (error) {
    console.error('加载数据失败:', error)
  }
}

const getOddsList = async (ids: any[]): Promise<void> => {
  if (oddslist.value.length > 0) return
  const res = await opportunityListApi({
    eid: props.eid,
    page: 1,
    limit: 0,
  })
  oddslist.value = JSON.parse(JSON.stringify(res.data.list || []))
  oddslist.value = oddslist.value.filter((item) => !item.is_sign)
  selectedIds.value = oddslist.value.map((item) => item.id)
  contractList.value = []
  ids = selectedIds.value
  if (ids && ids.length > 0) {
    res.data.list.map((item) => {
      if (ids.includes(item.id)) {
        productList.value = [...(item.product || [])]
      }
    })
  }
}

const getContractList = async (ids: any[]): Promise<void> => {
  if (contractList.value.length > 0) return
  const res = await clientContractListApi({
    eid: props.eid,
    page: 1,
    limit: 0,
  })
  contractList.value = JSON.parse(JSON.stringify(res.data.list || []))
  contractList.value = contractList.value.filter((item) => !item.is_sign)
  oddslist.value = []
  if (ids && ids.length > 0) {
    res.data.list.map((item) => {
      if (ids.includes(item.id)) {
        productList.value = [...(item.product || [])]
      }
    })
  }
}
</script>

<style scoped lang="scss">
// 变量定义
$primary-color: #1890ff;
$text-primary: #303133;
$text-secondary: #606266;
$border-color: #e4e7ed;
$background-color: #f5f7fa;

$spacing-xs: 16rpx;
$spacing-sm: 30rpx;
$spacing-md: 32rpx;
$spacing-lg: 40rpx;

$font-size-sm: 26rpx;
$font-size-md: 30rpx;

.order-item-container {
  width: 100%;
  background-color: $background-color;
}

::v-deep .pb60 {
  padding-bottom: 0;
}

.no-data {
  background-color: #fff;
  height: 200rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #999999;
  font-size: 26rpx;
}

.picker-selector {
  width: 100%;
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 500;
  font-size: $font-size-md;
  color: $text-primary;
  background-color: #fff;
  padding: $spacing-sm;
  padding-bottom: 0;

  .icon-jinru {
    color: $text-secondary;
    font-size: $font-size-sm;
    margin-left: $spacing-xs;
  }
}

.product-box {
  margin: 16rpx 0;
}

.examine-content {
  .item {
    margin-bottom: 8rpx;
    width: 100%;
    position: relative;
    background-color: #fff;
    padding: $spacing-md;
    padding-left: 96rpx;

    .iconfont {
      position: absolute;
      top: 40rpx;
      left: 30rpx;
      font-size: 36rpx;
      color: #c0c4cc;
      cursor: pointer;
    }

    .icon-denglu-tongyi {
      color: $primary-color;
    }

    .list-item {
      display: flex;
      margin-bottom: $spacing-xs;

      .list-text {
        height: 28rpx;
        width: 378rpx;
        margin-left: $spacing-lg;
        font-family:
          PingFang SC,
          PingFang SC;
        font-weight: 400;
        font-size: $font-size-sm;
        color: $text-primary;
      }

      .name {
        font-family:
          PingFang SC,
          PingFang SC;
        font-weight: 400;
        font-size: $font-size-sm;
        color: $text-secondary;
        width: 104rpx;
        text-align: left;
      }
    }
  }
}
</style>

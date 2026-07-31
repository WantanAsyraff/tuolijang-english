<template>
  <BaseContainer class="base-container">
    <!-- 顶部 -->
    <view class="head-wrap">
      <NavBar :is-right="true" :defaultTitle="data.cid ? '编辑订单' : ' 添加订单'" />
    </view>
    <!-- 表单内容 -->
    <view class="form-card">
      <oaForm ref="oaFormRef" :listData="listData" @submitOk="submitOk">
        <template v-slot:product>
          <ProductPanel :list="data.productList" ref="productPanelRef" />
        </template>
      </oaForm>
    </view>
    <view class="placeholder-box"></view>
    <BaseBottomBtn :text="$t('ui.replyComponentIndexSubmit')" @click="clickSubmit" />
    <success-popup ref="successPopupRef" :type="1" :title="$t('ui.customerContractAddContractOrder')" :button-title="$t('ui.customerContractAddContractAddPayment')" @change="successChange"> </success-popup>
  </BaseContainer>
</template>

<script setup>
import oaForm from '@/components/oaForm'
import BaseContainer from '@/components/BaseContainer/index.vue'
import BaseBottomBtn from '@/components/BaseBottomBtn/index.vue'
import successPopup from '@/pages/customer/list/components/successPopup.vue'
import NavBar from '@/components/defaultNavBar/index'
import { ref, reactive } from 'vue'
import { contractSaveApi, contractEditSaveApi, contractCreateFormApi, contractEditInfoApi, configApproveApi } from '@/api/customer'
import message from '@/utils/message'
import { delayedReLaunch, clickNavigateTo, delayedNavigateBack } from '@/utils/helper'
import { onLoad } from '@dcloudio/uni-app'
import ProductPanel from '../opportunity/components/product-panel.vue'
import { fillProductInfo, processSubmitProductData } from '../opportunity/components/product'
const productPanelRef = ref()
const productListData = computed(() => {
  return productPanelRef.value?.productListData || []
})

const oaFormRef = ref(null)
// 定义表单
const data = reactive({
  defaultTitle: '添加订单',
  cid: '', // 订单id
  eid: 0,
  jumpUrl: '',
  name: '', // 客户名称
  list: [], // 客户列表
  buildData: {},
  productList: [],
  odds_id: 0,
  customer_id: 0,
})

onLoad((e) => {
  // getcustomer()
  if (e.name) {
    data.name = e.name
  }
  data.eid = e.eid
  if (e.cid) {
    data.defaultTitle = '编辑订单'
    data.cid = e.cid
  }
  if (e.odds_id) {
    data.odds_id = e.odds_id
    const oddsData = uni.getStorageSync(`odds_${e.odds_id}`)
    if (oddsData) {
      data.productList = oddsData.product.map(fillProductInfo)
      let totalPrice = 0
      if (data.productList.length > 0) {
        data.productList.map((item) => {
          totalPrice += Number(item.price)
        })
      }

      setTimeout(() => {
        oaFormRef.value.formData.contract_price = totalPrice
      }, 200)
    }
  }
  getConfigApprove()
  // data.jumpUrl = toJumpUrl()
})
onMounted(() => {
  if (data.cid) {
    getcontractInfo()
  } else {
    getcontractCreate()
  }
})

const listData = ref([])

watch(data.productList, (newVal) => {
  if (newVal.length > 0) {
    let totalPrice = 0
    newVal.map((item) => {
      totalPrice += Number(item.price)
    })
    console.log(totalPrice, 999999)
    oaFormRef.value.formData.contract_price = totalPrice
  }
})

// 获取订单新增表单
const getcontractCreate = () => {
  contractCreateFormApi({
    odds_id: data.odds_id,
    eid: data.eid || 0,
  }).then((res) => {
    if (data.eid) {
      res.data.forEach((item) => {
        item.data.forEach((val) => {
          val.text = ''
          if (val.key == 'contract_customer') {
            val.text1 = data.name
            val.options_level = 1
            val.value = data.eid
          }
        })
      })
    } else if (data.odds_id) {
      const val = res.data[0].data.find((item) => item.key == 'contract_customer')
      if (val) {
        val.text1 = val.options[0].text
      }
      const oddsItem = res.data[0].data.find((item) => item.key == 'oid')
      if (oddsItem) {
        oddsItem.text1 = oddsItem.options[0].text
      }
    }
    listData.value = res.data
  })
}

// 获取编辑订单信息
const getcontractInfo = () => {
  contractEditInfoApi(data.cid, { edit: 1 }).then((res) => {
    if (data.eid) {
      data.productList = res.data.product
      res.data.form.forEach((item) => {
        item.data.forEach((val) => {
          val.text = ''
          if (val.key == 'contract_customer') {
            val.text1 = data.name
            val.options_level = 1
            val.value = data.eid
          }
        })
      })
    } else if (data.odds_id) {
      const val = res.data.list[0].data.find((item) => item.key == 'contract_customer')
      if (val) {
        val.text1 = val.options[0].text
      }
      const oddsItem = res.data.list[0].data.find((item) => item.key == 'oid')
      if (oddsItem) {
        oddsItem.text1 = oddsItem.options[0].text
      }
    }
    listData.value = res.data.form
  })
}

// 保存
const clickSubmit = () => {
  oaFormRef.value.submit()
}

// 新增订单
const submitOk = (form) => {
  form.types = data.types
  if (data.cid) {
    let dataId = data.cid

    clientPut(dataId, form)
  } else {
    clientSave({
      ...form,
      products: processSubmitProductData(productListData.value),
    })
  }
}
// 编辑
const clientPut = (id, data) => {
  data.products = processSubmitProductData(productListData.value)
  contractEditSaveApi(id, data)
    .then((res) => {
      message.success(res.message)
      delayedReLaunch(`/pages/customer/contract/details?id=${id}`)
    })
    .catch((err) => {
      message.error(err.message)
    })
}
// 新增
const clientSave = (data) => {
  contractSaveApi(data)
    .then((res) => {
      message.success(res.message)
      successPopupRef.value.popupOpen(res.data.id)
    })
    .catch((err) => {
      message.error(err.message)
    })
}
const getConfigApprove = () => {
  configApproveApi().then((res) => {
    data.buildData = res.data
  })
}

const toJumpUrl = (e, cid) => {
  // 判断跳转地址
  if (e === 1) {
    delayedReLaunch('/pages/customer/contract/index')
  } else {
  }
  // const formType = store.state.app.customerFormType
  // let url = ''
  // url = (formType.type && formType.type === 'list') ? '/pages/customer/list/index' :
  //   `/pages/customer/list/contract?eid=${formData.eid}&name=${data.name}`
  // return url
}

const loading = ref(false)
const successPopupRef = ref(null)

// 去添加付款
const successChange = (e, cid) => {
  if (e === 1) {
    delayedNavigateBack()
    // uni.redirectTo({
    //   url: `/pages/customer/list/contract?eid=${data.eid}&name=${data.name}&types=1`
    // });
  } else {
    if (data.buildData.contract_refund_switch) {
      clickNavigateTo(`/pages/users/examine/default?id=${data.buildData.contract_refund_switch}&eid=${data.eid}&cid=${cid}`)
    }
  }
}
</script>

<style lang="scss" scoped>
.head-wrap {
  padding-top: var(--status-bar-height);
  background-color: #fff;
  position: sticky;
  top: 0;
  z-index: 1;
}

.placeholder-box {
  height: calc(var(--bottom-area-height) + 180rpx);
}

::v-deep .pb60 {
  padding-bottom: 0;
}

.form-card {
  // &:last-child {
  //   margin-bottom: calc(var(--bottom-area-height) + 120rpx);
  // }
}
</style>

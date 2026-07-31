<template>
  <BaseContainer class="base-container">
    <!-- 顶部 -->
    <view class="head-wrap">
      <NavBar :is-right="true" :defaultTitle="data.eid ? '编辑客户' : ' 添加客户'" />
    </view>
    <!-- 表单内容 -->
    <view class="form-card">
      <oaForm ref="oaFormRef" :listData="listData" immediate @submitOk="submitOk"></oaForm>
    </view>
    <view class="placeholder-box" :class="isOaWangeditor ? 'placeholder-bottom' : ''"></view>
    <view :class="isOaWangeditor ? 'bottom-box' : ''">
      <BaseBottomBtn text="提交" @click="clickSubmit" />
    </view>
    <!-- 组件 -->
    <success-popup ref="successPopupRef" :type="0" title="客户" button-title="添加商机" @change="successChange"></success-popup>
  </BaseContainer>
</template>
<script setup>
import oaForm from '@/components/oaForm/index.vue'
import BaseContainer from '@/components/BaseContainer/index.vue'
import BaseBottomBtn from '@/components/BaseBottomBtn/index.vue'
import NavBar from '@/components/defaultNavBar/index.vue'
import { ref, reactive } from 'vue'
import message from '@/utils/message'
import successPopup from './components/successPopup.vue'
import { clickNavigateTo, delayedReLaunch } from '@/utils/helper'
import { clientSaveApi, clientPutApi, clientEditInfoApi, clientCreateFormApi } from '@/api/customer'
import { isWxWorkEnv } from '@/libs/wxwork'

const oaFormRef = ref(null)
const listData = ref([])
const isOaWangeditor = ref(false)
// 定义表单
const data = reactive({
  eid: '',
  types: 'customer',
  Tabtype: '',
  is_show: false,
  name: '', // 客户名称
  defaultTitle: '添加客户',
  isShowTitle: false,
})

const leadData = ref(null)

const getOptionId = (item) => {
  if (item && typeof item === 'object') return item.id ?? item.value
  return item
}

const getOptionName = (item) => {
  if (item && typeof item === 'object') return item.name ?? item.label ?? item.text ?? ''
  return item
}

const normalizeLeadLabelValue = (value, options = []) => {
  const values = Array.isArray(value)
    ? value
    : typeof value === 'string'
      ? value.split(',').map((item) => item.trim()).filter(Boolean)
      : value
        ? [value]
        : []

  const labelMap = new Map()
  options.forEach((group) => {
    const groupName = getOptionName(group)
    ;(group.children || []).forEach((item) => {
      const itemName = getOptionName(item)
      labelMap.set(String(getOptionId(item)), getOptionId(item))
      labelMap.set(String(itemName), getOptionId(item))
      if (groupName) {
        labelMap.set(`${groupName}·${itemName}`, getOptionId(item))
      }
    })
  })

  return values
    .map((item) => labelMap.get(String(getOptionId(item))) ?? labelMap.get(String(getOptionName(item))) ?? getOptionId(item))
    .filter((item) => item !== undefined && item !== null && item !== '')
}

const getLeadValueByKey = (key, field) => {
  if (!leadData.value) return undefined
  if (key === 'customer_label') {
    return normalizeLeadLabelValue(
      leadData.value.customer_label ?? leadData.value.label ?? leadData.value.label_id ?? leadData.value.label_ids,
      field.options,
    )
  }
  return leadData.value[key]
}

import { onLoad } from '@dcloudio/uni-app'
onLoad((e) => {
  if (e.lead_data_id) {
    leadData.value = uni.getStorageSync(e.lead_data_id)
  }
  if (e.Tabtype) {
    data.Tabtype = e.Tabtype
  }
  if (e.types) {
    data.types = e.types
  }
  if (e.eid) {
    data.defaultTitle = '编辑客户'
    data.eid = e.eid
    getclientInfo()
  } else {
    getclientCreate()
  }
})

// 获取客户新增表单
const getclientCreate = () => {
  let query = {}
  if (leadData.value && leadData.value.link_id) {
    query.link_id = leadData.value.link_id
  }
  clientCreateFormApi(query).then((res) => {
    if (leadData.value) {
      for (const item of res.data.map((c) => c.data).flat()) {
        if (item.input_type === 'oaWangeditor') {
          isOaWangeditor.value = true
        }
        const leadValue = getLeadValueByKey(item.key, item)
        if (leadValue !== undefined && leadValue !== null) {
          if (item.key !== 'customer_followed') {
            item.value = item.key === 'customer_label' || Array.isArray(leadValue) ? leadValue : leadValue + ''
          }
        }
      }
    }

    listData.value = res.data
    isOaWangeditor.value = listData.value.some((item) => {
      return item.data?.some((el) => el.input_type === 'oaWangeditor')
    })
  })
}
// 获取编辑客户信息
const getclientInfo = () => {
  clientEditInfoApi(data.eid, { edit: 1 }).then((res) => {
    listData.value = res.data.form
    isOaWangeditor.value = listData.value.some((item) => {
      return item.data?.some((el) => el.input_type === 'oaWangeditor')
    })
  })
}

const successPopupRef = ref(null)

// 保存
const clickSubmit = () => {
  oaFormRef.value.submit()
}
// 新增客户
const submitOk = (form) => {
  data.name = form.customer_name
  form.types = data.types
  if (data.eid) {
    let dataId = data.eid
    clientPut(dataId, form)
  } else {
    clientSave(form)
  }
}
// 编辑
const clientPut = (id, obj) => {
  clientPutApi(id, obj)
    .then((res) => {
      message.success(res.message)

      delayedReLaunch(`/pages/customer/list/details?id=${id}&types=${data.Tabtype}`)
    })
    .catch((err) => {
      message.error(err.message)
    })
}
// 新增
const clientSave = (obj) => {
  clientSaveApi(obj)
    .then((res) => {
      data.eid = res.data.id
      message.success(res.message)
      successPopupRef.value.popupOpen(res.data.id)
    })
    .catch((err) => {
      if (err.status == 2001) {
        uni.showModal({
          title: '提示',
          content: err.message,
          success: (res) => {
            if (res.confirm) {
              data.types = 'customer'
              obj.force = 1
              submitOk(obj)
              obj.force = 0
            }
            data.types = 'customer'
            obj.force = 0
          },
        })
      } else {
        message.error(err.message)
      }
    })
}

// 去添加订单
const successChange = (e, eid) => {
  if (e === 1) {
    if (isWxWorkEnv) {
      uni.redirectTo({
        url: '/pages/customer/list/details?id=' + eid,
      })
    } else {
      delayedReLaunch('/pages/customer/list/index')
    }
  } else {
    clickNavigateTo(`/pages/customer/opportunity/add?eid=${eid}`)

    // clickNavigateTo(
    //   `/pages/customer/contract/addContract?eid=${eid}&name=${data.name}`,
    // );
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

.form-card {
  margin-bottom: calc(var(--bottom-area-height) + 180rpx);
}

.placeholder-bottom {
  height: 40rpx;
}

.bottom-box {
  ::v-deep .base-bottom-btn-box {
    position: fixed;
    bottom: 35px;
    left: 0;
    right: 0;
    transform: none;
  }
}
</style>

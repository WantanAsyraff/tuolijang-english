<template>
  <view>
    <view class="contacts-tab-info">
      <template v-if="contactsData.length > 0">
        <view class="contacts-info-item" v-for="(item, index) in contactsData" :key="index">
          <view class="contacts-header">
            <view class="info-header">
              <image v-if="item.work_customer && item.work_customer.avatar" class="img" :src="item.work_customer.avatar" mode=""> </image>
              <view>
                <view class="info-name">{{ item.liaison_name || '--' }}</view>
                <view v-if="item.work_customer" class="info-label">{{ item.work_customer.corp_name }} </view>
              </view>
            </view>
            <dean-popover ref="deanPopoverRef" model-direction="right">
              <template #icon>
                <text class="iconfont icon-yunwenjian-gengduo" style="cursor: pointer"></text>
              </template>
              <view class="modal-item" @click="changePopover(item, index, 1)"><text class="iconfont icon-gongzuohuibao-bianji"></text>{{ $t('ui.customerQuickReplyIndexEdit') }} </view>
              <view class="modal-item" @click="changePopover(item, index, 2)"><text class="iconfont icon-shanchu1"></text>{{ $t('ui.examineFormApprovalBillDelete') }} </view>
            </dean-popover>
          </view>
          <view class="contacts-info">
            <uni-row v-for="field in displayFieldList" :key="field.field" style="margin-bottom: 8px" class="info-item">
              <uni-col :span="6" class="left">{{ field.name }}</uni-col>
              <uni-col :span="18" :class="{ phone: isPhoneField(field.field) }" @click="handleFieldClick(item, field)">
                {{ getContactFieldValue(item, field) }}
              </uni-col>
            </uni-row>
          </view>
        </view>
      </template>
      <empty v-else :index="9" :title="emptyTitle" style="height: 950rpx"></empty>
    </view>
    <view class="footer-text" v-if="contactsData.length > 0 && count <= contactsData.length">{{ $t('ui.customerListFollowRecordNoMore') }}</view>
  </view>
</template>

<script setup>
import empty from '@/components/empty/index.vue'
import { clickNavigateTo } from '@/utils/helper'
import deanPopover from '@/components/deanPopover/index.vue'
import { liaisonDeleteApi, salesmanCustomApi } from '@/api/customer'
import message from '@/utils/message'
import { ref, toRefs, onMounted, computed } from 'vue'
const props = defineProps({
  contactsData: {
    type: Array,
    default: () => {
      return []
    },
  },
  count: {
    type: Number,
    default: 0,
  },
  eid: {
    type: Number,
    default: 0,
  },
  emptyTitle: {
    type: String,
    default: '暂无联系人，快去添加吧！',
  },
})
const { contactsData, eid, emptyTitle, count } = toRefs(props)
const deanPopoverRef = ref(null)

const selectList = ref([])

// 头部已展示姓名字段，列表区不再重复展示
const HEADER_FIELDS = ['liaison_name']
const displayFieldList = computed(() => {
  return selectList.value.filter((item) => !HEADER_FIELDS.includes(item.field))
})

onMounted(() => {
  getLiaisonInfo()
})

const getLiaisonInfo = () => {
  salesmanCustomApi('liaison')
    .then((res) => {
      selectList.value = (res.data.list || []).filter((item) => res.data.list_select.includes(item.field))
    })
    .catch((error) => {
      message.error(error.message)
    })
}

const isPhoneField = (field) => field === 'liaison_tel'

const getContactFieldValue = (item, fieldConfig) => {
  const value = item[fieldConfig.field]
  if (value === undefined || value === null || value === '') return '--'

  if (Array.isArray(value)) {
    if (!value.length) return '--'
    return value.map((val) => val.name || val.label || val.text || val).join('、')
  }

  if (typeof value === 'object') {
    if (value.name !== undefined && value.name !== null && value.name !== '') return value.name
    if (value.label !== undefined && value.label !== null && value.label !== '') return value.label
    if (value.text !== undefined && value.text !== null && value.text !== '') return value.text
    if (fieldConfig.dict?.length && value.value !== undefined) {
      const option = fieldConfig.dict.find((dictItem) => dictItem.value == value.value)
      return option?.label || option?.name || value.value || '--'
    }
    return '--'
  }

  return value
}

const handleFieldClick = (item, fieldConfig) => {
  if (!isPhoneField(fieldConfig.field) || !item.liaison_tel) return
  uni.makePhoneCall({
    phoneNumber: item.liaison_tel,
  })
}

const changePopover = (item, index, type) => {
  if (type === 1) {
    clickNavigateTo(`/pages/customer/list/addLiaison?eid=${eid.value}&id=${item.id}&tab=3`)
  }

  if (type === 2) {
    let liaisonId = item.id
    uni.showModal({
      title: '提示',
      content: '您确定要删除该客户联系人吗?',
      success: (res) => {
        if (res.confirm) {
          liaisonDeleteApi(liaisonId)
            .then((res) => {
              message.success(res.message)
              contactsData.value.splice(index, 1)
            })
            .catch((error) => {
              message.error(error.message)
            })
        }
      },
    })
  }
}
</script>

<style scoped lang="scss">
.contacts-tab-info {
  background: #fff;
  /* 触发BFC（块级格式化上下文） */
  overflow: hidden;
  /* 或 auto, scroll */
  /* 或者 */
  display: flow-root;

  /* 现代解决方案 */
  .contacts-info-item {
    padding: 30rpx;
    font-size: 26rpx;
    color: $uni-text-color;
    background: #fff;
    border-bottom: 1px solid #eeeeee;
    // &:last-child {
    // 	border: none;
    // }
  }
  .left {
    color: #606266;
  }
}

.contacts-header {
  display: flex;
  justify-content: space-between;
}

.info-header {
  display: flex;
  align-items: center;

  .img {
    width: 80rpx;
    height: 80rpx;
    border-radius: 8rpx;
    margin-right: 16rpx;
  }

  .info-name {
    font-weight: 500;
    font-size: 28rpx;
  }

  .info-label {
    color: #909399;
    font-size: 24rpx;
    margin-top: 4rpx;
  }
}

.contacts-info {
  margin-top: 24rpx;

  .info-item {
    margin-bottom: 24rpx;
    color: $uni-text-color;
    display: flex;
    align-items: center;
    font-size: 26rpx;

    &:last-child {
      align-items: flex-start;
    }

    .phone {
      color: #1890ff;
    }
  }
}
</style>

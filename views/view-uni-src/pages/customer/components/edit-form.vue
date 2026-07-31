<template>
  <uni-popup ref="popupRef" type="bottom" @change="popupChange">
    <view class="popup-wrapper">
      <view class="popup-header">
        <text class="title">{{ $t('ui.customerEditFormBasicInformation') }}</text>
      </view>

      <view class="popup-content">
        <!-- <view class="form-item"> -->
        <oaForm ref="oaFormRef" :listData="listData" :immediate="true" @submitOk="handleSubmitOk" @editorChange="handleSubmitOk"></oaForm>
        <!-- </view> -->
      </view>

      <view
        class="popup-footer"
        :style="{
          paddingBottom:
            valData.input_type === 'oaWangeditor'
              ? 'calc(24rpx + env(safe-area-inset-bottom) + 100rpx)'
              : 'calc(24rpx + env(safe-area-inset-bottom))',
        }"
      >
        <view class="btn btn-cancel" @click="popupClose">{{ $t('ui.baTreePickerIndexCancel') }}</view>
        <view class="btn btn-confirm" @click="handleConfirm">{{ $t('ui.moduleFormCascadeOk') }}</view>
      </view>
    </view>
  </uni-popup>
</template>

<script setup>
import oaForm from '@/components/oaForm/index.vue'
import { clientPutApi, leadEditApi, opportunityEditApi, contractEditSaveApi } from '@/api/customer'
import { ref, reactive, nextTick } from 'vue'
import message from '@/utils/message'
const emit = defineEmits(['submit', 'close', 'refreshDetails'])
const popupRef = ref(null)
const listData = ref([{ data: [] }])
const oaFormRef = ref(null)
const props = defineProps({
  eid: {
    type: Number,
    default: 0,
  },
  type: {
    type: String,
    default: 'customer',
  },
  id: {
    type: Number,
    default: 0,
  },
})
const { eid, type, id } = toRefs(props)
// 样式配置
const placeholderStyle = ref('color: #C0C4CC; font-size: 30rpx')
const inputStyles = reactive({
  color: '#303133',
  disableColor: '#ffffff',
})
const valData = ref(null)
const isReadonlyField = (field) => {
  return !!(field?.readonly || field?.system_field || field?.disabled || ['contract_no', 'odds_no'].includes(field?.key))
}

// 打开弹窗
const popupOpen = async (val = {}) => {
  if (isReadonlyField(val)) return
  valData.value = val
  // 创建新数组引用，触发 oaForm 的 watch 监听
  listData.value = [{ data: [{ ...val }] }]
  await nextTick()
  popupRef.value?.open()
}

// 关闭弹窗
const popupClose = () => {
  popupRef.value?.close()
  emit('close')
}

// 弹窗状态变化
const popupChange = (e) => {
  if (!e.show) {
  }
}

// 修改成功
const isOk = () => {
  popupRef.value?.close()
  emit('refreshDetails')
}

const handleSubmitOk = (form) => {
  const key = Object.keys(form)[0]
  if (!key) return
  if (['contract_no', 'odds_no'].includes(key)) return
  const obj = { value: form[key], field: key }

  const apiMap = {
    customer: () => clientPutApi(eid.value, obj),
    clue: () => leadEditApi(id.value, obj),
    contract: () => contractEditSaveApi(id.value, obj),
    odds: () => opportunityEditApi(id.value, obj),
  }

  const apiFn = apiMap[type.value]
  if (apiFn) {
    apiFn()
      .then((res) => {
        message.success(res.message)
        isOk()
      })
      .catch((err) => {
        message.error(err.message)
      })
  }
}

// 提交表单数据
const handleConfirm = () => {
  oaFormRef.value.submit()
}

defineExpose({
  popupOpen,
})
</script>

<style lang="scss" scoped>
.popup-wrapper {
  width: 100%;
  background-color: #fff;
  border-radius: 24rpx 24rpx 0 0;
  max-height: 80vh;
  display: flex;
  flex-direction: column;
}

.popup-header {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 102rpx;
  flex-shrink: 0;

  .title {
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 500;
    font-size: 32rpx;
    color: #303133;
    text-align: center;
  }
}

.popup-content {
  flex: 1;
  padding: 20rpx 30rpx 80rpx 30rpx;
  overflow-y: auto;
}
::v-deep .content .list-item {
  background-color: #f7f7f7;
  border-radius: 16rpx;
}

.popup-footer {
  display: flex;
  gap: 24rpx;
  padding: 24rpx;
  padding-top: 0;
  padding-bottom: calc(24rpx + env(safe-area-inset-bottom));
  flex-shrink: 0;

  .btn {
    flex: 1;
    height: 84rpx;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16rpx 16rpx 16rpx 16rpx;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 26rpx;
    color: #333333;
  }

  .btn-cancel {
    background-color: #f5f5f5;
  }

  .btn-confirm {
    background-color: #308bf8;
    color: #ffffff;
  }
}

::v-deep .uni-scroll-view-content {
  overflow-y: auto !important;
}
::v-deep .uni-data-tree-dialog {
  position: fixed;
  left: 0;
  top: -90%;
  right: 0;
  bottom: 0;
}
::v-deep .flie .box {
  background-color: #fff;
}
</style>

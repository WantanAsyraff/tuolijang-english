<template>
  <uni-popup ref="popupRef" type="bottom" @change="popupChange">
    <view class="popup-wrapper">
      <view class="popup-header">
        <text class="title">{{ isEdit ? $t('ui.customerQuickReplyAddReplyEditQuickReplies') : $t('ui.customerQuickReplyAddReplyNewQuickReply') }}</text>
      </view>

      <view class="popup-content">
        <view class="form-item">
          <view class="form-label required">{{ $t('ui.customerQuickReplyAddReplyReplyContent') }}</view>
          <view class="textarea-wrapper">
            <uni-easyinput
              :inputBorder="false"
              v-model="formData.content"
              type="textarea"
              :clearable="false"
              :adjust-position="false"
              :maxlength="500"
              :placeholder="$t('ui.customerQuickReplyAddReplyEnterQuickReplyContent')"
              :styles="inputStyles"
              :placeholder-style="placeholderStyle"
            ></uni-easyinput>
          </view>
        </view>

        <view class="form-item">
          <view class="form-label">{{ $t('ui.customerQuickReplyAddReplySort') }} <text @click="handleSort" class="iconfont icon-yemiantishi"></text></view>
          <view class="input-wrapper">
            <uni-easyinput
              :inputBorder="false"
              v-model="formData.sort"
              type="number"
              :clearable="false"
              :styles="inputStyles"
              :placeholder-style="placeholderStyle"
              :autoHeight="true"
              :placeholder="$t('ui.customerQuickReplyAddReplyPleaseEnterSorting')"
            ></uni-easyinput>
          </view>
        </view>
      </view>

      <view class="popup-footer">
        <view class="btn btn-cancel" @click="popupClose">{{ $t('ui.baTreePickerIndexCancel') }}</view>
        <view class="btn btn-confirm" @click="handleConfirm">{{ $t('ui.moduleFormCascadeOk') }}</view>
      </view>
    </view>
  </uni-popup>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import message from '@/utils/message'
const emit = defineEmits(['submit', 'close'])

const popupRef = ref(null)
const formData = reactive({
  id: '',
  content: '',
  sort: '',
})
const isEdit = computed(() => !!formData.id)

// 样式配置
const placeholderStyle = ref('color: #C0C4CC; font-size: 30rpx')
const inputStyles = reactive({
  color: '#303133',
  disableColor: '#ffffff',
})

// 打开弹窗
const popupOpen = (data = {}) => {
  if (data.id) {
    formData.id = data.id
  }
  if (data.content) {
    formData.content = data.content
  }
  if (data.sort !== undefined && data.sort !== null) {
    formData.sort = data.sort
  }
  popupRef.value?.open()
}

// 关闭弹窗
const popupClose = () => {
  resetForm()
  popupRef.value?.close()
  emit('close')
}

// 弹窗状态变化
const popupChange = (e) => {
  if (!e.show) {
    resetForm()
  }
}

// 重置表单
const resetForm = () => {
  formData.id = ''
  formData.content = ''
  formData.sort = ''
}

const handleSort = () => {
  message.error('数字越大越往前')
}

// 提交表单
const handleConfirm = () => {
  // 验证内容
  if (!formData.content.trim()) {
    message.error('请输入内容')
    return
  }

  // 排序默认值
  const sort = formData.sort ? Number(formData.sort) : 0

  const submitData = {
    content: formData.content.trim(),
    sort: sort,
  }

  // 如果是编辑模式，添加 id
  if (formData.id) {
    submitData.id = formData.id
  }

  emit('submit', submitData)

  popupClose()
}

defineExpose({
  popupOpen,
})
</script>

<style lang="scss" scoped>
.popup-wrapper {
  width: 100%;
  border-radius: 24rpx 24rpx 0 0;
  background-color: #fff;

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
    font-size: 30rpx;
    color: #303133;
  }
}

.popup-content {
  flex: 1;
  padding: 20rpx 30rpx;
  overflow-y: auto;
}

.form-item {
  margin-bottom: 30rpx;

  &:last-child {
    margin-bottom: 0;
  }
}

.form-label {
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 30rpx;
  color: #303133;
  margin-bottom: 16rpx;

  &.required::after {
    content: '*';
    color: #ff2529;
    margin-right: 4rpx;
  }
}

.textarea-wrapper {
  background-color: #f7f7f7;
  border-radius: 8rpx;
  padding: 20rpx;
  height: 400rpx;
  box-sizing: border-box;
  overflow: hidden;
  position: relative;
}
::v-deep .uni-easyinput__content-textarea {
  height: 400rpx;
}

.input-wrapper {
  background-color: #f7f7f7;
  border-radius: 8rpx;
  padding: 20rpx;
}

.popup-footer {
  display: flex;
  gap: 24rpx;
  padding: 24rpx;
  // padding-bottom: ;
  flex-shrink: 0;

  .btn {
    flex: 1;
    height: 84rpx;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16rpx;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 26rpx;
    color: #333333;
  }

  .btn-cancel {
    background-color: #f7f7f7;
    color: #606266;
  }

  .btn-confirm {
    background-color: #308bf8;
    color: #ffffff;
  }
}

// 深度选择器覆盖 uni-easyinput 默认样式
:deep(.uni-easyinput__content-textarea) {
  background-color: transparent !important;
  padding: 0 !important;
}

:deep(.uni-easyinput__content-input) {
  background-color: transparent !important;
  padding: 0 !important;
}

:deep(.uni-textarea-textarea) {
  font-size: 30rpx;
  color: #303133;
  line-height: 1.6;
  height: 360rpx !important;
}

:deep(.uni-input-input) {
  font-size: 30rpx;
  color: #303133;
}

.icon-yemiantishi {
  cursor: pointer;
  font-size: 26rpx;
  color: #606266;
  line-height: 30rpx;
}
</style>

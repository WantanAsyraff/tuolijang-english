<template>
  <view class="customer-tab-info">
    <view class="info-item" v-for="(item, index) in customerData" :key="index">
      <uni-row style="margin-bottom: 12px" v-for="(val, index1) in item.data" :key="index1" v-show="val.key !== 'clue_id'">
        <uni-col :span="5" class="text-right info-item-left">{{ val.key_name }}</uni-col>
        <uni-col :span="19" v-if="val.type == 'oaWangeditor'" class="info-item-right" @click="clientEdit(val)"
          ><text v-html="val.value || '--'"></text
        ></uni-col>
        <!-- 关联字典颜色 -->
        <view
          v-else-if="val.value && Object.prototype.hasOwnProperty.call(val.value, 'color')"
          class="status-tag"
          :style="{
            color: val.value.color ? val.value.color : '#1890ff',
            background: val.value.color ? getColor(val.value.color, '0.1') : getColor('#1890ff', '0.1'),
          }"
          @click="clientEdit(val)"
        >
          {{ val.value.name }}
        </view>
        <uni-col :span="19" @click="clientEdit(val)" class="info-item-right" v-else-if="val.type == 'file'">
          <view v-if="val.files.length > 0">
            <oa-uploadList :listData="val.files" :clearBtn="false"></oa-uploadList>
          </view>
          <view v-else>--</view>
        </uni-col>

        <!-- 图片 -->
        <uni-col :span="19" @click="clientEdit(val)" v-else-if="val.type == 'images'" class="info-item-right">
          <view class="images" v-if="val.files.length > 0">
            <img
              class="img"
              :src="imgItem.url || imgItem.att_dir"
              alt=""
              v-for="(imgItem, index) in val.files"
              :key="index"
              @click="preview(imgItem)"
            />
          </view>
          <view v-else>--</view>
        </uni-col>
        <!-- 人员 -->
        <uni-col :span="19" @click="clientEdit(val)" v-else-if="val.input_type == 'member'" class="info-item-right">
          <view v-if="val.value.length > 0">
            <text v-for="(item, index) in val.value" :key="index"> {{ item.name }} <text v-if="index < val.value.length - 1">、</text> </text>
          </view>
          <view v-else>--</view>
        </uni-col>

        <!-- 下拉选项 -->
        <uni-col
          :span="19"
          @click="clientEdit(val)"
          v-else-if="!isCustomerLabelField(val) && (val.input_type == 'radio' || (val.input_type == 'select' && val.options_level == 1))"
          class="info-item-right"
        >
          <view v-if="val.value"> {{ getValue(val) }} </view>
          <view v-else>--</view>
        </uni-col>
        <!-- 标签 -->
        <uni-col :span="19" @click="clientEdit(val)" v-else-if="isCustomerLabelField(val)" class="info-item-right">
          <view v-if="getLabel(val).length > 0">
            <text v-for="(el, indexj) in getLabel(val)" :key="indexj">
              <text class="uni-tag">{{ el }}<text v-if="indexj < getLabel(val).length - 1">、</text> </text>
            </text>
          </view>
          <view v-else>--</view>
        </uni-col>

        <uni-col :span="19" @click="clientEdit(val)" v-else class="info-item-right">{{ val.text || cityFn(val.value) || '--' }} </uni-col>
      </uni-row>
    </view>
    <!-- 单字段编辑 -->
    <edit-form ref="editFormRef" :eid="eid" :type="type" :id="id" @refreshDetails="refreshDetails"></edit-form>
  </view>
</template>

<script setup>
import { formatBytes } from '@/utils/file'
import editForm from '@/pages/customer/components/edit-form.vue'
import oaUploadList from '@/components/oa-uploadList/index.vue'
import { isFileTypeIcon, lookPreview, isTypeImage, getColor } from '@/utils/helper'
import { reactive, toRefs, onMounted } from 'vue'
const props = defineProps({
  customerData: {
    type: Array,
    default: () => {
      return []
    },
  },
  eid: {
    type: Number,
    default: 0,
  },
  id: {
    type: Number,
    default: 0,
  },
  type: {
    type: String,
    default: 'customer',
  },
})

// 图片与文档预览
const preview = (item) => {
  lookPreview(item.url, item.real_name || item.name, [item.url])
}
const { customerData, eid, type, id } = toRefs(props)

watch(customerData, (newVal) => {
  formatData()
})

onMounted(() => {
  formatData()
})

const formatData = () => {
  customerData.value.forEach((item) => {
    item.data.forEach((val) => {
      if ((val.input_type === 'select' || val.input_type === 'checked') && val.value) {
        val.text = findNamesByIds(val.options, val.value)
      } else if (isCustomerLabelField(val)) {
        val.text = findNamesByIds(val.options, val.value)
      }
    })
  })
}

// 根据value值展示对应的lable
const getValue = (val) => {
  return val.options.find((item) => item.value == val.value)?.text || '--'
}

const isCustomerLabelField = (val) => {
  return val.key == 'customer_label' || val.type == 'customer_label'
}

const normalizeLabelValue = (value) => {
  if (Array.isArray(value)) return value
  if (typeof value === 'string') {
    return value
      .split(',')
      .map((item) => item.trim())
      .filter(Boolean)
  }
  return value ? [value] : []
}

const getOptionId = (option) => {
  if (option && typeof option === 'object') return option.id ?? option.value
  return option
}

const getOptionName = (option) => {
  if (option && typeof option === 'object') return option.name ?? option.label ?? option.text ?? ''
  return ''
}

// 根据标签值获取对应的标签数组
const getLabel = (val) => {
  const valueList = normalizeLabelValue(val.value)
  if (!valueList.length) return []

  const selectedIds = valueList.map((item) => String(getOptionId(item) ?? item))
  const selectedNames = valueList.map(getOptionName).filter(Boolean)
  const list = []

  ;(val.options || []).forEach((item) => {
    ;(item.children || []).forEach((el) => {
      if (selectedIds.includes(String(getOptionId(el)))) {
        const groupName = getOptionName(item)
        list.push(`${groupName ? groupName + '·' : ''}${getOptionName(el)}`)
      }
    })
  })

  return list.length ? list : selectedNames
}

const findNamesByIds = (options = [], value) => {
  // 扁平化树形数据
  const flattenOptions = (arr) => {
    const result = []
    arr.forEach((item) => {
      result.push(item)
      if (item.children && item.children.length > 0) {
        result.push(...flattenOptions(item.children))
      }
    })
    return result
  }

  const flatOptions = flattenOptions(options)

  // value 是字符串
  if (typeof value === 'string') {
    const item = flatOptions.find((opt) => String(getOptionId(opt)) === value)
    return item ? getOptionName(item) : ''
  }

  // value 是数组，用 / 隔开
  if (Array.isArray(value)) {
    const names = value
      .map((v) => {
        const item = flatOptions.find((opt) => String(getOptionId(opt)) === String(getOptionId(v) ?? v))
        return item ? getOptionName(item) : getOptionName(v)
      })
      .filter((name) => name !== '')
    return names.join('/')
  }

  return ''
}

const emit = defineEmits(['refreshDetails'])
const refreshDetails = () => {
  emit('refreshDetails')
}

const editFormRef = ref(null)

const isReadonlyField = (field) => {
  return !!(field?.readonly || field?.system_field || field?.disabled || ['contract_no', 'odds_no'].includes(field?.key))
}

// 单字段编辑
const clientEdit = (editData) => {
  if (isReadonlyField(editData)) return
  editFormRef.value.popupOpen(editData)
}

const cityFn = (val) => {
  let str = ''
  if (val == '') {
    str = '--'
  } else if (Array.isArray(val)) {
    str = val.toString()
  } else {
    str = val
  }
  return str
}
</script>

<style scoped lang="scss">
.label-text {
  width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.customer-tab-info {
  min-height: 1000rpx;
  padding: 15px 0;
  padding-right: 15px;
  padding-bottom: 80px;

  .info-item {
    padding-left: 15px;
    font-size: $uni-font-size-default;
    color: $uni-text-color;
    margin-bottom: 20rpx;

    &:last-of-type {
      margin-right: 0;
    }

    .info-item-left {
      font-family:
        PingFang SC,
        PingFang SC;
      font-weight: 400;
      color: #606266;
      font-size: 26rpx;
      text-align: left;
    }

    .info-item-right {
      line-height: 1.5;
      font-size: 26rpx;
      ::v-deep p {
        img {
          width: 40px !important;
          height: 40px;
        }
      }
    }
  }
}

.status-tag {
  height: 42rpx;
  border-radius: 8rpx;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 24rpx;
  font-weight: 400;
  padding: 0 10rpx;
  margin-right: 30rpx !important;
}

.images {
  .img {
    width: 82rpx;
    height: 82rpx;
    border-radius: 8rpx;
    margin-right: 16rpx;
  }
}
</style>

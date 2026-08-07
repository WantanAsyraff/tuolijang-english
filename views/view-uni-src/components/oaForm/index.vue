<template>
  <view class="content">
    <!-- 表单内容 -->
    <view class="examine-content">
      <uni-forms :border="false" :modelValue="formData" ref="form" label-width="80px" :rules="data.rules">
        <view class="list-item" v-for="(item, indexItem) in listData" :key="indexItem">
          <view v-if="item.ident == 'product'" style="padding-left: 0">
            <slot name="product"></slot>
          </view>
          <template v-else>
            <view v-for="(val, index) in item.data" :key="index" class="line-box">
              <!-- 输入框 -->
              <uni-forms-item class="input-label" required name="name" v-if="val.input_type === 'input' && val.type === 'text'">
                <template v-slot:label>
                  <view class="uni-forms-item__label">{{ $ts(val.key_name) }} <text class="iconfont" v-if="val.required == 1">*</text></view>
                </template>
                <uni-easyinput
                  :inputBorder="false"
                  :modelValue="formData[val.key]"
                  @update:modelValue="handleTextInput(val, $event)"
                  type="text"
                  :clearable="false"
                  :disabled="isReadonlyField(val)"
                  :styles="styles"
                  :placeholder-style="placeholderStyle"
                  :autoHeight="true"
                  :maxlength="val.max"
                  :placeholder="$t('ui.examineFormTimeFromPleaseEnter')"
                >
                </uni-easyinput>
              </uni-forms-item>
              <!-- 选择人员 -->
              <uni-forms-item class="input-label" v-if="val.input_type === 'member'">
                <template v-slot:label>
                  <view class="uni-forms-item__label">{{ $ts(val.key_name) }}<text class="iconfont" v-if="val.required == 1">*</text> </view>
                </template>

                <view class="picker-input picker-input-placeholder" @click="openMember(val, indexItem, index)" v-if="val.value.length == 0">
                  {{ $t('ui.examineFormCustomCheckboxPleaseSelect') }}
                  <view class="iconfont icon-fanhui"></view>
                </view>
                <view v-else class="picker-input label-flex" @click="openMember(val, indexItem, index)">
                  <text class="label-box">{{
                    val.options
                      .slice(0, 4)
                      .map((item) => item.name)
                      .join('、')
                  }}</text>
                  <text v-if="val.options.length > 4" class="more-text">...</text>
                  <view class="iconfont icon-fanhui" v-if="val.options.length > 0"></view>
                </view>
              </uni-forms-item>

              <!-- 数字-->
              <uni-forms-item class="input-label" name="price" v-if="val.type === 'number'">
                <template v-slot:label>
                  <view class="uni-forms-item__label">{{ $ts(val.key_name) }}<text class="iconfont" v-if="val.required == 1">*</text> </view>
                </template>
                <uni-easyinput
                  :inputBorder="false"
                  v-model="formData[val.key]"
                  type="number"
                  :clearable="false"
                  :disabled="isReadonlyField(val)"
                  :styles="styles"
                  maxlength="11"
                  :placeholder-style="placeholderStyle"
                  :autoHeight="true"
                  :placeholder="$t('ui.examineFormTimeFromPleaseEnter')"
                  @change="handleChange(val, $event)"
                >
                </uni-easyinput>
              </uni-forms-item>

              <!-- 客户标签 -->
              <uni-forms-item class="input-label" v-if="val.key === 'customer_label'">
                <template v-slot:label>
                  <view class="uni-forms-item__label">{{ $ts(val.key_name) }}<text class="iconfont" v-if="val.required == 1">*</text> </view>
                </template>
                <view class="picker-input picker-input-placeholder" @click="changeLabel" v-if="normalizeLabelValue(formData[val.key]).length == 0">
                  {{ $t('ui.examineFormCustomCheckboxPleaseSelect') }}
                  <view class="iconfont icon-fanhui"></view>
                </view>
                <view v-else class="picker-input label-flex" @click="changeLabel">
                  <text class="label-box">{{
                    data.labelName
                      .slice(0, 4)
                      .map((item) => item)
                      .join('、')
                  }}</text>
                  <text v-if="data.labelName.length > 4" class="more-text">...</text>
                  <view class="iconfont icon-fanhui" v-if="data.labelName.length > 0"></view>
                </view>
              </uni-forms-item>

              <!-- 单选按钮 -->
              <uni-forms-item class="input-label" v-if="val.type === 'radio'">
                <template v-slot:label>
                  <view class="uni-forms-item__label">{{ $ts(val.key_name) }} <text class="iconfont" v-if="val.required == 1">*</text> </view>
                </template>
                <uni-data-checkbox
                  v-model="formData[val.key]"
                  :disabled="isReadonlyField(val)"
                  :map="{ text: 'label', value: 'value' }"
                  :localdata="val.options"
                />
              </uni-forms-item>

              <!-- 省市区-级联选择器 -->
              <!-- <uni-forms-item class="input-label" v-if="val.input_type === 'select' && val.options_level > 1 && val.type == 'single'">
                <template v-slot:label>
                  <view class="uni-forms-item__label">{{ $ts(val.key_name) }} <text class="iconfont" v-if="val.required == 1">*</text></view>
                </template>
                <uni-data-picker
                  v-model="formData[val.key]"
                  :localdata="val.options"
                  :popup-title="String($ts(val.placeholder || '请选择'))"
                  @change="cityChange($event, indexItem, index)"
                >
                  <view v-if="!formData[val.key]" class="picker-input picker-input-placeholder">
                    请选择
                    <view class="iconfont icon-fanhui"></view>
                  </view>
                  <view class="picker-input" v-else>
                    {{ $ts(val.text) }}
                    <view class="iconfont icon-fanhui"></view>
                  </view>
                </uni-data-picker>
              </uni-forms-item> -->

              <!-- 下拉选择 -->
              <uni-forms-item v-if="val.input_type === 'select' && !['clue_id', 'customer_label'].includes(val.key)" class="input-label">
                <template v-slot:label>
                  <view class="uni-forms-item__label">
                    <text class="label-item">{{ $ts(val.key_name) }}</text>
                    <text v-if="val.required == 1" class="iconfont">*</text>
                  </view>
                </template>

                <!-- 复选 -->
                <view v-if="val.type == 'multiple'">
                  <view v-if="!formData[val.key]" class="picker-input picker-input-placeholder" @click="openSelect(val, indexItem, index)">
                    {{ $t('ui.examineFormCustomCheckboxPleaseSelect') }}
                    <view class="iconfont icon-fanhui"></view>
                  </view>
                  <view v-if="formData[val.key]" class="picker-input" @click="openSelect(val, indexItem, index)">
                    <text class="picker-input-text">{{ $ts(val.text) }}</text>
                    <view class="iconfont icon-fanhui"></view>
                  </view>
                  <multiplePicker
                    v-if="data.show"
                    :show="data.show"
                    :columns="data.options"
                    :defaultIndex="formData[val.key]"
                    @change="multiplePickerChange($event, val)"
                    @cancel="ontouchcancel"
                  ></multiplePicker>
                </view>

                <template v-if="val.type === 'single'">
                  <picker
                    v-if="val.options_level === 1"
                    mode="selector"
                    :disabled="isReadonlyField(val)"
                    :value="formData[val.key]"
                    range-key="label"
                    :range="val.options"
                    @change="contractChange($event, indexItem, index)"
                  >
                    <view v-if="!formData[val.key]" class="picker-input picker-input-placeholder">
                      {{ $t('ui.examineFormCustomCheckboxPleaseSelect') }}
                      <view class="iconfont icon-fanhui"></view>
                    </view>
                    <view v-if="formData[val.key]" class="picker-input" :style="{ color: isReadonlyField(val) ? '#909399' : '' }">
                      <text class="picker-input-text">{{ $ts(val.text || val.text1) }}</text>
                      <view class="iconfont icon-fanhui"></view>
                    </view>
                  </picker>

                  <uni-data-picker
                    v-else
                    v-model="formData[val.key]"
                    :localdata="val.options"
                    :map="{ text: 'text', value: 'value' }"
                    :popup-title="String($ts(val.placeholder || $t('ui.examineFormCustomCheckboxPleaseSelect')))"
                    @change="cityChange($event, indexItem, index)"
                  >
                    <view v-if="!formData[val.key]" class="picker-input picker-input-placeholder">
                      {{ $t('ui.examineFormCustomCheckboxPleaseSelect') }}
                      <view class="iconfont icon-fanhui"></view>
                    </view>
                    <view class="picker-input" v-else>
                      <text class="picker-input-text">{{ getSelectText(val) }}</text>
                      <view class="iconfont icon-fanhui"></view>
                    </view>
                  </uni-data-picker>
                </template>
              </uni-forms-item>

              <!-- 开始时间 -->
              <uni-forms-item class="input-label" v-if="val.type === 'date'">
                <template v-slot:label>
                  <view class="uni-forms-item__label">{{ $ts(val.key_name) }} <text class="iconfont" v-if="val.required == 1">*</text></view>
                </template>
                <picker mode="date" :value="formData[val.key]" :start="startDate" :end="endDate" @change="startDateChange($event, indexItem, index)">
                  <view v-if="!formData[val.key]" class="picker-input picker-input-placeholder">
                    {{ $t('ui.examineFormCustomCheckboxPleaseSelect') }}
                    <view class="iconfont icon-fanhui"></view>
                  </view>
                  <view class="picker-input" v-else>
                    {{ formData[val.key] }}
                    <view class="iconfont icon-fanhui"></view>
                  </view>
                </picker>
              </uni-forms-item>

              <uni-forms-item class="input-label" v-if="val.type === 'datetime'">
                <template v-slot:label>
                  <view class="uni-forms-item__label">{{ $ts(val.key_name) }} <text class="iconfont" v-if="val.required == 1">*</text></view>
                </template>

                <view class="xp-picker-content">
                  <xp-picker v-model="formData[val.key]" mode="ymdhi" actionPosition="top" :yearRange="[2008, 2070]" />
                  <view class="iconfont icon-fanhui"></view>
                </view>
              </uni-forms-item>

              <!-- 上传图片 -->
              <uni-forms-item class="is-direction-top" v-if="val.type === 'images'">
                <template v-slot:label>
                  <view class="uni-forms-item__label mt36 p24">
                    {{ $ts(val.key_name) }} <text class="tips">{{ $t('ui.moduleFormIndexRecommended7341034Max2MbJpgJpegAnd') }} </text>
                  </view>

                  <view class="upload">
                    <view class="box" v-for="(item, indexImg) in val.options" :key="indexImg">
                      <image class="img" :src="item.url" mode="" @click="preview(item)"> </image>
                      <view class="delete" @click="deleteImg(val, item.id)">
                        <text class="iconfont icon-shenpizhongxin-jujue"></text>
                      </view>
                    </view>
                    <view class="upload-box" v-if="val.options.length < val.max" @click="uploadAvatar(indexItem, index)">
                      <view class="iconfont icon-paizhao"></view>
                      <view class="text"> {{ $t('ui.oaFormIndexUpload') }}{{ $ts(val.key_name) }} </view>
                    </view>
                  </view>
                </template>
              </uni-forms-item>

              <!-- 上传附件 -->
              <uni-forms-item class="is-direction-top" v-if="val.type === 'file'">
                <template v-slot:label>
                  <view class="uni-forms-item__label mt36 p24">
                    <view class="label">
                      <view>
                        {{ $ts(val.key_name) }}
                      </view>
                      <view
                        class="iconfont icon-biaodan-tianjia"
                        v-if="data.flieList.length !== val.max"
                        @click="uploadFlieFn(indexItem, index)"
                      ></view>
                    </view>
                    <view class="tips">{{ $t('ui.moduleFormIndexRecommendedMaximumSize') }}{{ fileSizeOne }}{{ $t('ui.oaFormIndexImagesAttachmentsAndDocumentsAreSupported') }} </view>
                  </view>
                  <view class="flie">
                    <view class="box" v-for="(item, indexs) in val.options" :key="indexs" @click="preview(item)">
                      <view class="left">
                        <image class="slot-image" :src="`/static/image/cloudfile/${isFileTypeIcon(item.name)}`"> </image>
                        <view style="width: calc(100% - 40px)">
                          <view class="name">
                            {{ item.name }}
                          </view>
                          <view class="size"> {{ formatBytes(item.size) || '--' }} </view>
                        </view>
                        <view class="iconfont icon-guanbi-yangshiyi1" @click.stop="deleteFile(val, item.id)"> </view>
                      </view>
                    </view>
                  </view>
                </template>
              </uni-forms-item>

              <!-- 备注- 文本域 -->
              <uni-forms-item class="is-direction-top" v-if="val.type === 'textarea'">
                <template v-slot:label>
                  <view class="uni-forms-item__label mt36 mb10"> {{ $ts(val.key_name) }}<text class="iconfont" v-if="val.required == 1">*</text> </view>
                </template>
                <uni-easyinput
                  :inputBorder="false"
                  v-model="formData[val.key]"
                  type="textarea"
                  :clearable="false"
                  :disabled="isReadonlyField(val)"
                  :styles="styles"
                  :placeholder-style="placeholderStyle"
                  :adjust-position="false"
                  :maxlength="256"
                  :placeholder="String($ts(val.placeholder || $t('ui.attendanceShiftAddEnter') + val.key_name))"
                >
                </uni-easyinput>
              </uni-forms-item>

              <!-- 富文本 -->
              <uni-forms-item class="is-direction-top height-350" v-if="val.type === 'oaWangeditor'">
                <template v-slot:label>
                  <view class="uni-forms-item__label mt36"> {{ $ts(val.key_name) }}<text class="iconfont" v-if="val.required == 1">*</text> </view>
                </template>

                <c-editor
                  :key="val.key"
                  :content="formData[val.key]"
                  @saveContent="saveContent($event, val.key)"
                  :placeholder="String($ts(val.placeholder || $t('ui.attendanceShiftAddEnter') + val.key_name))"
                ></c-editor>
              </uni-forms-item>
            </view>
          </template>
        </view>
      </uni-forms>
    </view>

    <!-- 组件 -->
    <!-- 选择人员组件 -->
    <oa-member ref="memberRef" :onlyOne="onlyOne" @confirm="confirmMember"></oa-member>
    <selected-label v-if="labelShow" :title="$t('ui.customerListCustomerMoreCustomerLabels')" ref="selectedLabelRef" @changeItem="changeItem" @resetLabel="resetLabel"> </selected-label>
    <success-popup ref="successPopupRef" :type="0" :title="$t('ui.customerListAddCustomerCustomer')" :button-title="$t('ui.oaFormIndexAddOrder')" @change="successChange"> </success-popup>
  </view>
</template>
<script setup>import appI18n from '@/locale';

import { ref, reactive, toRefs, onMounted } from 'vue'
import { formatBytes } from '@/utils/file'
import message from '@/utils/message'
import selectedLabel from '@/pages/customer/list/components/selectedLabel.vue'
import oaMember from '@/components/oaMember/index.vue'
import successPopup from '@/pages/customer/list/components/successPopup.vue'
import cEditor from '@/components/editor-common/editor.vue'
import multiplePicker from '@/components/multiplePicker/index.vue'
import { lookPreview, fileSizeOne, isTypeImage, isFileTypeIcon } from '@/utils/helper'
import { clientlabelApi } from '@/api/customer'
import { useStore } from 'vuex'
import { navigateToDepartment, resetSelectDepartment, resetExamineIndex } from '@/utils/autoload'

const store = useStore()
const props = defineProps({
  listData: {
    type: Array,
    default() {
      return []
    },
  },
  immediate: {
    type: Boolean,
    default: false,
  },
  deep: {
    type: Boolean,
    default: false,
  },
})
const { listData } = toRefs(props)
const memberRef = ref(null)
// 定义data数据
const placeholderStyle = ref('color: #C0C4CC;font-size: 30rpx')
const styles = reactive({
  color: '#303133',
  disableColor: '#ffffff',
})

const onlyOne = ref(false)
const data = reactive({
  editorOption: {
    placeholder: appI18n.global.t('ui.oaFormIndexPleaseEnter'),
  },
  indexMember: -1,
  indexItemMember: -1,
  show: false,
  options: [],
  readOnly: false,
  photoUrl: 'http://test.com', // 服务器图片域名或者ip
  api: '/upload', // 上传图片接口地址
  txt: '',
  name: 'file',
  value: '<div>Hello World!</div>',
  flieList: [],
  indexItem: 0,
  index: 0,
  imgs: [],
  contents: '',
  imageList: [],
  selectedLabel: [],
  eid: '',
  labelName: [], // 客户标签
})
// 自定义表单
const formData = reactive({})
const labelShow = ref(false)
const isReadonlyField = (field) => {
  return !!(field?.readonly || field?.system_field || field?.disabled || ['contract_no', 'odds_no'].includes(field?.key))
}
const getFieldByKey = (key) => {
  return listData.value
    .map((item) => item.data || [])
    .flat()
    .find((item) => item.key === key)
}
const getSubmitData = () => {
  const submitData = { ...formData }
  delete submitData.contract_no
  delete submitData.odds_no
  return submitData
}
// 编辑获取标签
const getLabel = async (val) => {
  data.labelName = []
  const valueList = normalizeLabelValue(val.value)
  if (valueList.length == 0) return false
  data.selectedLabel = valueList
    .map((item) => Number(getOptionId(item) ?? item))
    .filter((item) => !Number.isNaN(item))
  const selectedIds = valueList.map((item) => String(getOptionId(item) ?? item))
  const selectedNames = valueList.map(getOptionName).filter(Boolean)

  const setLabelNameByOptions = (options = []) => {
    const names = []
    options.forEach((item) => {
      ;(item.children || []).forEach((el) => {
        if (selectedIds.includes(String(getOptionId(el)))) {
          names.push(el.name)
        }
      })
    })
    return names
  }

  data.labelName = setLabelNameByOptions(val.options)

  if (data.labelName.length == 0) {
    try {
      const res = await clientlabelApi()
      data.labelName = setLabelNameByOptions(res.data?.list)
    } catch (error) {
      console.log(error)
    }
  }

  if (data.labelName.length == 0) {
    data.labelName = selectedNames
  }
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
  return option
}
const normalizeCascaderValue = (value) => {
  if (Array.isArray(value)) return value
  if (typeof value === 'string' && value.includes(',')) {
    return value.split(',').filter(Boolean)
  }
  return value
}
const normalizeTreeOptions = (tree = []) => {
  tree.forEach((item) => {
    item.text = item.text ?? item.label ?? item.name
    item.value = item.value ?? item.id
    if (item.children?.length) {
      normalizeTreeOptions(item.children)
    }
  })
}
const getSelectText = (val) => {
  if (val.text) return val.text
  const text = findNamesByIds(val.options, formData[val.key])
  return text || ''
}
const initFormData = (formList = []) => {
  if (!formList.length) return

  formList.forEach((item) => {
    item.data.forEach((val) => {
      if (val.input_type === 'select' && val.options_level > 1 && val.type === 'single') {
        val.value = normalizeCascaderValue(val.value)
        normalizeTreeOptions(val.options)
      }
      formData[val.key] = val.value

      if (val.input_type === 'select' && val.key !== 'customer_label') {
        let text = findNamesByIds(val.options, val.value)
        val.text = text
      } else if (val.key == 'customer_label') {
        labelShow.value = true
        getLabel(val)
      }
      if (val.type == 'checked') {
        val.input_type = 'select'
        val.type = 'multiple'
        if (val.value && val.value.length > 0) {
          let text = findNamesByIds(val.options, val.value)
          val.text = text
        }
      }
      if (val.type == 'file' && val.files && val.files.length > 0) {
        val.options = val.files
        val.options.map((item) => {
          if (isTypeImage(item.name) && !data.imageList.includes(item.url)) {
            data.imageList.push(item.url)
          }
        })
      }
      if (val.input_type == 'member') {
        let ids = []
        if (val.value && val.value.length > 0) {
          val.value.map((item) => {
            ids.push(item.value || item.id)
          })
        }
        val.value = ids || []
        formData[val.key] = val.value
      }

      if (val.type == 'images' && val.files) {
        val.options = val.files
        val.options.map((item) => {
          if (isTypeImage(item.name) && !data.imageList.includes(item.url)) {
            data.imageList.push(item.url)
          }
        })
      }
      if (val.type == 'radio' && val.options.length) {
        const valType = typeof val.value
        if (valType === 'number' && typeof val.options[0].value == 'string') {
          val.options = val.options.map((item) => ({
            ...item,
            value: Number(item.value),
          }))
        }
      }
    })
  })
}

onMounted(() => {
  initFormData(listData.value)
})

// 监听数据变化
watch(
  () => [listData.value],
  (newvalue) => {
    initFormData(newvalue[0])
  },
  {
    immediate: props.immediate,
    deep: props.deep,
  },
)

const openSelect = (val, itemIndex, index) => {
  if (isReadonlyField(val)) return
  data.index = index
  data.indexItem = itemIndex
  data.show = true
  data.options = val.options
}

// 打开人员选择
const openMember = (val, indexItem, index) => {
  if (isReadonlyField(val)) return
  if (val.type === 'singleMember') {
    onlyOne.value = true
  } else {
    onlyOne.value = false
  }
  data.indexMember = indexItem
  data.indexItemMember = index
  memberRef.value.popupOpen(val.options)
}

const confirmMember = (e) => {
  const currentItem = listData.value[data.indexMember].data[data.indexItemMember]

  if (currentItem) {
    // 更新选项数据
    currentItem.options = e

    // 提取 ids
    const ids = e.map((item) => item.id)

    // 更新表单数据
    currentItem.value = ids
    formData[currentItem.key] = ids
    emit('editorChange', { [currentItem.key]: ids })
  }
}

// 级联选择
const cityChange = (e, index, index1) => {
  if (isReadonlyField(listData.value[index].data[index1])) return
  let len = e.detail.value
  let text = []
  let id = []
  len.map((item) => {
    text.push(item.text)
    id.push(item.value)
  })
  listData.value[index].data[index1].text = text.join('-')
  let key = listData.value[index].data[index1].key
  formData[key] = id
  emit('editorChange', { [key]: id })
}
const multiplePickerChange = (e) => {
  if (isReadonlyField(listData.value[data.indexItem].data[data.index])) return
  let text = []
  let key = null
  if (e.selected.length > 0) {
    e.selected.map((item) => {
      text.push(item.text)
    })
    listData.value[data.indexItem].data[data.index].text = text.join('-')
    key = listData.value[data.indexItem].data[data.index].key
    formData[key] = e.value
  }
  emit('editorChange', { [key]: e.value })

  ontouchcancel()
}
// 关闭弹窗
const ontouchcancel = () => {
  setTimeout(() => {
    data.show = false
  }, 200)
}
// 下拉选择
const contractChange = (e, index, index1) => {
  if (isReadonlyField(listData.value[index].data[index1])) return
  // debugger;
  let len = e.detail.value || 0
  let text = listData.value[index].data[index1].options[len].text
  let id = listData.value[index].data[index1].options[len].value
  let key = listData.value[index].data[index1].key
  formData[key] = id
  listData.value[index].data[index1].text = text
  emit('editorChange', { [key]: id })
}

// 日期选择
const startDateChange = (e, index, index1) => {
  if (isReadonlyField(listData.value[index].data[index1])) return
  let key = listData.value[index].data[index1].key
  formData[key] = e.detail.value

  emit('editorChange', { [key]: e.detail.value })
}

// 图片与文档预览
const preview = (item) => {
  lookPreview(item.url, item.name, data.imageList)
}
let emit = defineEmits(['submitOk', 'change', 'editorChange'])
// 提交表单自定义事件
const submit = () => {
  if (handleConfirm()) {
    emit('submitOk', getSubmitData())
  }
}
// 上传图片
import { uploadImage } from '@/utils/file'
const uploadAvatar = (index, index1) => {
  if (isReadonlyField(listData.value[index].data[index1])) return
  uploadImage(
    'attach/imgs',
    {
      relation_type: 'client',
    },
    fileSizeOne,
  )
    .then((res) => {
      message.success(res.message)
      let newData = {
        url: res.data.src,
        id: res.data.id,
        size: res.data.size,
        name: res.data.name,
      }
      let ids = []
      listData.value[index].data[index1].options.push(newData)
      listData.value[index].data[index1].options.map((item) => {
        ids.push(item.id)
        if (isTypeImage(item.name)) {
          data.imageList.push(item.url)
        }
      })
      let key = listData.value[index].data[index1].key
      formData[key] = ids
    })
    .catch((error) => {
      message.error(error)
    })
}

// 上传附件
import { uploadFlie } from '@/utils/file'
const uploadFlieFn = (index, index1) => {
  if (isReadonlyField(listData.value[index].data[index1])) return
  const datas = {
    relation_id: 1,
    relation_type: 'client',
  }

  uploadFlie('attach/imgs', datas, fileSizeOne)
    .then((res) => {
      if (res.status == 200) {
        message.success(res.message)
        let newData = {
          url: res.data.src,
          id: res.data.id,
          size: res.data.size,
          name: res.data.name,
        }
        let ids = []
        listData.value[index].data[index1].options.push(newData)
        listData.value[index].data[index1].options.map((item) => {
          ids.push(item.id)
          if (isTypeImage(item.name)) {
            data.imageList.push(item.url)
          }
        })
        let key = listData.value[index].data[index1].key
        formData[key] = ids
      } else {
        message.error(res.message)
      }
    })
    .catch((error) => {
      message.error(error)
    })
}

// 富文本内容
const saveContent = (e, key) => {
  if (isReadonlyField(getFieldByKey(key))) return
  formData[key] = e
}

// 图片删除
const deleteImg = (val, id) => {
  if (isReadonlyField(val)) return
  formData[val.key] = formData[val.key].filter(function (item) {
    return item !== id
  })
  val.options = val.options.filter(function (item) {
    return item.id !== id
  })
}
// 附件删除
const deleteFile = (val, id) => {
  if (isReadonlyField(val)) return
  formData[val.key] = formData[val.key].filter(function (item) {
    return item !== id
  })
  val.options = val.options.filter(function (item) {
    return item.id !== id
  })
}

const successPopupRef = ref(null)
const form = ref(null)
// 提交表单
const handleConfirm = () => {
  let is_passed = true
  const allData = listData.value.map((item) => item.data).flat()

  for (const val of allData) {
    if (val.type == 'oaWangeditor') {
      // 富文本内容已经通过saveContent方法保存到formData中，不需要再次设置
    }

    if (val.required == 1) {
      if (!formData[val.key]) {
        message.error(`${val.key_name}不能为空`)
        is_passed = false
        break
      } else {
        is_passed = true
      }
    }
  }

  return is_passed
}

// 获取级联数据
function findNamesByIds(tree, id) {
  if (!id) {
    return false
  }
  if (id && id.constructor !== Array) {
    id = [id]
  }
  let result = []

  function traverse(node) {
    if (id.includes(node.value + '') || id.includes(node.value)) {
      result.push(node.text ?? node.label ?? node.name)
    }
    if (node.children) {
      for (const child of node.children) {
        traverse(child)
      }
    }
  }

  if (tree) {
    for (const node of tree) {
      if (node) {
        traverse(node)
      }
    }
  }

  let str = ''
  if (result.length > 1) {
    str = result.join(' - ')
  } else {
    str = result[0]
  }
  return str
}
// 标签选择
const selectedLabelRef = ref(null)
const changeLabel = () => {
  if (isReadonlyField(getFieldByKey('customer_label'))) return
  selectedLabelRef.value.popupOpen(data.selectedLabel, data.labelName)
}
const resetLabel = () => {
  data.selectedLabel = []
  data.labelName = []
}
// 标签选择回调
const changeItem = (value, labelName) => {
  const key = getKey('customer_label', 'key')
  formData[key] = value
  data.labelName = labelName
  data.selectedLabel = value
  emit('editorChange', { [key]: value })
}
const getKey = (row, key) => {
  let formKey = ''
  listData.value.map((item) => {
    item.data.map((val) => {
      if (key) {
        if (val.key === row) {
          formKey = val.key
        }
      }
      if (val.type === row) {
        formKey = val.key
      }
    })
  })
  return formKey
}

const startDate = computed(() => {
  return getDate('start')
})

const endDate = computed(() => {
  return getDate('end')
})
// 日期选择器
const getDate = (type) => {
  const date = new Date()
  let year = date.getFullYear()
  let month = date.getMonth() + 1
  let day = date.getDate()
  if (type === 'start') {
    year = year - 60
  } else if (type === 'end') {
    year = year + 20
  }
  month = month > 9 ? month : '0' + month
  day = day > 9 ? day : '0' + day
  return `${year}-${month}-${day}`
}
const successChange = () => {}
defineExpose({
  submit,
  formData,
})

// 文本输入仅允许中文、字母、数字、空格及 @，其余特殊字符过滤
const TEXT_INPUT_ALLOWED_REG = /[^\u4e00-\u9fa5a-zA-Z0-9@\s]/g

const filterTextSpecialChars = (value) => {
  if (value === undefined || value === null) return ''
  return String(value).replace(TEXT_INPUT_ALLOWED_REG, '')
}

const getInputValue = (event) => {
  if (typeof event === 'string' || typeof event === 'number') return String(event)
  if (event?.detail?.value !== undefined) return String(event.detail.value)
  return ''
}

const handleTextInput = (val, event) => {
  if (isReadonlyField(val)) return
  const filtered = filterTextSpecialChars(getInputValue(event))
  formData[val.key] = filtered
  // emit('change', {
  //   key: val.key,
  //   value: filtered,
  // })
}

const handleChange = (val, event) => {
  if (isReadonlyField(val)) return
  if (val.fixDecimal) {
    event = Math.floor(Number(event) * 100) / 100
  }
  if (val.validate) {
    const targetValue = val.validate(event)
    formData[val.key] = targetValue.toString()
  }
  emit('change', {
    key: val.key,
    value: formData[val.key],
  })
}
</script>
<style lang="scss" scoped>
.height-350 {
  height: 600rpx;
}

::v-deep .editor-content {
  padding-left: 0 !important;
}

.content {
  width: 100%;
  position: relative;

  .label {
    padding-right: 24rpx;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16rpx;

    .icon-biaodan-tianjia {
      color: #c0c4cc !important;
      font-size: 34rpx;
    }
  }

  .tips {
    font-size: 24rpx;
    font-family:
      PingFang SC-常规体,
      PingFang SC;
    font-weight: 400;
    color: #999999;
    margin-bottom: 20rpx;
  }

  .uni-forms-item__label {
    height: auto;
    padding: 0;
    font-size: 30rpx;
    color: $uni-text-color;
    line-height: 1;
    font-family:
      PingFang SC,
      PingFang SC;

    .iconfont {
      margin-top: 8rpx;
      margin-left: 5rpx;
      color: #ff2529;
    }
  }

  .list-item {
    background-color: #fff;
    width: 100%;
    padding-left: 24rpx;
    margin-top: 16rpx;
  }

  .mt36 {
    margin-top: 36rpx;
  }

  .mt20 {
    margin-top: 20rpx;
  }

  .uni-easyinput__content-textarea {
    min-height: 252rpx;
  }

  ::v-deep.uni-data-checklist .checklist-group {
    display: flex;
    justify-content: flex-end;

    .checklist-box {
      margin-left: 20px;
      margin-right: 0;
    }
  }

  ::v-deep .form-card {
    margin: 0;
    padding-left: 0;
  }

  .picker-input {
    text-align: right;
    color: $uni-text-color;
    font-size: 30rpx;
    align-items: center;
    display: flex;
    justify-content: flex-end;
    min-width: 0;
    line-height: 1.2;

    .picker-input-text {
      min-width: 0;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      line-height: 1.2;
    }

    .iconfont {
      padding-right: 16rpx;
      flex: none;
      display: flex;
      align-items: center;
      justify-content: center;
      transform: rotate(180deg);
      font-size: 24rpx;
      line-height: 1;
      color: #c0c4cc;
    }
  }

  .picker-input-placeholder {
    color: #c0c4cc;
    font-size: 30rpx;
  }

  .input-label {
    padding: 18rpx 24rpx 18rpx 0;
    align-items: center;
    min-height: 108rpx;

    ::v-deep .uni-easyinput__content-input {
      text-align: right;
      padding-right: 0 !important;
    }

    ::v-deep .uni-forms-item__label {
      display: flex;
      line-height: 1.2;

      .iconfont {
        width: 16rpx;
      }
    }
  }

  .label-flex {
    padding-left: 50rpx;
    display: flex;
    flex-wrap: nowrap;
    overflow: hidden;
    align-items: center;

    .label-box {
      flex-shrink: 0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      // padding: 0 10rpx;
      // height: 50rpx;
      // line-height: 56rpx;
      font-size: 26rpx;
      text-align: center;

      // background-color: #f4f4f5;
      // border-color: #e9e9eb;
      // color: #909399;
      // border-radius: 8rpx;
      // margin-left: 10rpx;
    }

    .more-text {
      flex-shrink: 0;
      margin-left: 10rpx;
      color: #909399;
      font-size: 26rpx;
    }
  }
}

.mb10 {
  margin-bottom: 20rpx;
}

::v-deep .uni-forms-item {
  margin-bottom: 0;
  border-bottom: 1px solid #ebeef5;
}

.line-box:last-child {
  ::v-deep .uni-forms-item {
    border-bottom: none;
  }
}

::v-deep .uni-input-input {
  font-size: 30rpx !important;
}

::v-deep .uni-textarea-textarea {
  font-size: 30rpx !important;
}

// 上传附件
.flie {
  padding: 24rpx 24rpx 24rpx 0;

  .box {
    width: 100%;
    height: 40px;
    background: #f6f7f9;
    border-radius: 4px 4px 4px 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12rpx;
    padding-right: 20rpx;

    .icon-guanbi-yangshiyi1 {
      color: #999999;
    }

    .left {
      width: 100%;
      display: flex;
      align-items: center;
      font-family:
        PingFang SC,
        PingFang SC;
      font-weight: 400;

      .slot-image {
        display: flex;
        flex-shrink: 0; // flex布局下图片挤压变形
        width: 52rpx;
        height: 52rpx;
        align-items: center;
        margin-right: 10rpx;
      }

      .name {
        width: calc(100% - 40px);
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 24rpx;
        color: #303133;
      }

      .size {
        font-size: 20rpx;
        color: #909399;
        margin-top: 2rpx;
      }
    }
  }
}

// 上传图片
.upload {
  width: 100%;
  min-height: 236rpx;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  padding: 28rpx 28rpx 0 0;

  .upload-box {
    width: 140rpx;
    height: 140rpx;
    border-radius: 8rpx 8rpx 8rpx 8rpx;
    border: 2rpx solid #dddddd;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-bottom: 20rpx;

    .icon-paizhao {
      font-size: 40rpx;
      color: #bfbfbf;
    }

    .text {
      margin-top: 20rpx;
      font-size: 24rpx;
      font-family:
        PingFang SC-常规体,
        PingFang SC;
      font-weight: 400;
      color: #999999;
    }
  }

  .box {
    position: relative;

    .img {
      display: block;
      width: 140rpx;
      height: 140rpx;
      margin-right: 24rpx;
      margin-bottom: 24rpx;
    }

    .delete {
      position: absolute;
      top: 0;
      right: 20rpx;
      width: 32rpx;
      height: 32rpx;
      background: rgba(0, 0, 0, 0.6);
      border-radius: 0 8rpx 0 16rpx;
      display: flex;
      align-items: center;
      justify-content: center;

      .icon-paizhao {
        font-size: 35rpx;
        color: #bfbfbf;
      }
    }
  }
}

::v-deep .picker-input .iconfont {
  padding-right: 4px !important;
}
</style>

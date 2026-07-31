<template>
  <view>
    <view class="search-box">
      <!-- 输入框搜索 -->
      <picker
        class="picker-selector"
        mode="selector"
        @change="(e) => changeSearchFiled(e)"
        :value="data.typeIndex"
        :range="data.textlist"
        range-key="name"
      >
        <view class="label">{{ data.typeText }} <text class="iconfont icon-jinru"></text></view>
      </picker>
      <uni-search-bar
        style="flex: 1"
        :placeholder="data.typeText"
        :focus="false"
        @clear="search"
        @blur="search"
        @change="search"
        bgColor="#F5F5F5"
        v-model="data.searchName"
      >
        <template v-slot:searchIcon>
          <text class="iconfont icon-sousuo1"></text>
        </template>
      </uni-search-bar>
    </view>

    <!-- 动态筛选 -->
    <view class="cr-search-content">
      <!-- 全部 -->
      <view v-if="typeData.length > 0" class="flex-item">
        <picker
          class="picker-selector"
          mode="selector"
          @change="(e) => changeSearch(e)"
          :value="getViewSearchIndex()"
          :range="typeData"
          range-key="name"
        >
          <view class="search-default-label line1">{{ data.searchText }} <text class="iconfont icon-jinru"></text> </view>
        </picker>
      </view>

      <template v-for="(item, index) in data.searchlist">
        <view :key="index" class="flex-item" v-if="item">
          <!-- 人员选择  负责人 -->
          <template v-if="item.input_type == 'personnel'">
            <picker
              class="picker-selector"
              mode="selector"
              @change="(e) => changeUsers(e, index)"
              :value="data.usersIndex"
              :range="data.usersData"
              range-key="name"
            >
              <view class="search-default-label">{{ item.name }} <text class="iconfont icon-jinru"></text></view>
            </picker>
          </template>

          <!-- 标签 -->
          <view class="picker-selector" v-if="item.field == 'customer_label'">
            <view class="search-default-label" @click="changeLabel(item, index)">
              {{ item.name }}
              <text class="date-open-icon iconfont icon-jinru" v-if="!formData[item.field]"></text>
              <text class="date-open-icon iconfont icon-guanbi" v-else @click.stop="clearFn(item)"></text>
            </view>
          </view>

          <!-- 下拉选择 -->
          <picker
            class="picker-selector"
            v-if="(item.input_type == 'radio' || item.input_type == 'select') && item.field != 'customer_label'"
            mode="selector"
            @change="(e) => bindPickerChange(e, index)"
            :value="data.typeIndex.toString()"
            :range="item.dict"
            range-key="label"
          >
            <view class="search-default-label"
              >{{ item.name }}
              <text class="date-open-icon iconfont icon-jinru" v-if="!formData[item.field]"></text>
              <text class="date-open-icon iconfont icon-guanbi" v-else @click.stop="clearFn(item)"></text>
            </view>
          </picker>

          <!-- 日期筛选 -->
          <view class="picker-selector" v-if="item.input_type == 'date'">
            <view class="search-default-label" @click="openTime(item, index)">
              {{ item.name }}
              <text class="date-open-icon iconfont icon-jinru" v-if="!formData[item.field]"></text>
              <text class="date-open-icon iconfont icon-guanbi" v-else @click.stop="clearFn(item)"></text>
            </view>
          </view>

          <!-- 省市区 -->
          <view class="picker-selector line1" v-if="item.input_type == 'area_cascade'">
            <uni-data-picker
              class="search-default-label"
              v-model="formData.area_cascade"
              :localdata="data.addressData"
              :map="{ text: 'name', value: 'value' }"
              @change="cityChange($event, item, index)"
            >
              {{ item.name }}
              <text class="date-open-icon iconfont icon-jinru" v-if="!formData[item.field]"></text>
              <text class="date-open-icon iconfont icon-guanbi" v-else @click.stop="clearFn(item)"></text>
            </uni-data-picker>
          </view>

          <!-- 特殊的部门筛选 -->
          <view class="picker-selector" v-if="item.input_type == 'scope_frame'">
            <view class="search-default-label" @click="openCascade(1, item, index)">
              {{ item.name }}
              <text class="date-open-icon iconfont icon-jinru"></text>
            </view>
          </view>
        </view>
      </template>
    </view>
  </view>
  <selected-label title="客户标签" ref="selectedLabelRef" @changeItem="changeItem"> </selected-label>
  <timePopup ref="timePopupRef" @change="changeTime"></timePopup>
  <!-- 级联选择 -->
  <departmentPopup ref="departmentRef" @change="changeCascade" :managementScope="true"> </departmentPopup>
</template>

<script setup>
import { ref, reactive, onMounted, toRefs } from 'vue'
import selectedLabel from './selectedLabel.vue'
import timePopup from '@/components/timePopup/index.vue'
import departmentPopup from '@/components/departmentPopup/index.vue'
import message from '@/utils/message'
import { salesmanCustomApi } from '@/api/customer'
import { getDictTreeListApi } from '@/api/crud'
const props = defineProps({
  type: {
    type: String,
    default: 'center',
  },
  keyWord: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: '客户名称',
  },
  typeData: {
    // 系统筛选数据
    type: Array,
    default: () => [],
  },
})
const { keyWord, placeholder, typeData } = toRefs(props)
const data = reactive({
  typeText: placeholder.value,
  timeText: '创建日期',
  usersText: '负责人',
  labelText: '标签筛选',
  areaText: '省市区',
  searchText: '我负责的',
  searchName: '',
  fieldName: '',
  typeIndex: 0,
  usersIndex: 0,
  rowIndex: 0,
  usersData: [],
  textlist: [],
  searchlist: [],
  addressData: '',
  selectedLabels: [],
})
let formData = reactive({
  view_search: 1,
})

watch(
  () => keyWord.value,
  (newVal, oldVal) => {
    if (newVal != oldVal) {
      getSalesman()
    }
  },
)

const timeRef = ref(null)
const timePopupRef = ref(null)
let emit = defineEmits(['change'])

onMounted(() => {
  getPersonnel()
  getSalesman()
  getDict()
})

import { enterpriseSalesmanApi } from '@/api/public'
const getSalesman = async () => {
  data.searchlist = []
  data.textlist = []
  try {
    const res = await salesmanCustomApi(keyWord.value)
    // 过滤出 search_select 包含的字段
    const list = res.data.search.filter((item) => res.data.search_select.includes(item.field))
    // 按 input_type 分组
    list.forEach((item) => {
      if (item.field == 'area_cascade') {
        item.input_type = 'area_cascade'
      }
      if (item.input_type == 'checked') {
        item.input_type = 'select'
      }
      if (item.input_type === 'input') {
        data.textlist.push({ name: item.name, id: item.field })
        if (data.textlist && data.textlist.length > 0) {
          data.typeText = data.textlist[0].name
          data.fieldName = data.textlist[0].id
          const fieldName = data.textlist[0].id
          formData[fieldName] = data.searchName
        }
      } else {
        if (item.dict && item.dict.length > 0) {
          item.dict.forEach((item) => {
            item.id = item.value
          })
        }

        data.searchlist.push(item)
      }
    })
    if (typeData.value.length > 0) {
      data.searchlist.length = 3
    } else {
      data.searchlist.length = 4
    }

    data.searchlist.forEach((itm) => {
      itm.title = `${itm.name}`
    })
  } catch (error) {
    message.error(error.message)
  }
}

const getPersonnel = async () => {
  enterpriseSalesmanApi()
    .then((res) => {
      const datas = res.data ? res.data : []
      datas.unshift({ name: '所有人员', id: '' })
      data.usersData = datas
    })
    .catch((error) => {
      message.error(error.message)
    })
}

const selectedLabelRef = ref(null)
// 标签选择
const changeLabel = (item, index) => {
  data.rowIndex = index
  selectedLabelRef.value.popupOpen(data.selectedLabels)
}

const clearFn = (item) => {
  formData[item.field] = ''
  item.name = item.title
  emit('change', formData)
}

// 标签选择回调
const changeItem = (e, name) => {
  const fieldName = data.searchlist[data.rowIndex].field
  formData[fieldName] = e
  data.selectedLabels = e
  if (e.length > 0) {
    data.labelText = name[0]
  } else {
    data.labelText = ''
  }
  emit('change', formData)
}

const getDict = () => {
  let obj = {
    type_id: 2,
  }
  getDictTreeListApi(obj).then((res) => {
    data.addressData = res.data
  })
}
// 省市区选择
const cityChange = (e, item, index) => {
  let len = e.detail.value
  let text = []
  let id = []
  len.map((item) => {
    text.push(item.text)
    id.push(item.value)
  })
  let field = item.field
  formData[field] = id
  if (text.length > 0) {
    item.name = text[0]
  }
  emit('change', formData)
}

const bindPickerChange = (e, index) => {
  const selectedIndex = e.detail.value
  const selectedValue = data.searchlist[index].dict[selectedIndex].name
  // 更新选中状态
  data.searchlist[index].selectedIndex = selectedIndex
  data.searchlist[index].selectedValue = selectedValue
  // 如果需要直接修改 item.name（注意这会覆盖原始名称）
  data.searchlist[index].name = selectedValue
  const fieldName = data.searchlist[index].field
  formData[fieldName] = data.searchlist[index].dict[selectedIndex].value
  emit('change', formData)
}

// 类型选择
const changeUsers = (e, index) => {
  const selectedIndex = e.detail.value
  const selectedValue = data.usersData[selectedIndex].name
  // 更新选中状态
  data.searchlist[index].selectedIndex = selectedIndex
  data.searchlist[index].selectedValue = selectedValue
  // 如果需要直接修改 item.name（注意这会覆盖原始名称）
  data.searchlist[index].name = selectedValue
  const fieldName = data.searchlist[index].field
  formData[fieldName] = data.usersData[selectedIndex].id
  emit('change', formData)
}
// 输入框左侧搜索条件切换
const changeSearchFiled = (e) => {
  const index = e.detail.value
  data.fieldName = data.textlist[index].id
  data.typeText = data.textlist[index].name
  const fieldName = data.textlist[index].id
  formData = deleteKeysMatchingIds(formData, data.textlist)
  formData[fieldName] = data.searchName
  emit('change', formData)
}
/**
 * 删除第一个对象中属性名等于第二个数组任意元素id的键值对
 * @param {Object} sourceObj 要处理的对象
 * @param {Array} idArray 包含id字段的对象数组
 * @param {string} idKey 数组中表示id的字段名，默认 'id'
 * @returns {Object} 处理后的新对象
 */
const deleteKeysMatchingIds = (sourceObj, idArray, idKey = 'id') => {
  const result = { ...sourceObj }
  // 收集所有需要删除的属性名（id值）
  const idsToDelete = idArray.map((item) => item[idKey])
  // 删除匹配的属性
  idsToDelete.forEach((id) => {
    const key = String(id) // 统一转为字符串比较
    if (result.hasOwnProperty(key)) {
      delete result[key]
    }
  })
  return result
}
// 搜索框搜索
const search = () => {
  const fieldName = data.fieldName
  formData[fieldName] = data.searchName
  emit('change', formData)
}
// 搜索切换
const getViewSearchIndex = () => {
  if (!typeData.value?.length) return 0
  const index = typeData.value.findIndex((item) => item.id == formData.view_search)
  return index >= 0 ? index : 0
}

const changeSearch = (e) => {
  const index = e.detail.value
  // 1. 更新 view_search 的值
  formData.view_search = typeData.value[index].id
  data.searchText = typeData.value[index].name
  emit('change', formData)
}
// 清除日期
const clickClear = () => {
  timeRef.value.clear()
}
// 打开时间选择器
const openTime = (item, index) => {
  data.rowIndex = index
  timePopupRef.value.popupOpen()
}
// 选择时间
const changeTime = (value) => {
  const fieldName = data.searchlist[data.rowIndex].field
  if (value.timeText) {
    data.searchlist[data.rowIndex].name = value.timeText
    formData[fieldName] = value.time
  } else {
    formData[fieldName] = ''
    data.searchlist[data.rowIndex].name = data.searchlist[data.rowIndex].title
  }
  emit('change', formData)
}
const departmentRef = ref(null)
// 部门选择
const openCascade = (val, item, index) => {
  data.rowIndex = index
  departmentRef.value.popupOpen(val, true)
}

const changeCascade = (obj) => {
  const fieldName = data.searchlist[data.rowIndex].field
  const member = Object.values(obj.member)
  if (obj.btnText && Number.isFinite(obj.type)) {
    data.searchlist[data.rowIndex].name = obj.btnText
    formData[fieldName] = obj.field || member.toString()
  } else {
    formData[fieldName] = ''
    data.searchlist[data.rowIndex].name = data.searchlist[data.rowIndex].title
  }
  emit('change', formData)
}
// 暴露方法给父组件调用
defineExpose({ getSalesman })
</script>

<style scoped lang="scss">
.search-default-label {
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 24rpx;
  color: #606266;
}

.cr-search-content {
  display: flex;
  .flex-item {
    width: 100%;
  }
}

::v-deep .uni-searchbar__cancel {
  display: none;
}
::v-deep .uni-searchbar__text-placeholder {
  font-size: 24rpx;
  color: #909399;
}
::v-deep .uni-input-input {
  font-size: 24rpx;
  color: #303133;
}
::v-deep .uni-input-placeholder,
.uni-input-input {
  font-size: 24rpx !important;
  color: #909399;
}
::v-deep .dialog-title {
  font-weight: 500;
}

.search-box {
  font-family:
    PingFang SC,
    PingFang SC;
  margin: 6rpx 30rpx;

  display: flex;
  align-items: center;
  height: 64rpx;
  background: #f5f5f5;
  border-radius: 12rpx;
  font-size: 24rpx;
  padding-left: 20rpx;

  ::v-deep .uni-searchbar__box-icon-search {
    padding: 0;
  }
  .icon-sousuo1 {
    font-size: 28rpx;
  }

  ::v-deep .uni-searchbar__box {
    height: 2rem;
    padding-right: 0 !important;
  }

  .label {
    font-weight: 400;
    font-size: 24rpx;
    color: #303133;
    padding-right: 20rpx;
    border-right: 2rpx solid #dddddd;
  }

  .icon-jinru {
    font-size: 22rpx !important;
    margin-left: 4rpx;
  }
}
::v-deep .uni-searchbar__box-icon-clear {
  margin-top: 2px;
}
</style>

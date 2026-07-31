<template>
  <view>
    <view v-for="(item, index) in tableData" :key="index">
      <view class="title">
        <view
          >明细 <text v-if="tableData.length > 1">{{ index + 1 }}</text></view
        >
        <view class="iconfont icon-shanchu2" v-if="tableData.length > 1" @click="delTable(index)"></view>
      </view>
      <list ref="oaFormRef" :listData="listDataArr[index]" :info="item" :formInfo="formInfo"> </list>
      <view class="addText" @click="addFn">+ 添加明细</view>
    </view>
  </view>
</template>

<script setup>
import list from './index'
import { ref, onMounted } from 'vue'
const props = defineProps({
  listData: {
    type: Array,
    default: () => {
      return []
    },
  },
  addressData: {
    type: Array,
    default() {
      return []
    },
  },
  valueData: {
    type: Array,
    default() {
      return [{}]
    },
  },
  info: {
    type: Object,
    default() {
      return {}
    },
  },
  keyName: {
    type: String,
    default() {
      return ''
    },
  },
  formInfo: {
    type: Object,
    default() {
      return {}
    },
  },
})
const oaFormRef = ref([])
const { listData, formInfo, valueData } = toRefs(props)
let tableData = ref([{}])
let listDataArr = ref([])
const addFn = () => {
  tableData.value.push({})
  // 为新增的明细克隆一份 listData，避免共享引用导致联动
  const cloned = JSON.parse(JSON.stringify(listData.value))
  listDataArr.value.push(cloned)
}
const delTable = (index) => {
  tableData.value.splice(index, 1)
  listDataArr.value.splice(index, 1)
}

onMounted(() => {
  setTimeout(() => {
    tableData.value.length = 0
    let data = JSON.parse(JSON.stringify(valueData.value))
    tableData.value.push(...data)
    // 根据明细数量为每条明细克隆一份 listData，避免多条明细共享配置状态
    listDataArr.value = tableData.value.map(() => JSON.parse(JSON.stringify(listData.value)))
  }, 500)
})

const getData = () => {
  let allPass = true
  oaFormRef.value.forEach((childRef, index) => {
    if (childRef && typeof childRef.validateForm === 'function') {
      const isTrue = childRef.validateForm('detailed')
      tableData.value[index] = childRef.formData
      if (!isTrue) {
        allPass = false
      }
    } else {
      allPass = false
    }
  })
  return allPass
}
defineExpose({
  getData,
  tableData,
})
</script>

<style scoped lang="scss">
.title {
  width: 100%;
  border-top: 1px solid #eeeeee;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 13px;
  color: #999999;
  padding-right: 14px;
  padding-top: 15px;
  margin-right: 14px;
}

::v-deep .list-item {
  padding: 0 !important;
}

.addText {
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 15px;
  color: #1890ff;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  margin-bottom: 30rpx;
}
</style>

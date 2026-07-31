<template>
  <BaseContainer class="base-container">
    <view class="head-wrap">
      <NavBar :is-right="true" />
    </view>
    <view class="form-card">
      <oaForm :listData="formConfig" ref="formRef" deep immediate @change="handleChange" />
    </view>
    <view class="remark-card">
      <view class="remark-title"> 备注 </view>
      <textarea placeholder="填写备注信息" v-model="remark" class="remark-textarea" />
    </view>
    <BaseBottomBtn text="提交" @click="handleSubmit" />
  </BaseContainer>
</template>

<script setup lang="ts">
import BaseContainer from '@/components/BaseContainer/index.vue'
import NavBar from '@/components/defaultNavBar/index.vue'
import BaseBottomBtn from '@/components/BaseBottomBtn/index.vue'
import oaForm from '@/components/oaForm/index.vue'

interface Props {
  count: string
  discount: string
  price: string
  total_price: string
  remark: string
  discount_price: string
  unique: string
  event_name: string
}

const props = defineProps<Props>()

const realPrice = computed(() => {
  const price = Number(props.price)
  if (Number.isNaN(price) || price < 0) return 0

  return price
})

const formRef = ref<InstanceType<typeof oaForm>>()
const remark = ref(props.remark)

const generateValidate = (min: number = 0, max: number = Number.MAX_SAFE_INTEGER, requireInt: boolean = false) => {
  return (v: string) => {
    const num = Number(v)
    if (Number.isNaN(num)) return min
    if (num < min) return min
    if (num > max) return max
    if (requireInt && !Number.isInteger(num)) return Math.floor(num)
    return Number(num.toFixed(2))
  }
}

const formConfig = ref([
  {
    data: [
      {
        input_type: 'input',
        type: 'number',
        key: 'count',
        key_name: '成交数量',
        required: 1,
        validate: generateValidate(1, Number.MAX_SAFE_INTEGER, true),
        value: props.count,
      },
      {
        input_type: 'input',
        type: 'number',
        key: 'discount',
        key_name: '折扣(%)',
        required: 1,
        validate: generateValidate(0),
        value: props.discount,
        fixDecimal: true,
      },
      {
        input_type: 'input',
        type: 'number',
        key: 'price',
        key_name: '成交单价',
        required: 1,
        validate: generateValidate(),
        value: props.price,
        fixDecimal: true, // 修正为两位小数
      },
      {
        input_type: 'input',
        type: 'number',
        key: 'total_price',
        key_name: '成交总价',
        required: 1,
        validate: generateValidate(),
        value: props.total_price,
        fixDecimal: true, // 修正为两位小数
      },
    ],
  },
])

const handleSubmit = () => {
  const formData = formConfig.value[0].data.reduce(
    (acc, item) => {
      acc[item.key] = item.value
      return acc
    },
    {
      remark: remark.value,
      unique: props.unique,
    } as Record<string, string>,
  )
  // console.log(props.event_name, formData, 999999)
  // return
  uni.$emit(props.event_name, formData)
  uni.navigateBack()
}

const findItemByKey = (key: string) => {
  return formConfig.value[0].data.find((item: any) => item.key === key)
}

const handleChange = (val: { key: string; value: string }) => {
  const { key, value } = val
  const config = findItemByKey(key)
  if (!config) return
  config.value = value
  if (key === 'count') {
    // 成交数量变化，成交总价自动计算
    const discountPriceItem = findItemByKey('price')
    const totalPriceItem = findItemByKey('total_price')
    totalPriceItem.value = (Number(discountPriceItem.value) * Number(value)).toFixed(2)
  } else if (key === 'discount') {
    // 折扣变化，成交单价和总价自动计算
    const discountPriceItem = findItemByKey('price')
    const totalPriceItem = findItemByKey('total_price')
    const countItem = findItemByKey('count')
    const discountValue = Number(value) / 100
    discountPriceItem.value = (realPrice.value * discountValue).toFixed(2)
    totalPriceItem.value = (Number(discountPriceItem.value) * Number(countItem.value)).toFixed(2)
  } else if (key === 'price') {
    // 成交单价变化，折扣比例和成交总价自动计算
    const totalPriceItem = findItemByKey('total_price')
    const countItem = findItemByKey('count')
    const discountItem = findItemByKey('discount')
    if (realPrice.value === 0) {
      discountItem.value = '0'
    } else {
      discountItem.value = ((Number(value) / realPrice.value) * 100).toFixed(2)
    }
    totalPriceItem.value = ((Number(value) || 1) * Number(countItem.value)).toFixed(2)
  } else if (key === 'total_price') {
    // 成交总价变化，成交单价和折扣比例自动计算
    const discountPriceItem = findItemByKey('price')
    const discountItem = findItemByKey('discount')
    const countItem = findItemByKey('count')

    const discountPrice = Number(value) / Number(countItem.value)
    discountPriceItem.value = discountPrice.toFixed(2)
    if (realPrice.value === 0) {
      discountItem.value = '0'
    } else {
      discountItem.value = ((discountPrice / realPrice.value) * 100).toFixed(2)
    }
  }
}
</script>

<style scoped lang="scss">
.head-wrap {
  padding-top: var(--status-bar-height);
  background-color: #fff;
  position: sticky;
  top: 0;
  z-index: 1;
}

.form-card {
  background-color: #fff;
}

.remark-title {
  font-size: 30rpx;
  color: #303133;
  line-height: 30rpx;
}

.remark-card {
  margin: 20rpx;
  background-color: #fff;
  border-radius: 12rpx;
  padding: 36rpx 24rpx;
}

.remark-textarea {
  width: 100%;
  margin-top: 24rpx;
  height: 120rpx;
}
</style>

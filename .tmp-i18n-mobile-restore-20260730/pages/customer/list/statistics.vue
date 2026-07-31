<template>
  <BaseContainer>
    <view class="nav">
      <view class="cr-position-header">
        <default-nav-bar :backgroundColor="data.backgroundColor" :color="`#fff`"> </default-nav-bar>
      </view>
      <!-- 筛选条件 -->
      <view class="s-search">
        <view class="search">
          <view class="s-title" @click="departmentChange(1)">
            <text class="iconfont icon-richengshezhi1"></text>
            <view class="over-title"> {{ data.btnText }}</view>
            <text class="iconfont icon-zhankai1"></text>
          </view>
          <view class="line" />
          <view class="s-title" @click="departmentChange(2)">
            <text class="iconfont icon-richengshezhi1"></text>
            <view class="over-title">
              {{ data.timeText }}
            </view>
            <text class="iconfont icon-zhankai1"></text>
          </view>
        </view>
        <view class="mb130">
          <!-- 业绩简报 -->
          <view class="per">
            <view class="per-title">
              业绩简报
              <view class="line" />
            </view>
            <view class="per-content">
              <view class="per-flex" v-for="(item, index) in data.list" :key="index">
                <text class="iconfont" :class="item.icon ? item.icon : ''"></text>
                <view class="flex">
                  <text class="num">{{ item.num }}</text>
                  <text class="text">{{ item.title }}</text>
                </view>
              </view>
            </view>
          </view>
          <!-- 产品分类统计 -->
          <view class="per">
            <view class="per-title">
              <text> 产品分类统计</text>
              <view class="line" />
            </view>
            <view class="el-breadcrumb">
              <uni-breadcrumb-item v-for="(route, index) in data.breadcrumbList2" :key="index">
                <text class="name" :class="{ breadcrumb: data.active2 == route.name }" @click="changeActive2(route, index)">
                  {{ route.name }} {{ route.icon }}
                </text>
              </uni-breadcrumb-item>
            </view>
            <view class="charts-box" v-if="data.contractList2.length > 0">
              <qiun-data-charts type="ring" :opts="data.opts2" :chartData="chartData2" @getIndex="tap2" />
            </view>
            <view v-else class="no-data">暂无数据</view>
          </view>
          <!-- 订单分类统计 -->
          <view v-if="data.contractCategoryEnabled" class="per">
            <view class="per-title">
              <text> 订单分类统计</text>
              <view class="line" />
            </view>
            <view class="el-breadcrumb">
              <uni-breadcrumb-item v-for="(route, index) in data.breadcrumbList" :key="index">
                <text class="name" :class="{ breadcrumb: data.active == route.name }" @click="changeActive(route, index)">
                  {{ route.name }} {{ route.icon }}
                </text>
              </uni-breadcrumb-item>
            </view>
            <view class="charts-box" v-if="data.contractList.length > 0">
              <qiun-data-charts type="ring" :opts="data.opts" :chartData="chartData" @getIndex="tap" />
            </view>
            <view v-else class="no-data">暂无数据</view>
          </view>

          <!-- 业务员业绩 -->
          <view class="per" v-if="data.salesmanList.length > 0">
            <view class="per-title">
              业务员业绩排行榜
              <view class="line" />
            </view>
            <view class="per-ranking">
              <view class="title">
                <text>业绩排名</text>
                <text>业绩金额(元)</text>
              </view>
              <view class="list">
                <view class="list-item" v-for="(item, index) in data.salesmanList" :key="index">
                  <view class="left">
                    <view class="number">
                      <image v-if="index === 0" src="../../../static/image/first.png" mode="" class="rank"> </image>
                      <image v-if="index === 1" src="../../../static/image/second.png" mode="" class="rank"></image>
                      <image v-if="index === 2" src="../../../static/image/third.png" mode="" class="rank"> </image>
                      <text v-if="index > 2" class="text">{{ index + 1 }}</text>
                    </view>
                    <image :src="item.avatar ? item.avatar : '../../../static/image/logo.png'" mode="" class="img"> </image>
                    {{ item.name }}
                  </view>
                  <view class="mr20">
                    {{ item.price }}
                  </view>
                </view>
              </view>
            </view>
          </view>
          <!--产品业绩 -->
          <view class="per product-per" v-if="data.productRankList.length > 0">
            <view class="per-title">
              产品业绩排行榜
              <view class="line" />
            </view>
            <view class="per-ranking">
              <view class="title">
                <text>排名</text>
                <text class="w160">商品</text>
                <text class="mr10">销量</text>
                <text>业绩净额</text>
              </view>
              <view class="list">
                <view class="list-item" v-for="(item, index) in data.productRankList" :key="index">
                  <view class="left w200">
                    <view class="number">
                      <image v-if="index === 0" src="../../../static/image/first.png" mode="" class="rank"> </image>
                      <image v-if="index === 1" src="../../../static/image/second.png" mode="" class="rank"></image>
                      <image v-if="index === 2" src="../../../static/image/third.png" mode="" class="rank"> </image>
                      <text v-if="index > 2" class="text">{{ index + 1 }}</text>
                    </view>
                    <image :src="item.image ? item.image : '../../../static/image/logo.png'" mode="" class="img"> </image>
                    <view class="overText"> [{{ item.spec_type == 1 ? '多规格' : '单规格' }}] {{ item.product_name }} </view>
                  </view>

                  <view class="mr20">
                    {{ item.order_count }}
                  </view>
                  <view class="mr20">
                    {{ item.unit_price }}
                  </view>
                </view>
              </view>
            </view>
          </view>
        </view>
      </view>
      <tabbar :currentIndex="7" navigateType="redirectTo" />
      <!-- 筛选标签 -->
      <departmentPopup ref="departmentRef" @change="change"> </departmentPopup>
    </view>
  </BaseContainer>
</template>

<script setup>
import BaseContainer from '@/components/BaseContainer/index.vue'
import defaultNavBar from '@/components/defaultNavBar/index.vue'
import tabbar from '@/components/tabbar/index.vue'
import departmentPopup from '@/components/departmentPopup/index.vue'
import { ref, reactive, onMounted } from 'vue'
import { briefStatisticsApi, contractCategoryEnabledApi, contractRankApi, productRankApi, productRankListApi, salesmanRankApi } from '@/api/customer'
import message from '@/utils/message'
import moment from 'moment'

onMounted(async () => {
  data.where.time =
    moment().month(moment().month()).startOf('month').format('YYYY/MM/DD') +
    '-' +
    moment().month(moment().month()).endOf('month').format('YYYY/MM/DD')
  await initContractCategoryStatus()
  getframe()
  getSalesmanList()
  getProductRankList()
  if (data.contractCategoryEnabled) {
    getRank()
  }
  getRank2()
})
const data = reactive({
  backgroundColor: 'rgba(0,0,0,0)',
  chartData: {},
  opts: {},
  opts2: {},
  ids: [],
  pickerShow: false,
  contractCategoryEnabled: false,
  contractList: [],
  salesmanList: [], // 业务员数据
  productRankList: [],
  list: [
    {
      icon: 'icon-yeji-zongshouru',
      title: '总收入(元)',
      num: 0,
    },
    {
      icon: 'icon-yeji-xinzengkehu',
      title: '新增客户',
      num: 0,
    },
    {
      icon: 'icon-yeji-xinzenghetong',
      title: '新增订单',
      num: 0,
    },
    {
      icon: 'icon-yeji-xinzenghetonge',
      title: '新增订单金额(元)',
      num: 0,
    },
    {
      icon: 'icon-yeji-xinzenghetonge',
      title: '订单续费金额(元)',
      num: 0,
    },
    {
      icon: 'icon-a-yeji-weihuikuanxufei',
      title: '至今未回款(元)',
      num: 0,
    },
  ],
  where: {
    time: '',
    member: '', // 成员id
    type: 2,
  },
  totalData: 0,
  timeText: '本月',
  btnText: '本部门',
  active: '订单分类',
  active2: '产品分类',
  activeId: '',
  activeId2: '',
  breadcrumbList: [
    {
      name: '订单分类',
      id: '',
    },
  ],
  breadcrumbList2: [
    {
      name: '产品分类',
      id: '',
    },
  ],
})
const change = (obj) => {
  const member = Object.values(obj.member)
  data.where.type = obj.type
  data.where.time = obj.time
  data.where.member = member.toString()
  data.timeText = obj.timeText
  data.btnText = obj.btnText
  getframe()
  getSalesmanList()
  getProductRankList()
  if (data.contractCategoryEnabled) {
    getRank()
  } else {
    resetContractRank()
  }
  getRank2()
}

const initContractCategoryStatus = async () => {
  try {
    const res = await contractCategoryEnabledApi()
    data.contractCategoryEnabled = !!res.data?.enabled
  } catch {
    data.contractCategoryEnabled = true
  }
  if (!data.contractCategoryEnabled) {
    resetContractRank()
  }
}

const resetContractRank = () => {
  data.active = '订单分类'
  data.activeId = ''
  data.ids = ''
  data.contractList = []
  chartData.value = {}
  data.breadcrumbList = [
    {
      name: '订单分类',
      id: '',
    },
  ]
}

// 圆环图点击事件
const tap = (e) => {
  if (!data.contractCategoryEnabled) {
    return
  }
  const len = e.currentIndex
  if (data.activeId == e.opts._series_[len].id) {
    return
  } else {
    data.active = e.opts._series_[len].name
    data.activeId = e.opts._series_[len].id
    data.ids = e.opts._series_[len].id
    getRank()
    data.breadcrumbList.push(e.opts._series_[len])
    data.breadcrumbList.forEach((item, index) => {
      if (index < data.breadcrumbList.length - 1) {
        item.icon = '/'
      }
    })
  }
}
// 圆环图点击事件
const tap2 = (e) => {
  const len = e.currentIndex
  if (data.activeId2 == e.opts._series_[len].id) {
    return
  } else {
    data.active2 = e.opts._series_[len].name
    data.activeId2 = e.opts._series_[len].id
    data.ids = e.opts._series_[len].id
    getRank2()
    data.breadcrumbList2.push(e.opts._series_[len])
    data.breadcrumbList2.forEach((item, index) => {
      if (index < data.breadcrumbList2.length - 1) {
        item.icon = '/'
      }
    })
  }
}

// 点击面包屑
const changeActive2 = (row, index) => {
  data.active2 = row.name
  data.activeId2 = row.id
  data.breadcrumbList.splice(index + 1)
  if (row.name == '产品分类') {
    data.ids = ''
    getRank2()
    data.breadcrumbList = [
      {
        name: '产品分类',
        id: '',
      },
    ]
  } else {
    getRank2()
  }
}

// 点击面包屑
const changeActive = (row, index) => {
  if (!data.contractCategoryEnabled) {
    return
  }
  data.active = row.name
  data.ids = row.id
  data.breadcrumbList.splice(index + 1)
  if (row.name == '订单分类') {
    data.ids = ''
    getRank()
    data.breadcrumbList = [
      {
        name: '订单分类',
        id: '',
      },
    ]
  } else {
    getRank()
  }
}

// 获取业绩简报数据
const getframe = () => {
  briefStatisticsApi(data.where)
    .then((res) => {
      data.list[0].num = res.data.income
      data.list[1].num = res.data.new_customer
      data.list[2].num = res.data.new_contract
      data.list[3].num = res.data.new_contract_price
      data.list[4].num = res.data.renew
      data.list[5].num = res.data.uncollected_price
    })
    .catch((err) => {
      message.error(err.message)
    })
}

// 获取业务员数据
const getSalesmanList = () => {
  salesmanRankApi(data.where).then((res) => {
    data.salesmanList = res.data.list
  })
}
const getProductRankList = () => {
  productRankListApi(data.where).then((res) => {
    data.productRankList = res.data.list
  })
}

// 定义数据
let chartData = ref({})
let chartData2 = ref({})

// 获取产品分类
const getRank2 = () => {
  let obj = {
    category_id: data.activeId2,
    time: data.where.time,
    member: data.where.member,
    type: data.where.type,
  }
  productRankApi(obj)
    .then((res) => {
      data.contractList2 = res.data
      data.totalData = 0
      let sumData = data.contractList2.reduce((sum, e) => sum + Number(e.price || 0), 0)
      data.totalData = sumData.toFixed(2)

      uni.setStorageSync('contractList2', JSON.stringify(data.contractList2))
      if (res.data.length !== 0) {
        chartData2.value = {
          series: [
            {
              data: res.data.map((item) => {
                return {
                  value: Number(item.price),
                  name: item.category_name,
                  id: item.category_id,
                }
              }),
            },
          ],
        }
      } else {
        chartData.value.series = []
      }

      data.opts = {
        rotate: false,
        rotateLock: false,
        color: ['#1890FF', '#91CB74', '#FAC858', '#EE6666', '#73C0DE', '#3CA272', '#FC8452', '#9A60B4', '#ea7ccc'],
        // padding: [5, 5, 5, 5],
        dataLabel: true,
        enableScroll: false,
        legend: {
          show: true,
          position: 'right',
          lineHeight: 25,
        },
        title: {
          name: data.totalData,
          fontSize: 18,
          color: '#303133',
        },

        legend: {
          show: true,
          position: 'bottom',
          lineHeight: 0,
          itemHeight: 10, // 图标大小设置
          icon: 'circle',
          rich: {
            a: {
              padding: [0, 10, 0, 10],
              color: '#FF0000',
            },
            b: {
              color: '#666',
            },
          },
          format: 'legendFormat',
        },
        extra: {
          ring: {
            ringWidth: 30,
            activeOpacity: 0.5,
            activeRadius: 0,
            offsetAngle: 0,
            labelWidth: 10,
            border: false,
            borderWidth: 30,
            borderColor: '#FFFFFF',
          },
        },
      }
    })
    .catch((err) => {
      message.error(err.message)
    })
}
// 获取订单分类
const getRank = () => {
  if (!data.contractCategoryEnabled) {
    resetContractRank()
    return
  }
  let obj = {
    category_id: data.ids,
    time: data.where.time,
    member: data.where.member,
    type: data.where.type,
  }
  contractRankApi(obj)
    .then((res) => {
      data.contractList = res.data
      data.totalData = 0
      let sumData = data.contractList.reduce((sum, e) => sum + Number(e.price || 0), 0)
      data.totalData = sumData.toFixed(2)

      uni.setStorageSync('contractList', JSON.stringify(data.contractList))
      if (res.data.length !== 0) {
        chartData.value = {
          series: [
            {
              data: res.data.map((item) => {
                return {
                  value: Number(item.price),
                  name: item.category_name,
                  id: item.category_id,
                }
              }),
            },
          ],
        }
      } else {
        chartData.value.series = []
      }

      data.opts = {
        rotate: false,
        rotateLock: false,
        color: ['tomato', 'deepskyblue', 'orange', 'mediumseagreen', 'violet', 'dodgerblue', 'khaki', 'turquoise', 'salmon', 'darkslategray'],
        // padding: [5, 5, 5, 5],
        dataLabel: true,
        enableScroll: false,
        legend: {
          show: true,
          position: 'right',
          lineHeight: 25,
        },
        title: {
          name: data.totalData,
          fontSize: 18,
          color: '#303133',
        },
        subtitle: {
          name: '总计订单金额(元)',
          fontSize: 12,
          color: '#909399',
        },
        legend: {
          show: true,
          position: 'bottom',
          lineHeight: 0,
          itemHeight: 10, // 图标大小设置
          icon: 'circle',
          rich: {
            a: {
              padding: [0, 10, 0, 10],
              color: '#FF0000',
            },
            b: {
              color: '#666',
            },
          },
          format: 'legendFormat',
        },
        extra: {
          ring: {
            ringWidth: 30,
            activeOpacity: 0.5,
            activeRadius: 0,
            offsetAngle: 0,
            labelWidth: 10,
            border: false,
            borderWidth: 30,
            borderColor: '#FFFFFF',
          },
        },
      }
    })
    .catch((err) => {
      message.error(err.message)
    })
}

const departmentRef = ref(null)
// 部门选择
const departmentChange = (val) => {
  departmentRef.value.popupOpen(val)
}
</script>

<style scoped lang="scss">
.nav {
  position: relative;
}

.name {
  font-size: 26rpx;
  cursor: pointer;
  margin-right: 8rpx;
}

.el-breadcrumb {
  font-family:
    PingFang SC-Regular,
    PingFang SC;
  padding: 24rpx;
}

.icon-zhankai1 {
  font-size: 24rpx;
}

.breadcrumb {
  color: #1890ff !important;
  font-size: 26rpx;
  cursor: pointer;
}

.nav-content {
  display: flex;
  justify-content: space-around;
  align-items: center;
  width: 100%;

  .title {
    font-size: 34rpx;
    font-weight: 500;
    color: #f5f5f5;
    line-height: 34rpx;
  }
}

.s-search {
  padding: 20rpx;
  width: 100%;
  /* #ifdef APP-PLUS */
  padding-top: 180rpx;
  /* #endif */
  /* #ifndef APP-PLUS */
  padding-top: 120rpx;
  /* #endif */
  height: 610rpx;
  background: linear-gradient(to bottom, #308bf8 30%, rgba(245, 245, 245, 1));

  .search {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    height: 70rpx;
    background: rgba(255, 255, 255, 0.12) !important;
    border-radius: 8rpx;
    font-size: 28rpx;
    font-family:
      PingFang SC-Regular,
      PingFang SC;
    font-weight: 400;
    color: #ffffff;
    padding: 0 60rpx;

    .s-title {
      width: 48%;
      display: flex;
      align-items: center;
      justify-content: center;

      .over-title {
        margin: 0 10rpx;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }

    .line {
      height: 26rpx;
      width: 2rpx;
      border: 2rpx solid rgba(255, 255, 255, 0.4);
      margin-left: 20rpx;
      margin-right: 40rpx;
    }
  }

  .per {
    margin-top: 20rpx;
    width: 100%;
    background-color: #fff;
    border-radius: 16rpx;

    .per-title {
      position: relative;
      width: 100%;
      height: 82rpx;
      background-image: url('@/static/image/per-b.png');
      background-repeat: no-repeat;
      background-size: 100% 82rpx;
      font-size: 30rpx;
      font-family:
        PingFang SC-Semibold,
        PingFang SC;
      font-weight: 600;
      color: #303133;
      line-height: 82rpx;
      padding-left: 24rpx;
      display: flex;
      justify-content: space-between;

      .line {
        position: absolute;
        top: 28rpx;
        left: 0;
        width: 6rpx;
        height: 28rpx;
        background-color: #308bf8;
      }

      .text {
        margin-right: 28rpx;
        font-size: 28rpx !important;
        font-family:
          PingFang SC-Regular,
          PingFang SC;
        font-weight: 400;
        color: #303133 !important;

        .icon-zhankai1 {
          font-size: 18rpx;
          color: #909399;
        }
      }
    }

    .per-flex {
      display: flex;
      width: 50%;
      margin-bottom: 40rpx;
    }

    .flex {
      margin-left: 20rpx;
      display: flex;
      flex-direction: column;
    }

    .per-content {
      padding: 40rpx 32rpx;
      display: flex;
      flex-wrap: wrap;

      .iconfont {
        color: rgba(48, 139, 248, 1);
        font-size: 52rpx;
      }

      .num {
        font-size: 38rpx;
        font-weight: 500;
        color: #303133;
      }

      .text {
        font-size: 24rpx;
        font-family:
          PingFang SC-Regular,
          PingFang SC;
        font-weight: 400;
        color: #909399;
      }
    }
  }
}

.charts-box {
  width: 100%;
  height: 500rpx;
}
.no-data {
  width: 100%;
  height: 500rpx;
  line-height: 500rpx;
  text-align: center;
  font-size: 26rpx;
  font-family:
    PingFang SC-Regular,
    PingFang SC;
  font-weight: 400;
  color: #909399;
}

.per-ranking {
  width: 100%;
  // height: 400rpx;
  padding: 24rpx;

  .title {
    margin-top: 20rpx;
    width: 100%;
    height: 64rpx;
    background: #f0f1f5;
    font-size: 22rpx;
    font-family:
      PingFang SC-Regular,
      PingFang SC;
    font-weight: 400;
    color: #303133;
    line-height: 64rpx;
    display: flex;
    justify-content: space-between;
    padding: 0 15rpx;
  }
}

.list-item {
  height: 100rpx;
  border-bottom: 2rpx solid rgba(235, 238, 245, 1);
  line-height: 100rpx;
  display: flex;
  justify-content: space-between;
  font-size: 22rpx;
  font-family:
    PingFang SC-Regular,
    PingFang SC;
  font-weight: 400;
  color: #303133;

  .left {
    margin-left: 20rpx;
    display: flex;
    align-items: center;

    .rank {
      display: block;
      width: 40rpx;
      height: 40rpx;
    }
  }

  .text {
    font-size: 26rpx;
    font-family:
      PingFang SC-Medium,
      PingFang SC;
    font-weight: 500;
    color: #909399 !important;
  }

  .mr20 {
    margin-right: 20rpx;
  }

  .img {
    width: 60rpx;
    height: 60rpx;
    display: block;
    border-radius: 50%;
    margin: 0 20rpx;
  }
}

.cr-position-header {
  position: fixed;
  padding-top: var(--status-bar-height);
  height: calc($uni-default-bar-height + var(--status-bar-height));
  background: linear-gradient(#308bf8 0%, #308bf8 100%);
}

.number {
  width: 60rpx;

  .text {
    padding-left: 10rpx;
  }
}

.mb130 {
  padding-bottom: 123rpx;
}
.w200 {
  width: 400rpx;
}
.w160 {
  width: 360rpx;
}

.product-per {
  .left {
    margin-left: 14rpx !important;
  }
  .img {
    border-radius: 8rpx !important;
    width: 60rpx;
    height: 60rpx;
    flex-shrink: 0;
  }
  .overText {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 320rpx;
  }
}
</style>

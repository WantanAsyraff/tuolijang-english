<template>
  <Transition name="mask-fade">
    <view class="float-nav-box-mask" v-if="showFloatNavBox && moreTabs.length > 0" @click="handleCloseFloatNavBox"></view>
  </Transition>
  <Transition name="float-fade">
    <view class="float-nav-box" v-if="showFloatNavBox && moreTabs.length > 0">
      <view class="float-nav-item" v-for="item in moreTabs" :key="item.id" @click="switchTab(item)">
        <view class="float-nav-item-img-box">
          <image class="float-nav-item-img" :src="item.selectedIconPath"></image>
        </view>
        <view class="float-nav-item-text">{{ item.text }}</view>
      </view>
    </view>
  </Transition>
  <view class="tab">
    <view v-for="(item, index) in mainTabs" :key="item.id" class="tab-item" @click="switchTab(item)">
      <image class="tab_img" :src="checkIsActive(index) ? item.selectedIconPath : item.iconPath"></image>
      <view class="tab_text" :style="{ color: checkIsActive(index) ? selectedColor : color }">{{ item.text }}</view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { isModuleEnabled, CUSTOMER_MODULE_KEYS } from '@/utils/customerSwitch'

defineOptions({
  name: 'Tabbar',
})

const props = withDefaults(
  defineProps<{
    currentIndex: number
    navigateType?: 'navigateTo' | 'redirectTo' | 'switchTab'
  }>(),
  {
    navigateType: 'navigateTo',
    currentIndex: 0,
  },
)

const selectedColor = ref('#308BF8')
const color = ref('#687383')
const showFloatNavBox = ref(false)

interface TabItem {
  id: number
  text: string
  pagePath?: string
  func?: () => void
  iconPath: string
  selectedIconPath: string
}

const handleCloseFloatNavBox = () => {
  showFloatNavBox.value = false
}

// 权限菜单列表（不包含更多）
const permissionTabs: TabItem[] = [
  {
    id: 0,
    text: '线索',
    pagePath: '/pages/customer/lead/index',
    iconPath: '/static/image/lead.png',
    selectedIconPath: '/static/image/lead-active.png',
  },
  {
    id: 1,
    text: '客户',
    pagePath: '/pages/customer/list/index',
    iconPath: '/static/image/customer.png',
    selectedIconPath: '/static/image/customer-s.png',
  },
  {
    id: 2,
    text: '商机',
    pagePath: '/pages/customer/opportunity/index',
    iconPath: '/static/image/opportunity.png',
    selectedIconPath: '/static/image/opportunity-active.png',
  },
  {
    id: 7,
    text: '合同',
    pagePath: '/pages/customer/signing/index',
    iconPath: '/static/image/contract.png',
    selectedIconPath: '/static/image/contract-s.png',
  },
  {
    id: 4,
    text: '订单',
    pagePath: '/pages/customer/contract/index',
    iconPath: '/static/image/contract.png',
    selectedIconPath: '/static/image/contract-s.png',
  },
  {
    id: 5,
    text: '发票',
    pagePath: '/pages/customer/invoice/index',
    iconPath: '/static/image/invoice.png',
    selectedIconPath: '/static/image/invoice-s.png',
  },
  {
    id: 6,
    text: '统计',
    pagePath: '/pages/customer/list/statistics',
    iconPath: '/static/image/statistics.png',
    selectedIconPath: '/static/image/statistics-s.png',
  },
]

// 更多按钮
const moreButton: TabItem = {
  id: 3,
  text: '更多',
  iconPath: '/static/image/more.png',
  selectedIconPath: '/static/image/more-active.png',
  func: () => {
    showFloatNavBox.value = !showFloatNavBox.value
  },
}

// 根据权限过滤后的菜单
const filteredTabs = computed(() => {
  return permissionTabs.filter((item) => checkMenuPermission(item))
})

// 检查菜单权限
const checkMenuPermission = (item: TabItem): boolean => {
  if (!item.pagePath) return true
  const moduleKey = pathToModuleKey(item.pagePath)
  if (!moduleKey) return true
  return isModuleEnabled(moduleKey)
}

// 路径转模块key
const pathToModuleKey = (path: string): string | null => {
  const mapping: Record<string, string> = {
    '/pages/customer/lead/index': CUSTOMER_MODULE_KEYS.LEADS,
    '/pages/customer/list/index': CUSTOMER_MODULE_KEYS.CUSTOMER,
    '/pages/customer/opportunity/index': CUSTOMER_MODULE_KEYS.OPPORTUNITY,
    '/pages/customer/signing/index': CUSTOMER_MODULE_KEYS.CONTRACT,
    '/pages/customer/contract/index': CUSTOMER_MODULE_KEYS.ORDER,
    '/pages/customer/invoice/index': CUSTOMER_MODULE_KEYS.INVOICE,
    '/pages/customer/list/statistics': CUSTOMER_MODULE_KEYS.CUSTOMER,
  }
  return mapping[path] || null
}

// 更多菜单（超出前3个的菜单）
const moreTabs = computed(() => {
  return filteredTabs.value.slice(3)
})

// 底部导航显示的菜单（最多3个，更多有值时永远在最后）
const mainTabs = computed(() => {
  if (moreTabs.value.length > 0) {
    return [...filteredTabs.value.slice(0, 3), moreButton]
  }
  return filteredTabs.value.slice(0, 4)
})

const checkIsActive = (index: number) => {
  return mainTabs.value[index]?.pagePath && isCurrentRoute(mainTabs.value[index].pagePath)
}

const isCurrentRoute = (pagePath: string) => {
  const pages = getCurrentPages()
  const route = pages[pages.length - 1]?.route
  return pagePath.includes(route || '')
}

const switchTab = (item: TabItem) => {
  if (item.pagePath) {
    handleCloseFloatNavBox()
    const pages = getCurrentPages()
    const route = pages[pages.length - 1].route
    if (item.pagePath.includes(route)) return

    if (props.navigateType === 'navigateTo') {
      uni.navigateTo({
        url: item.pagePath,
        animationType: 'fade-in',
      })
    } else if (props.navigateType === 'redirectTo') {
      uni.redirectTo({
        url: item.pagePath,
      })
    } else if (props.navigateType === 'switchTab') {
      uni.switchTab({
        url: item.pagePath,
      })
    }
  } else if (item.func) {
    item.func()
  }
}
</script>

<style lang="scss">
$tab-height: 103rpx;

.float-nav-box-mask {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  z-index: 100;
}

.float-nav-box {
  position: fixed;
  left: 0;
  right: 0;
  z-index: 105;
  height: 220rpx;
  background: #f7f7f7;
  bottom: calc(var(--bottom-area-height) + #{$tab-height});
  padding-inline: 86rpx;
  border-radius: 16rpx 16rpx 0rpx 0rpx;

  display: flex;
  align-items: center;
  justify-content: space-between;

  & + .tab {
    z-index: 110;
  }

  .float-nav-item-img-box {
    width: 80rpx;
    height: 80rpx;
    background-color: #fff;
    border-radius: 16rpx;
    margin-bottom: 12rpx;
    display: flex;
    align-items: center;
    justify-content: center;

    .float-nav-item-img {
      width: 52rpx;
      height: 52rpx;
    }
  }

  .float-nav-item-text {
    font-size: 12px;
    color: #303133;
    text-align: center;
  }
}

.mask-fade {
  &-enter-active,
  &-leave-active {
    transition: opacity 0.3s ease;
  }

  &-enter-from,
  &-leave-to {
    opacity: 0;
  }
}

.float-fade {
  &-enter-active,
  &-leave-active {
    transition: transform 0.3s ease;
  }

  &-enter-from,
  &-leave-to {
    transform: translateY(100%);
  }
}

.tab {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: $tab-height;
  background: white;
  display: flex;
  justify-content: center;
  align-items: center;
  padding-bottom: env(safe-area-inset-bottom);

  .tab-item {
    flex: 1;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;

    .tab_img {
      width: 44rpx;
      height: 44rpx;
    }

    .tab_text {
      font-size: 22rpx;
      font-family:
        PingFang SC,
        PingFang SC;
      font-weight: 400;
      color: #687383;
    }
  }
}
</style>

<template>
  <view class="address-list">
    <!-- #ifdef APP-PLUS -->
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <default-nav-bar :is-left="false" :is-right="false"></default-nav-bar>
    </view>
    <!-- #endif -->
		<view class="cr-position-header">
		  <view class="default-search">
		    <uni-search-bar 
				 v-model="searchKeyword"
				:placeholder="$t('ui.customerAddressSearchIndexSearchLocations')"
				:radius="20"
				bgColor="#ffffff"
				@confirm="handleSearch"
				@input="handleInputChange"
				@cancel="goBack">
		    </uni-search-bar>
		  </view>
		</view>
		<!-- 加载中 -->
		<view v-if="loading" class="loading">
			<uni-load-more status="loading"></uni-load-more>
		</view>
    <view class="address-content">
      <search-list :list-data="locationList" empty-title="暂无搜索结果～"></search-list>
    </view>
  </view>
</template>

<script setup>
import defaultNavBar from "@/components/defaultNavBar/index.vue";
import searchList from "./components/searchList.vue";
import { useStore } from "vuex";
import { toLogin } from "@/libs/login";
import message from "@/utils/message";
import moment from "moment";
import { messageCateApi, scheduleTypesApi, scheduleListApi } from "@/api/user";
import { clickNavigateTo } from "@/utils/helper";
import { useBarHeight } from "@/utils/useVerifyCode";


const store = useStore();
const isLogin = computed(() => store.state.app.isLogin);
onShow(() => {
  if (!isLogin.value) {
    toLogin();
  }
});
// 搜索关键词（从上一页传过来）
const searchKeyword = ref('')
// 位置列表
const locationList = ref([])
// 加载状态
const loading = ref(false)
// 当前位置（用于计算距离）
const currentLocation = ref(null)

const data = reactive({
  placeholder: "搜索位置",
  where: {
    limit: 10,
    page: 1,
    name: ""
  },
  listData: [
		{
			keyword: "西安雁塔",
			name: "",
			distance: "18.9km",
			address: "陕西省西安市"
		},
		{
			keyword: "西安雁塔",
			name: "政府服务中心",
			distance: "18.9km",
			address: "陕西省西安市翠花天地120号"
		},
		{
			keyword: "西安雁塔",
			name: "第二中心",
			distance: "18.9km",
			address: "陕西省西安市"
		},
		{
			keyword: "西安雁塔",
			name: "第二中心",
			distance: "18.9km",
			address: "陕西省西安市"
		},
		{
			keyword: "西安雁塔",
			name: "第二中心",
			distance: "18.9km",
			address: "陕西省西安市"
		},
		{
			keyword: "西安雁塔",
			name: "第二中心",
			distance: "18.9km",
			address: "陕西省西安市"
		},
	],

});


// 页面加载时接收参数
onLoad((options) => {
  console.log('接收参数:', options)
    
	if (options.keyword) {
		searchKeyword.value = decodeURIComponent(options.keyword)
		// 自动搜索
		setTimeout(() => {
			searchLocations(searchKeyword.value)
		}, 300)
	}
	// 获取当前位置
	getCurrentLocation()
});

// 获取当前位置
const getCurrentLocation = () => {
  uni.getLocation({
    type: 'gcj02', // 高德地图使用gcj02坐标系
    success: (res) => {
      console.log('当前位置:', res)
      currentLocation.value = {
        latitude: res.latitude,
        longitude: res.longitude
      }
      // 逆地理编码获取城市信息
      reverseGeocode(res.latitude, res.longitude)
    },
    fail: (err) => {
      console.log('获取位置失败:', err)
      // 使用默认位置（北京）
      currentLocation.value = {
        latitude: 39.9042,
        longitude: 116.4074
      }
      // 使用默认城市
      uni.setStorageSync('currentCity', '北京')
    }
  })
};

// 逆地理编码（获取城市信息）
const reverseGeocode = async (latitude, longitude) => {
  try {
    // 高德地图逆地理编码API
    const res = await uni.request({
      url: 'https://restapi.amap.com/v3/geocode/regeo',
      data: {
        location: `${longitude},${latitude}`,
        key: 'YOUR_AMAP_KEY', // 替换为你的高德地图key
        extensions: 'base'
      }
    })
    
    if (res.data.status === '1') {
      const addressComponent = res.data.regeocode.addressComponent
      const city = addressComponent.city || addressComponent.province
      uni.setStorageSync('currentCity', city)
      console.log('当前城市:', city)
    }
  } catch (error) {
    console.error('逆地理编码失败:', error)
  }
};

// 搜索地点 - 使用高德地图API
const searchLocations = async (keyword) => {
  if (!keyword.trim()) {
    locationList.value = []
    return
  }
  
  loading.value = true
  locationList.value = []
  
  console.log('开始搜索:', keyword)
  
  try {
    // 使用高德地图API
    await searchWithAmap(keyword)
    
  } catch (error) {
    console.error('搜索异常:', error)
    uni.showToast({
      title: '搜索异常，请检查网络',
      icon: 'error'
    })
    // 降级方案：使用模拟数据
    useMockData(keyword)
  } finally {
    loading.value = false
  }
};
// 高德地图搜索
const searchWithAmap = async (keyword) => {
  // 高德地图Web服务API Key
  // 申请地址：https://lbs.amap.com/api/webservice/guide/create-project/get-key
  const AMAP_KEY = 'cf5c437b14780406af75a81b380cafac' // ⚠️ 替换为你的高德地图Key
  
  // 获取当前城市（优先使用存储的城市，没有则使用"全国"）
  let city = uni.getStorageSync('currentCity') || ''
  
  // 构建请求参数
  const params = {
    keywords: keyword,
    key: AMAP_KEY,
    city: city, // 城市限制（可空）
    citylimit: city ? 'true' : 'false', // 是否限制在城市内
    extensions: 'all', // 返回详细信息
    offset: 20, // 每页记录数
    page: 1, // 页码
    output: 'JSON'
  }
  
  console.log('高德地图搜索参数:', params)
  
  const res = await uni.request({
    url: 'https://restapi.amap.com/v3/place/text',
    method: 'GET',
    data: params,
    timeout: 10000
  })
  
  console.log('高德地图响应:', res.data)
  if (res.data.status === '1') {
    // status为'1'表示成功！
    if (res.data.pois && res.data.pois.length > 0) {
      locationList.value = res.data.pois.map((item, index) => {
        return {
          id: item.id || `item_${index}`,
          name: item.name || '未知地点',
          address: item.address || item.pname || '地址不详',
          latitude: item.location ? parseFloat(item.location.split(',')[1]) : 0,
          longitude: item.location ? parseFloat(item.location.split(',')[0]) : 0,
          distance: formatAmapDistance(item.distance),
          province: item.pname || '',
          city: item.cityname || '',
          district: item.adname || '',
          type: item.type || '',
          typecode: item.typecode || '',
          tel: item.tel || '',
          biz_ext: item.biz_ext || {}
        }
      })
      
      console.log('处理后的地点列表:', locationList.value)
    } else {
      console.log('搜索结果为空')
      locationList.value = []
    }
  } else {
    console.error('高德地图返回错误:', res.data)
    uni.showToast({
      title: `搜索失败: ${res.data.info || '未知错误'}`,
      icon: 'none'
    })
  }
};

// 格式化高德地图返回的距离
const formatAmapDistance = (distance) => {
  if (!distance || distance === '') return ''
  
  const meters = parseInt(distance)
  if (meters < 1000) {
    return `${meters}米`
  } else {
    const km = (meters / 1000).toFixed(1)
    return `${km.replace('.0', '')}km`
  }
};

// 模拟数据（备选方案）
const useMockData = (keyword) => {
  const mockData = [
    {
      id: 'B0FFG5CCZA',
      name: `${keyword}`,
      address: '陕西省西安市雁塔区',
      distance: '18.9km',
      province: '陕西省',
      city: '西安市',
      district: '雁塔区',
      latitude: 34.21812,
      longitude: 108.94067
    },
    {
      id: 'B0FFG6T7RX',
      name: `${keyword}政府服务中心`,
      address: '陕西省西安市翠华天地120号',
      distance: '18.9km',
      province: '陕西省',
      city: '西安市',
      district: '雁塔区',
      latitude: 34.22045,
      longitude: 108.94321
    },
    {
      id: 'B0FFG8K9YH',
      name: `${keyword}第二中心`,
      address: '陕西省西安市科技路88号',
      distance: '19.2km',
      province: '陕西省',
      city: '西安市',
      district: '雁塔区',
      latitude: 34.22567,
      longitude: 108.93894
    },
    {
      id: 'B0FFG9L1ZM',
      name: `${keyword}第三中心`,
      address: '陕西省西安市小寨路',
      distance: '18.5km',
      province: '陕西省',
      city: '西安市',
      district: '雁塔区',
      latitude: 34.21678,
      longitude: 108.94532
    }
  ]
  
  locationList.value = mockData
};
// 输入变化（实时搜索）
let searchTimer = null
const handleInputChange = (e) => {
  searchKeyword.value = e.value
  
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    if (searchKeyword.value.length >= 1) {
      searchLocations(searchKeyword.value)
    } else {
      locationList.value = []
    }
  }, 800)
};

// 确认搜索
const handleSearch = (e) => {
  const keyword = e.value || searchKeyword.value
  searchLocations(keyword)
};

// 选择地点
const selectLocation = (item) => {
  console.log('选择的地点:', item)
  
  // 方法1：使用页面通信（推荐）
  const pages = getCurrentPages()
  if (pages.length >= 2) {
    const prevPage = pages[pages.length - 2]
    // 传递数据给上一页
    prevPage.$vm?.locationSelected?.(item)
  }
  
  // 方法2：使用全局事件
  uni.$emit('locationSelected', item)
  
  // 方法3：使用存储
  uni.setStorageSync('selectedLocation', item)
  
  // 返回上一页
  uni.navigateBack({
    delta: 1
  })
};

// 返回
const goBack = () => {
  uni.navigateBack()
};






const { height, getBarHeight } = useBarHeight();
const instance = getCurrentInstance();
const scrollToPage = () => {
  nextTick(() => {
    getBarHeight(".address-content", instance, false);
    uni.pageScrollTo({
      scrollTop: height.value,
      duration: 0
    });
  });
};


</script>

<style scoped lang="scss">
	.address-list {

	}
  .cr-position-header {
    position: sticky;
    top: 0;
  }
	
  .address-content {
    padding: 20rpx;
  }
	
</style>

<template>
  <view class="details-foot">
    <view class="foot-item">
      <view class="foot-phone" @click="call(customerData.customer_tel)">
        <text class="iconfont icon-dianhua1"></text>
        打电话
      </view>
    </view>
    <view class="btn-line" />
    <view class="foot-item">
      <view class="foot-add" @click="handleShowAddPopup">
        <!-- <text class="iconfont icon-dianhua1"></text> -->
        <text class="iconfont icon-xuanfuanniu-jia"></text>
      </view>
    </view>
    <view class="btn-line ml10" />
    <view class="foot-item">
      <view class="foot-location" @click="handlePickerLocation">
        <text class="iconfont icon-weizhi"></text>
        位置
      </view>
    </view>

  </view>
</template>

<script setup lang="ts">
  import { ref } from "vue";
  import { clickNavigateTo } from "@/utils/helper";
  import { useStore } from 'vuex'
  let emit = defineEmits(["openPopup"]);
  const store = useStore()
  const props = defineProps({
    customerData: {
      type: Object,
      default: () => {
        return {};
      }
    }
  });
  const { customerData } = toRefs(props);
  // 打电话
  const call = (phone) => {
    uni.makePhoneCall({
      phoneNumber: phone, // 电话号码
      success: function () {
        console.log("拨打电话成功");
      },
      fail: function () {
        console.error("拨打电话失败");
      }
    });
  };
  const handleShowAddPopup = () => {
    emit("openPopup");
  };
  // 使用 async/await
  const handlePickerLocation = async () => {
    try {
      // uni.chooseLocation() 返回的是单个对象，不是数组
      const result = await uni.chooseLocation();

      // 用户取消选择（返回null或undefined）
      if (!result) {
        console.log('用户取消选择');
        return;
      }

      const { name, address, latitude, longitude } = result;

      // 使用 store（假设你已经配置了pinia或vuex）
      useStore().commit('SET_LOCATION', {
        latitude,
        longitude
      });
      useStore().commit('SET_ADDRESS', name);

      // 如果有回调函数，可以触发
      emit('location-selected', { name, address, latitude, longitude });

    } catch (error) {
      console.error('选择位置失败:', error);

      // 处理错误
      if (error?.errMsg) {
        if (error.errMsg.includes('auth deny')) {
          uni.showToast({
            title: '缺少定位权限',
            icon: 'error'
          });
          return;
        } else if (error.errMsg.includes('cancel') || error.errMsg === "chooseLocation:fail") {
          // 用户取消选择，不处理
          return;
        }
      }

      // 其他错误
      uni.showToast({
        title: `定位失败: ${error?.message || error?.errMsg || error}`,
        icon: 'none',
      });
    }
  };




  // 查找位置
  // const handleSearchAddress = () => {
  // 	let area = customerData.value.area_cascade.join('')
  //  clickNavigateTo(`/pages/customer/addressSearch/index?keyword=${area}`);

  // };
  // 暴露方法给父组件调用
  defineExpose({});
</script>

<style scoped lang="scss">
  .details-foot {
    position: fixed;
    z-index: 50;
    width: 440rpx;
    height: 96rpx;
     /* 新增：高斯模糊核心属性（毛玻璃效果） */
    backdrop-filter: blur(5rpx);
    /* 兼容微信小程序/移动端webkit内核 */
    -webkit-backdrop-filter: blur(10rpx);
    background: rgba(255, 255, 255, 0.9);
    box-shadow: 0px 3px 12px 0px rgba(0, 0, 0, 0.05);
    border-radius: 172px 172px 172px 172px;
    left: 155rpx;
    bottom: 28rpx;
    display: flex;
    align-items: center;
    justify-content: center;

    .foot-item {
    cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      // margin-right: 24rpx;
      font-size: 24rpx;
      color: #333;

      // &:nth-child(2){
      // 	position: relative;
      // 	&::before,&::after {
      // 		content: "";
      // 		width: 1rpx;
      // 		height: 56rpx;
      // 		background: linear-gradient(to bottom, transparent 0%, #ddd 50%, transparent 100%);
      // 		display: block;
      // 	}
      // }
    }

    .btn-line {
      width: 1px;
      height: 56rpx;
  margin: 0 24rpx;
      background: linear-gradient(to bottom, transparent 0%, #ddd 50%, transparent 100%);
    }

    .ml24 {
      margin-left: 24rpx;
    }

    .mr24 {
      margin-right: 24rpx;
    }

    .foot-add {
      width: 64rpx;
      height: 48rpx;
      background: #1A91FF;
      border-radius: 16rpx;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;

      .icon-xuanfuanniu-jia {
        margin-left: 2rpx;
      }

      // margin: 0 24rpx;

    }

    .iconfont {
      font-size: 26rpx;
      // margin-left: 12rpx;
      margin-right: 4rpx;
    }
  }
</style>
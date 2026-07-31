<template>
  <view class="">

    <map style="width: 100%; height: 100vh" :latitude="latitude" :longitude="longitude" :markers="covers"
      :circles="circles" :enable-zoom="false" :enable-scroll="false">
    </map>
    <uni-nav-bar background-color="transparent" @click-left="handleBack" :border="false" status-bar left-icon="left"
      title="" class="custom-nav-bar" />
  </view>
</template>

<script setup>
  import {
    ref,
    reactive
  } from "vue";
  import {
    onLoad
  } from "@dcloudio/uni-app";
  const mapShow = ref(false);
  const latitude = ref(0);
  const longitude = ref(0);
  const covers = reactive([{
    latitude: 0, // 纬度
    longitude: 0, // 经度
    iconPath: "/static/image/attendance/range.png",
  }]);
  const circles = reactive([{
    latitude: 0, // 纬度
    longitude: 0, // 经度
    fillColor: "#9db0a1A",
    radius: 1000,
    strokeWidth: 2,
    color: "#00aaff",
  }]);
  onLoad((options) => {
    latitude.value = options.lat;
    longitude.value = options.lng;
    covers[0].latitude = options.lat;
    covers[0].longitude = options.lng;
    circles[0].latitude = options.lat;
    circles[0].longitude = options.lng;
    circles[0].radius = Number(options.radius);

    mapShow.value = true;
  });
  const handleBack = () => {
    uni.navigateBack({
      delta: 1,
      // 可选：返回成功后的回调
      success: () => {
        console.log('返回上一页成功');
      }
    });
    // uni.navigateBack({
    //   delta: 1,
    //   // fail: () => {
    //   //   uni.switchTab({ url: '/pages/index/index' })
    //   // }
    // })
  }
</script>

<style lang="scss" scoped>
  .custom-nav-bar {
    position: fixed;
    left: 0;
    width: 100%;
    top: 0;
    z-index: 500;
  }
</style>
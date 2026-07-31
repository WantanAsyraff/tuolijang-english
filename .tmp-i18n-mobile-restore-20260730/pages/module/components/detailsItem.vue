<template>
  <view class="details-box">
    <view v-for="(item,index) in infoList" :key="index">
      <uni-row style="display:flex">

        <uni-col :span="6" class="text-right title">
          明细 <text v-if="infoList.length>1">{{index+1}}</text>
        </uni-col>
      </uni-row>
      <!-- <view class="title">明细 <text v-if="infoList.length>1">{{index+1}}</text></view> -->
      <basicInfo :list="item" type="details"></basicInfo>
    </view>
  </view>
</template>


<script setup>
  import basicInfo from "./basicInfo.vue";
  const props = defineProps({
    infoList: {
      type: Array,
      default: () => [],
    },
  });
</script>

<style scoped lang="scss">
  .details-box {
    width: 100%;
  }

  .title {

    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    font-size: 13px;
    color: #999999;
    padding-right: 14px;
    margin-bottom: 20rpx;
  }

  ::v-deep .list-info {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  ::v-deep .info-item-right {
    // padding-left: 0 !important;
  }
</style>
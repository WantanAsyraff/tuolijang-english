<template>
  <view class="examine-from">
    <view class="examine-from-list">
      <view v-for="(item, index) in options" :key="index">
        <!-- 明细组件 -->
        <view v-if="item.type == 'approvalBill'">
          <view v-for="(val, indexJ) in item.value" :key="indexJ">
            <view class="line"></view>
            <view class="mb10 name">{{ item.label }}{{ indexJ + 1 }}</view>
            <view v-for="(el, indexJ) in val" :key="indexV">

              <!-- 时长组件 -->
              <view v-if="el.type === 'timeFrom'">
                <uni-row class="examine-from-list-item" v-for="(timeVal, indexK) in el.value" :key="indexK">
                  <uni-col class="examine-from-left">{{ timeVal.label }}</uni-col>
                  <uni-col class="examine-from-right">{{ timeVal.value || '--' }}</uni-col>
                </uni-row>
              </view>
              <uni-row class="examine-from-list-item" v-else>
                <uni-col class="examine-from-left">{{ el.label }}</uni-col>
                <uni-col v-if="!Array.isArray(el.value)" class="examine-from-right">{{ el.value || '--' }}</uni-col>
                <uni-col v-else class="examine-from-right">
                  <upload-from-list v-if="el.value && el.value.length > 0"
                    :upload-from-data="el.value"></upload-from-list>
                  <text v-else>--</text>
                </uni-col>
              </uni-row>
            </view>
          </view>
        </view>
        <uni-row class="examine-from-list-item" v-else>
          <view v-if="item.type === 'timeFrom'">
            <uni-row class="examine-from-list-item" v-for="(timeVal, indexK) in item.value" :key="indexK">
              <uni-col class="examine-from-left">{{ timeVal.label }}</uni-col>
              <uni-col class="examine-from-right">{{ timeVal.value || '--' }}</uni-col>
            </uni-row>
          </view>
          <template v-else>
            <uni-col class="examine-from-left">{{ item.label }}</uni-col>

            <uni-col v-if="!Array.isArray(item.value)" class="examine-from-right">{{ item.value || '--' }}</uni-col>
            <uni-col v-else class="examine-from-right">
              <upload-from-list v-if="item.value && item.value[0]"
                :upload-from-data="item.value"></upload-from-list>
              <text v-else>--</text>
            </uni-col>
          </template>
        </uni-row>
      </view>
    </view>
  </view>
</template>

<script setup>
import { toRefs } from "vue";
import uploadFromList from "./uploadFromList.vue";
const props = defineProps({
  options: {
    type: Array,
    default() {
      return [];
    }
  }
});

const { options } = toRefs(props);
</script>

<style scoped lang="scss">
.examine-from {
  width: 100%;
  padding: 30rpx;



  .examine-from-list {
    font-family: PingFang SC, PingFang SC;

    .examine-from-list-item {
      font-size: 26rpx;
      width: 100%;
      display: flex;
      margin-bottom: 18rpx;

    

      .examine-from-left {
        min-width: 120rpx;
        flex: 1;
        margin-right: 16px;
        font-weight: 400;
        font-size: 26rpx;
        color: #606266;
      
      }
        

      .examine-from-right {
        line-height: 42rpx;
        font-weight: 400;
        font-size: 26rpx;
        color: #303133
      }
    }

    
  }
}
.line {
  width: 100%;
  height: 1px;
  background-color: #eeeeee;
  margin-top: 30rpx;
  margin-bottom: 24rpx;
}
.name {
  font-weight: 500;
  font-size: 26rpx;

}

</style>
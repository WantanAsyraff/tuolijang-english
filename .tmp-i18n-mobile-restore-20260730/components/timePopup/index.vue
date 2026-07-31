<template>
  <view>
    <uni-popup ref="popupRefTime" type="bottom">
      <view class="slider">
        <view class="tab-title"> 时间筛选 </view>


        <!--  tab切换内容 -->
        <view class="content p30">
          <view class="btn-flex">
            <view class="btn" v-for="(item, index) in data.TimeList" :key="index"
              :class="data.timeIndex == item.id ? 'btnActive' : ''" @click="btnFn(item.id, 'time', item.title)">
              {{ item.title }}
            </view>
            <view class="flex mt20" style="width: 100%;" v-if="data.timeIndex == 9">
              <uni-datetime-picker  class="btnshou"  style="width: 45%;" type="date" placeholder="开始时间" :clear-icon="false"    v-model="data.start" />
              <text class="text">-</text>
              <uni-datetime-picker  class="btnshou"  style="width: 45%;" type="date" placeholder="结束时间" :clear-icon="false" v-model="data.end"
                />
            </view>
          </view>
        </view>
        <!-- </scroll-view> -->

        <view class="slider-laber-bottom">
          <button class="reset laber-bottom" @click="reset">重置</button>
          <button class="laber-bottom confirm" @click="confirm">确认</button>
        </view>
      </view>
    </uni-popup>
  </view>
</template>

<script setup>
import { ref, reactive, computed, watch } from "vue";
import { useStore } from "vuex";
import moment from "moment";
import message from "@/utils/message";
import { navigateToDepartment } from "@/utils/autoload";
const store = useStore();
const data = reactive({
  showPerson: false,
  infoUser: [],
  infoDepartment: [],
  mode: "selector",


  timeIndex: -1,
  start: "",
  end: "",
  where: {
    time:'',
    timeText: "",
  },
  TimeList: [{
    title: "今天",
    id: 1,
  },
  {
    title: "昨天",
    id: 2,
  },
  {
    title: "本周",
    id: 3,
  },
  {
    title: "上周",
    id: 4,
  },
  {
    title: "本月",
    id: 5,
  },
  {
    title: "上月",
    id: 6,
  },
  {
    title: "今年",
    id: 7,
  },
  {
    title: "去年",
    id: 8,
  },
  {
    title: "自定义",
    id: 9,
  },
  ],

});
const emit = defineEmits(["change"]);
// 确定
const confirm = () => {
if(data.timeIndex == 9){
  if(!data.start || !data.end){
    return message.error("请选择时间");
  }
  // 验证结束时间不能小于开始时间
  if(moment(data.end).isBefore(data.start)){
    return message.error("结束时间不能小于开始时间");
  }
  data.where.time = moment(data.start).format("YYYY/MM/DD") + "-" + moment(data.end).format("YYYY/MM/DD");
  data.where.timeText = moment(data.start).format("MM/DD") + "-" + moment(data.end).format("MM/DD");
}
 

  emit("change", data.where);
  popupRefTime.value.close();
};
const reset = () => {
  data.where.time=''
  data.timeIndex = -1;
  data.where.timeText = "";
  emit("change", data.where);

  data.start = "";
  data.end = "";
    popupRefTime.value.close();
};
// 点击部门btn
const btnFn = (index, type, name) => {
  if (type == "time") {
    data.timeIndex = index;
    data.where.timeText = name;
    if (index == 1) {
      data.where.time = moment(new Date()).format("YYYY/MM/DD") + "-" + moment(new Date()).format("YYYY/MM/DD");
    } else if (index == 2) {
      data.where.time = moment().add("days", -1).format("YYYY/MM/DD") + "-" + moment().add("days", -1).format(
        "YYYY/MM/DD");
    } else if (index == 3) {
      data.where.time = moment().weekday(1).format("YYYY/MM/DD") + "-" + moment().weekday(7).format(
        "YYYY/MM/DD");
    } else if (index == 4) {
      data.where.time
        = moment()
          .week(moment().week() - 1)
          .startOf("week")
          .add(1, "days")
          .format("YYYY/MM/DD")
        + "-"
        + moment()
          .week(moment().week() - 1)
          .endOf("week")
          .add(1, "days")
          .format("YYYY/MM/DD");
    } else if (index == 5) {
      data.where.time
        = moment().month(moment().month()).startOf("month").format("YYYY/MM/DD")
        + "-"
        + moment().month(moment().month()).endOf("month").format("YYYY/MM/DD");
    } else if (index == 6) {
      data.where.time
        = moment()
          .month(moment().month() - 1)
          .startOf("month")
          .format("YYYY/MM/DD")
        + "-"
        + moment()
          .month(moment().month() - 1)
          .endOf("month")
          .format("YYYY/MM/DD");
    } else if (index == 7) {
      data.where.time
        = moment().year(moment().year()).startOf("year").format("YYYY/MM/DD") + "-" + moment().year(moment()
          .year())
          .endOf("year").format("YYYY/MM/DD");
    } else if (index == 8) {
      data.where.time
        = moment()
          .year(moment().year() - 1)
          .startOf("year")
          .format("YYYY/MM/DD")
        + "-"
        + moment()
          .year(moment().year() - 1)
          .endOf("year")
          .format("YYYY/MM/DD");
    }
  } 
  data.where.type = index;

};
const popupRefTime = ref(null);
// 打开弹窗
const popupOpen = (val) => {

  popupRefTime.value.open();
};

defineExpose({ popupOpen });
</script>

<style lang="scss" scoped>
.p30 {
  padding: 0 30rpx;
}

.mt20 {
  margin-top: 36rpx;
}


.flex {
  // margin-top: 20rpx;
  display: flex;
  justify-content: space-between;
  align-items: center;

  .text {
    margin: 0 20rpx;
  }
}

.slider {
  position: relative;
  // height: 750rpx;
  width: 100%;
  background-color: #fff;
  border-radius: 20rpx 20rpx 0 0;
  padding-top: 44rpx;
  padding-bottom: 158rpx;
}

.slider-laber-bottom {
  position: absolute;
  display: flex;
  bottom: 20rpx;
  height: 86rpx;
  left: 30rpx;
  right: 30rpx;
  align-items: center;
  background-color: #fff;

  .laber-bottom {
    height: 86rpx;
    border-radius: 8rpx;
    border: none;
    font-size: $uni-font-size-default;
    line-height: 86rpx;

    &::after {
      border: 0;
    }
  }
}

.confirm {
  width: calc(100% - 214rpx);
  background: #308bf8;
  color: #fff;
}

.reset {
  width: 184rpx;
  margin-right: 24rpx;
  background: #f5f5f5;
  color: $uni-text-color;
}

.tab-title {
  display: flex;
  justify-content: center;
  font-family: PingFang SC, PingFang SC;
  font-weight: 500;
  font-size: 30rpx;
  color: #303133;
}

.btnActive {
  background-color: rgba(24, 144, 255, 1) !important;
  color: #fff !important;
}

.content {
  height: 100%;
  // overflow-y: auto;
  z-index: 88;

  .btn-flex {
    display: flex;
    flex-wrap: wrap;
    gap: 24rpx;
    margin-top: 40rpx;

    .btn {
      cursor: pointer;
      flex: 1 1 30%;          /* 每行三个，宽度自适应 */
      height: 72rpx;
      line-height: 72rpx;
      text-align: center;
      font-family: PingFang SC, PingFang SC;
      font-weight: 400;
      font-size: 28rpx;
      color: #303133;
      background-color: rgba(240, 241, 245, 1);
      border-radius: 8rpx;
      // margin-bottom: 30rpx;
    }
  }
}


::v-deep .uni-date-single--x{
  position: fixed;
  bottom: 0;
  top: -10px;
  right: 0;
  left: 0;
}

</style>

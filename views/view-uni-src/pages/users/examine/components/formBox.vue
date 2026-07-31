<template>
  <view class="cr-search-content plr10">

    <view class="flex-item" v-if="status != 1">
      <picker class="picker-selector" mode="selector" @change="bindPickerChange" :value="data.typeIndex"
        :range="typeData" range-key="name">
        <view class="search-default-label">{{ data.typeText }} <text class="iconfont icon-jinru"></text></view>
      </picker>
    </view>
    <view class="flex-item">
      <picker class="picker-selector" mode="selector" @change="changeUsers" :value="data.usersIndex"
        :range="data.usersData" range-key="name">
        <view class="search-default-label">{{ data.usersText }} <text class="iconfont icon-jinru"></text></view>
      </picker>
    </view>

    <view class="flex-item">
      <view class="search-default-label" @click="openTime()">
							{{ data.timeText }}
							<text class="date-open-icon iconfont icon-jinru" v-if="!formData.time"></text>
							<text class="date-open-icon iconfont icon-guanbi" v-else @click.stop="clickClear()"></text>
						</view>
    </view>
  </view>
  <timePopup ref="timePopupRef" @change="changeTime"></timePopup>
</template>

<script setup>
import { ref, reactive, onMounted, toRefs } from "vue";
import message from "@/utils/message";
import timePopup from "@/components/timePopup/index.vue";
const props = defineProps({
  type: {
    type: String,
    default: "center"
  },
  status: {
    type: Number,
    default: -2
  },
});
const { status } = toRefs(props);
const data = reactive({
  typeText: "所有状态",
  timeText: "所有日期",
  usersText: "所有类型",
  typeIndex: 0,
  usersIndex: 0,
  usersData: []
});

const formData = reactive({
  time: "",
  approveId: "",
  status: ""
});

const timePopupRef = ref(null);

const timeRef = ref(null);

let emit = defineEmits(["change"]);

const typeData = reactive([
  { name: "所有状态", id: "" },
  { name: "审核中", id: 0 },
  { name: "已通过", id: 1 },
  { name: "已拒绝", id: 2 },
  { name: "已撤销", id: -1 },
]);

onMounted(() => {
  getConfigSearch(3);
});
import { approveConfigSearchApi } from "@/api/business";
const getConfigSearch = (id) => {
  approveConfigSearchApi(id).then((res) => {
    const datas = res.data ? res.data : [];
    datas.unshift({ name: "所有类型", id: "" });
    data.usersData = datas;
  }).catch((error) => {
    message.error(error.message);
  });
};



// 状态选择
const bindPickerChange = (e) => {
  const len = e.detail.value;
  data.typeText = typeData[len].name;
  formData.status = typeData[len].id;
  emit("change", formData);
};

// 类型选择
const changeUsers = (e) => {
  const len = e.detail.value;
  data.usersText = data.usersData[len].name;
  formData.approveId = data.usersData[len].id;
  emit("change", formData);
};

// 清除日期
const clickClear = () => {
  data.timeText = "所有日期";
  formData.time = "";
  emit("change", formData);
};

// 打开时间选择器
const openTime = () => {

	timePopupRef.value.popupOpen();
};
// 选择时间
const changeTime = (value) => {

	if (value.timeText) {
		data.timeText = value.timeText;
		formData.time = value.time;
	} else {
		formData.time = "";
		data.timeText = "所有日期";
	}
	emit("change", formData);
};


</script>

<style scoped lang="scss">
.cr-search-content {
  height: 72rpx;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  display: flex;
  align-items: center;

  .flex-item {
    width: 100%;
    display: flex;
    justify-content: center;
       font-family: PingFang SC, PingFang SC;
font-weight: 400;
font-size: 22rpx;
color: #606266;
.iconfont {
font-size: 20rpx;
color: #606266;
}

  }

  .search-default-label {
 


  }
}
</style>
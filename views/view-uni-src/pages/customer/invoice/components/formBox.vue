<template>
  	<uni-search-bar  :placeholder="$t('ui.customerInvoiceFormBoxSearchInvoice')"  @blur="search" @change="search"
			bgColor="#F5F5F5" v-model="formData.name" @clear="search" >

       <template v-slot:searchIcon>
					<text class="iconfont icon-sousuo1"></text>
				</template>
		</uni-search-bar>
  <view class="cr-search-content">
      <picker class="picker-selector" mode="selector" @change="changeUsers" 
        :range="viewData" range-key="name">
        <view class="search-default-label">{{data.usersText}} <text class="iconfont icon-jinru"></text></view>
      </picker>
    <picker class="picker-selector" mode="selector" @change="changeStatus" :value="data.typeIndex"
      :range="invoiceStatus" range-key="name">
      <view class="search-default-label">{{data.labelText}} <text class="iconfont icon-jinru"></text></view>
    </picker>

    <picker class="picker-selector" mode="selector" @change="bindPickerChange" :value="data.typeIndex" :range="typeData"
      range-key="name">
      <view class="search-default-label">{{data.typeText}} <text class="iconfont icon-jinru"></text></view>
    </picker>

    <view class="picker-selector">
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
  typeText: "发票类型",
  timeText: "创建日期",
  usersText: "我负责的",
  labelText: "审核状态",
  typeIndex: 0,
  usersData: []
});

const formData = reactive({
  time: "",
  view_search: 1,
  status: "",
  types: "",
  name:''
});

const timeRef = ref(null);
const timePopupRef = ref(null);

let emit = defineEmits(["change"]);

const typeData = reactive([
  { name: "所有类型", id: "" },
  { name: "个人普通发票", id: 1 },
  { name: "企业普通发票", id: 2 },
  { name: "企业专用发票", id: 3 },
]);
const viewData = reactive([
  { name: "我负责的", id: 1 },
  { name: "下属负责的", id: 2 },
]);

const invoiceStatus = reactive([
  { name: "所有状态", id: "" },
  { name: "发票作废", id: -1 },
  { name: "待审核", id: 0 },
  { name: "待开票", id: 1 },
  { name: "已拒绝", id: 2 },
  { name: "撤回开票", id: 3 },
  { name: "申请作废", id: 4 },
  { name: "已开票", id: 5 },

]);

onMounted(() => {
  getSalesman();
});

import { enterpriseSalesmanApi } from "@/api/public";
const getSalesman = () => {
  enterpriseSalesmanApi().then((res) => {
    const datas = res.data ? res.data : [];
    datas.unshift({ name: "所有人员", id: "" });
    data.usersData = datas;
  }).catch((error) => {
    message.error(error.message);
  });
};

// 状态选择
const bindPickerChange = (e) => {
  const len = e.detail.value;
  data.typeText = typeData[len].name;
  if (data.typeText === "所有类型") {
    data.typeText = "发票类型";
  }
  formData.types = typeData[len].id;
 
  emit("change", formData);
};
const search = (e) => {
  emit("change", formData);
}

// 类型选择
const changeUsers = (e) => {
  const len = e.detail.value;
  data.usersText = viewData[len].name;
  formData.view_search = viewData[len].id;  
  emit("change", formData);
};

// 类型状态
const changeStatus = (e) => {
  const len = e.detail.value;
  data.labelText = invoiceStatus[len].name;
  if (data.labelText === "所有状态") {
    data.labelText = "审核状态";
  }
  formData.status = invoiceStatus[len].id;
  emit("change", formData);
};

// 清除日期
const clickClear = () => {
  data.timeText = "创建日期";
  formData.time = "";
  emit("change", formData);
};

// 打开时间选择器
const openTime = (item, index) => {

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

// 清除日期
// const clickClear = () => {
//   timeRef.value.clear();
// };

// 选择时间
// const changeTime = (e) => {
//   if (e.length > 0) {
//     const time = e[0].replace(/-/g, "/") + "-" + e[1].replace(/-/g, "/");
//     data.timeText = e[0].substring(5).replace("-", "/") + "-" + e[1].substring(5).replace("-", "/");
//     formData.time = time;
//   } else {
//     data.timeText = "所有日期";
//     formData.time = "";
//   }
//   emit("change", formData);
// };
</script>

<style scoped lang="scss">
  ::v-deep .uni-searchbar__cancel {
	display: none;
}
::v-deep .uni-searchbar__box-search-input {
  font-weight: 400;
font-size: 24rpx;
color: #909399;
}
.cr-search-content {
  height: 72rpx;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  display: flex;
  align-items: center;

  .picker-selector {
    width: 100%;
    display: flex;
    justify-content: center;
       font-family: PingFang SC, PingFang SC;
font-weight: 400;
font-size: 24rpx;
color: #606266;
.iconfont {
font-size: 20rpx;
color: #606266;
}

  }

  
}
::v-deep .uni-searchbar__text-placeholder {
	font-size: 24rpx;
	color: #909399;
}
::v-deep .uni-input-input {
	font-size: 24rpx;
	color: #303133;
}
 ::v-deep .uni-input-placeholder, .uni-input-input {
  font-size: 24rpx !important;
  color: #909399;
}

::v-deep .uni-searchbar__box-icon-search{
  padding-right: 8rpx !important;
}
</style>

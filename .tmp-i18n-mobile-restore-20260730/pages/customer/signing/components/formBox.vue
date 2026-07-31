<template>

  <view>
    <!-- 输入框搜索 -->
    <uni-search-bar placeholder="搜索合同"  bgColor="#F5F5F5" v-model="data.where.name" @clear="searchFn" @blur="searchFn" @change="searchFn">
    
    <template v-slot:searchIcon>
					<text class="iconfont icon-sousuo1"></text>
				</template>
    </uni-search-bar>
    <!-- 动态筛选 -->
    <view class="cr-search-content" style="margin-top: 12rpx;">
      <!-- 人员选择 -->
      <view v-for="(item, index) in search" :key="index">
        <!-- 下拉选择 -->
        <picker v-if="item.type === 'select'" class="picker-selector" mode="selector"
          @change="bindPickerChange($event, item)" :value="item.value" :range="item.options" range-key="text">
          <view class="search-default-label">{{ item.text }} 
           <text class="date-open-icon iconfont icon-jinru" v-if="!data.where[item.key]"></text>
							<text class="date-open-icon iconfont icon-guanbi" v-else @click.stop="clearFn(item)"></text>
          
          </view>
        </picker>

        <!-- 日期筛选 -->
        <view class="picker-selector" v-if="item.type == 'time'">
          <view class="search-default-label" @click="openTime(item, index)">
            {{ item.text }}
           <text class="date-open-icon iconfont icon-jinru" v-if="!data.where[item.key]"></text>
							<text class="date-open-icon iconfont icon-guanbi" v-else @click.stop="clearFn(item)"></text>
          </view>
        </view>
      </view>
    </view>
    <timePopup ref="timePopupRef" @change="changeTime"></timePopup>
  </view>
</template>

<script setup>
import { ref, reactive, onMounted, toRefs } from "vue";
import message from "@/utils/message";
import timePopup from "@/components/timePopup/index.vue";

const props = defineProps({
  search: {
    type: Array,
    default: () => []
  }
});
const { search } = toRefs(props);
const timePopupRef = ref(null);
const data = reactive({
  where: {},
  searchName: '',
  rowIndex: 0
});



const timeRef = ref(null);
let emit = defineEmits(["change"]);

const clearFn = (item) => {
  data.where[item.key] = ''
  item.text = item.title
  emit("change", data.where);
}
const searchFn = () => {
  emit("change", data.where);
}


// 打开时间选择器
const openTime = (item, index) => {
  data.rowIndex = index;
  timePopupRef.value.popupOpen();
};


// 标签选择回调
const changeItem = (e, name) => {
  formData.customer_label = e;
  if (e.length > 0) {
    data.labelText = name[0];
  } else {
    data.labelText = "标签筛选";
  }
  emit("change", formData);
};

// 下拉选择
const bindPickerChange = (e, item) => {
  const len = e.detail.value;
  item.text = item.options[len].text;
  if (item.text === "全部") {
    item.text = item.title;
  }
  data.where[item.key] = item.options[len].value;
  emit("change", data.where);
};



// 清除日期
const clickClear = () => {
  timeRef.value.clear();
};

// 选择时间
const changeTime = (value) => {
  if (value.timeText) {
    search.value[data.rowIndex].text = value.timeText;
    data.where[search.value[data.rowIndex].key] = value.time;
  } else {
    search.value[data.rowIndex].text = search.value[data.rowIndex].title
    data.where[search.value[data.rowIndex].key] = ''
  }

  emit("change", data.where, data.searchName);

};
</script>

<style scoped lang="scss">
.search-default-label {
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 22rpx;
  color: #606266;
}

.cr-search-content {
  padding: 2rpx 30rpx 20rpx 30rpx;
  display: flex;
  justify-content: space-between;

}
::v-deep .uni-searchbar{
  padding-bottom: 0;
}

::v-deep .uni-searchbar__cancel {
  display: none;
}

.label {
  font-weight: 400;
  font-size: 24rpx;
  color: #303133;
  padding-right: 20rpx;
  border-right: 2rpx solid #DDDDDD;
}

.icon-jinru {
  font-size: 22rpx !important;
  margin-left: 4rpx;
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
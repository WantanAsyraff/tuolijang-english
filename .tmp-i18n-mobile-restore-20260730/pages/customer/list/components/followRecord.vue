<template>
  <view class="follow-main">
    <view class="follow-content">
      <view v-if="showTitle" class="follow-title">
        {{title}}
      </view>
      <steps v-if="followList.length > 0" direction="column" :options="followList" :isEdit="!showTitle" ref="stepsRef" :active="0"
        @editFollow="editFollow">
      </steps>
      <empty v-else :index="7" :title="emptyTitle" style="min-height: 920rpx;"></empty>
    </view>
  </view>
<view class="footer-text" v-if="isFooterText&&followList.length>0&&count<=followList.length">-没有更多了-</view>
</template>

<script setup>
  import empty from "@/components/empty/index.vue";
  import steps from "./followSteps.vue";
  import { ref, toRefs } from "vue";

  const props = defineProps({
    followList: {
      type: Array,
      default () {
        return [{}, {}, {}, {}, {}];
			},
    },
    isFooterText: {
      type: Boolean,
      default: true
    },
    
    	count:{
			type:Number,
			default:0
		},
		emptyTitle: {
		  type: String,
		  default: "暂无动态记录！"
		},
		title: {
			type: String,
			default: "跟进记录"
		},
		showTitle: {
			type: Boolean,
			default: true
		}
  });
  const { followList, emptyTitle,title, showTitle } = toRefs(props);

  import { clickNavigateTo } from "@/utils/helper";
  const emit = defineEmits(["getfollowList"]);
  const stepsRef = ref(null);
  const editFollow = (type, val) => {
    if (type === 3) {
      emit("getfollowList", val.eid);
    }
    if (type === 1) {
      // 编辑
      if (val.link_type === "clue") {
        clickNavigateTo(`/pages/customer/list/addFollow?lead_id=${val.eid}&type=1&data=${JSON.stringify(val)}`);
        return;
      } else if (val.link_type === "odds") {
        clickNavigateTo(`/pages/customer/list/addFollow?odds_id=${val.eid}&type=1&data=${JSON.stringify(val)}`);
        return;
      }
      let typeData = 0;
      if (val.types == 1) {
        typeData = 2;
      } else {
        typeData = 1;
      }
      clickNavigateTo(`/pages/customer/list/addFollow?eid=${val.eid}&type=1&data=${JSON.stringify(val)}`);
    }
  };
</script>

<style lang="scss">
  .follow-main {
    margin-top: 16rpx;

  }

  .follow-title {
    margin-left: 30rpx;
    font-family: PingFang SC, PingFang SC;
    font-weight: 500;
    font-size: 26rpx;
    color: #2B2C32;
    margin-bottom: 36rpx;
  }
  .follow-content {
    background-color: #fff;
    padding: 15px 15px 0 0;
  }

</style>
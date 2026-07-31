<template>
  <view v-if="businessData.length>0" class="customer-tab-info">

    <view class="info-item" v-for="(item,index) in businessData" :key="index">
		<view style="padding: 30rpx;">

	
      <uni-row style="margin-bottom: 12px;">
        <uni-col :span="5" class="text-right info-item-left">{{ $t('ui.customerListBusinessFollowOpportunityNo') }}</uni-col>
        <uni-col :span="19" class="info-item-right">
					<text>{{item.odds_no}}</text>
				</uni-col>
      </uni-row>


			<uni-row style="margin-bottom: 12px;">
			 <uni-col :span="5" class="text-right info-item-left">{{ $t('ui.customerListBusinessFollowOpportunityQuote') }}</uni-col>
			 <uni-col :span="19" class="info-item-right">
				<text>{{item.total_amount}}</text>
			 </uni-col>
			</uni-row>
			<uni-row style="margin-bottom: 12px;">
			 <uni-col :span="5" class="text-right info-item-left">{{ $t('ui.customerContractPayDetailSalesperson') }}</uni-col>
			 <uni-col :span="19" class="info-item-right">
				<text>{{item.salesman.name}}</text>
			 </uni-col>
			</uni-row>
			<uni-row style="margin-bottom: 12px;">
			 <uni-col :span="5" class="text-right info-item-left">{{ $t('ui.customerListBusinessFollowFollowUpTime') }}</uni-col>
			 <uni-col :span="19" class="info-item-right">
				<text>{{item.created_at}}</text>
			 </uni-col>
			</uni-row>
			<uni-row class="bgf">
				<view  class="genjinBox">
					<uni-easyinput :inputBorder="false" v-model="item.content" type="textarea" :clearable="false"
					  :maxlength="256" :placeholder="$t('ui.customerListBusinessFollowEnterOpportunityFollowUp')" class="mb10">
					</uni-easyinput>
					<!-- 文件 -->
					<view class="btn-box">
					  <view class="addfujian" @click="uploadAvatar(item)"> <text class="iconfont icon-fujian"></text>
					    {{ $t('ui.replyComponentIndexAddAttachment') }}</view>
					  <view class="btn" @click="handleConfirm(item)">{{ $t('ui.replyComponentIndexSubmit') }}</view>
					</view>
				</view>
				<view>
					<view class="flie" v-if="item.imgs&&item.imgs.length>0">
					  <view class="box" v-for="(itm, idx) in item.imgs" :key="idx" @click="preview(itm)">
					    <view class="left">
					      <image class="slot-image" :src="itm.src">
					      </image>
					      <view style=" width: calc(100% - 40px);">
					        <view class="name">
					          {{ itm.name }}
					        </view>
					        <view class="size"> {{ formatBytes(itm.size) || '--' }} </view>
					      </view>
					      <view class="iconfont icon-guanbi-yangshiyi1" @click.stop="deleteFile(item,idx)">
					      </view>
					    </view>
					  </view>
					</view>
				</view>
			</uni-row>
			</view>
			<uni-row >
		
				<view class="item-follow-btn" v-if="!item.showList&&getMatchedData(item.id,index).length>0">
					<view @click="toggleList(item,true)">
						<text>{{ $t('ui.customerListBusinessFollowExpand') }}</text>
						<text class="iconfont icon-fanhui" :class="{'pack' : item.showList}"></text>
					</view>	
				</view>
					
				<follow-record v-if="item.showList" :link="`customer`" :followList="getMatchedData(item.id,index)" :isFooterText="false" :showTitle="false" @getfollowList='getfollowList'></follow-record>
		
			<view class="item-follow-btn" v-if="item.showList">
					<view @click="toggleList(item,false)">
						<text>{{ $t('ui.customerListBusinessFollowCollapse') }}</text>
						<text class="iconfont icon-fanhui" :class="{'pack' : item.showList}"></text>
					</view>	
				</view>
			</uni-row>
    </view>
  </view>
</template>

<script setup>
	import { uploadImage,formatBytes } from "@/utils/file";
	import { debounce, fileSizeOne, lookPreview } from "@/utils/helper";
	import { followSaveApi } from "@/api/customer";
  import { reactive, toRefs } from "vue";
  import followRecord from "./followRecord.vue";
	import message from "@/utils/message";
  const props = defineProps({
    businessData: {
      type: Array,
      default: () => {
        return [];
      }
    },
		businessRecord: {
			type: Object,
			default: () => {
			  return {};
			}
		},

  });
	const emit = defineEmits(["initData"]);
  // 图片与文档预览
  const preview = (item) => {
    lookPreview(item.url, item.real_name || item.name, [item.url]);
  };
  const { businessData, businessRecord } = toRefs(props);
	//获取每一项的记录
	const getMatchedData = (id,index) => {
	  const key = id.toString()
		if(index == 0 &&businessRecord.value&& businessRecord.value[key].length>0 && businessData.value[0].showList === undefined) {
			businessData.value[0].showList = true;
		}
	  return businessRecord.value[key] || []
	};
	// 添加图片
	const uploadAvatar = (data) => {
	  const config = {
	    eid: data.id,
	    relation_type: "follow"
	  };
	  if (data.id > 0) {
	    config.relation_id = data.id;
	  }
	  uploadImage("attach/imgs", config, fileSizeOne).then((res) => {
	    data.imgs.push(res.data);
	  }).catch((error) => {
	    message.error(error);
	  });
	};
	
	// 附件删除
	const deleteFile = (item,index) => {
	 item.imgs.splice(index, 1);
	};
	 
	// 展开收起记录
	const toggleList = (item,ishow) => {
					item.showList = ishow;
	};
	// 提交跟进记录
	const handleConfirm = debounce((item) => {
	  if (!item.content) {
	    message.error("跟进信息不能为空");
	    return false;
	  }
	  let attach_ids = item.imgs.map((img) => img.id);
		let parmas = {
			attach_ids: attach_ids,
			eid: item.id,
			content: item.content,
			link_type: "odds",
			types: 0
		}	
	  const task = followSaveApi(parmas);
			task.then((res) => {
				emit("initData");
				message.success(res.message);
			}).catch(err => {
				message.error(err.message);
			});
	});
	const getfollowList = () => {
		emit("initData");
	}
</script>

<style scoped lang="scss">
  .customer-tab-info {
    .info-item {
    //   padding: 30rpx;
      font-size: $uni-font-size-default;
      color: $uni-text-color;
      margin-bottom: 20rpx;
			background: #fff;
      &:last-of-type {
        margin-right: 0;
      }

      .info-item-left {
     
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        color: #606266;
        font-size: 26rpx;
        text-align: left;
      }

      .info-item-right {
    
        line-height: 1.5;
        font-size: 26rpx;
      }
    }
  }


	.bgf {
		background: #fff;
	}
	.btn-box {
	  height: 32px;
	  display: flex;
	  align-items: center;
	  justify-content: space-between;
	  .addfujian {
	    cursor: pointer;
	    font-family: PingFang SC, PingFang SC;
	    font-weight: 400;
	    font-size: 24rpx;
	    color: #282828;
	  }
	  .btn {
	    cursor: pointer;
	    width: 64px;
	    height: 32px;
	    background: #308BF8;
	    border-radius: 6px 6px 6px 6px;
	    display: flex;
	    justify-content: center;
	    align-items: center;
	    background: #308BF8;
	    border-radius: 12rpx;
	    font-family: PingFang SC, PingFang SC;
	    font-weight: 400;
	    font-size: 12px;
	    color: #FFFFFF;
	
	  }
	}
	.genjinBox {
    width: 100%;
    height: 194px;
    border-radius: 6px 6px 6px 6px;
    border: 1px solid #EEEEEE;
    padding: 12rpx 30rpx 24rpx;
  }
  ::v-deep .uni-easyinput__content-textarea {
    height: 120px !important;
  }
  // 上传附件
  .flie {
    width: 100%;
    padding: 24rpx 0rpx 0px 0;
    .box {
      width: 100%;
      height: 40px;
      background: #f6f7f9;
      border-radius: 4px 4px 4px 4px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12rpx;
      padding-right: 10px;
      padding-left: 10px;
      .icon-guanbi-yangshiyi1 {
        cursor: pointer;
        color: #999999;
        margin-top: 7px;
      }
      .left {
        width: 100%;
        display: flex;
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        .slot-image {
          // display: inline-block;
          flex-shrink: 0; // flex布局下图片挤压变形
          width: 52rpx;
          height: 52rpx;
          margin-right: 10rpx;
        }
        .name {
          width: calc(100% - 40px);
          overflow: hidden;
          white-space: nowrap;
          text-overflow: ellipsis;
          font-size: 24rpx;
          color: #303133;
        }
        .size {
          font-size: 20rpx;
          color: #909399;
          margin-top: 2rpx;
        }
      }
    }
  }
	.item-follow-btn {
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 26rpx;
		color: #909399;
		padding-bottom: 20rpx;
		.iconfont {
			display: inline-block;
			transform: rotate(270deg);
			margin: 4rpx 0 0 10rpx;
			font-size: 28rpx;
			&.pack {
				transform: rotate(90deg);
			}
		}
	}

</style>
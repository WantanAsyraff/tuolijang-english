<template>
  <view class="step-list">
    <view class="follow-step-item" v-for="(item, index) in options" :key="index">
      <view class="follow-time">
				<view>{{ item.created_at || item.time || item.updated_at }}</view>
        <view>{{getStatusText(item.type,item.link_type)}}</view>
      </view>
      <view class="follow-content">
        <view class="follow-top" >
          <template v-if="item.card||item.creator">
          <image class="follow-user-avatar" :src="item.card?item.card.avatar:item.creator.avatar" mode="aspectFill" />
          <view class="follow-user-name line1">
            {{ item.card?item.card.name:item.creator.name }} 
						
          </view>
          </template>
          <view v-else>--</view>
          <dean-popover v-if="item.type ==0" ref="deanPopoverRef" model-direction="right">
            <template #icon>
              <text class="iconfont icon-yunwenjian-gengduo" style="cursor: pointer;"></text>
            </template>
            <view class="modal-item" @click="addRecord(1, item, index)"><text
                class="iconfont icon-gongzuohuibao-bianji"></text>编辑
            </view>
            <view class="modal-item" @click="addRecord(2, item, index)"><text class="iconfont icon-shanchu1"></text>删除
            </view>
          </dean-popover>
        </view>
        <view class="follow-body">
          <view class="follow-text">
            {{ item.content || item.reason }}
          </view>
          
          <template v-if="item.attachs.length>0">
            <view class="document" v-for="(img, index) in item.attachs" :key="index">
              <view class="document-left display-align" @click="lookOver(img)">
                <image class="img" v-if="isTypeImage(img.real_name)" :src="img.url" mode=""></image>
                <image class="img" v-else :src="'/static/image/cloudfile/' + isFileTypeIcon(img.real_name)" mode="">
                </image>
                <text class="imgText"> {{ img.real_name }}</text>
              </view>
            </view>
          </template>
          <view class="remind" v-else-if="item.types == 1">
            <image src="@/static/image/cloudfile/tag.png" mode="" class="img"></image>
            提醒时间：{{ item.time }}
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
  import { ref, toRefs } from "vue";
  import deanPopover from "@/components/deanPopover/index.vue";
  import { followDeleteApi } from "@/api/customer";
  import message from "@/utils/message";
  import { showModal, uploadDownload, lookPreview, isTypeImage, isFileTypeIcon } from "@/utils/helper";

  const props = defineProps<{
    options : any[];
		isEdit: Boolean,
    // link: String 
  }>();
	
  const emit = defineEmits(["editFollow"]);
  const { options, isEdit } = toRefs(props);
  const deanPopoverRef = ref(null);
	
	

	
	// 获取类型文本的方法
	const getStatusText = (type,link) => {
      if (link === 'customer') {
        let obj = {
           0: '跟进客户',
          1: '转为公海客户',
          2: '领取客户',
          3: '标为流失',
          4: '取消流失',
          5: '移交客户',
          6: '新增客户',
          7: '线索转客户',
          10: '数据变更'
        }
        return obj[type]
      } else if (link === 'clue') {
        let obj = {
           0: '跟进线索',
          1: '新增线索',
          2: '修改线索',
          3: '领取线索',
          4: '退回线索池',
          5: '转客户',
          6: '转移',
          10: '数据变更'
        }
        return obj[type]
      } else if (link === 'contract') {
        let obj = {
           0: '跟进订单',
          10: '数据变更',
          5: '移交订单',
          6: '新增订单'
        }
        return obj[type]
      } else if (link === 'liaison') {
        let obj = {
          6: '新增联系人',
          10: '数据变更'
        }
        return obj[type]
      } else if (link === 'odds') {
        let obj = {
           0: '跟进商机',
          1: '新增商机',
          2: '修改商机',
          6: '转移',
          10: '数据变更'
        }
        return obj[type]
      }else if (link === 'contract_doc') {
        let obj = {
           0: '跟进合同',
         '-1':'合同签约审批拒绝',
          '1':'新增合同签约',
          '2':'合同签约审批通过',
          '3':'合同签约完成',
          '4':'拒绝合同签约',
          '5':'签约已过期',
          '6':'签约已撤销'
        }
        return obj[type]
      }
	  // return TYPE_MAP[type] || '未知状态'
	};
  const addRecord = (type : number, item : any, index : number) => {
    deanPopoverRef.value[index].close();
    if (type === 2) {
      showModal("您确定要删除此跟进记录吗").then(() => {
        followDeleteApi(item.link_type==='customer'?item.follow_id:item.id).then((res : any) => {
          message.success(res.message);
          emit("editFollow", 3, item);
        }).catch((error : any) => {
          message.error(error.message);
        });
      }).catch(() => {
        console.log("取消了");
      });
    } else {
      emit("editFollow", type, item);
    }
  };
  // 预览
  const lookOver = (item : any) => {
    lookPreview(item.url, item.real_name, [item.url]);
  };
  // 下载
  const lookDownload = (val : any) => {
    uploadDownload(val.url, val.real_name);
  };
</script>

<style scoped lang="scss">
  .step-list {
    padding-bottom: calc(var(--window-bottom) + 20rpx);
  }

  .follow-step {
    padding: 40rpx 0;
    background-color: #fff;
  }

  .follow-step-item {
    padding-left: 64rpx;
    position: relative;

    &:last-child {
      &::before {
        display: none;
      }
    }

    &:first-child {
      &::after {
        background-color: #fff;
        border: 2rpx solid #1890FF;
        box-sizing: border-box;
      }
    }

    &::before {
      content: "";
      position: absolute;
      width: 2rpx;
			height: calc(100% - 12rpx);
      top: 32rpx;
      left: 35rpx;
      bottom: -46rpx;
      background: #eee;
    }

    &::after {
      content: "";
      position: absolute;
      width: 12rpx;
      height: 12rpx;
      background: #1890FF;
      left: 30rpx;
      top: 6rpx;
      border-radius: 50%;
    }

    &+.follow-step-item {
      margin-top: 30rpx;
    }
  }

  .follow-time {
    font-size: 24rpx;
    color: #909399;
    margin-bottom: 20rpx;
		display: flex;
		justify-content: space-between;
  }

  .follow-content {
    background-color: #f7f7f7;
    border-radius: 8rpx;
    padding: 30rpx 24rpx 30rpx 26rpx;
  }

  .follow-top {
    display: flex;
    align-items: center;
    font-size: 26rpx;
    color: #666;

    .follow-user-avatar {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      margin-right: 12rpx;
    }

    .follow-user-name {
      flex: 1;
      font-size: 26rpx;
    }
  }

  .follow-text {
    color: #333;
    font-size: 26rpx;
  }

  .follow-body {
    // padding-left: 60rpx;
    margin-top: 22rpx;
  }

  .remind {
    margin-top: 16rpx;
    width: 100%;
    height: 80rpx;
    border-radius: 12rpx;
    background-color: rgba(24, 144, 255, 0.1);
    font-size: 26rpx;
    font-family: PingFang SC-Regular, PingFang SC;
    font-weight: 400;
    color: #1890FF;
    padding-left: 18rpx;
    display: flex;
    align-items: center;

    .img {
      width: 34rpx;
      height: 38rpx;
      display: block;
      margin-right: 12rpx;
    }
  }

  .document {
    margin-top: 16rpx;
    width: 100%;
    height: 80rpx;
    border-radius: 12rpx;
    background: #FFFFFF;
    font-size: 26rpx;
    font-weight: 400;
    color: #333333;
    display: flex;
    align-items: center;
    padding-left: 20rpx;

    .document-left {
      // width: calc(100% - 60rpx);
      overflow: hidden;

      .img {
        display: block;
        width: 40rpx;
        height: 40rpx;
        margin-right: 12rpx;
      }

      .imgText {
        display: inline-block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: calc(100% - 52rpx);
        font-size: 26rpx;
      }
    }

    .document-right {
      width: 60rpx;
      text-align: center;
    }

  }
</style>

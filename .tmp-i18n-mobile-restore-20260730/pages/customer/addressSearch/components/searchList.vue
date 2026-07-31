<template>
  <view>
    <view class="content">
      <uni-list :border="false" v-if="listData.length > 0">
        <uni-list-item v-for="(item,index) in listData" :key="'items'+index">
					<template v-slot:header>
					  <view class="address-list-item-left" @click.native="listItemClick(item)">
					    <text class="iconfont icon-kaoqin-dingwei"></text>
					  </view>
					</template>
          <template v-slot:body>
            <view class="address-list-item-right" @click.native="listItemClick(item)">
              <uni-row class="address-list-item-right-top display-center">
                <uni-col :span="18">
									<view class="right-top-ttile">
										<text class="right-top-keyword">{{item.keyword}}</text>{{item.name}}
									</view>
									<view class="right-top-address">{{item.address}}</view>
								</uni-col>
                <uni-col :span="6" class="right-top-distance text-right">
                  <text>{{item.distance}}</text>
                </uni-col>
              </uni-row>
              
            </view>
          </template>
        </uni-list-item>
      </uni-list>
      <empty v-else :index="5" :title="emptyTitle"></empty>
    </view>
  </view>
</template>

<script setup>
import empty from "@/components/empty/index";
import message from "@/utils/message";
import { formatDate } from "@/utils/schedule";
const props = defineProps({
  listData: {
    type: Array,
    default() {
      return [];
    }
  },
  emptyTitle: {
    type: String,
    default: ""
  },
});
const { listData, emptyTitle } = toRefs(props);

import { clickNavigateTo } from "@/utils/helper";
const listItemClick = (item) => {
  // if (item.count > 0) {
  //   messageCate(item.id, 1)
  // }
  clickNavigateTo(`/pages/notice/info?id=${item.id}&title=${item.cate_name}`);
};

import { userMessageHandleCateApi } from "@/api/user";
const messageCate = (id, status) => {
  userMessageHandleCateApi(id, status).then((res) => {
    // message.success( res.message )
  }).catch((error) => {
    message.error(error.message);
  });
};
</script>

<style scoped lang="scss">
  .content {
    width: 100%;
	
    ::v-deep .uni-list {
			border-radius: 12rpx;
      .uni-list-item {
				padding: 20rpx;
        position: relative;

        &::after {
          position: absolute;
          bottom: 0;
          left: 0;
          content: '';
          width: 100%;
          border-bottom: 1px solid $uni-line-style-color-three;
        }

        &:last-of-type {
          margin-bottom: 0;
        }

        .uni-list-item__container {
          padding: 0;
          
        }
      }

      .uni-list--border {
        left: auto;
        top: auto;
      }
    }

    .address-list-item-left {
      .iconfont {
				display: block;
        margin-top: 7rpx;
      }
    }
		.right-top-keyword {
			color: #1890FF;
		}
    .address-list-item-right {
      width:100%;
      padding-left: 24rpx;

      .address-list-item-right-top {
        font-size: $uni-font-size-default;
        font-weight: 400;
        color: $uni-text-color;
        line-height: 1.5;

        .right-top-distance {
          font-weight: normal;
          font-size: 22rpx;
          color: $nui-text-color-four;
        }
      }
			.right-top-address {
				margin-top: 6rpx;
				font-size: 24rpx;
				word-wrap: break-word;
				color: $nui-text-color-four;
				line-height: 1.5;
			}
      
    }
  }
</style>

<template>
  <view>
    <view class="p_box">
      <view v-for="(item, index) in list" :key="index">
        <view class="flex">
          <view class="p_left">
            <image :src="item.from_user.avatar" alt="" class="avatar" />
          </view>
          <view class="p_right">
            <view class="flex-between">
              <view class="flex-name">
                <text class="p_name">{{ item.from_user.name }}</text>
                <text class="p_time">{{ item.created_at }} </text>
              </view>
              <view>
                <dean-popover v-if="item.from_user.id == data.userId" ref="deanPopoverRef" model-direction="right"
                  :btnList="data.btnList" @select="selectFn($event, item)">
                  <template #icon>
                    <text class="iconfont icon-gengduo"></text>
                  </template>
                </dean-popover>
                <image v-else src="/static/image/schedule/pinglun.png" mode="" class="img" @click="replyFn(item)" />
              </view>
            </view>
            <view class="p_content">
              <view> {{ item.content }}</view>
            </view>
            <view class="flie" v-if="item.files.length>0">
              <view class="box" v-for="(file, indexs) in item.files" :key="indexs" @click="preview(file)">
                <view class="left">
                  <image class="slot-image" :src="file.src">
                  </image>
                  <view style=" width: calc(100% - 40px);">
                    <view class="name">
                      {{ file.name }}
                    </view>
                    <view class="size"> {{ formatBytes(file.size) || '--' }} </view>
                  </view>

                </view>
              </view>
            </view>


            <!-- 二级回复 -->
            <template v-if="item.children.length !== 0">
              <view class="flex mt14" v-for="(per, v) in item.children" :key="v">
                <view class="p_left">
                  <image :src="per.from_user.avatar" alt="" class="avatar" />
                </view>
                <view class="p_right">
                  <view class="flex-between">
                    <view class="flex-name">
                      <text class="p_name">{{ per.from_user.name }}</text>
                      <text class="p_time">{{ item.created_at }} </text>
                    </view>

                    <view>
                      <dean-popover v-if="per.from_user.id == data.userId" ref="deanPopoverRef" model-direction="right"
                        :btnList="data.btnList" @select="selectFn($event, item,per)">
                        <template #icon>
                          <text class="iconfont icon-gengduo"></text>
                        </template>
                      </dean-popover>
                      <image v-else src="/static/image/schedule/pinglun.png" mode="" class="img"
                        @click="replyFn(item,per)" />
                    </view>


                  </view>
                  <view class="p_content ">
                    <view class="huiname">
                      <!-- <text class="p_time huiname"> 回复{{ per.to_user.name }} : </text> -->
                      {{ per.content }}
                    </view>
                  </view>
                  <view class="flie" v-if="per.files.length>0">
                    <view class="box" v-for="(file2, index2) in per.files" :key="index2" @click="preview(file2)">
                      <view class="left">
                        <image class="slot-image" :src="file2.src">
                        </image>
                        <view style=" width: calc(100% - 40px);">
                          <view class="name">
                            {{ file2.name }}
                          </view>
                          <view class="size"> {{ formatBytes(file2.size) || '--' }} </view>
                        </view>
                      </view>
                    </view>
                  </view>
                </view>
              </view>
            </template>
          </view>
        </view>
        <div class="splitLine" v-if="index+1!=list.length"></div>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
  import { formatBytes } from "@/utils/file";
  import { lookPreview, } from "@/utils/helper";
  import deanPopover from "@/components/deanPopover/index.vue";
  import { reactive, toRefs } from "vue";
  const props = defineProps({
    list: {
      type: Array,
      default: () => {
        return [];
      }
    },
  });
  const data = reactive({
    userId: JSON.parse(uni.getStorageSync("storageUserData")).userInfo.id,
    btnList: [{
      icon: "iconfont icon-huifu",
      type: 1,
      name: "回复",
    },
    {
      icon: "iconfont icon-shanchu2",
      type: 2,
      name: "删除",
    },
    ],
  });
  const { list } = toRefs(props);
  // 回复
  const replyFn = (data : any, row : any) : void => {
    emit("replyFn", data, row);
  };
  // 删除
  const commentDel = (row : any) => {
    emit("commentDel", row);
  };
  const selectFn = (val : object, row : object, per : object) => {
    if (val.type == 1) {
      replyFn(row, per)
    } else {
      commentDel(row)
    }
  }
  // 图片与文档预览
  const preview = (item) => {
    lookPreview(item.src, item.name, [item.src]);
  };
  const emit = defineEmits(["replyFn", "commentDel"]);
</script>

<style lang="scss" scoped>
  .reply {
    cursor: pointer;
  }

  .mt14 {
    margin-top: 40rpx;
  }



  .avatar {

    display: block;
    width: 30px;
    height: 30px;
    border-radius: 4px;
  }

  .splitLine {
    width: 100%;
    // height: 2rpx;
    margin: 28rpx 0;
    margin-bottom: 20rpx;
    // border-bottom: 2rpx solid #f7f7f7;
  }

  .flex {
    display: flex;
  }

  .flex-between {
    display: flex;
    justify-content: space-between;

  }

  .icon-gengduo {
    color: #C0C4CC;
  }

  .p_box {
    margin-top: 20rpx;
    width: 100%;
    border-radius: 4rpx;

    .p_left {
      margin-right: 14rpx;


    }

    .p_right {
      width: 100%;
      font-family: PingFang SC-Medium, PingFang SC;
      font-size: 24rpx;

      .img {
        display: block;
        width: 16px;
        height: 16px;
      }

      .flex-name {
        display: flex;
        align-content: center;
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;

        .p_name {
          margin-right: 6px;
          font-size: 13px;
          color: #333333;
          margin-bottom: 8rpx;
        }

        .p_time {
          font-size: 12px;
          color: #909399;
        }
      }



      .p_content {
        font-size: 28rpx;
        font-family: PingFang SC-Regular, PingFang SC;
        font-weight: 400;
        color: #41485B;
        display: flex;
        justify-content: space-between;

        .hui {
          display: block;
          font-size: 22rpx;
          font-family: PingFang SC-Regular, PingFang SC;
          font-weight: 400;
          color: #1890FF;
          flex-shrink: 0;
        }
      }
    }
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


  .icon-shanchu1 {
    font-size: 24rpx;
    color: rgba(192, 196, 204, 1);
  }

  .p_time {
    font-size: 22rpx;
    font-family: PingFang SC-Regular, PingFang SC;
    font-weight: 400;
    color: #909399;
    margin-bottom: 10rpx;
    margin-right: 10rpx;
  }

  .huiname {

    font-size: 26rpx !important;
    font-family: PingFang SC-Regular, PingFang SC;
    font-weight: 400;
    // color: #909399;
  }
</style>
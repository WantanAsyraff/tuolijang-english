<template>
  <view>
    <uni-popup ref="popupRef" type="bottom" :mask-click="false">
      <view class="slider">
        <view class="share-header display-align">
          <view class="plr10">
            <uni-row class="share-header-top display-align">
              <uni-col :span="20" class="line1-1">{{ dataInfo.customer_name }}</uni-col>
              <uni-col :span="4" class="text-right">
                <view @click="cancel" class="iconfont icon-guanbi-yangshiyi1"></view>
              </uni-col>
            </uni-row>
            <view class="share-header-bottom">
              <text class="text">业务员：{{ dataInfo.salesman ? dataInfo.salesman.name : '--' }}</text>
              <view class="text">{{ timeTitle }}：<uni-dateformat format="yyyy/MM/dd hh:mm"
                  :date="dataInfo.created_at ? dataInfo.created_at : dataInfo.last_follow_time">
                </uni-dateformat>
              </view>
            </view>
          </view>
        </view>
        <view class="share-list plr10">
          <view class="share-list-item" v-for="item in data.listData" :key="item.type" @click="shareClick(item.type)">
            <text class="iconfont" :class="item.icon"></text>
            <view class="share-name">{{ item.text }}</view>
          </view>
        </view>

        <view class="customer-list plr10">
          <uni-row class="customer-list-item display-align" v-for="item in data.tableData" :key="'list' + item.type"
            @click="customerClick(item.type)">
            <uni-col :span="4" class="list-item-left">
              <text class="iconfont" :class="item.icon"></text>
            </uni-col>
            <uni-col :span="20" class="list-item-right">{{ item.text }}</uni-col>
          </uni-row>
        </view>
      </view>
    </uni-popup>
    <selected-label title="客户标签" ref="selectedLabelRef" @changeItem="changeItem"> </selected-label>
    <!-- 备注弹窗 -->
    <textarea-popup ref="textareaPopupRef" :config-data="data.configData" @change="changePop" />
    <bottom-action-sheet ref="sheetRef" @select="handleChangeStatus" />
  </view>
</template>

<script setup lang="ts">
  import { ref, reactive, toRefs } from "vue";
  import message from "@/utils/message";
  import textareaPopup from "@/components/textareaPopup/index.vue";
  import selectedLabel from "./selectedLabel.vue";
  import BottomActionSheet from "@/components/BottomActionSheet/index.vue";

  import {
    clientDeleteApi,
    clientStatusApi,
    clientContractLabelApi,
    customerReturnApi,
    clientlostApi,
    clientCancelLostApi,
    clientclaimApi,
    leadRefundApi,
    leadClaimApi,
    dictSelectApi,
    leadEditFormApi,
    leadEditApi,
    leadDeleteApi,
    opportunityEditFormApi,
    opportunityEditApi,
    opportunityDelApi
  } from "@/api/customer";
  const props = defineProps({
    typeData: {
      type: Array,
      default: () => {
        return [];
      }
    },
    types: {
      type: Number,
      default: () => {
        return 1;
      }
    },
    dataInfo: {
      type: Object,
      default: () => {
        return {};
      }
    },
    timeTitle: {
      type: String,
      default: "创建时间"
    }
  });
  const { dataInfo, types } = toRefs(props);
  const emit = defineEmits(["handleItem", "change"]);
  const popupRef = ref(null);
  const textareaPopupRef = ref(null);
  const formData = reactive({ label: [] });
  const sheetRef = ref<InstanceType<typeof BottomActionSheet>>(null);

  const data = reactive({
    type: 1,
    configData: {
      title: "退回客户公海",
      placeholder: "说明原因",
      type: "",
      text: "",
      refundType: 0, // 4 -> 客户公海 802 -> 线索池
    },
    listData: [
      { type: 1, icon: "icon-danchuang-bianji", text: "编辑" },
      { type: 2, icon: "icon-danchuang-tianjiahetong", text: "添加订单" },
      { type: 3, icon: "icon-tianjiashangji", text: "添加商机" },

    ],
    tableData: [
      // { type: 5, icon: "icon-danchuang-genjinzhong", text: "填写跟进" },
      { type: 1, icon: "icon-danchuang-zhuanyi", text: "移交同事" },
      { type: 4, icon: "icon-tuihuixiansuochi", text: "退回公海" },
      // { type: 6, icon: "icon-xiugaizhuangtai", text: "修改状态" },
      // { type: 2, icon: "icon-danchuang-shezhibiaoqian", text: "设置标签" },
      { type: 3, icon: "icon-danchuang-shanchu", text: "删除客户" },
    ],
  });

  // 打开弹出
  const popupOpen = () => {
    popupRef.value.open();
    if (types.value == 3) {
      data.listData = [
        { type: 1, icon: "icon-danchuang-bianji", text: "编辑" },
        { type: 4, icon: "icon-danchuang-liushi", text: dataInfo.value.customer_status == 2 ? "取消流失" : "标为流失" },
        { type: 5, icon: "icon-danchuang-lingqu", text: "领取" },
      ];

      data.tableData = [
        { type: 1, icon: "icon-danchuang-zhuanyi", text: "分配" },
        // { type: 2, icon: "icon-danchuang-shezhibiaoqian", text: "设置标签" },
        { type: 3, icon: "icon-danchuang-shanchu", text: "删除客户" },
      ];
    } else if (types.value == 8) {
      // 线索弹窗

      data.listData = [
        { type: 81, icon: "icon-danchuang-bianji", text: "编辑" },
        { type: 84, icon: "icon-danchuang-zhuanyi", text: "移交同事" },
        { type: 85, icon: "icon-danchuang-genjinzhong", text: "填写跟进" },
      ];

      const unassignedList = [
        { type: 805, icon: "icon-fenpei", text: "分配" },
        { type: 806, icon: "icon-lingqu", text: "领取" },
      ];

      const assignedList = [
        // { type: 801, icon: "icon-danchuang-zhuanyi", text: "移交同事" },
        { type: 802, icon: "icon-tuihuixiansuochi", text: "退回线索池" },
      ];

      data.tableData = (dataInfo.value.salesman ? assignedList : unassignedList).concat([
        { type: 803, icon: "icon-xiugaizhuangtai", text: "修改状态" },
        { type: 804, icon: "icon-danchuang-shanchu", text: "删除线索" },
      ]);
    } else if (types.value == 9) {
      // 商机弹窗
      data.listData = [
        { type: 91, icon: "icon-danchuang-bianji", text: "编辑" },
        { type: 94, icon: "icon-danchuang-tianjiahetong", text: "添加订单" },
        { type: 95, icon: "icon-danchuang-genjinzhong", text: "填写跟进" },
      ];

      data.tableData = [
        { type: 901, icon: "icon-danchuang-zhuanyi", text: "移交同事" },
        { type: 903, icon: "icon-xiugaizhuangtai", text: "修改状态" },
        { type: 904, icon: "icon-danchuang-shanchu", text: "删除商机" },
      ];
    }
  };

  // 关闭
  const cancel = () => {
    popupRef.value.close();
  };

  import { clickNavigateTo, delayedNavigateBack } from "@/utils/helper";
  // 列表点击
  const shareClick = (type : number) => {
    if (type === 1) {
      cancel();
      clickNavigateTo(`/pages/customer/list/addCustomer?eid=${dataInfo.value.id}`);
    }
    if (type === 2) {
      clickNavigateTo(
        `/pages/customer/contract/addContract?eid=${dataInfo.value.id}&name=${dataInfo.value.customer_name}`);
    }
    if (type === 3) {
      clickNavigateTo(`/pages/customer/opportunity/add?eid=${dataInfo.value.id}`);
    }
    if (type == 4) {
      if (dataInfo.value.customer_status == 2) {
        clientCancelLost(dataInfo.value.id);
      } else {
        clientLost(dataInfo.value.id);
      }
    }
    if (type == 5) {
      clientclaim(dataInfo.value.id);
    } else if (type == 81) {
      cancel();
      clickNavigateTo(`/pages/customer/lead/add?id=${dataInfo.value.id}`);
    } else if (type == 84) {
      // 移交同事
      emit("handleItem", 801);

    } else if (type == 85) {
      cancel();
      // 填写跟进
      clickNavigateTo(`/pages/customer/list/addFollow?lead_id=${dataInfo.value.id}&type=1`);
    } else if (type == 91) {
      // 编辑商机
      cancel();
      clickNavigateTo(`/pages/customer/opportunity/add?id=${dataInfo.value.id}`);
    } else if (type == 94) {
      const key = `odds_${dataInfo.value.id}`;
      uni.setStorageSync(key, { 
        customer_name: dataInfo.value.customer_name,
        customer_id: dataInfo.value.contract_customer?.id,
        product: dataInfo.value.product
      });
      // 添加订单
      clickNavigateTo(`/pages/customer/contract/addContract?odds_id=${dataInfo.value.id}&name=${dataInfo.value.customer_name}`);
    } else if (type == 95) {
      cancel();
      clickNavigateTo(`/pages/customer/list/addFollow?odds_id=${dataInfo.value.id}&type=1`);
    }
  };

  // 领取客户
  const clientclaim = (id) => {
    uni.showModal({
      title: "提示",
      content: "您确定要领取将此客户吗?",
      success: (res) => {
        if (res.confirm) {
          clientclaimApi(id).then((res) => {
            if (res.status === 200) {
              message.success(res.message);
              delayedReLaunch("/pages/customer/list/index");
            }
          }).catch((error) => {
            message.error(error.message);
          });
        }
      }
    });
  };

  // 取消流失
  const clientCancelLost = (id) => {
    uni.showModal({
      title: "提示",
      content: "您确定要将此客户取消流失吗?",
      success: (res) => {
        if (res.confirm) {
          clientCancelLostApi(id).then((res) => {
            if (res.status === 200) {
              message.success(res.message);
              delayedReLaunch("/pages/customer/list/index");
            }
          }).catch((error) => {
            message.error(error.message);
          });
        }
      }
    });
  };
  // 标为流失
  const clientLost = (id) => {
    uni.showModal({
      title: "提示",
      content: "您确定要将此客户标为流失吗?",
      success: (res) => {
        if (res.confirm) {
          clientlostApi(id).then((res) => {
            if (res.status === 200) {
              message.success(res.message);
              delayedReLaunch("/pages/customer/list/index");
            }
          }).catch((error) => {
            message.error(error.message);
          });
        }
      }
    });
  };
  const selectedLabelRef = ref(null);
  // 标签选择回调
  const changeItem = (e) => {
    formData.label = e;
    let id = dataInfo.value.id;
    clientContractLabelApi(id, formData).then((res) => {
      message.success(res.message);
      emit("change");
      cancel();
    });
  };
  // 退回公海池
  const changePop = (params) => {

    if (data.configData.refundType == 4) {
      let form = {
        reason: params.value
      };
      customerReturnApi(dataInfo.value.id, form).then((res) => {
        message.success(res.message);
      }).catch((err) => {
        message.error(err.message);
      });
    } else if (data.configData.refundType == 802) {
      const formData = {
        reason: params.value,
        data: [dataInfo.value.id]
      };
      leadRefundApi(formData).then((res) => {
        message.success(res.message);
        emit("change");
      }).catch((err) => {
        message.error(err.message);
      });
    }

  };

  // 展现形式选择
  import { delayedReLaunch } from "@/utils/helper";
  const customerClick = async (type) => {
    cancel();
    if (type === 1) {
      clickNavigateTo(`/pages/customer/list/shift?type=1&eid=${dataInfo.value.id}`);
    } else if (type === 2) {
      let id = [];

      if (dataInfo.value.customer_label && dataInfo.value.customer_label.length > 0) {
        dataInfo.value.customer_label.map((item) => {
          id.push(item.id);
        });
      }

      selectedLabelRef.value.popupOpen(id);
    } else if (type === 3) {
      let liaisonId = dataInfo.value.id;
      uni.showModal({
        title: "提示",
        content: "您确定要删除该客户吗?",
        success: (res) => {
          if (res.confirm) {
            clientDeleteApi(liaisonId).then((res) => {
              if (res.status === 200) {
                message.success(res.message);
                delayedReLaunch("/pages/customer/list/index");
              }
            }).catch((error) => {
              message.error(error.message);
            });
          }
        }
      });
    } else if (type === 4) {
      data.configData.refundType = 4;
      data.configData.title = "退回客户公海";
      textareaPopupRef.value.popupOpen();
    } else if (type === 5) {
      clickNavigateTo(`/pages/customer/list/addFollow?eid=${dataInfo.value.id}&type=1`);
    } else if (type === 6) {
      sheetRef.value.openActionSheet({
        title: "修改客户状态",
        type: "client_status",
        options: [
          {
            label: "暂未成交",
            value: "1"
          },
          {
            label: "已成交",
            value: "2"
          },
          {
            label: "已放弃",
            value: "3"
          }
        ]
      });
    } else if (type == 802) {
      data.configData.refundType = 802;
      data.configData.title = "退回线索池";
      textareaPopupRef.value.popupOpen();
    } else if (type == 806) {
      uni.showModal({
        title: "提示",
        content: "您确定要领取此线索吗?",
        success: (res) => {
          if (res.confirm) {
            leadClaimApi({
              data: [dataInfo.value.id]
            }).then((res) => {
              message.success(res.message);
              emit("change");
            }).catch((err) => {
              message.error(err.message);
            });
          }
        }
      });
    } else if (type == 803) {
      const res = await dictSelectApi({
        types: "clue_status"
      });
      if (!res.data.length) return;
      sheetRef.value.openActionSheet({
        title: "修改线索状态",
        type: "clue_status",
        options: res.data.map((item) => ({
          label: item.name,
          value: item.id
        }))
      });
    } else if (type == 804) {
      uni.showModal({
        title: "提示",
        content: "您确定要删除此线索吗?",
        success: (res) => {
          if (res.confirm) {
            leadDeleteApi(dataInfo.value.id).then((res) => {
              message.success(res.message);
              delayedNavigateBack();
            }).catch((err) => {
              message.error(err.message);
            });
          }
        }
      });
    } else if (type == 903) {
      const res = await dictSelectApi({
        types: "odds_status"
      });
      if (!res.data.length) return;
      sheetRef.value.openActionSheet({
        title: "修改商机状态",
        type: "odds_status",
        options: res.data.map((item) => ({
          label: item.name,
          value: item.id
        }))
      });
    } else if (type == 904) {
      uni.showModal({
        title: "提示",
        content: "您确定要删除此商机吗?",
        success: (res) => {
          if (res.confirm) {
            opportunityDelApi(dataInfo.value.id).then((res) => {
              message.success(res.message);
              delayedNavigateBack();
            }).catch((err) => {
              message.error(err.message);
            });
          }
        }
      });
    } else {
      emit("handleItem", type);
    }
  };

  const handleChangeStatus = async ({ type, value } : any) => {
    uni.showLoading({ mask: true });
    if (type == "client_status") {
      // 修改客户成交状态，暂废弃
      // try {
      //   await clientStatusApi(dataInfo.value.id, value);
      //   uni.hideLoading();
      //   message.success("修改成功");
      // } catch (error: any) {
      //   uni.hideLoading();
      //   message.error(error.message || error);
      // }
    } else if (type == "clue_status") {
      try {
        const formRes = await leadEditFormApi(dataInfo.value.id);
        const formData = formRes.data
          .map((item : any) => item.data)
          .flat()
          .reduce((acc : any, item : any) => {
            acc[item.key] = item.value;
            return acc;
          }, {});
        formData["status"] = value;
        await leadEditApi(dataInfo.value.id, formData);
        uni.hideLoading();
        message.success("修改成功");
        setTimeout(() => {
          emit("change");
        }, 500);
      } catch (error : any) {
        uni.hideLoading();
        message.error(error.message || error);
      }
    } else if (type == "odds_status") {
      try {
        const formRes = await opportunityEditFormApi(dataInfo.value.id);
        const formData = formRes.data.list
          .map((item : any) => item.data)
          .flat()
          .reduce((acc : any, item : any) => {
            acc[item.key] = item.value;
            return acc;
          }, {});
        formData["status"] = value;
        await opportunityEditApi(dataInfo.value.id, formData);
        uni.hideLoading();
        message.success("修改成功");
        setTimeout(() => {
          emit("change");
        }, 500);
      } catch (error : any) {
        uni.hideLoading();
        message.error(error.message || error);
      }
    }
  };

  const changeStatus = (e) => {
    const len = e.detail.value;
    if (dataInfo.value.status !== len) {
      let liaisonId = dataInfo.value.id;
      let data = {
        status: len,
        types: 0
      };
      uni.showModal({
        title: "提示",
        content: "您确定要修改此状态吗?",
        success: (res) => {
          if (res.confirm) {
            clientStatusApi(liaisonId, data).then((res) => {
              if (res.status === 200) {
                message.success(res.message);
                emit("change");
                cancel();
              }
            }).catch((error) => {
              message.error(error.message);
            });
          }
        }
      });
    }
  };

  defineExpose({ popupOpen });
</script>

<style lang="scss" scoped>
  ::v-deep .uni-popup {
    z-index: 100;
  }

  .slider {
    position: relative;
    // height: 740rpx;
    width: 100%;
    background-color: #fff;
    border-radius: 16rpx 16rpx 0px 0px;

    .share-header {
      width: 100%;
      height: 156rpx;
      position: relative;
      border-bottom: 1px solid rgba(236, 237, 240, 0.5);

      .plr10 {
        width: 100%;
      }

      .share-header-top {
        font-size: $uni-font-size-default;
        color: $uni-text-color;
        font-weight: $uni-default-font-weight;

        .iconfont {
          font-weight: normal;
          font-size: 40rpx;
          color: $uni-text-color-five;
        }
      }

      .share-header-bottom {
        font-size: 24rpx;
        margin-top: 20rpx;
        color: $nui-text-color-four;

        .text {
          margin-right: 20rpx;
          padding-right: 24rpx;
          display: inline-block;

          &:last-of-type {
            padding-right: 0;
          }
        }
      }
    }

    .share-list {
      width: 100%;
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      padding-top: 34rpx;

      .share-list-item {
        cursor: pointer;
        width: calc((100% - 40rpx)/3);
        text-align: center;
        margin-right: 20rpx;
        background-color: #f7f7f7;
        border-radius: 8rpx;
        padding: 24rpx 0;

        &:last-of-type {
          margin-right: 0;
        }

        .iconfont {
          font-size: 46rpx;
          color: $uni-text-color;
        }

        .share-name {
          font-size: 28rpx;
          font-weight: 400;
          color: $nui-text-color-two;
          margin-top: 10rpx;
        }
      }
    }

    .customer-list {
      width: 100%;
      padding-top: 20rpx;

      .customer-list-item {
        cursor: pointer;
        width: 100%;

        .list-item-left {
          width: 44rpx;

          .iconfont {
            font-size: 44rpx;
            color: $uni-text-color;
          }

        }

        .list-item-right {
          width: calc(100% - 44rpx);
          padding-left: 12rpx !important;
          padding: 30rpx 0;
          border-bottom: 1px solid rgba(236, 237, 240, 0.5);
          font-size: 28rpx;
          color: $nui-text-color-two;
        }

        &:last-of-type {
          .list-item-right {
            border-bottom: none;
          }
        }
      }
    }
  }

  .icon-guanbi-yangshiyi1 {
    cursor: pointer;
  }
</style>
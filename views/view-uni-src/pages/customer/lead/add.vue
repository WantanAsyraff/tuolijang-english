<template>
  <BaseContainer class="base-container">
    <view class="head-wrap">

      <NavBar :is-right="true" :defaultTitle="leadId ? $t('mobile.ui.navigation.editLead') : $t('mobile.navigation.pages/customer/lead/add')" />
    </view>
    <view class="form-card">
      <oaForm :listData="formDesignInfo" ref="formRef" @submitOk="handleSubmit" />
    </view>
    <view class="placeholder-box" :class="isOaWangeditor?'placeholder-bottom':''"></view>

    <view :class="isOaWangeditor?'bottom-box':''">
      <BaseBottomBtn :text="$t('ui.replyComponentIndexSubmit')" @click="handleEmitSubmit" />
    </view>
  </BaseContainer>
</template>

<script setup lang="ts">
  import BaseContainer from "@/components/BaseContainer/index.vue";
  import BaseBottomBtn from "@/components/BaseBottomBtn/index.vue";
  import NavBar from "@/components/defaultNavBar/index.vue";
  import oaForm from "@/components/oaForm/index.vue";
  import { leadAddApi, leadAddFormApi, leadEditFormApi, leadEditApi } from "@/api/customer";

  const formRef = ref<InstanceType<typeof oaForm>>();
  const formDesignInfo = ref(null);
  const leadId = ref(null);
  const types = ref('')
  const isOaWangeditor = ref(false)
  const handleEmitSubmit = () => formRef.value.submit();
  // 提交数据
  const handleSubmit = async (value : any) => {
    uni.showLoading({ mask: true });
    if(types.value){
      value.types = types.value
    }
    try {
      const task = leadId.value ? leadEditApi(leadId.value, value) : leadAddApi(value);
      const res = await task;
      uni.hideLoading();
      uni.showToast({
        title: res.message,
        icon: "success"
      });
      // setTimeout(() => {
        uni.navigateBack();
      // }, 800);
    } catch (error) {
      uni.hideLoading();
      uni.showToast({
        title: error.message,
        icon: "none"
      });
    }
  };

  const deepAddProp = (list : any[], source : string, target : string) => {
    for (const item of list) {
      item[target] = item[target] || item[source];
      if (item.children) {
        deepAddProp(item.children, source, target);
      }
    }
  };

  const getFormDesignInfo = async () => {
    try {
      const res = leadId.value ? await leadEditFormApi(leadId.value,{edit:1}) : await leadAddFormApi();
      const form = leadId.value ? res.data.form : res.data
      form.map((item : any) => item.data).flat().forEach((item : any) => {
        if (item.input_type === 'oaWangeditor') {
          isOaWangeditor.value = true
        }
        if (item.input_type === "select" || item.type === "radio") {
          deepAddProp(item.options, "label", "text");
          // 地址选择器无需转换
          if (!Array.isArray(item.value)) {
            item.value = item.value + "";
          }
        }
      });
   
      formDesignInfo.value = form;
     
      
      
    } catch (error) {
      console.log(error);
    }
  };

  onLoad((options) => {
    leadId.value = options.id;
    if(options.types ){
types.value = options.types
    }
    getFormDesignInfo();
  });
</script>

<style scoped lang="scss">
  .head-wrap {
    padding-top: var(--status-bar-height);
    background-color: #fff;
    position: sticky;
    top: 0;
    z-index: 1;
  }

  .form-card {
  

    &:last-child {
      margin-bottom: calc(var(--bottom-area-height) + 120rpx);
    }
  }

  .form-title {
    font-weight: 500;
    font-size: 32rpx;
    color: #303133;
    line-height: 44rpx;
    display: flex;
    align-items: center;
  }

  .placeholder-box {
    height: calc(var(--bottom-area-height) + 120rpx);
  }

  .placeholder-bottom {
    height: calc(var(--bottom-area-height) + 180rpx);
  }

  .bottom-box {
    ::v-deep .base-bottom-btn-box {
      position: fixed;
      bottom: 35px;
      left: 0;
      right: 0;
      transform: none;
    }

  }
</style>
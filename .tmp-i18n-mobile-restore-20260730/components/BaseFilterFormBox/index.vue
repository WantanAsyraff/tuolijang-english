<template>
  <view class="form-box">
    <template v-for="item of props.config" :key="item.key">
      <!-- 普通 picker 选择器 -->
      <picker class="form-item" mode="selector"
        @change="($event: PickerChangeEvent) => handlePickerChange($event, item)" :range="item.range"
        :range-key="item.rangeKey" :value="formIndexState[item.key]" v-if="item.type === FormItemType.PICKER">
        <view class="search-default-label">{{ formState[item.key] ? formState[item.key][item.rangeKey] : item.label }}
          <text class="iconfont icon-zhankai1"></text>
        </view>
      </picker>

      <!-- 日期范围选择器 -->
       <view class="form-item search-default-date"  v-else-if="item.type === FormItemType.DATERANGE"  @click="handleClearDaterange(item)">
            {{ formState[item.key] || item.label }}
            <text v-if="!formState[item.key]" class="date-open-icon iconfont icon-zhankai1"></text>
            <text v-else class="iconfont date-clear  icon-shenpizhongxin-jujue" @click.stop="handleClear(item)"></text>
          </view>
    </template>
       <timePopup ref="timePopupRef" @change="changeTime"></timePopup>
  </view>
</template>

<script lang="ts">

  export enum FormItemType {
    PICKER = "picker",
    DATERANGE = "daterange"
  }
  export type FormItemPickerConfig = {
    rangeKey : string;
    range : any[];
    type : FormItemType.PICKER;
    defaultVal ?: number;
    label : string;
    key : string;
  };

  export type FormItemDaterangeConfig = {
    label : string;
    key : string;
    type : FormItemType.DATERANGE;
  };

  export type FormBoxConfig = FormItemPickerConfig | FormItemDaterangeConfig;
</script>

<script setup lang="ts">
import timePopup from "@/components/timePopup/index.vue";
  const props = defineProps<{
    config : FormBoxConfig[];
  }>();

  const emit = defineEmits<{
    change : [item: FormBoxConfig, value: any];
  }>();

  const formState = reactive<Record<string, any>>({});
  const formIndexState = reactive<Record<string, number>>({});

  watch(() => props.config, (newVal) => {
    for (const item of newVal) {
      if (item.type === FormItemType.PICKER && item.defaultVal > 0 && item.range.length) {
        const index = item.range.findIndex(i => i.id === item.defaultVal);
        if (index !== -1) {
          formIndexState[item.key] = index;
          formState[item.key] = item.range[index];
        }
      }
    }
  }, { immediate: true });

  interface PickerChangeEvent {
    detail : {
      value : number | string;
    };
  }

  type DateRangeChangeEvent = string[];
 

  // const handleDateRangeChange = (event : DateRangeChangeEvent, item : FormItemDaterangeConfig) => {
  //   if (event.length > 0) {
  //     event = event.map(i => i.replace(/-/g, "/"));
  //     formState[item.key] = event.map(i => i.substring(5)).join("~");
  //   } else {
  //     formState[item.key] = "";
  //   }
  //   emit("change", item, event);
  // };

  const handlePickerChange = (event : PickerChangeEvent, item : FormItemPickerConfig) => {
    formState[item.key] = item.range[event.detail.value as number];
    emit("change", item, formState[item.key]);
  };


const timePopupRef = ref<InstanceType<typeof timePopup> | null>(null);
  const itemData = ref<FormItemDaterangeConfig | null>(null);

const handleClearDaterange = (item : FormItemDaterangeConfig) => {
  itemData.value = item;
  timePopupRef.value?.popupOpen?.();
};
const handleClear = (item : FormItemDaterangeConfig) => {

    formState[item.key] = "";
    emit("change", item, formState[item.key]);
  
};
const changeTime = (event : DateRangeChangeEvent) => {
  if (itemData.value) {
 formState[itemData.value.key] =event.time
  emit("change", itemData.value, formState[itemData.value.key]);
  }
};
</script>

<style scoped lang="scss">
  .form-box {
    display: flex;
    flex-flow: row nowrap;
    height: 72rpx;
    align-items: center;

    .form-item {
      flex: 1;
      text-align: center;
      font-size: 12px;
      color: #606266;
      line-height: 28rpx;

      .iconfont {
        font-size: 9px;
        margin-left: 4rpx;
        color: #c0c4cc;
      }
    }
  }

  ::v-deep .uni-calendar .uni-calendar__month-item:nth-child(1) {
    display: none !important;
  }

  /* 兼容不同层级结构（若上面不生效，试这个） */
  ::v-deep .uni-calendar__months>.uni-calendar__month-item:nth-child(1) {
    display: none !important;
  }
</style>
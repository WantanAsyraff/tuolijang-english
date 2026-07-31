<template>
  <view class="form-base">
    <slot name="title" />
    <view class="form-item" v-for="item of formData" :key="item.key" :type="item.type">
      <view class="form-item-label" :required="item.required">{{ $ts(item.label) }}</view>
      <view class="form-item-content">
        <template v-if="item.type === 'input'">
          <input :placeholder="String($ts(item.placeholder))" v-model="item.value" :type="(item as FormItemInput).inputType" />
        </template>
        <template v-else-if="item.type === 'select'">
          <picker :range="$localize((item as FormItemWithOptions).options)" @change="handleSelectChange(item, $event)"
            range-key="name">
            <view class="picker-content">
              <template v-if="optionalData.selectText[item.key]">{{ $ts(optionalData.selectText[item.key]) }}</template>
              <text class="item-placeholder" v-else>
                {{ $ts(item.placeholder) }}
              </text>
              <i class="iconfont icon-jinru-copy" />
            </view>
          </picker>
        </template>
        <template v-else-if="item.type === 'address'">
          <uni-data-picker :localdata="$localize(optionalData.address)" :popup-title="$t('ui.examineFormIndexPleaseSelectProvinceCityDistrict')"
            :map="{ text: 'name', value: 'value' }" @change="handleAddressChange(item, $event)">
            <view class="picker-content">
              <template v-if="optionalData.addressText[item.key]">{{ optionalData.addressText[item.key] }}</template>
              <text class="item-placeholder" v-else>
                {{ $ts(item.placeholder) }}
              </text>
              <i class="iconfont icon-jinru-copy" />
            </view>
          </uni-data-picker>
        </template>
        <template v-else-if="item.type === 'date'">
          <uni-datetime-picker type="date" v-model="item.value">
            <view class="picker-content">
              <template v-if="item.value">{{ item.value }}</template>
              <text class="item-placeholder" v-else>
                {{ $ts(item.placeholder) }}
              </text>
              <i class="iconfont icon-jinru-copy" />
            </view>
          </uni-datetime-picker>
        </template>
        <template v-if="item.type === 'radio'">
          <radio-group @change="handleRadioChange(item, $event)">
            <label class="radio-label" v-for="(option) in (item as FormItemWithOptions).options" :key="option.value">
              <radio :value="option.value" :checked="item.value === option.value" />
              {{ $ts(option.name) }}
            </label>
          </radio-group>
        </template>
        <template v-else-if="item.type === 'textarea'">
          <textarea :placeholder="String($ts(item.placeholder))" v-model="item.value" />
        </template>
      </view>
    </view>
  </view>
</template>

<script lang="ts">
type FormItemBase = {
  label: string;
  key: string;
  placeholder?: string;
  required: boolean;
};

type OptionItem = {
  name: string;
  value: string;
};

type FormItemWithOptions = FormItemBase & {
  type: "select" | "radio";
  options: OptionItem[];
  value: string;
};

type FormItemInput = FormItemBase & {
  type: "input";
  inputType?: string;
  value: string;
};

type FormItemAddressPicker = FormItemBase & {
  type: "address";
  value: string[];
};

type FormItemWithoutOptions = FormItemBase & {
  type: "date" | "textarea";
  value: string;
};

export type FormItem = FormItemWithOptions | FormItemWithoutOptions | FormItemInput | FormItemAddressPicker;
</script>

<script setup lang="ts">
import {
  getDictTreeListApi
} from "@/api/crud";

const props = defineProps<{
  data: FormItem[];
}>();

const optionalData = ref({
  address: [],
  addressText: {} as Record<string, string>,
  selectText: {} as Record<string, string>
});

const formData = ref<FormItem[]>(structuredClone(toRaw(props.data)));

const handleSelectChange = (item: FormItemWithOptions, event: any) => {
  item.value = item.options[event.detail.value].value;
  optionalData.value.selectText[item.key] = item.options[event.detail.value].name;
};

const handleAddressChange = (item: FormItemAddressPicker, event: any) => {
  const address = event.detail.value.map((item: any) => item.text).join("/");
  optionalData.value.addressText[item.key] = address;
  item.value = event.detail.value.map((item: any) => item.value);
};

const handleRadioChange = (item: FormItemWithOptions, event: any) => {
  item.value = event.detail.value;
};

const getAddressData = () => {
  let obj = {
    type_id: 2,
    isCityShow: 1,
  };
  getDictTreeListApi(obj).then((res: any) => {
    optionalData.value.address = res.data;
  });
};

if (formData.value.some(item => item.type === "address")) {
  getAddressData();
}

const handleGetValue = () => {
  let result: Record<string, any> = {};
  for (const item of formData.value) {
    if (item.required) {
      if (item.type === "address") {
        if (item.value.length === 0) return false;
      } else {
        if (item.value === "") return false;
      }
    }
    result[item.key] = item.value;
  }

  return result;
};

defineExpose({
  formData,
  handleGetValue
});

</script>

<style scoped lang="scss">
.form-base {

  .form-item {
    display: flex;
    align-items: center;
    height: 110rpx;
    border-bottom: 1px solid #f0f1f5;

    &:last-child {
      border-bottom: none;
    }

    &[type="textarea"] {
      height: initial;
      display: block;
      padding-top: 36rpx;

      .form-item-content {
        text-align: left;
      }

      textarea {
        margin-top: 24rpx;
        width: 100%;
        height: 100rpx;
      }
    }

    .form-item-label {
      font-size: 30rpx;
      color: #303133;
      margin-right: 24rpx;

      &[required="true"] {
        &::after {
          content: "*";
          color: #ff4949;
          margin-left: 8rpx;
          line-height: 30rpx;
          vertical-align: middle;
        }
      }
    }

    .form-item-content {
      padding-right: 24rpx;
      flex: 1;
      text-align: right;

      input {
        text-align: right;
      }

      ::v-deep .uni-input-placeholder {
        color: #C0C4CC;
      }

      .picker-content {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        line-height: 30rpx;
      }

      .item-placeholder {
        color: #C0C4CC;

        &+.icon-jinru-copy {
          color: #C0C4CC;
        }
      }

      .icon-jinru-copy {
        font-size: 20rpx;
        color: #303133;
        margin-left: 12rpx;
      }

      .radio-label {
        display: inline-flex;
        align-items: center;
        font-size: 30rpx;
        color: #282828;

        ::v-deep .uni-radio-input {
          width: 30rpx;
          height: 30rpx;
        }

        &+.radio-label {
          margin-left: 40rpx;
        }
      }

    }
  }
}
</style>

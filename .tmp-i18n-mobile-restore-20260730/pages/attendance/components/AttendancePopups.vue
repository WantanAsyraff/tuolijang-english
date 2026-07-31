<template>
  <view>
    <uni-popup ref="remindPopup" type="center" :is-mask-click="false">
      <SignInModal
        :clock-time="clockTime"
        :type="signInType"
        :on-work="onWork"
        @ok="emit('successOk')"
      />
    </uni-popup>

    <uni-popup ref="menuPopup" type="bottom" background-color="#fff" :is-mask-click="false">
      <ApplyForMenuModal :date-val="nowDate" @cancel="closeApply" />
    </uni-popup>

    <uni-popup ref="externalPopup" type="center" :is-mask-click="false">
      <ExternalWorkModal
        :is-effective-range="isEffectiveRange"
        :is-text="externalTextRequired"
        :is-pic="externalPicRequired"
        :address="address"
        :on-work="onWork"
        :record-length="recordLength"
        @cancel="closeExternal"
        @ok="handleExternalOk"
      />
    </uni-popup>
  </view>
</template>

<script setup lang="ts">
import { ref } from "vue";
import SignInModal from "./signInModal.vue";
import ApplyForMenuModal from "./applyForMenuModal.vue";
import ExternalWorkModal from "./externalWorkModal.vue";

interface ExternalFormData {
  text?: string;
  imgs?: string[];
  is_external?: number;
}

withDefaults(defineProps<{
  clockTime?: string;
  signInType?: number;
  onWork: string;
  nowDate: string;
  isEffectiveRange: boolean;
  externalTextRequired: number;
  externalPicRequired: number;
  address?: string;
  recordLength: number;
}>(), {
  clockTime: "",
  signInType: 1,
  address: "",
});

const emit = defineEmits<{
  (e: "successOk"): void;
  (e: "externalOk", formData: ExternalFormData): void;
}>();

const remindPopup = ref<any>(null);
const menuPopup = ref<any>(null);
const externalPopup = ref<any>(null);

function openSuccess() {
  remindPopup.value?.open();
}

function closeSuccess() {
  remindPopup.value?.close();
}

function openApply() {
  menuPopup.value?.open();
}

function closeApply() {
  menuPopup.value?.close();
}

function openExternal() {
  externalPopup.value?.open();
}

function closeExternal() {
  externalPopup.value?.close();
}

function handleExternalOk(formData: ExternalFormData) {
  closeExternal();
  emit("externalOk", formData);
}

defineExpose({
  openSuccess,
  closeSuccess,
  openApply,
  closeApply,
  openExternal,
  closeExternal,
});
</script>

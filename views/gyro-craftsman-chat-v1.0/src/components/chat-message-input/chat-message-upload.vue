<template>
  <div class="flex gap-10px">
    <el-upload :show-file-list="false" :action="uploadOption.action" :headers="uploadOption.headers"
      v-for="item in uploadCompList" :key="item.type" :on-success="genUploadCallback(item.type, handleUploadSuccess)"
      :accept="item.accept"
      :on-error="genUploadCallback(item.type, handleUploadError)"
      :on-progress="genUploadCallback(item.type, handleUploadProgress)" class="text-0">
      <i class="text-24px ai-icon" :class="item.icon" />
    </el-upload>
  </div>
</template>

<script setup lang="ts">
import { getLanguage } from "@/locale";
import type { UploadFile, UploadProgressEvent } from "element-plus";

const uploadOption = ref({
  action: "/api/upload",
  headers: {
    Authorization: "Bearer test",
    laravel_lang: getLanguage(),
  }
});

const enum UploadCompType {
  File = "file",
  Picture = "picture",
  Audio = "audio",
}

const uploadCompList = [
  {
    type: UploadCompType.File,
    icon: "ai-icon-wenjian3",
    accept: "*",
  },
  {
    type: UploadCompType.Picture,
    icon: "ai-icon-tupian-01",
    accept: "image/*",
  },
  {
    type: UploadCompType.Audio,
    icon: "ai-icon-yinpin",
    accept: "audio/*",
  }
];

const genUploadCallback = (type: UploadCompType, func: (...args: any[]) => void) => {
  return (...args: any[]) => {
    func(type, ...args);
  };
};

const handleUploadProgress = (type: UploadCompType, evt: UploadProgressEvent, uploadFile: UploadFile) => {
  console.log(type, evt, uploadFile);
};

const handleUploadSuccess = (type: UploadCompType, response: any, uploadFile: UploadFile) => {
  console.log(type, response, uploadFile);
};

const handleUploadError = (type: UploadCompType, error: Error, uploadFile: UploadFile) => {
  console.log(type, error, uploadFile);
};

</script>

<style scoped lang="scss"></style>

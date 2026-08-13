<template>
  <el-form-item :label="$('designer.setting.showFileList')">
    <el-switch v-model="optionModel.showFileList"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "showFileList-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
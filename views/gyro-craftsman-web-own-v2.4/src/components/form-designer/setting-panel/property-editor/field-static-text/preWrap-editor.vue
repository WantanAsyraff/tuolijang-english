<template>
  <el-form-item :label="$('designer.setting.preWrap')">
    <el-switch v-model="optionModel.preWrap"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "static-text-preWrap-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
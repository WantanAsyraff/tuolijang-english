<template>
  <el-form-item :label="$('designer.setting.readonly')">
    <el-switch v-model="optionModel.readonly"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "readonly-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
<template>
  <el-form-item :label="$('designer.setting.labelHidden')">
    <el-switch v-model="optionModel.labelHidden"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "labelHidden-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
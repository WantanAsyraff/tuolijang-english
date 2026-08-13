<template>
  <el-form-item :label="$('designer.setting.showFold')">
    <el-switch v-model="optionModel.showFold"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "showFold-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
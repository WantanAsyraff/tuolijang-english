<template>
  <el-form-item :label="$('designer.setting.showScore')">
    <el-switch v-model="optionModel.showScore"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "showScore-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
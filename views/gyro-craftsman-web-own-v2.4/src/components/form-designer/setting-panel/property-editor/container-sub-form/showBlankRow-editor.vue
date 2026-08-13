<template>
  <el-form-item :label="$('designer.setting.showBlankRow')">
    <el-switch v-model="optionModel.showBlankRow"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "showBlankRow-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
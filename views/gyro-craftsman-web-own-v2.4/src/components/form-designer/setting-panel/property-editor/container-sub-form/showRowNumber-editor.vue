<template>
  <el-form-item :label="$('designer.setting.showRowNumber')">
    <el-switch v-model="optionModel.showRowNumber"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "showRowNumber-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
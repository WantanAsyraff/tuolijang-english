<template>
  <el-form-item :label="$('designer.setting.labelTooltip')">
    <el-input type="text" v-model="optionModel.labelTooltip"></el-input>
  </el-form-item>
</template>

<script>
  export default {
    name: "labelTooltip-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
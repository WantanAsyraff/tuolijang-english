<template>
  <el-form-item :label="$('designer.setting.showStops')">
    <el-switch v-model="optionModel.showStops"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "showStops-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
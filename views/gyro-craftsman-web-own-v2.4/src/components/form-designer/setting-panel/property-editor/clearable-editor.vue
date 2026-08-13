<template>
  <el-form-item :label="$('designer.setting.clearable')">
    <el-switch v-model="optionModel.clearable"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "clearable-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
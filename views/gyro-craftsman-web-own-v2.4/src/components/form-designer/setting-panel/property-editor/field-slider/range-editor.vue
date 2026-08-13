<template>
  <el-form-item :label="$('designer.setting.range')">
    <el-switch v-model="optionModel.range"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "range-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
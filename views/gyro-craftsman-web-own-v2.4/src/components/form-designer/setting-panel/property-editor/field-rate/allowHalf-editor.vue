<template>
  <el-form-item :label="$('designer.setting.allowHalf')">
    <el-switch v-model="optionModel.allowHalf"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "allowHalf-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
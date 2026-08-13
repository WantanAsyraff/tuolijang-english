<template>
  <el-form-item :label="$('designer.setting.showText')">
    <el-switch v-model="optionModel.showText"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "showText-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
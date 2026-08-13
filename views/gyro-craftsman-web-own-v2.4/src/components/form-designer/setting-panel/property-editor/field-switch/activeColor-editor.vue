<template>
  <el-form-item :label="$('designer.setting.activeColor')">
    <el-color-picker v-model="optionModel.activeColor"></el-color-picker>
  </el-form-item>
</template>

<script>
  export default {
    name: "activeColor-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
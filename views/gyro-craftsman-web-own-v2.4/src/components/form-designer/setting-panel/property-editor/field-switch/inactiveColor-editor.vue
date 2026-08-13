<template>
  <el-form-item :label="$('designer.setting.inactiveColor')">
    <el-color-picker v-model="optionModel.inactiveColor"></el-color-picker>
  </el-form-item>
</template>

<script>
  export default {
    name: "inactiveColor-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
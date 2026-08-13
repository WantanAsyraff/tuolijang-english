<template>
  <el-form-item :label="$('designer.setting.startPlaceholder')">
    <el-input type="text" v-model="optionModel.startPlaceholder"></el-input>
  </el-form-item>
</template>

<script>
  export default {
    name: "startPlaceholder-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
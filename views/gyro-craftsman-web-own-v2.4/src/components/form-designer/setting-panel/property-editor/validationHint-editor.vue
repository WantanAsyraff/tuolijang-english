<template>
  <el-form-item :label="$('designer.setting.validationHint')">
    <el-input type="text" v-model="optionModel.validationHint"></el-input>
  </el-form-item>
</template>

<script>
  export default {
    name: "validationHint-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
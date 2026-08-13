<template>
  <el-form-item :label="$('designer.setting.switchWidth')">
    <el-input-number v-model="optionModel.switchWidth" style="width: 100%"></el-input-number>
  </el-form-item>
</template>

<script>
  export default {
    name: "switchWidth-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
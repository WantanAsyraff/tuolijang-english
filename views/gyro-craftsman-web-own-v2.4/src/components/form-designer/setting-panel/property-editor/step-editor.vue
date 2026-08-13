<template>
  <el-form-item :label="$('designer.setting.step')">
    <el-input-number v-model="optionModel.step" class="hide-spin-button" style="width: 100%" :min="0.01"></el-input-number>
  </el-form-item>
</template>

<script>
  export default {
    name: "step-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
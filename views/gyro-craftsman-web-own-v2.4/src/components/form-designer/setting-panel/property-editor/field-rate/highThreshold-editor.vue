<template>
  <el-form-item :label="$('designer.setting.highThreshold')">
    <el-input-number v-model="optionModel.highThreshold" :min="optionModel.lowThreshold" :max="optionModel.max"
                     class="hide-spin-button" style="width: 100%"></el-input-number>
  </el-form-item>
</template>

<script>
  export default {
    name: "highThreshold-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
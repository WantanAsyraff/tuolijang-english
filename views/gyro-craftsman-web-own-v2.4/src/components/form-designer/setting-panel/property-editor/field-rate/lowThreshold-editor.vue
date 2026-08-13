<template>
  <el-form-item :label="$('designer.setting.lowThreshold')">
    <el-input-number v-model="optionModel.lowThreshold" :min="1" :max="optionModel.highThreshold"
                     class="hide-spin-button" style="width: 100%"></el-input-number>
  </el-form-item>
</template>

<script>
  export default {
    name: "lowThreshold-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
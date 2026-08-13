<template>
  <el-form-item :label="$('designer.setting.wordBreak')">
    <el-switch v-model="optionModel.wordBreak"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "table-cell-wordBreak-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
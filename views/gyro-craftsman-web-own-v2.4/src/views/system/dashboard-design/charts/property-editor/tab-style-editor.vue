<template>
  <div class="tab-style-editor">
    <el-radio-group v-model="optionModel.tabStyle" size="small">
      <el-radio label="trapezoid">{{ $("legacy.45538f127d6fa103") }}</el-radio>
      <el-radio label="rectangle">{{ $("legacy.88b75a101595a23a") }}</el-radio>
      <el-radio label="linear">{{ $("legacy.078e4caeda5cffbc") }}</el-radio>
    </el-radio-group>
  </div>
</template>

<script>
export default {
  name: 'tab-style-editor',
  props: {
    designer: Object,
    selectedWidget: Object,
    optionModel: Object
  }
}
</script>
<style lang="scss" scoped>
.tab-style-editor {
  padding-left: 2px;
}
</style>

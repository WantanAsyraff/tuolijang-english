<template>
  <el-form-item :label="$('designer.setting.displayType')">
    <el-select v-model="optionModel.type">
      <el-option label="datetime" value="datetime"></el-option>
      <el-option label="date" value="date"></el-option>
      <el-option label="dates" value="dates"></el-option>
      <el-option label="year" value="year"></el-option>
      <el-option label="month" value="month"></el-option>
      <el-option label="week" value="week"></el-option>
    </el-select>
  </el-form-item>
</template>

<script>
  export default {
    name: "date-picker-type-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
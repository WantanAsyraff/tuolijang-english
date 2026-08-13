<template>
  <el-form-item :label="$('designer.setting.valueFormat')">
    <el-select v-model="optionModel.valueFormat" filterable allow-create>
      <el-option label="yyyy-MM-DD" value="yyyy-MM-DD"></el-option>
      <el-option label="yyyy-MM-dd HH:mm:ss" value="yyyy-MM-dd HH:mm:ss"></el-option>
      <el-option label="yyyy-MM-dd hh:mm:ss" value="yyyy-MM-dd hh:mm:ss"></el-option>
    </el-select>
  </el-form-item>
</template>

<script>
export default {
  name: 'date-time-picker-valueFormat-editor',
  props: {
    designer: Object,
    selectedWidget: Object,
    optionModel: Object
  }
}
</script>

<style scoped></style>
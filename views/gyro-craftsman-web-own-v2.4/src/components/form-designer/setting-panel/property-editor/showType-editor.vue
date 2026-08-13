<template>
  <el-form-item :label='$("legacy.f2482e7d32aa7b06")'>
    <el-radio-group v-model="optionModel.showType">
      <el-radio label="0">{{ $("legacy.a35ec1959600d4ab") }}</el-radio>
      <el-radio label="1">{{ $("legacy.2ce2119c7b28b673") }}</el-radio>
    </el-radio-group>
  </el-form-item>
</template>

<script>
export default {
  name: 'showType-editor',
  props: {
    designer: Object,
    selectedWidget: Object,
    optionModel: Object
  },
  mounted() {
    if (!this.optionModel?.showType) {
      this.$set(this.optionModel, 'showType', '0')
    }
  }
}
</script>

<style scoped></style>
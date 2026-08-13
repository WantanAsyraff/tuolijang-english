<template>
  <el-form-item :label="$('designer.setting.labelAlign')">
    <el-radio-group v-model="optionModel.labelAlign" class="radio-group-custom">
      <el-radio-button label="label-left-align">
        {{$('designer.setting.leftAlign')}}</el-radio-button>
      <el-radio-button label="label-center-align">
        {{$('designer.setting.centerAlign')}}</el-radio-button>
      <el-radio-button label="label-right-align">
        {{$('designer.setting.rightAlign')}}</el-radio-button>
    </el-radio-group>
  </el-form-item>
</template>

<script>
  export default {
    name: "sub-form-labelAlign-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style lang="scss" scoped>
  .radio-group-custom {
    ::v-deep .el-radio-button__inner {
      padding-left: 12px;
      padding-right: 12px;
    }
  }

</style>
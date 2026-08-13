<template>
  <el-form-item :label="$('designer.setting.displayStyle')">
    <el-radio-group v-model="optionModel.displayStyle">
      <el-radio label="inline">{{$('designer.setting.inlineLayout')}}</el-radio>
      <el-radio label="block">{{$('designer.setting.blockLayout')}}</el-radio>
    </el-radio-group>
  </el-form-item>
</template>

<script>
  export default {
    name: "displayStyle-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
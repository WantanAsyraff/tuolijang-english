<template>
  <div>
    <el-form-item label-width="0">
      <el-divider class="custom-divider">{{$('designer.setting.inputButton')}}</el-divider>
    </el-form-item>
    <el-form-item :label="$('designer.setting.appendButton')">
      <el-switch v-model="optionModel.appendButton"></el-switch>
    </el-form-item>
  </div>
</template>

<script>
  export default {
    name: "appendButton-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
<template>
  <div>
    <el-form-item label-width="0">
      <el-divider class="custom-divider">{{$('designer.setting.uploadSetting')}}</el-divider>
    </el-form-item>
    <el-form-item :label="$('designer.setting.uploadURL')">
      <el-input type="text" v-model="optionModel.uploadURL"></el-input>
    </el-form-item>
  </div>
</template>

<script>
  export default {
    name: "uploadURL-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
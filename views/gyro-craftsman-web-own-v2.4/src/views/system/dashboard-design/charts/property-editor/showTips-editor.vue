<template>
  <div>
    <el-form-item :label='$("legacy.c5840218a987e0c1")'>
      <el-switch v-model="optionModel.isShowTips"></el-switch>
    </el-form-item>
    <el-form-item :label='$("ui.developCrudEventHintContent")' v-if="optionModel.isShowTips">
      <el-input v-model="optionModel.tips" type="textarea" :rows="3" :placeholder='$("legacy.d2b00e13b4f35df4")'></el-input>
    </el-form-item>
  </div>
</template>

<script>
export default {
  name: 'showTips-editor',

  props: {
    designer: Object,
    selectedWidget: Object,
    optionModel: Object
  }
}
</script>

<style scoped></style>

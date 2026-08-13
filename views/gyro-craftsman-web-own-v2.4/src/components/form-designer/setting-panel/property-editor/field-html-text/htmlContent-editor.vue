<template>
  <div>
    <el-form-item :label="$('designer.setting.htmlContent')">
    </el-form-item>
    <el-form-item label-width="0">
      <el-input type="textarea" v-model="optionModel.htmlContent" :rows="5" class="html-content-editor"></el-input>
    </el-form-item>
  </div>
</template>

<script>
  export default {
    name: "htmlContent-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>
  .html-content-editor {
    font-size: 13px;
  }
</style>
<template>
  <el-form-item :label="$('designer.setting.allowCreate')">
    <el-switch v-model="optionModel.allowCreate"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "allowCreate-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },

  }
</script>

<style scoped>

</style>
<template>
  <el-form-item :label="$('designer.setting.vertical')">
    <el-switch v-model="optionModel.vertical"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "vertical-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
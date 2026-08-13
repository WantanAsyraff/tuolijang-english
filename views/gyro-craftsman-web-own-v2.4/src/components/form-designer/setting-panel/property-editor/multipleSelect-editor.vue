<template>
  <el-form-item :label="$('designer.setting.multipleSelect')">
    <el-switch v-model="optionModel.multipleSelect"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "multipleSelect-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },

  }
</script>

<style scoped>

</style>
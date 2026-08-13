<template>
  <el-form-item :label="$('designer.setting.circle')">
    <el-switch v-model="optionModel.circle"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "circle-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
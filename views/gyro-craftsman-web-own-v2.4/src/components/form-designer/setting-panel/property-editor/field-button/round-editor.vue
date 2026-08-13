<template>
  <el-form-item :label="$('designer.setting.round')">
    <el-switch v-model="optionModel.round"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "round-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
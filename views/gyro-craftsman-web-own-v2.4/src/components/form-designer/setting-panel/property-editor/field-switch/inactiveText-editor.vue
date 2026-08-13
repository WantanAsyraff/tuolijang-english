<template>
  <el-form-item :label="$('designer.setting.inactiveText')">
    <el-input v-model="optionModel.inactiveText"></el-input>
  </el-form-item>
</template>

<script>
  export default {
    name: "inactiveText-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
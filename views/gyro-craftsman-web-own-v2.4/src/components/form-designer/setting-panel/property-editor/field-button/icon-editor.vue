<template>
  <el-form-item :label="$('designer.setting.buttonIcon')">
    <el-input type="text" v-model="optionModel.icon"></el-input>
  </el-form-item>
</template>

<script>
  export default {
    name: "icon-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
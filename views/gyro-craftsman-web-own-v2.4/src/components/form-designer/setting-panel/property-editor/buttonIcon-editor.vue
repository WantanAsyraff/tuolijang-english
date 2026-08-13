<template>
  <el-form-item :label="$('designer.setting.appendButtonIcon')">
    <el-input type="text" v-model="optionModel.buttonIcon"></el-input>
  </el-form-item>
</template>

<script>
  export default {
    name: "buttonIcon-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
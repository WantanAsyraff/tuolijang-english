<template>
  <el-form-item :label="$('designer.setting.showPassword')" v-if="optionModel.type === 'password'">
    <el-switch v-model="optionModel.showPassword"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "showPassword-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },

  }
</script>

<style scoped>

</style>
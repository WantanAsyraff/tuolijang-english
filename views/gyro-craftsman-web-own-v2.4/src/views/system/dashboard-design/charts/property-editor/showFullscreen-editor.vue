<template>
  <el-form-item :label='$("legacy.172e191a2db4f9a8")' v-if="optionModel.showHeader">
    <el-switch v-model="optionModel.showFullscreen"></el-switch>
    
  </el-form-item>
</template>

<script>
  export default {
    name: "showFullscreen-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },

  }
</script>

<style scoped>

</style>

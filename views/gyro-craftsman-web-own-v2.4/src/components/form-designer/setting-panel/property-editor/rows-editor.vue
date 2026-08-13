<template>
  <el-form-item :label="$('designer.setting.rows')">
    <el-input-number v-model="optionModel.rows" style="width: 100%"></el-input-number>
  </el-form-item>
</template>

<script>
  export default {
    name: "rows-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },

  }
</script>

<style scoped>

</style>
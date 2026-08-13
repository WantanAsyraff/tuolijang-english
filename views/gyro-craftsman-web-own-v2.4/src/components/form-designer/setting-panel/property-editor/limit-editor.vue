<template>
  <el-form-item :label="$('designer.setting.limit')">
    <el-input-number v-model="optionModel.limit" :min="1" class="hide-spin-button" style="width: 100%"></el-input-number>
  </el-form-item>
</template>

<script>
  export default {
    name: "limit-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },

  }
</script>

<style scoped>

</style>
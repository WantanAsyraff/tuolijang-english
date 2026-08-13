<template>
  <el-form-item :label="$('designer.setting.colPushTitle')">
    <el-input-number v-model.number="optionModel.push" :min="0" :max="24"
                     style="width: 100%"></el-input-number>
  </el-form-item>
</template>

<script>
  export default {
    name: "grid-col-push-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>
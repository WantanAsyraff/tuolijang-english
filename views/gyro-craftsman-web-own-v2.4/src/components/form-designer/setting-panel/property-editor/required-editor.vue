<template>
  <el-form-item :label="$('designer.setting.required')">
    <span slot="label"
      >{{ $('designer.setting.required') }}
      <el-tooltip effect="light" :content='$("legacy.4e9142dd06272ae0")'>
        <i class="el-icon-info"></i
      ></el-tooltip>
    </span>
    <el-switch v-model="optionModel.required"></el-switch>
  </el-form-item>
</template>

<script>
export default {
  name: 'required-editor',
  props: {
    designer: Object,
    selectedWidget: Object,
    optionModel: Object
  }
}
</script>

<style scoped></style>
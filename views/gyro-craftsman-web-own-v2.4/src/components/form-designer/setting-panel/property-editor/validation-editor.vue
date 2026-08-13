<template>
  <el-form-item>
    <span slot="label">{{$('designer.setting.validation')}}
      <el-tooltip effect="light" :content="$('designer.setting.validationHelp')">
        <i class="el-icon-info"></i></el-tooltip>
    </span>
    <el-select v-model="optionModel.validation" filterable clearable allow-create default-first-option>
      <el-option v-for="(fv, fvIdx) in fieldValidators"
                 :key="fvIdx"
                 :label="fv.label"
                 :value="fv.value">
      </el-option>
    </el-select>
  </el-form-item>
</template>

<script>
  export default {
    name: "validation-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
    data() {
      return {
        fieldValidators: [
          {value: 'number', label: this.$('designer.hint.numberValidator')},
          {value: 'letter', label: this.$('designer.hint.letterValidator')},
          {value: 'letterAndNumber', label: this.$('designer.hint.letterAndNumberValidator')},
          {value: 'mobilePhone', label: this.$('designer.hint.mobilePhoneValidator')},
          {value: 'email', label: this.$('designer.hint.emailValidator')},
          {value: 'url', label: this.$('designer.hint.urlValidator')},
          {value: 'noChinese', label: this.$('designer.hint.noChineseValidator')},
          {value: 'chinese', label: this.$('designer.hint.chineseValidator')},
        ],
      }
    },

  }
</script>

<style scoped>

</style>
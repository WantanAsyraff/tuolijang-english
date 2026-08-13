<template>
  <el-form-item :label="$('designer.setting.minLength')">
    <el-input type="number" @input.native="inputNumberHandler" class="hide-spin-button"
              min="0" v-model="minLength" style="width: 100%"></el-input>
  </el-form-item>
</template>

<script>
  import propertyMixin from "@/components/form-designer/setting-panel/property-editor/propertyMixin"

  export default {
    name: "minLength-editor",
    mixins: [propertyMixin],
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
    computed: {
      minLength: {
        get() {
          return this.optionModel['minLength']
        },

        set(newValue) {
          if (!newValue || isNaN(newValue)) {
            this.optionModel.minLength = null
          } else {
            this.optionModel.minLength = Number(newValue)
          }
        }
      },

    }
  }
</script>

<style scoped>

</style>
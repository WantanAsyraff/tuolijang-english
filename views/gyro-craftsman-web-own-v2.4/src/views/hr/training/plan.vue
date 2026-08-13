import { $ } from '@/lang'
<template>
<div class="divBox">
  <div class="box-height">
    <ueditorFrom
      :border="true"
      :height="height"
      :training="true"
      :editor-border="false"
      ref="ueditorFrom"
      :placeholder="$('ui.hrTrainingPlanDevelopTheStrategicPlanAcrossRevenueProfitScaleTalent')"
      :content="content"
    />
    <div class="cr-bottom-button btn-shadow">
      <el-button size="small" :loading="loading" type="primary" @click="handleConfirm()">{{ $("ui.formDesignerFormWidgetFieldWidgetRichTextWidgetSave") }}</el-button>
    </div>
  </div>
</div>
</template>
<script>
import ueditorFrom from '@/components/form-common/oa-wangeditor'
import { employeeTrainApi, getEmployeeTrainApi } from '@/api/config.js'
export default {
  name: '',
  components: { ueditorFrom },
  props: {},
  data() {
    return {
      height: 'calc(100vh - 250px)',
      content: '',
      loading: false
    }
  },

  mounted() {
    this.getConent()
  },
  methods: {
    async getConent() {
      let type = 'strategic_plan'
      const result = await getEmployeeTrainApi(type)
      this.content = result.data.content
    },
    handleConfirm() {
      this.content = this.$refs.ueditorFrom.getValue()
      if (!this.content) {
        return this.$message.error($('customer.message02'))
      }

      this.loading = true
      let type = 'strategic_plan'
      let data = {
        content: this.content
      }
      employeeTrainApi(type, data)
        .then((res) => {
          this.loading = false
        })
        .catch((err) => {
          this.loading = false
        })
    }
  }
}
</script>
<style scoped lang="scss">
.divBox {
  padding: 0;
  position: relative;
  margin-left: 14px;
  border-radius: 8px;
  overflow: hidden;
}
.cr-bottom-button {
  position: absolute;
  left: -0px;
  right: 0;
  bottom: 0;
  width: calc(100% + 0px);
  border-radius: 0 0 8px 8px;
}

::v-deep .w-e-toolbar {
  border-radius: 8px 8px 0 0;
  border-bottom-color: #eee;
  background-color: transparent;
}
::v-deep .main {
  background-color: transparent;
}
::v-deep .w-e-text-container {
  background-color: transparent;
}
</style>

<template>
<el-dialog :title="$t('ui.workFlowDialogErrorDialogHint')" :visible.sync="visibleDialog">
  <div class="ant-confirm-body">
    <i class="anticon anticon-close-circle" style="color: #f00"></i>
    <span class="ant-confirm-title">{{ $t("ui.workFlowDialogErrorDialogCannotPublishNow") }}</span>
    <div class="ant-confirm-content">
      <div>
        <p class="error-modal-desc">{{ $t("ui.workFlowDialogErrorDialogTheFollowingContentIsIncompleteAndMustBeUpdated") }}</p>
        <div class="error-modal-list">
          <div class="error-modal-item" v-for="(item, index) in list" :key="index">
            <div class="error-modal-item-label">{{ $t("ui.workFlowDialogErrorDialogWorkflowDesign") }}</div>
            <div class="error-modal-item-content">
              {{ item.name }} {{ item.type === 1 ? $t('ui.workFlowDialogErrorDialogNotSelected') : $t('ui.workFlowDialogErrorDialogNotAdded') }}{{ item.type }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <span slot="footer" class="dialog-footer">
    <el-button @click="visibleDialog = false">{{ $t("ui.workFlowDialogErrorDialogIGotIt") }}</el-button>
    <el-button type="primary" @click="visibleDialog = false">{{ $t("ui.workFlowDialogErrorDialogUpdateNow") }}</el-button>
  </span>
</el-dialog>
</template>

<script>
export default {
  name: 'TipError',
  props: {
    list: {
      type: Array,
      default: () => {
        return [];
      },
    },
    visible: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      visibleDialog: false,
    };
  },
  watch: {
    visible(val) {
      this.visibleDialog = val;
    },
    visibleDialog(val) {
      this.$emit('update:visible', val);
    },
  },
};
</script>

<style scoped lang="scss">
.ant-confirm-body .ant-confirm-title {
  color: rgba(0, 0, 0, 0.85);
  font-weight: 500;
  font-size: 16px;
  line-height: 1.4;
  display: block;
  overflow: hidden;
}

.ant-confirm-body .ant-confirm-content {
  margin-left: 38px;
  font-size: 14px;
  color: rgba(0, 0, 0, 0.65);
  margin-top: 8px;
}
.ant-confirm-body > .anticon {
  font-size: 22px;
  margin-right: 16px;
  float: left;
}
</style>

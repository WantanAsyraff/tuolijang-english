<template>
<div>
  <el-dialog
    top="10%"
    :visible.sync="dialogVisible"
    width="80%"
    :show-close="false"
    :append-to-body="true"
    :title="$t('ui.developConditionGroupDialogDataLinkage')"
    :close-on-click-modal="false"
  >
    <div class="title">{{ $t("ui.developConditionGroupDialogSetConditions") }}</div>
    <div class="condition-box">
      <ConditionGroup :item="rootCondition" :widgetList="widgetList" :on-remove="() => {}" />
    </div>
    <div class="title mt30">{{ $t("ui.developConditionGroupDialogTriggerTheFollowingAction") }}</div>
    <div class="box mt14">
      {{ $t("ui.developConditionGroupDialogCurrentComponentLinkageDisplay") }}
      <el-select v-model="value" class="mlr6">
        <el-option :label="$t('ui.developConditionGroupDialogHide')" value="hide"></el-option>
        <el-option :label="$t('ui.developConditionGroupDialogDisabled')" value="disable"></el-option>
      </el-select>
      {{ $t("ui.developConditionGroupDialogValue") }}
    </div>
    <span slot="footer" class="dialog-footer">
      <el-button size="small" @click="handleCancel">{{ $t("ui.formCommonSelectLabelCancel") }}</el-button>
      <el-button size="small" type="primary" @click="handleCreate">{{ $t("ui.formCommonOaLogEstablish") }}</el-button>
    </span>
  </el-dialog>
</div>
</template>

<script>
import ConditionGroup from './ConditionGroup'

export default {
  name: 'conditionGroupDialog',
  components: {
    ConditionGroup
  },
  props: {},
  data() {
    return {
      dialogVisible: false,
      options: [],
      widgetList: [],
      value: '0',
      rootCondition: {
        type: 'group',
        relation: 'AND',
        children: [{ type: 'condition', field: '', operator: '' }]
      }
    }
  },
  methods: {
    openBox(list) {
      this.widgetList = list
      this.dialogVisible = true
    },
    handleCancel() {
      this.dialogVisible = false
    },
    handleCreate() {
      // 这里添加创建逻辑
      this.dialogVisible = false
    }
  }
}
</script>

<style scoped lang="scss">
.title {
  position: relative;
  font-family: PingFang SC;
  font-weight: 500;
  font-size: 13px;
  color: #303133;
}

.title::before {
  content: '';
  background-color: #1890ff;
  width: 3px;
  height: 14px;
  position: absolute;
  left: -10px;
  top: 50%;
  margin-top: -7px;
}

.box {
  display: flex;
  align-items: center;
  font-family: PingFang SC;
  font-weight: 400;
  font-size: 13px;
  color: #303133;
}

.mlr6 {
  margin: 0 6px;
}
.condition-box {
  width: 100%;
  height: 500px;
  overflow-y: auto;
}
</style>

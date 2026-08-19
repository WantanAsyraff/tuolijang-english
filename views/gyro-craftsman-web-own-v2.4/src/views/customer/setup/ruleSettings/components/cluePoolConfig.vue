<template>
  <div>
    <!-- 线索池配置 -->
    <div class="card-box">
      <el-card :body-style="{ padding: '20px 20px 0' }" class="box-card">
        <div class="setting-container">
          <el-form
            ref="elForm"
            :model="formData"
            :rules="rules"
            size="medium"
            label-width="125px"
            label-position="right"
          >
            <div class="card-list">
              <div class="head">
                <span class="title">{{ $("ui.customerSetupRuleSettingsCluePoolConfigReminderRules") }}</span>
              </div>
              <el-form-item :label="$('ui.customerSetupRuleSettingsCluePoolConfigLeadFollowUpReminder')" prop="clue_follow_switch">
                <el-switch
                  v-model="formData.clue_follow_switch"
                  :active-value="1"
                  :inactive-value="0"
                  active-color="#1890ff"
                  :active-text="$('开启')"
                  inactive-color="#cccccc"
                  :inactive-text="$('关闭')"
                >
                </el-switch>
              </el-form-item>

              <el-form-item :label="$('ui.customerSetupRuleSettingsCluePoolConfigLeadFollowUpReminder')" prop="clue_follow_date" v-if="formData.clue_follow_switch">
                <el-input
                  v-model="formData.clue_follow_date"
                  clearable
                  :placeholder="$('ui.customerSetupRuleSettingsCluePoolConfigEnterAPositiveInteger')"
                  show-word-limit
                  size="small"
                >
                  <template slot="suffix">{{ $("ui.hrApprovaTimeDay") }}</template>
                </el-input>
                <div class="info">{{ $("ui.customerSetupRuleSettingsCluePoolConfigSetHowOftenActiveLeadsMustBeFollowedUp") }}</div>
              </el-form-item>
              <!-- <div class="dash-line"></div> -->
              <div class="head mt20">
                <span class="title">{{ $("ui.customerSetupRuleSettingsCluePoolConfigLeadPoolReturnRules") }}</span>
              </div>
              <el-form-item :label="$('ui.customerSetupRuleSettingsCluePoolConfigAutomaticLeadPoolReturnRules')" prop="return_clue_switch" label-width="auto">
                <el-switch
                  v-model="formData.return_clue_switch"
                  :active-value="1"
                  :inactive-value="0"
                  active-color="#1890ff"
                  :active-text="$('开启')"
                  inactive-color="#cccccc"
                  :inactive-text="$('关闭')"
                >
                </el-switch>
              </el-form-item>
              <template v-if="formData.return_clue_switch">
                <el-form-item :label="$('ui.customerSetupRuleSettingsCluePoolConfigReturnIfNotConvertedToCustomer')" prop="return_clue_date">
                  <el-input
                    v-model="formData.return_clue_date"
                    clearable
                    :placeholder="$('ui.customerSetupRuleSettingsCluePoolConfigEnterAPositiveInteger')"
                    show-word-limit
                    size="small"
                  >
                    <template slot="suffix">{{ $("ui.hrApprovaTimeDay") }}</template>
                  </el-input>
                  <div class="info">{{ $("ui.customerSetupRuleSettingsCluePoolConfigUsedToSetHowManyDaysBeforeUnconvertedLeads") }}</div>
                </el-form-item>
                <el-form-item :label="$('ui.customerSetupRuleSettingsCluePoolConfigReturnIfNotFollowedUp')" prop="return_clue_cycle">
                  <el-input
                    v-model="formData.return_clue_cycle"
                    clearable
                    :placeholder="$('ui.customerSetupRuleSettingsCluePoolConfigEnterAPositiveInteger')"
                    show-word-limit
                    size="small"
                  >
                    <template slot="suffix">{{ $("ui.hrApprovaTimeDay") }}</template>
                  </el-input>
                  <div class="info">{{ $("ui.customerSetupRuleSettingsCluePoolConfigSetHowManyDaysWithoutFollowUpBeforeA") }}</div>
                </el-form-item>
                <el-form-item :label="$('ui.customerSetupRuleSettingsCluePoolConfigLeadReturnReminder')" prop="return_clue_remind">
                  <el-input
                    v-model="formData.return_clue_remind"
                    clearable
                    :placeholder="$('ui.customerSetupRuleSettingsCluePoolConfigEnterAPositiveInteger')"
                    show-word-limit
                    size="small"
                  >
                    <template slot="suffix">{{ $("ui.hrApprovaTimeDay") }}</template>
                  </el-input>
                  <div class="info">{{ $("ui.customerSetupRuleSettingsCluePoolConfigSetHowManyDaysInAdvanceToRemindUsers") }}</div>
                </el-form-item>
              </template>
              <div class="head">
                <span class="title">{{ $("ui.customerSetupRuleSettingsCluePoolConfigLeadRetentionRules") }}</span>
              </div>
              <el-form-item :label="$('ui.customerSetupRuleSettingsCluePoolConfigLeadRetentionRules2')" prop="clue_policy_switch">
                <el-switch
                  v-model="formData.clue_policy_switch"
                  :active-value="1"
                  :inactive-value="0"
                  active-color="#1890ff"
                  :active-text="$('开启')"
                  inactive-color="#cccccc"
                  :inactive-text="$('关闭')"
                >
                </el-switch>
              </el-form-item>
              <el-form-item :label="$('ui.customerSetupRuleSettingsCluePoolConfigPolicyCount')" prop="clue_policy_count" v-if="formData.clue_policy_switch">
                <el-input
                  v-model="formData.clue_policy_count"
                  clearable
                  :placeholder="$('ui.customerSetupRuleSettingsCluePoolConfigEnterAPositiveInteger')"
                  show-word-limit
                  size="small"
                >
                  <template slot="suffix">{{ $("ui.commonOaFromBoxItems") }}</template>
                </el-input>
                <div class="info">{{ $("ui.customerSetupRuleSettingsCluePoolConfigSetTheMaximumNumberOfLeadsEachSalespersonCan") }}</div>
              </el-form-item>
            </div>
          </el-form>
        </div>
      </el-card>
      <div class="cr-bottom-button">
        <el-button type="primary" @click="saveEvt" size="small">{{ $("ui.formDesignerFormWidgetFieldWidgetRichTextWidgetSave") }}</el-button>
      </div>
    </div>
  </div>
</template>

<script>
import { $ } from '@/lang'
export default {
  name: 'CluePoolConfig',
  props: {
    formData: {
      default: () => {},
      type: Object
    }
  },
  data() {
    return {
      grid1: {
        xl: 2,
        lg: 4,
        md: 2,
        sm: 24,
        xs: 24
      },
      grid2: {
        xl: 20,
        lg: 18,
        md: 20,
        sm: 24,
        xs: 24
      },
      rules: {
        clue_follow_date: [{ required: true, message: $('legacyScript.pleaseEnterCluesBasedOnTheReminder') }],
        return_clue_date: [{ required: true, message: $('legacyScript.pleaseEnterReturnIfNotConvertedToCustomer') }],
        return_clue_cycle: [{ required: true, message: $('legacyScript.pleaseEnterReturnIfNotFollowedUp') }],
        return_clue_remind: [{ required: true, message: $('legacyScript.pleaseEnterCustomerPoolReturnReminder') }],
        clue_policy_count: [{ required: true, message: $('legacyScript.pleaseEnterPolicyCount') }]
      }
    }
  },

  methods: {
    saveEvt() {
      this.$emit('saveEvt')
    }
  }
}
</script>
<style lang="scss" scoped>
.cr-bottom-button {
  left: 14px;
  right: 14px;
  width: initial;
}
.card-box {
  font-size: 13px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  color: #303133;

  .box-card {
    height: calc(100vh - 180px);
    overflow-y: auto;

    .setting-container {
      width: 460px;
      margin: 0 auto;
      .head {
        display: flex;
        margin-bottom: 20px;
        margin-left: -8px;
        .title {
          padding-left: 6px;
          font-size: 14px;
          font-weight: 600;
          position: relative;
        }
        .title:before {
          content: '';
          background-color: #1890ff;
          width: 3px;
          height: 14px;
          position: absolute;
          left: -10px;
          top: 50%;
          margin-top: -7px;
        }
      }
      .card-list {
        // border-spacing: 2px;
        .info {
          margin-top: 10px;
          // margin-left: 10px;
          font-size: 13px;
          line-height: 18px;
          color: #909399;
        }
      }
      .dash-line {
        width: 100%;
        height: 1px;
        background-image: linear-gradient(to right, #dcdfe6 0%, #dcdfe6 50%, transparent 50%);
        background-size: 12px 0.5px; //第一个值（20px）越大线条越长间隙越大
        background-repeat: repeat-x;
        margin-top: 20px;
      }
    }
  }
}
::v-deep .el-card.is-always-shadow {
  border: none;
}
</style>

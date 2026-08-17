<template>
<div>
  <div class="card-box">
    <el-card :body-style="{ padding: '20px 20px 0' }" class="box-card">
      <div class="setting-container">
        <el-form ref="elForm" :model="formData" :rules="rules" size="medium" label-width="125px">
          <div class="card-list">
            <div class="head">
              <span class="line">|</span>
              <span class="title">{{ $("ui.customerSetupRuleSettingsReturnRuleAutomaticReturnToCustomerPool") }}</span>
            </div>
            <el-form-item :label="$('ui.customerSetupRuleSettingsReturnRuleAutomaticReturnToCustomerPool2')" prop="return_high_seas_switch">
              <el-switch
                v-model="formData.return_high_seas_switch"
                :active-value="1"
                :inactive-value="0"
                active-color="#1890ff"
                active-text="开启"
                inactive-color="#cccccc"
                inactive-text="关闭"
              >
              </el-switch>
            </el-form-item>
            <template v-if="formData.return_high_seas_switch">
              <el-form-item :label="$('ui.customerSetupRuleSettingsReturnRuleReturnOpenDeals')" prop="unsettled_cycle">
                <el-input
                  v-model="formData.unsettled_cycle"
                  clearable
:placeholder="$('ui.customerSetupRuleSettingsCluePoolConfigEnterAPositiveInteger')"
                  show-word-limit
                  size="small"
                  style="width: 350px"
                  type="number"
                >
                  <template slot="suffix">{{ $("ui.hrApprovaTimeDay") }}</template>
                </el-input>
                <div class="info">{{ $("ui.customerSetupRuleSettingsReturnRuleUsedToSetHowManyDaysBeforeUnsettledCustomers") }}</div>
              </el-form-item>
              <el-form-item :label="$('ui.customerSetupRuleSettingsCluePoolConfigReturnIfNotFollowedUp')" prop="unfollowed_cycle">
                <el-input
                  v-model="formData.unfollowed_cycle"
                  clearable
:placeholder="$('ui.customerSetupRuleSettingsCluePoolConfigEnterAPositiveInteger')"
                  show-word-limit
                  size="small"
                  style="width: 350px"
                  type="number"
                >
                  <template slot="suffix">{{ $("ui.hrApprovaTimeDay") }}</template>
                </el-input>
                <div class="info">{{ $("ui.customerSetupRuleSettingsReturnRuleUsedToSetHowManyDaysBeforeUnfollowedUnsettled") }}</div>
              </el-form-item>
              <el-form-item :label="$('ui.customerSetupRuleSettingsReturnRuleCustomerPoolReturnReminder')" prop="advance_cycle">
                <el-input
                  v-model="formData.advance_cycle"
                  clearable
:placeholder="$('ui.customerSetupRuleSettingsCluePoolConfigEnterAPositiveInteger')"
                  show-word-limit
                  size="small"
                  style="width: 350px"
                  type="number"
                >
                  <template slot="suffix">{{ $("ui.hrApprovaTimeDay") }}</template>
                </el-input>
                <div class="info">{{ $("ui.customerSetupRuleSettingsReturnRuleUsedToSetHowManyDaysBeforeReturnTo") }}</div>
              </el-form-item>
            </template>
          </div>
          <!-- <div class="dash-line"></div> -->
          <div class="card-list mt30">
            <div class="head">
              <span class="line">|</span>
              <span class="title">{{ $("ui.customerSetupRuleSettingsReturnRuleCustomerPolicyRule") }}</span>
            </div>
            <el-form-item :label="$('ui.customerSetupRuleSettingsReturnRuleCustomerPolicyRule2')" prop="client_policy_switch">
              <el-switch
                v-model="formData.client_policy_switch"
                :active-value="1"
                :inactive-value="0"
                active-color="#1890ff"
                active-text="开启"
                inactive-color="#cccccc"
                inactive-text="关闭"
              >
              </el-switch>
            </el-form-item>
            <template v-if="formData.client_policy_switch">
              <el-form-item :label="$('ui.customerSetupRuleSettingsCluePoolConfigPolicyCount')" prop="unsettled_client_number">
                <el-input
                  v-model="formData.unsettled_client_number"
                  clearable
:placeholder="$('ui.customerSetupRuleSettingsCluePoolConfigEnterAPositiveInteger')"
                  show-word-limit
                  size="small"
                  style="width: 350px"
                  type="number"
                >
                  <template slot="suffix">{{ $("ui.settingAuthAdminIndexIndividual") }}</template>
                </el-input>
                <div class="info">{{ $("ui.customerSetupRuleSettingsReturnRuleUsedToSetHowManyUnsettledCustomersEachSalesperson") }}</div>
              </el-form-item>
            </template>
          </div>
        </el-form>
      </div>
    </el-card>
    <div class="cr-bottom-button">
      <el-button size="small" type="primary" @click="saveEvt">{{ $("ui.formDesignerFormWidgetFieldWidgetRichTextWidgetSave") }}</el-button>
    </div>
  </div>
</div>
</template>
<script>
import { $ } from '@/lang'
export default {
  name: 'ReturnRule',
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
        unsettled_cycle: [{ required: true, message: $('legacyScript.pleaseEnterTheCustomerReturnCycleToThePublicPool') }],
        advance_cycle: [{ required: true, validator: this.checkAdvanceCycle }],
        unsettled_client_number: [{ required: true, message: $('legacyScript.pleaseEnterTheNumberOfUnconvertedCustomers') }],
        unfollowed_cycle: [{ required: true, message: $('legacyScript.pleaseEnterTheNumberOfUnfollowedItemsReturnedToThe') }]
      }
    }
  },
  watch: {
    'formData.unsettled_cycle'(newValue, oldValue) {
      if (newValue === '') {
        return
      }
      const numericValue = parseInt(newValue, 10)
      if (isNaN(numericValue)) {
        this.formData.unsettled_cycle = oldValue || ''
      } else {
        this.formData.unsettled_cycle = Math.abs(numericValue).toString()
      }
    },
    'formData.advance_cycle'(newValue, oldValue) {
      if (newValue === '') {
        return
      }
      const numericValue = parseInt(newValue, 10)
      if (isNaN(numericValue)) {
        this.formData.advance_cycle = oldValue || ''
      } else {
        this.formData.advance_cycle = Math.abs(numericValue).toString()
      }
    },
    'formData.unsettled_client_number'(newValue, oldValue) {
      if (newValue === '') {
        return
      }
      const numericValue = parseInt(newValue, 10)
      if (isNaN(numericValue)) {
        this.formData.unsettled_client_number = oldValue || ''
      } else {
        this.formData.unsettled_client_number = Math.abs(numericValue).toString()
      }
    }
  },
  methods: {
    sendReturnData() {
      const data = this.formData
      this.$emit('returnData', data)
    },
    saveEvt() {
      this.$emit('saveEvt')
    },
    checkAdvanceCycle(rule, value, callback) {
      if (value === '') {
        callback(new Error('请输入客户退回公海提醒提前天数'))
      } else if (
        parseInt(value, 10) >= parseInt(this.formData.unsettled_cycle, 10) ||
        parseInt(value, 10) >= parseInt(this.formData.unfollowed_cycle, 10)
      ) {
        callback(new Error('退回公海提醒天数要小于未成交退回天数和未跟进退回天数'))
      } else {
        callback()
      }
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
      // padding-bottom: 20px;
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
        .line {
          width: 3px;
          background-color: #1890ff;
          color: #1890ff;
        }
      }
      .card-list {
        border-spacing: 2px;
        .info {
          margin-top: 10px;
          font-size: 13px;
          line-height: 18px;
          color: #909399;
        }
      }
      .dash-line {
        // width: 100%;
        // height: 1px;
        // background-image: linear-gradient(to right, #dcdfe6 0%, #dcdfe6 50%, transparent 50%);
        // background-size: 12px 0.5px; //第一个值（20px）越大线条越长间隙越大
        // background-repeat: repeat-x;
        // margin-top: 20px;
      }
    }
  }
}
::v-deep .el-card.is-always-shadow {
  border: none;
}
::v-deep .el-form-item__label {
  line-height: 36px;
}
.el-form-item::before,
.el-form-item::after {
  display: none;
}
::v-deep .el-form-item__content::before,
::v-deep .el-form-item__content::after {
  display: none;
}
</style>

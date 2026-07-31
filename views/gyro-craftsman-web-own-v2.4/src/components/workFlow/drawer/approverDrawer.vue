<template>
<el-drawer
  :append-to-body="true"
  :title="$t('ui.workFlowDrawerApproverDrawerApproverSettings')"
  :visible.sync="$store.state.business.approverDrawer"
  direction="rtl"
  class="set_promoter"
  size="550px"
  :before-close="closeDrawer"
>
  <div class="demo-drawer__content">
    <div class="drawer_content">
      <div class="approver_content">
        <el-radio-group v-model="approverConfig.settype" class="clear" @change="changeType">
          <el-radio label="1">{{ $t("ui.workFlowDrawerApproverDrawerSpecifiedMembers") }}</el-radio>
          <el-radio label="2">{{ $t("ui.workFlowDrawerApproverDrawerSpecifiedSupervisor") }}</el-radio>
          <el-radio label="7">{{ $t("ui.workFlowDrawerApproverDrawerConsecutiveSupervisors") }}</el-radio>
          <el-radio label="5">{{ $t("ui.workFlowDrawerApproverDrawerApplicant") }}</el-radio>
          <el-radio label="4" v-if="typeStr !== 'lowCode'">{{ $t("ui.workFlowDrawerApproverDrawerSelectedByApplicant") }}</el-radio>
        </el-radio-group>
      </div>
      <div class="approver_node_user" v-if="approverConfig.settype == 1">
        <p class="title mt15">
          {{ $t("ui.workFlowDrawerApproverDrawerSpecifiedMembers") }}
          <span>{{ $t("ui.workFlowDrawerApproverDrawerNoMoreThan100People") }}</span>
        </p>
        <select-member
          :value="approverConfig.nodeUserList || []"
          @getSelectList="getSelectList"
          style="width: 100%"
        ></select-member>

        <div v-if="approverConfig.nodeUserList.length > 1">
          <p class="title mt15">{{ $t("ui.workFlowDrawerApproverDrawerMultipleApproverMethod") }}</p>
          <el-radio-group v-model="approverConfig.examineMode" class="more-content" style="width: 100%">
            <el-radio label="1">{{ $t("ui.workFlowDrawerApproverDrawerAnyApproverOneApprovalIsSufficient") }}</el-radio>
            <el-radio label="2">{{ $t("ui.workFlowDrawerApproverDrawerAllApproversEveryoneMustApprove") }}</el-radio>
            <el-radio label="3">{{ $t("ui.workFlowDrawerApproverDrawerSequentialApprovalApproveInOrder") }}</el-radio>
          </el-radio-group>
          <div v-if="approverConfig.examineMode === '1'">
            <p class="title mt15">
              {{ $t("ui.workFlowDrawerApproverDrawerApprovalRatio") }}
              <span style="color: #999; font-size: 12px; font-weight: normal;">{{ $t("ui.workFlowDrawerApproverDrawerThisStepIsApprovedWhenTheApprovalRatioIs") }}</span>
            </p>
            <div class="approval-rate-input">
              <el-input-number
                v-model="approverConfig.pass_ratio"
                :precision="0"
                :min="0"
                :max="100"
                :step="1"
                controls-position="right"
                style="width: 100%"
              ></el-input-number>
              <span class="approval-rate-suffix">%</span>
            </div>
          </div>
        </div>
      </div>
      <div class="approver_manager" v-if="approverConfig.settype == 2">
        <p class="title">{{ $t("ui.workFlowDrawerApproverDrawerSpecifiedLevel") }}</p>
        <el-row>
          <el-col :span="11">
            <el-select v-model="approverConfig.directorOrder">
              <el-option value="0" :label="$t('ui.workFlowDrawerApproverDrawerTopToBottom')"></el-option>
              <el-option value="1" :label="$t('ui.workFlowDrawerApproverDrawerBottomToTop')"></el-option>
            </el-select>
          </el-col>
          <el-col :span="11" class="pull-right">
            <el-select v-model="approverConfig.directorLevel">
              <el-option
                v-for="item in directorMaxLevel"
                :label="item == 1 ? $t('ui.workFlowDrawerApproverDrawerDirectSuperior') : $t('ui.workFlowDrawerApproverDrawerThe') + item + $t('ui.workFlowDrawerApproverDrawerLevelSAbove')"
                :value="item.toString()"
                :key="item"
              ></el-option>
            </el-select>
          </el-col>
        </el-row>
      </div>
      <div class="approver_manager" v-if="approverConfig.settype == 7">
        <p class="title">
          {{ $t("ui.workFlowDrawerApproverDrawerSpecifiedEndpoint") }}
          <span>{{ $t("ui.workFlowDrawerApproverDrawerApproveSequentiallyFromTheApplicantSDirectSupervisorThrough") }}</span>
        </p>
        <el-row>
          <el-col :span="11">
            <el-select v-model="approverConfig.directorOrder">
              <el-option value="0" :label="$t('ui.workFlowDrawerApproverDrawerTopToBottom')"></el-option>
              <el-option value="1" :label="$t('ui.workFlowDrawerApproverDrawerBottomToTop')"></el-option>
            </el-select>
          </el-col>
          <el-col :span="11" class="pull-right">
            <el-select v-model="approverConfig.directorLevel">
              <el-option
                v-for="item in directorMaxLevel"
                :label="item === 1 ? $t('ui.workFlowDrawerApproverDrawerDirectSuperior') : $t('ui.workFlowDrawerApproverDrawerThe') + item + $t('ui.workFlowDrawerApproverDrawerLevelSAbove')"
                :value="item.toString()"
                :key="item"
              ></el-option>
            </el-select>
          </el-col>
        </el-row>
      </div>
      <div class="approver_self" v-if="approverConfig.settype == 5">
        <p>{{ $t("ui.workFlowDrawerApproverDrawerWhenThisStepIsSetToTheInitiatorThe") }}</p>
      </div>
      <div class="approver_self_select" v-show="approverConfig.settype == 4">
        <h3>{{ $t("ui.workFlowDrawerApproverDrawerSelectableRange") }}</h3>
        <el-radio-group v-model="approverConfig.selectRange" style="width: 100%" @change="changeRange">
          <el-radio label="1">{{ $t("ui.workFlowDrawerApproverDrawerNoRangeLimit") }}</el-radio>
          <el-radio label="2">{{ $t("ui.workFlowDrawerApproverDrawerSpecifiedMembers") }}</el-radio>
        </el-radio-group>
        <select-member
          v-if="approverConfig.selectRange == 2"
          :value="approverConfig.nodeUserList || []"
          @getSelectList="getSelectList"
          style="width: 100%"
        ></select-member>

        <h3>{{ $t("ui.workFlowDrawerApproverDrawerSelectionMethod") }}</h3>
        <el-radio-group v-model="approverConfig.selectMode" style="width: 100%">
          <el-radio label="1">{{ $t("ui.workFlowDrawerApproverDrawerSingleSelect") }}</el-radio>
          <el-radio label="2">{{ $t("ui.workFlowDrawerApproverDrawerMultipleSelection") }}</el-radio>
        </el-radio-group>
        <div v-if="approverConfig.selectMode == 2">
          <h3>{{ $t("ui.workFlowDrawerApproverDrawerMultipleApproverMethod") }}</h3>
          <el-radio-group v-model="approverConfig.examineMode" class="more-content" style="width: 100%">
            <el-radio label="1">{{ $t("ui.workFlowDrawerApproverDrawerAnyApproverOneApprovalIsSufficient") }}</el-radio>
            <el-radio label="2">{{ $t("ui.workFlowDrawerApproverDrawerAllApproversEveryoneMustApprove") }}</el-radio>
            <el-radio label="3">{{ $t("ui.workFlowDrawerApproverDrawerSequentialApprovalApproveInOrder") }}</el-radio>
          </el-radio-group>
          <div v-if="approverConfig.examineMode === '1'">
            <p class="title mt15">
              {{ $t("ui.workFlowDrawerApproverDrawerApprovalRatio") }}
              <span style="color: #999; font-size: 12px; font-weight: normal;">{{ $t("ui.workFlowDrawerApproverDrawerThisStepIsApprovedWhenTheApprovalRatioIs") }}</span>
            </p>
            <div class="approval-rate-input">
              <el-input-number
                v-model="approverConfig.pass_ratio"
                :precision="0"
                :min="0"
                :max="100"
                :step="1"
                controls-position="right"
                style="width: 100%"
              ></el-input-number>
              <span class="approval-rate-suffix">%</span>
            </div>
          </div>
        </div>
      </div>
      <div class="approver_some" v-if="approverConfig.settype == 2 || approverConfig.settype == 7">
        <p class="title">{{ $t("ui.workFlowDrawerApproverDrawerWhenTheCurrentLevelHasNoDepartmentManager") }}</p>
        <el-radio-group class="person" v-model="approverConfig.noHanderAction">
          <el-radio label="1">{{ $t("ui.workFlowDrawerApproverDrawerUseTheManagerOfTheParentDepartment") }}</el-radio>
          <el-radio label="2">{{ $t("ui.workFlowDrawerApproverDrawerSkipThisStepWhenItHasNoApprover") }}</el-radio>
        </el-radio-group>
      </div>
    </div>
    <div class="button from-foot-btn fix btn-shadow">
      <el-button size="small" @click="closeDrawer">{{ $t('public.cancel') }}</el-button>
      <el-button size="small" type="primary" @click="saveApprover">{{ $t('public.ok') }}</el-button>
    </div>
  </div>
</el-drawer>
</template>
<script>
export default {
  props: {
    directorMaxLevel: {
      type: Number,
      default: 5
    },
    typeStr: {
      // 判断是低代码还是审批流程
      type: String,
      default: ''
    }
  },
  components: {
    selectMember: () => import('@/components/form-common/select-member')
  },
  data() {
    return {
      approverConfig: {},
      type: 1,
      checkedList: []
    }
  },
  computed: {
    approverConfig1() {
      return this.$store.state.business.approverConfig.value
    }
  },
  watch: {
    approverConfig1(val) {
      this.approverConfig = val
    }
  },
  methods: {
    changeRange() {
      this.approverConfig.nodeUserList = []
      this.checkedList = []
    },
    changeType(val) {
      this.approverConfig.nodeUserList = []
      this.checkedList = []
      this.approverConfig.examineMode = '3'
      this.approverConfig.noHanderAction = '2'
      if (val == 2) {
        this.approverConfig.directorLevel = '1'
      } else if (val == 4) {
        this.approverConfig.selectMode = '1'
        this.approverConfig.selectRange = '1'
      } else if (val == 7) {
        this.approverConfig.directorLevel = '1'
      }
    },
    saveApprover() {
      if (
        this.approverConfig.settype == 1 ||
        (this.approverConfig.settype == 4 && this.approverConfig.selectRange == 2)
      ) {
        if (this.approverConfig.nodeUserList.length <= 0) {
          this.$message.warning('至少选择一个指定成员')
          return false
        }
      }
      this.approverConfig.error = !this.$func.setApproverStr(this.approverConfig)
      this.$store.commit('updateApproverConfig', {
        value: this.approverConfig,
        flag: true,
        id: this.$store.state.business.approverConfig.id
      })
      this.$emit('update:nodeConfig', this.approverConfig)
      this.closeDrawer()
    },
    closeDrawer() {
      this.$store.commit('updateApprover', false)
    },
    // 选择成员完成回调
    getSelectList(data) {
      this.approverConfig.nodeUserList = data
    }
  }
}
</script>
<style lang="scss">
.set_promoter {
  .approver_content {
    padding-bottom: 10px;
    font-size: 13px;
    border-bottom: 1px solid #f2f2f2;
  }
  .approver_self_select .el-button,
  .approver_content .el-button {
    margin-bottom: 20px;
  }
  .approver_content .el-radio,
  .approver_some .el-radio,
  .approver_self_select .el-radio {
    margin-bottom: 20px;
  }
  .approver_node_user {
    padding: 0 20px;
    .title {
      font-size: 14px;
      font-weight: bold;
      span {
        font-size: 13px;
        color: #999999;
        font-weight: normal;
      }
    }
  }
  .approver_manager {
    p {
      line-height: 32px;
    }
    .title {
      font-size: 14px;
      font-weight: bold;
      span {
        font-size: 13px;
        color: #999999;
        font-weight: normal;
      }
    }
  }
  .approver_self {
    padding: 28px 20px;
    p {
      font-size: 14px;
    }
  }
  .approver_self_select,
  .approver_manager,
  .approver_content,
  .approver_some {
    padding: 20px 20px 0;
    .title {
      font-size: 14px;
      font-weight: bold;
    }
    .person {
      .el-radio {
        margin-bottom: 15px;
        display: block;
      }
    }
  }
  .approver_manager p:first-of-type,
  .approver_some p {
    line-height: 19px;
    font-size: 14px;
    margin-bottom: 14px;
  }
  .approver_self_select {
    h3 {
      margin: 5px 0 20px;
      font-size: 14px;
      font-weight: bold;
      line-height: 19px;
    }
  }
  .more-content {
    .el-radio {
      display: block;
      margin-bottom: 15px;
    }
  }
  .approval-rate-input {
    position: relative;
    display: inline-block;
    width: 100%;
    .approval-rate-suffix {
      position: absolute;
      right: 40px;
      top: 50%;
      transform: translateY(-50%);
      color: #999;
      font-size: 12px;
      pointer-events: none;
    }
  }
}
.plan-footer-one {
  height: auto;
}
</style>

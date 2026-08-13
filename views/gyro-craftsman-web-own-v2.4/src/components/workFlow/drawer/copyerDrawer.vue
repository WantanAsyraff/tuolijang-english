import { $ } from '@/lang'
<template>
<el-drawer
  :append-to-body="true"
  :title="title"
  :visible.sync="$store.state.business.copyerDrawer"
  direction="rtl"
  class="set_copyer"
  size="550px"
  :before-close="closeDrawer"
>
  <div class="demo-drawer__content">
    <div class="copyer_content drawer_content">
      <p class="title">
        {{ $("ui.workFlowDrawerApproverDrawerSpecifiedMembers") }}
        <span>{{ $("ui.workFlowDrawerApproverDrawerNoMoreThan100People") }}</span>
      </p>
      <select-member
        :value="checkedList || []"
        :placeholder="$('ui.workFlowDrawerCopyerDrawerSelectMembers')"
        @getSelectList="getSelectList"
        style="width: 100%"
      ></select-member>

      <p class="title mt20">
        {{ $("ui.workFlowDrawerApproverDrawerSpecifiedSupervisor") }}
        <span>{{ $("ui.workFlowDrawerCopyerDrawerAllowSpecifiedSupervisorsAsCcRecipients") }}</span>
      </p>
      <el-select multiple v-model="copyerConfig.departmentHead">
        <el-option
          v-for="item in directorMaxLevel"
          :label="item === 1 ? $('ui.workFlowDrawerCopyerDrawerDirectSuperior') : $('ui.workFlowDrawerApproverDrawerThe') + item + $('ui.workFlowDrawerCopyerDrawerLevelS') + $('ui.workFlowDrawerCopyerDrawerSuperior')"
          :value="item.toString()"
          :key="item"
        ></el-option>
      </el-select>
      <p class="title mt20" v-if="typeStr !== 'lowCode'">
        {{ $("ui.workFlowDrawerApproverDrawerSelectedByApplicant") }}
        <span>{{ $("ui.workFlowDrawerCopyerDrawerAllowApplicantsToSelectCcRecipients") }}</span>
      </p>
      <el-checkbox-group v-model="ccSelfSelectFlag" class="mt15" v-if="typeStr !== 'lowCode'">
        <el-checkbox label="1">{{ $("ui.workFlowDrawerCopyerDrawerAllowApplicantsToSelectCcRecipients") }}</el-checkbox>
      </el-checkbox-group>
    </div>
    <div class="button from-foot-btn fix btn-shadow">
      <el-button size="small" @click="closeDrawer">{{ $('public.cancel') }}</el-button>
      <el-button size="small" type="primary" @click="saveCopyer">{{ $('public.ok') }}</el-button>
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
      title: $('legacyScript.cCRecipientSettings'),
      memberShow: false,
      copyerConfig: {},
      ccSelfSelectFlag: [],
      checkedList: []
    }
  },
  computed: {
    copyerConfig1() {
      return this.$store.state.business.copyerConfig.value
    }
  },
  watch: {
    copyerConfig1(val) {
      this.copyerConfig = val
      this.ccSelfSelectFlag = this.copyerConfig.ccSelfSelectFlag == 0 ? [] : [this.copyerConfig.ccSelfSelectFlag]
      this.checkedList = this.copyerConfig.nodeUserList
    }
  },
  methods: {
    saveCopyer() {
      if (
        this.copyerConfig.departmentHead.length <= 0 &&
        this.checkedList.length <= 0 &&
        this.ccSelfSelectFlag.length <= 0
      ) {
        this.$message.warning($('customer.placeholder22'))
        return false
      }
      this.copyerConfig.ccSelfSelectFlag = this.ccSelfSelectFlag.length == 0 ? 0 : '1'
      this.copyerConfig.nodeUserList = this.checkedList
      this.$store.commit('updateCopyerConfig', {
        value: this.copyerConfig,
        flag: true,
        id: this.$store.state.business.copyerConfig.id
      })
      this.closeDrawer()
    },
    closeDrawer() {
      this.$store.commit('updateCopyer', false)
    },
    // 选择成员完成回调
    getSelectList(data) {
      this.checkedList = data
    }
  }
}
</script>

<style lang="scss" scoped>
.set_copyer {
  .copyer_content {
    padding: 20px 20px 0;
    .title {
      font-size: 14px;
      font-weight: bold;
      span {
        font-size: 13px;
        color: #999999;
        font-weight: normal;
      }
    }
    .el-select--medium {
      width: 100%;
    }
    .el-button {
      margin-bottom: 20px;
    }
    .el-checkbox {
      margin-bottom: 20px;
    }
  }
  ::v-deep .plan-footer-one {
    height: auto;
  }
}
.from-foot-btn button {
  width: auto;
  height: auto;
}
</style>

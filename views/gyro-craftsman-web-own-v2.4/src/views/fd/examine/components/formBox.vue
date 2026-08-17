<template>
<div>
  <el-form :inline="true" class="from-s">
    <el-row class="flex-row">
      <el-form-item :label="$('customer.businesstype')" class="select-bar">
        <el-select v-model="tableFrom.type" size="small" @change="handleConfirm">
          <el-option v-for="item in sexOptions" :key="item.value" :label="item.label" :value="item.value"></el-option>
        </el-select>
      </el-form-item>

      <el-form-item :label="$('ui.fdExamineFormBoxManagementScope')" class="select-bar">
        <manage-range ref="manageRange" @change="changeMastart"></manage-range>
      </el-form-item>
      <el-form-item :label="$('ui.fdExamineFormBoxReviewStatus')" class="select-bar">
        <el-select v-model="tableFrom.status" size="small" @change="handleConfirm">
          <el-option
            v-for="(itemn, indexn) in fromStatus"
            :key="indexn"
            :value="itemn.val"
            :label="itemn.text"
          ></el-option>
        </el-select>
      </el-form-item>

      <el-form-item class="select-bar">
        <el-select
          class="sel"
          size="small"
          style="width: 100px"
          :placeholder="$('ui.fdExamineFormBoxTimeType')"
          v-model="tableFrom.time_field"
        >
          <el-option :label="$('ui.fdExamineFormBoxPaymentDate')" value="date"></el-option>
          <el-option :label="$('ui.fdExamineFormBoxApplicationDate')" value="time"></el-option>
        </el-select>

        <el-date-picker
          class="time"
          v-model="paymentTimeVal"
          size="small"
          type="daterange"
          :placeholder="$('toptable.selecttime')"
          format="yyyy/MM/dd"
          value-format="yyyy/MM/dd"
          :range-separator="$('toptable.to')"
          :start-placeholder="$('toptable.startdate')"
          :end-placeholder="$('toptable.endingdate')"
          clearable
          :picker-options="pickerOptions"
          @change="establishTime"
        />
      </el-form-item>
      <el-form-item>
        <el-tooltip effect="dark" :content="$('ui.administrationMaterialFixedRecordResetSearchConditions')" placement="top">
          <div class="reset" @click="reset"><i class="iconfont iconqingchu"></i></div>
        </el-tooltip>
      </el-form-item>
    </el-row>
  </el-form>
</div>
</template>

<script>
import { $ } from '@/lang'
import manageRange from '@/components/form-common/select-manageRange'

export default {
  name: 'FormBox',
  components: { manageRange },
  props: ['activeName'],
  data() {
    return {
      pickerOptions: this.$pickerOptionsTimeEle,
      fromStatus: [
        { text: this.$('toptable.all'), val: '' },
        { text: this.$('customer.audit'), val: 0 },
        { text: this.$('customer.passed'), val: 1 },
        { text: this.$('customer.fail'), val: 2 }
      ],

      paymentTimeVal: [
        this.$moment().startOf('months').format('YYYY/MM/DD'),
        this.$moment().endOf('months').format('YYYY/MM/DD')
      ],

      tableFrom: {
        time_field: 'date',
        type: '',
        status: '',
        name: '',
        time:
          this.$moment().startOf('months').format('YYYY/MM/DD') +
          '-' +
          this.$moment().endOf('months').format('YYYY/MM/DD'),
        salesman_name: '',
        scope_frame: 'all'
      },
      sexOptions: [
        { label: $('finance.all'), value: '' },
        { label: $('customer.orderPayment'), value: 0 },
        { label: $('customer.orderRenewal'), value: 1 },
        { label: $('customer.orderExpense'), value: 2 }
      ]
    }
  },
  computed: {
    lang: function () {
      return this.$store.getters.lang
    }
  },
  watch: {
    lang() {
      this.setOptions()
    }
  },
  methods: {
    setOptions() {
      this.sexOptions = [
        { label: this.$('finance.all'), value: 2 },
        { label: this.$('customer.Contractpayment'), value: 1 },
        { label: this.$('customer.contractrenewal'), value: 0 }
      ]
      this.fromStatus = [
        { text: this.$('toptable.all'), val: '' },
        { text: this.$('customer.audit'), val: 0 },
        { text: this.$('customer.passed'), val: 1 },
        { text: this.$('customer.fail'), val: 2 }
      ]
    },

    changeStatus() {
      this.confirmData()
    },

    // 确认
    handleConfirm() {
      this.confirmData()
    },
    handType(e) {
      if (e === '') {
        this.tableFrom.type = ''
      }
      this.confirmData()
    },
    // 选择成员完成回调
    changeMastart(data) {
      this.tableFrom.scope_frame = data
      this.confirmData()
    },
    establishTime(e) {
      if (e == null) {
        this.tableFrom.time = ''
        this.confirmData()
      } else {
        this.establishTimeVal = e
        this.tableFrom.time = this.establishTimeVal[0] + '-' + this.establishTimeVal[1]
        this.confirmData()
      }
    },
    // 类型
    handStatus(e) {
      this.tableFrom.status = e
    },
    reset() {
      this.tableFrom = {
        time:
          this.$moment().startOf('months').format('YYYY/MM/DD') +
          '-' +
          this.$moment().endOf('months').format('YYYY/MM/DD'),
        type: '',
        status: '',
        search: '',
        select: 2,
        time_field: 'date',
        scope_frame: 'all'
      }
      this.$refs.manageRange.frame_id = 'all'
      this.paymentTimeVal = [
        this.$moment().startOf('months').format('YYYY/MM/DD'),
        this.$moment().endOf('months').format('YYYY/MM/DD')
      ]
      this.confirmData()
    },
    confirmData() {
      this.$emit('confirmData', this.tableFrom)
    }
  }
}
</script>

<style lang="scss" scoped></style>

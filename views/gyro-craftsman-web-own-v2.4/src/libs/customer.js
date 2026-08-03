import i18n from '@/lang'
import moment from 'moment'

/**
 * 客户状态的文字判断
 * @param {Object} row
 * @returns {String}
 */
export const getAbnormalText = (row) => {
  let now = moment()
  let str = ''
  if (now < moment(row.start_date)) {
    str = '未开始'
  } else if (now > moment(row.end_date + ' 23:59:59')) {
    str = '已结束'
  } else if (now > moment(row.start_date) && now <= moment(row.end_date + ' 23:59:59')) {
    str = '进行中'
  } else {
    str = '进行中'
  }
  return str
}

/**
 * 客户状态按钮类型判断
 * @param {Object} row
 * @returns {String}
 */
 export const getAbnormalTagType = (row) => {
  let now = moment()
  let tagType = null
  if (now < moment(row.start_date)) {
    tagType = 'success'
  } else if (now > moment(row.end_date + ' 23:59:59')) {
    tagType = 'info'
  } else if (now > moment(row.start_date) && now <= moment(row.end_date + ' 23:59:59')) {
    tagType = ''
  }
  return tagType
}

/**
 * 订单状态的文字判断
 * @param {Object} row
 * @returns {String}
 */
export const getContractText = (row) => {
  let str = ''
  if (row.contract_status == '0') {
    str = '未开始'
  } else if (row.contract_status == '1') {
    str = '进行中'
  } else if (row.contract_status == '2') {
    str = '已结束'
  } else {
    str = '异常订单'
  }
  return str
}

/**
 * 订单状态按钮类型判断
 * @param {Object} row
 * @returns {String}
 */
 export const getContractTagType = (row) => {
  let tagType = null
  if (row.contract_status == '0') {
    tagType = 'warning'
  } else if (row.contract_status == '1') {
    tagType = ''
  } else if (row.contract_status == '2') {
    tagType = 'info'
  } else {
    tagType = 'danger'
  }
  return tagType
}

/**
 * 根据发票状态判断的返回文字
 * * @param {Number} status
 * @returns {String}
 */
let invoiceText = {
  0: '待审核',
  1: '待开票',
  2: '已拒绝',
  3: '撤回开票',
  '-1': '已作废',
  4: '申请作废',
  5: '已开票'
}
export const getInvoiceText = (status) => {
  return invoiceText[status]
}

/**
 * 根据发票状态判断的返回颜色 class
 * * @param {Number} status
 * @returns {String}
 */
export const getInvoiceClassName = (status) => {
  let className = ''
  if (status === -1 || status === 6) {
    className = 'blue'
  } else if (status === 0 ) {
    className = 'blue'
  } else if(status === 3){
 className = 'gray'
  } else if (status === 2 || status === 4) {
    className = 'red'
  } else if (status === 5  ) {
    className = 'green'
  }
  else if (status === 1) {
    className = 'yellow'
  }
  return className
}

/**
 * 获取发票名称
 * @param {Number} status
 * @returns {String}
 */
const invoiceType = {
  1: '个人普通发票',
  2: '企业普通发票',
  3: '企业专用发票'
}
export const getInvoiceType = (status) => {
  return invoiceType[status]
}

/**
 * 发票审核状态筛选下拉框
 */
export const selectInvoiceTitle = [
  { label: i18n.t('finance.all'), value: '' },
  { label: i18n.t('customer.pendingApproval'), value: 0 },
  { label: i18n.t('ui.customerInvoiceIndexPendingInvoicing'), value: 1 },
  { label: i18n.t('ui.userExamineExamineRejected'), value: 2 },
  { label: i18n.t('ui.customerContractPaymentTableWithdrawInvoice'), value: 3 },
  { label: i18n.t('ui.customerInvoiceIndexApplyToVoid'), value: 4 },
  { label: i18n.t('customer.invoiced'), value: 5 },
  { label: i18n.t('ui.customerInvoiceIndexVoided'), value: -1 }
]

/**
 * 发票审核状态筛选下拉框
 */
export const selectInvoiceFd = [
  { label: i18n.t('ui.customerInvoiceIndexPendingInvoicing'), value: 1 },
  { label: i18n.t('ui.customerContractPaymentTableWithdrawInvoice'), value: 3 },
  { label: i18n.t('ui.customerInvoiceIndexApplyToVoid'), value: 4 },
  { label: i18n.t('customer.invoiced'), value: 5 },
  { label: i18n.t('ui.customerInvoiceIndexVoided'), value: -1 }
]

/**
 * 发票类型筛选下拉框
 */
export const selectInvoiceType = [
  { value: '', label: i18n.t('finance.all') },
  { value: 1, label: i18n.t('customer.personalinvoice') },
  { value: 2, label: i18n.t('customer.enterpriseinvoice') },
  { value: 3, label: i18n.t('customer.specialinvoice') }
]

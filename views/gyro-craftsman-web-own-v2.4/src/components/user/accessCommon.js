import i18n from '@/lang'
// @FileDescription: 绩效考核用到的静态数据、函数、变量
const periodOptions = [
  { value: 1, label: i18n.t('hr.weeklyassessment') },
  { value: 2, label: i18n.t('hr.monthlyassessment') },
  { value: 5, label: i18n.t('legacyScript.quarterlyAssessment') },
  { value: 4, label: i18n.t('legacyScript.semiannualAssessment') },
  { value: 3, label: i18n.t('hr.annualassessment') }
]
const periodOption = [
  { value: 1, label: i18n.t('hr.weeklyassessment') },
  { value: 2, label: i18n.t('hr.monthlyassessment') },
  { value: 5, label: i18n.t('legacyScript.quarterlyAssessment') },
  { value: 4, label: i18n.t('legacyScript.semiannualAssessment') },
  { value: 3, label: i18n.t('hr.annualassessment') }
]
const statusOptions = [
  { name: '目标制定', value: '0' },
  { name: '执行期', value: 1 },
  { name: '上级评价', value: 2 },
  { name: '绩效审核', value: 3 },
  { name: '结束', value: 4 },
  { name: '未开始', value: 5 }
]
function getStatusText(id) {
  if (id == 0) {
    return '目标制定'
  } else if (id == 1) {
    return '执行期'
  } else if (id == 2) {
    return '上级评价'
  } else if (id == 3) {
    return '绩效审核'
  } else if (id == 4) {
    return '结束'
  } else if (id == 5) {
    return '未开始'
  } else {
    return '结束'
  }
}
function getStatusTag(status) {
  if (status == 0) {
    return {
      type: '',
      text: i18n.t('access.goalsetting')
    }
  } else if (status == 1) {
    return {
      type: '',
      text: i18n.t('access.executionphase')
    }
  } else if (status == 2) {
    return {
      type: 'success',
      text: i18n.t('access.higherevaluation')
    }
  } else if (status == 3) {
    return {
      type: '',
      text: i18n.t('access.performancereview')
    }
  } else if (status == 4) {
    return {
      type: 'info',
      text: i18n.t('access.end')
    }
  } else if (status == 5) {
    return {
      type: 'info',
      text: i18n.t('customer.notstarted')
    }
  } else {
    return {
      type: 'info',
      text: i18n.t('access.end')
    }
  }
}
function getPeriodText(id) {
  if (id == 1) {
    return '周考核'
  } else if (id == 2) {
    return '月考核'
  } else if (id == 3) {
    return '年考核'
  } else if (id == 5) {
    return '季度考核'
  } else if (id == 4) {
    return '半年考核'
  } else {
    return '月考核'
  }
}
export default {
  periodOptions,
  getStatusText,
  getStatusTag,
  getPeriodText,
  statusOptions,
  periodOption
}

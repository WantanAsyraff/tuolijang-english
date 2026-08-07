import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique'
const label = '订单支出'
const name = 'contractExpenditure'
export default {
  icon: 'iconfont iconqingjia2',
  label,
  name,
  loadChildren: false,
  rule() {
    return {
      type: name,
      field: uniqueId(),
      children: [
        {
          checkType: 0,
          display: true,
          effect: { fetch: '', required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
          input: false,
          title: i18n.t('customer.contractname'),
          symbol: 'contractList',
          type: 'select',
          props: { disabled: false, readonly: true, placeholder:i18n.t('legacyScript.pleaseSelectOrderName')},
          _fc_drag_tag: 'select',
          options: []
        },
        {
          checkType: 0,
          display: true,
          effect: { fetch: '', required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
          input: false,
          title: i18n.t('legacyScript.financialExpenseCategory'),
          symbol: 'expenditureCategories',
          type: 'cascader',
          _fc_drag_tag: 'cascader',
          props: {
            filterable: true,
            expandTrigger: 'hover',
            options: []
          }
        },
        {
          checkType: 0,
          display: true,
          effect: { fetch: '', required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
          input: false,
          title: i18n.t('customer.paymentMethod'),
          symbol: 'payType',
          type: 'select',
          _fc_drag_tag: 'select',
          options: []
        },
        {
          effect: { fetch: '', required: true },
          field: uniqueId(),
          props: { type: 'moneyFrom' },
          title: i18n.t('customer.expenseAmountYuan'),
          symbol: 'expenditureAmount',
          type: 'moneyFrom',
          _fc_drag_tag: 'moneyFrom'
        },
        {
          checkType: 0,
          display: true,
          effect: { fetch: '', required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
          props: { type: 'datetime', placeholder: i18n.t('legacyScript.pleaseSelectTime') },
          title: i18n.t('legacyScript.expenseTime'),
          symbol: 'payTime',
          type: 'datePicker',
          _fc_drag_tag: 'datetimerange'
        },
        {
          effect: { fetch: '', required: false },
          field: uniqueId(),
          props: { type: 'uploadFrom' },
          title: i18n.t('legacyScript.paymentProof'),
          symbol: 'paymentVoucher',
          type: 'uploadFrom',
          _fc_drag_tag: 'uploadFrom'
        },
        {
          type: 'input',
          field: uniqueId(),
          display: true,
          hidden: false,
          info: '',
          props: { type: 'textarea', placeholder: i18n.t('finance.pleaseinput') },
          title: i18n.t('customer.remark'),
          symbol: 'remark',
          _fc_drag_tag: 'textarea'
        }
      ]
    }
  },
  props() {
    return [
      // {
      //   type: 'switchStatus',
      //   field: 'payVoucher',
      //   title: '',
      //   display: true,
      //   hidden: false,
      //   props: {
      //     activeText: '显示',
      //     inactiveText: '隐藏',
      //     value: true,
      //     name: '支出凭证'
      //   },
      //   _fc_drag_tag: 'switchStatus'
      // },
      // {
      //   type: 'switchStatus',
      //   field: 'mustHave',
      //   display: true,
      //   hidden: false,
      //   props: {
      //     activeText: '必填',
      //     inactiveText: '选填',
      //     value: true,
      //     name: '是否必填'
      //   },
      //   title: '',
      //   _fc_drag_tag: 'switchStatus'
      // },
      // {
      //   type: 'switchStatus',
      //   field: 'remark',
      //   title: '',
      //   display: true,
      //   hidden: false,
      //   props: {
      //     activeText: '显示',
      //     inactiveText: '隐藏',
      //     value: true,
      //     name: '备注'
      //   },
      //   _fc_drag_tag: 'switchStatus'
      // },
      // {
      //   type: 'switchStatus',
      //   field: 'isMustHave',
      //   display: true,
      //   hidden: false,
      //   props: {
      //     activeText: '必填',
      //     inactiveText: '选填',
      //     value: false,
      //     name: '是否必填'
      //   },
      //   title: '',
      //   _fc_drag_tag: 'switchStatus'
      // }
    ]
  },
  basic() {
    return [
      {
        checkType: 0,
        display: true,
        field: uniqueId(),
        hidden: false,
        info: '',
        props: {
          value:
            '1.支出是否需要审批流，在客户规格设置中配置<br>2.支持财务在付款记录中进行回款修改、管理<br>3.订单相关支出根据财务支出科目，自动同步财务账目支出记录',
          title: i18n.t('legacyScript.expenseRules')
        },
        input: false,
        title: '',
        type: 'infoForm',
        _fc_drag_tag: 'infoForm'
      }
    ]
  }
}

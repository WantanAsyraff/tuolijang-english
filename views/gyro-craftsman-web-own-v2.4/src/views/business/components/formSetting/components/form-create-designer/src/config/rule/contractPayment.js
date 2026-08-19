import { $ } from '@/lang'
import uniqueId from '@form-create/utils/lib/unique'
const label = $('订单付款')
const name = 'contractPayment'
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
          title: $('customer.orderNumber'),
          type: 'select',
          props: { disabled: false, placeholder: $('legacyScript.pleaseSelectOrderNo') },
          symbol: 'contractList',
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
          title: $('setting.group.financialReviewTextInput'),
          symbol: 'incomeCategories',
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
          title: $('customer.paymentMethod'),
          type: 'select',
          symbol: 'payType',
          _fc_drag_tag: 'select',
          options: []
        },
        {
          effect: { fetch: '', required: true },
          field: uniqueId(),
          props: { type: 'moneyFrom', min: '0', precision: 2 },
          title: 'amountReceived', 
          symbol: 'collectionAmount',
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
          props: { type: 'datetime', placeholder: $('legacyScript.pleaseSelectTime') },
          title: $('customer.paymentTime'),
          symbol: 'payTime',
          type: 'datePicker',
          _fc_drag_tag: 'datetimerange'
        },
        {
          effect: { fetch: '', required: false },
          field: uniqueId(),
          props: { type: 'uploadFrom' },
          title: $('legacyScript.paymentProof'),
          symbol: 'paymentVoucher',
          type: 'uploadFrom',
          _fc_drag_tag: 'uploadFrom'
        },
        {
          type: 'input',
          field: uniqueId(),
          display: true,
          hidden: false,
          symbol: 'remark',
          info: '',
          props: { type: 'textarea', placeholder: $('finance.pleaseinput') },
          title: $('customer.remark'),
          _fc_drag_tag: 'textarea'
        }
      ]
    }
  },
  props() {
    return [
      // {
      //   type: 'switchStatus',
      //   field: 'paymentVoucher',
      //   title: '',
      //   display: true,
      //   hidden: false,
      //   props: {
      //     activeText: '显示',
      //     inactiveText: '隐藏',
      //     value: true,
      //     name: '付款凭证'
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
            '1. 回款是否需要审批流，在客户规格设置中配置<br>2.支持财务在付款记录中进行回款修改、管理<br>3. 订单回款根据财务收入科目，自动同步财务账目收入记录',
          title: $('legacyScript.paymentCollectionRules')
        },
        input: false,
        title: '',
        type: 'infoForm',
        _fc_drag_tag: 'infoForm'
      }
    ]
  }
}

import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique'
const label = '开具发票'
const name = 'issueInvoice'
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
          title: i18n.t('customer.customerName'),
          symbol: 'customerList',
          type: 'select',
          _fc_drag_tag: 'select',
          options: []
        },
        {
          checkType: 0,
          display: true,
          field: uniqueId(),
          hidden: true,
          info: '',
          input: false,
          title: i18n.t('ui.invoiceInvoiceDetailsRelatedPaymentOrder'),
          symbol: 'billId',
          type: 'select',
          _fc_drag_tag: 'select',
          options: []
        },
        {
          checkType: 0,
          display: true,
          field: uniqueId(),
          hidden: false,
          info: '',
          input: false,
          title: i18n.t('legacyScript.relatedPaymentAmount'),
          symbol: 'billAmount',
          props: { disabled: true,readonly:true,placeholder:i18n.t('legacyScript.pleaseEnterRelatedPaymentOrder') },
          type: 'input',
          _fc_drag_tag: 'input',
          options: []
        },
        {
          checkType: 0,
          display: true,
          effect: { fetch: '', required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
          props: { placeholder: i18n.t('legacyScript.pleaseSelectBillingDate') },
          title: i18n.t('customer.invoicingdate'),
          symbol: 'desireDate',
          type: 'datePicker',
          _fc_drag_tag: 'datePicker'
        },
        {
          effect: { fetch: '', required: true },
          field: uniqueId(),
          props: { type: 'radio' },
          title: i18n.t('legacyScript.invoiceRequirements'),
          symbol: 'invoicingMethod',
          value: 'mail',
          type: 'radio',
          _fc_drag_tag: 'radio',
          options: [
            { value: 'mail', label: i18n.t('ui.customerInvoiceInvoiceViewElectronic') },
            { value: 'express', label: i18n.t('ui.customerInvoiceInvoiceViewPaper') }
          ]
        },
        {
          display: true,
          effect: { required: false },
          field: uniqueId(),
          hidden: false,
          info: '',
          input: false,
          props: { type: 'input', placeholder: i18n.t('customer.placeholder55')  },
          title: i18n.t('customer.emailaddress'),
          symbol: 'invoicingEmail',
          type: 'input',
          _fc_drag_tag: 'input'
        },
        {
          display: true,
          effect: { required: false },
          field: uniqueId(),
          hidden: true,
          info: '',
          input: false,
          props: { type: 'input', placeholder: i18n.t('customer.placeholder52') },
          title: i18n.t('customer.contacts'),
          symbol: 'liaisonMan',
          type: 'input',
          _fc_drag_tag: 'input'
        },
        {
          display: true,
          effect: { required: false },
          field: uniqueId(),
          hidden: true,
          info: '',
          input: false,
          props: { type: 'input', placeholder: i18n.t('customer.placeholder53') },
          title: i18n.t('customer.contactnumber'),
          symbol: 'telephone',
          type: 'input',
          _fc_drag_tag: 'input'
        },
        {
          display: true,
          effect: { required: false },
          field: uniqueId(),
          hidden: true,
          info: '',
          input: false,
          props: { type: 'input', placeholder: i18n.t('customer.placeholder56') },
          title: i18n.t('customer.mailingaddress'),
          symbol: 'mailingAddress',
          type: 'input',
          _fc_drag_tag: 'input'
        },
        {
          checkType: 0,
          display: true,
          effect: { fetch: '', required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
          input: false,
          title: i18n.t('customer.headerinformation'),
          symbol: 'invoiceType',
          type: 'select',
          _fc_drag_tag: 'select',
          options: []
        },
        {
          effect: { fetch: '', required: true },
          field: uniqueId(),
          props: { type: 'moneyFrom', placeholder: i18n.t('customer.placeholder44') },
          title: i18n.t('legacyScript.invoiceAmountCNY'),
          symbol: 'invoiceAmount',
          type: 'moneyFrom',
          _fc_drag_tag: 'moneyFrom'
        },
        {
          display: true,
          effect: {fetch: "", required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
      
          props: { type: 'input', placeholder: i18n.t('customer.placeholder46') },
          title: i18n.t('customer.invoiceheader'),
          symbol: 'invoiceHeader',
          type: 'input',
          _fc_drag_tag: 'input'
        },
        {
          display: true,
          effect: { required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
     
          props: { type: 'input', placeholder: i18n.t('customer.placeholder47') },
          title: i18n.t('customer.paytaxes'),
          symbol: 'dutyParagraph',
          type: 'input',
          _fc_drag_tag: 'input'
        },
        {
          type: 'input',
          field: uniqueId(),
          display: true,
          hidden: false,
          info: '',
          props: { type: 'textarea', placeholder: i18n.t('customer.placeholder18') },
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
      //   field: 'invoice',
      //   title: '',
      //   display: true,
      //   hidden: false,
      //   props: {
      //     activeText: '显示',
      //     inactiveText: '隐藏',
      //     value: true,
      //     name: '发票类目'
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
          value: '1.开具发票是否需要审批流，在客户规格设置中配置<br>2.支持财务进行发票开具/拒绝开票',
          title: i18n.t('legacyScript.issueInvoiceRules')
        },
        input: false,
        title: '',
        type: 'infoForm',
        _fc_drag_tag: 'infoForm'
      }
    ]
  }
}

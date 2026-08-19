import { $ } from '@/lang'
import uniqueId from '@form-create/utils/lib/unique'
const label = $('合同签约')
const name = 'contractSigning'
export default {
  icon: 'iconfont iconqingjia2',
  label,
  name,
  loadChildren: false,
  rule() {
    return {
      type: name,
      field: uniqueId(),
      symbol: name,
      children: [
        {
          checkType: 0,
          display: true,
          effect: { fetch: '' },
          field: uniqueId(),
          hidden: false,
          info: '',
          input: false,
          title: $('customer.customerName'),
          symbol: 'eid',
          type: 'select',
          _fc_drag_tag: 'select',
          options: []
        },
        {
          effect: { fetch: '', required: true },
          field: uniqueId(),
          props: { type: 'radio' },
          title: $('ui.customerSigningIndexSigningMethod'),
          symbol: 'signType',
          value: '2',
          type: 'radio',
          _fc_drag_tag: 'radio',
          options: [
            { value: '2', label: $('ui.customerSigningInfoItemESign') },
            { value: '1', label: $('ui.customerSigningInfoItemOfflineSigning') }
          ]
        },
        {
          effect: { fetch: '', required: true },
          field: uniqueId(),
          props: { type: 'radio' },
          title: $('legacyScript.contractTerm'),
          symbol: 'termType',
          value: '2',
          type: 'radio',
          _fc_drag_tag: 'radio',
          options: [
            { value: '2', label: $('ui.customerSigningAddContractSignStartFromSigningDate') },
            { value: '1', label: $('ui.customerSigningAddContractSignFixedTerm') },
            { value: '0', label: $('ui.customerSigningAddContractSignNoFixedTerm') }
          ]
        },
        {
          type: 'input',
          field: uniqueId(),
          effect: { fetch: '', required: true },
          display: true,
          hidden: false,
          info: '',
          props: { placeholder: $('legacyScript.pleaseEnterContractDuration') },
          title: $('legacyScript.contractDurationDays'),
          symbol: 'dateCount',
          _fc_drag_tag: 'input'
        },
        {
          checkType: 0,
          display: true,
          effect: { fetch: '', required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
          props: { placeholder: $('legacyScript.pleaseSelectTheContractStartDate') },
          title: $('legacyScript.contractStartDate'),
          symbol: 'startDate',
          type: 'datePicker',
          _fc_drag_tag: 'datePicker'
        },
        {
          checkType: 0,
          display: true,
          effect: { fetch: '', required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
          props: { placeholder: $('legacyScript.pleaseSelectTheContractEndDate') },
          title: $('legacyScript.contractEndDate'),
          symbol: 'endDate',
          type: 'datePicker',
          _fc_drag_tag: 'datePicker'
        },

        {
          display: true,
          field: uniqueId(),
          hidden: false,
          info: '',
          title: $('ui.userDailyAddBoxAttachment'),
          symbol: 'signFile', // 合同附件
          type: 'uploadFrom',
          _fc_drag_tag: 'uploadFrom'
        },

        {
          type: 'input',
          field: uniqueId(),
          display: true,
          hidden: false,
          info: '',
          props: { type: 'textarea', placeholder: $('customer.placeholder18') },
          title: $('customer.remark'),
          symbol: 'mark',
          _fc_drag_tag: 'textarea'
        },
        {
          field: uniqueId(),
          display: true,
          hidden: false,
          title: $('legacyScript.signer'),
          label: $('legacyScript.signer'),
          props: { member: false },
          type: 'approvalBill',
          symbol: 'signatory',
          _fc_drag_tag: 'approvalBill',
          children: [
            {
              type: 'input',
              field: uniqueId(),
              display: true,
              hidden: false,
              info: '',
              props: { placeholder: $('setting.info.title1') },
              title: $('toptable.enterprisename'),
              symbol: 'companyName',
              _fc_drag_tag: 'input'
            },
            {
              display: true,
              field: uniqueId(),
              hidden: false,
              info: '',
              props: { member: true, range: ['oneself'], placeholder: '' },
              title: $('ui.customerSigningIndexHandler'),
              symbol: 'name',
              type: 'departmentTree',
              _fc_drag_tag: 'memberTree'
            },
            {
              type: 'input',
              field: uniqueId(),
              display: true,
              hidden: false,
              info: '',
              props: { placeholder: $('setting.info.title3') },
              title: $('customer.tel'),
              symbol: 'phone',
              _fc_drag_tag: 'input'
            }
          ]
        },
        {
          field: uniqueId(),
          display: true,
          hidden: false,
          title: $('ui.customerSigningAddContractSignProductList'),
          label: $('ui.customerSigningAddContractSignProductList'),
          props: { member: false },
          type: 'approvalBill',
          symbol: 'productInfo',
          _fc_drag_tag: 'approvalBill',
          children: [
            {
              type: 'input',
              field: uniqueId(),
              display: true,
              hidden: false,
              info: '',
              props: { placeholder: $('legacyScript.pleaseEnterProductName') },
              title: $('legacyScript.productName'),
              symbol: 'product_name',
              _fc_drag_tag: 'input'
            },
            {
              type: 'input',
              field: uniqueId(),
              display: true,
              hidden: false,
              info: '',
              props: { placeholder: $('legacyScript.pleaseEnterSpec') },
              title: $('customer.specification'),
              symbol: 'sku',
              _fc_drag_tag: 'input'
            },

            {
              type: 'input',
              field: uniqueId(),
              display: true,
              hidden: false,
              info: '',
              props: { placeholder: $('legacyScript.pleaseEnterQuantity') },
              title: $('legacyScript.quantity'),
              symbol: 'count',
              _fc_drag_tag: 'input'
            },

            {
              type: 'input',
              field: uniqueId(),
              display: true,
              hidden: false,
              info: '',
              props: { placeholder: $('legacyScript.pleaseEnterDiscount') },
              title: $('legacyScript.discount'),
              symbol: 'discount',
              _fc_drag_tag: 'input'
            },
            {
              type: 'input',
              field: uniqueId(),
              display: true,
              hidden: false,
              info: '',
              props: { placeholder: $('legacyScript.pleaseEnterTotalPrice') },
              title: $('legacyScript.totalPrice'),
              symbol: 'total_price',
              _fc_drag_tag: 'input'
            },
            {
              type: 'input',
              field: uniqueId(),
              display: true,
              hidden: false,
              info: '',
              props: { placeholder: $('legacyScript.pleaseEnterRemarks') },
              title: $('customer.remark'),
              symbol: 'remark',
              _fc_drag_tag: 'input'
            }
          ]
        }
      ]
    }
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
          title: $('legacyScript.issueInvoiceRules')
        },
        input: false,
        title: '',
        type: 'infoForm',
        _fc_drag_tag: 'infoForm'
      }
    ]
  }
}

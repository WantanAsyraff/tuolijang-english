import { $ } from '@/lang'
<script>
// 自定义表单-字段类型选项
const typeOptions = [
  {
    label: $('legacyScript.text2'),
    value: 'text',
    type: 'input'
  },
  {
    label: $('legacyScript.number'),
    value: 'number',
    type: 'input'
  },
  {
    label: $('legacyScript.textArea'),
    value: 'textarea',
    type: 'input'
  },
  {
    label: $('legacyScript.personSingleSelect'),
    value: 'singleMember',
    type: 'member'
  },
  {
    label: $('legacyScript.personnelMultipleSelect'),
    value: 'multipleMember',
    type: 'member'
  },

  {
    label: $('legacyScript.dropdownSingleSelect'),
    value: 'single',
    type: 'select'
  },
  {
    label: $('legacyScript.multiSelectDropdown'),
    value: 'multiple',
    type: 'select'
  },
  {
    label: $('legacyScript.radioButtons'),
    value: 'radio',
    type: 'radio'
  },
  {
    label: $('legacyScript.checkboxGroup'),
    value: 'checked',
    type: 'checked'
  },
  {
    label: $('legacyScript.datePicker2'),
    value: 'date',
    type: 'date'
  },
  {
    label: $('legacyScript.dateAndTimePicker'),
    value: 'datetime',
    type: 'datetime'
  },
  {
    label: $('legacyScript.imagePicker'),
    value: 'images',
    type: 'images'
  },
  {
    label: $('legacyScript.attachmentUploader'),
    value: 'file',
    type: 'file'
  },
  {
    label: $('legacyScript.richText'),
    value: 'oaWangeditor',
    type: 'oaWangeditor'
  }
]
const maxDisabledList = [
  'customer_way',
  'area_cascade',
  'customer_followed',
  'contract_cate',
  'contract_customer_name',
  '8ae68ada',
  'contract_status',
  'signing_status',
  'contract_followed',
  'e06d7153',
  'contract_category',
  'contract_customer',
  '8ae68ada',
  'contract_followed'
]
let typeDisabled = ['text', 'number', 'textarea', 'date', 'datetime', 'images', 'file', 'oaWangeditor', 'member']
const contractList = ['customer_status', 'customer_followed', 'contract_status', 'contract_followed', 'signing_status']

export default {
  typeOptions,
  contractList,
  typeDisabled,
  maxDisabledList
}
</script>

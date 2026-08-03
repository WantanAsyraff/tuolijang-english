<script>
import i18n from '@/lang'
// 自定义表单-字段类型选项
const typeOptions = [
  {
    label: i18n.t('legacyScript.text2'),
    value: 'text',
    type: 'input'
  },
  {
    label: i18n.t('legacyScript.number'),
    value: 'number',
    type: 'input'
  },
  {
    label: i18n.t('legacyScript.textArea'),
    value: 'textarea',
    type: 'input'
  },
  {
    label: i18n.t('legacyScript.personSingleSelect'),
    value: 'singleMember',
    type: 'member'
  },
  {
    label: i18n.t('legacyScript.personnelMultipleSelect'),
    value: 'multipleMember',
    type: 'member'
  },

  {
    label: i18n.t('legacyScript.dropdownSingleSelect'),
    value: 'single',
    type: 'select'
  },
  {
    label: i18n.t('legacyScript.multiSelectDropdown'),
    value: 'multiple',
    type: 'select'
  },
  {
    label: i18n.t('legacyScript.radioButtons'),
    value: 'radio',
    type: 'radio'
  },
  {
    label: i18n.t('legacyScript.checkboxGroup'),
    value: 'checked',
    type: 'checked'
  },
  {
    label: i18n.t('legacyScript.datePicker2'),
    value: 'date',
    type: 'date'
  },
  {
    label: i18n.t('legacyScript.dateAndTimePicker'),
    value: 'datetime',
    type: 'datetime'
  },
  {
    label: i18n.t('legacyScript.imagePicker'),
    value: 'images',
    type: 'images'
  },
  {
    label: i18n.t('legacyScript.attachmentUploader'),
    value: 'file',
    type: 'file'
  },
  {
    label: i18n.t('legacyScript.richText'),
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

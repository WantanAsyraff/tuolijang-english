import i18n from '@/lang'
export default function form() {
  return [
    {
      type: 'radio',
      field: 'labelPosition',
      value: 'left',
      title: i18n.t('legacyScript.labelPosition'),
      options: [
        { value: 'right', label: 'right' },
        { value: 'left', label: 'left' },
        { value: 'top', label: 'top' },
      ],
    },
    {
      type: 'radio',
      field: 'size',
      value: 'mini',
      title: i18n.t('legacyScript.labelPosition'),
      options: [
        { value: 'medium', label: 'medium' },
        { value: 'small', label: 'small' },
        { value: 'mini', label: 'mini' },
      ],
    },
    {
      type: 'input',
      field: 'labelWidth',
      value: '125px',
      title: i18n.t('legacyScript.labelWidth'),
    },
    {
      type: 'switch',
      field: 'hideRequiredAsterisk',
      value: false,
      title: i18n.t('legacyScript.hideTheRedAsteriskNextToTheRequiredFieldLabel'),
    },
    {
      type: 'switch',
      field: 'showMessage',
      value: true,
      title: i18n.t('legacyScript.displayValidationErrorMessage'),
    },
    {
      type: 'switch',
      field: 'inlineMessage',
      value: false,
      title: i18n.t('legacyScript.displayValidationInformationInline'),
    },
  ];
}

import { $ } from '@/lang'
export default function form() {
  return [
    {
      type: 'radio',
      field: 'labelPosition',
      value: 'left',
      title: $('legacyScript.labelPosition'),
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
      title: $('legacyScript.labelPosition'),
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
      title: $('legacyScript.labelWidth'),
    },
    {
      type: 'switch',
      field: 'hideRequiredAsterisk',
      value: false,
      title: $('legacyScript.hideTheRedAsteriskNextToTheRequiredFieldLabel'),
    },
    {
      type: 'switch',
      field: 'showMessage',
      value: true,
      title: $('legacyScript.displayValidationErrorMessage'),
    },
    {
      type: 'switch',
      field: 'inlineMessage',
      value: false,
      title: $('legacyScript.displayValidationInformationInline'),
    },
  ];
}

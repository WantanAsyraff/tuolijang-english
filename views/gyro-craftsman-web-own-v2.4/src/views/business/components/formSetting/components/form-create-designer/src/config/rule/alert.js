import { $ } from '@/lang'
const label = $('提示')
const name = 'el-alert';

export default {
  icon: 'icon-alert',
  label,
  name,
  rule() {
    return {
      type: name,
      props: {
        title: $('public.tips'),
        description: 'form-create',
        type: 'success',
        effect: 'dark',
      },
      children: [],
    };
  },
  props() {
    return [
      { type: 'input', field: 'title', title: $('ui.settingSystemQuickIndexTitle') },
      {
        type: 'select',
        field: 'type',
        title: $('legacyScript.theme'),
        options: [
          { label: 'success', value: 'success' },
          { label: 'warning', value: 'warning' },
          {
            label: 'info',
            value: 'info',
          },
          { label: 'error', value: 'error' },
        ],
      },
      { type: 'input', field: 'description', title: $('legacyScript.helperText') },
      {
        type: 'switch',
        field: 'closable',
        title: $('legacyScript.isClosable'),
        value: true,
      },
      { type: 'switch', field: 'center', title: $('legacyScript.isTextCentered'), value: true },
      {
        type: 'input',
        field: 'closeText',
        title: $('legacyScript.customCloseButtonText'),
      },
      { type: 'switch', field: 'showIcon', title: $('legacyScript.showIcon') },
      {
        type: 'select',
        field: 'effect',
        title: $('legacyScript.selectAProvidedTheme'),
        options: [
          { label: 'light', value: 'light' },
          { label: 'dark', value: 'dark' },
        ],
      },
    ];
  },
};

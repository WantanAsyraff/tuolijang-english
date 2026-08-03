import i18n from '@/lang'
const label = '提示';
const name = 'el-alert';

export default {
  icon: 'icon-alert',
  label,
  name,
  rule() {
    return {
      type: name,
      props: {
        title: i18n.t('public.tips'),
        description: 'form-create',
        type: 'success',
        effect: 'dark',
      },
      children: [],
    };
  },
  props() {
    return [
      { type: 'input', field: 'title', title: i18n.t('ui.settingSystemQuickIndexTitle') },
      {
        type: 'select',
        field: 'type',
        title: i18n.t('legacyScript.theme'),
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
      { type: 'input', field: 'description', title: i18n.t('legacyScript.helperText') },
      {
        type: 'switch',
        field: 'closable',
        title: i18n.t('legacyScript.isClosable'),
        value: true,
      },
      { type: 'switch', field: 'center', title: i18n.t('legacyScript.isTextCentered'), value: true },
      {
        type: 'input',
        field: 'closeText',
        title: i18n.t('legacyScript.customCloseButtonText'),
      },
      { type: 'switch', field: 'showIcon', title: i18n.t('legacyScript.showIcon') },
      {
        type: 'select',
        field: 'effect',
        title: i18n.t('legacyScript.selectAProvidedTheme'),
        options: [
          { label: 'light', value: 'light' },
          { label: 'dark', value: 'dark' },
        ],
      },
    ];
  },
};

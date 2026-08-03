import i18n from '@/lang'
const label = '按钮';
const name = 'el-button';

export default {
  icon: 'icon-button',
  label,
  name,
  rule() {
    return {
      type: name,
      props: {},
      children: ['按钮'],
    };
  },
  props() {
    return [
      {
        type: 'input',
        field: 'formCreateChild',
        title: i18n.t('legacyScript.content'),
      },
      {
        type: 'select',
        field: 'size',
        title: i18n.t('legacyScript.size'),
        options: [
          { label: 'medium', value: 'medium' },
          { label: 'small', value: 'small' },
          {
            label: 'mini',
            value: 'mini',
          },
        ],
      },
      {
        type: 'select',
        field: 'type',
        title: i18n.t('file.type'),
        options: [
          { label: 'primary', value: 'primary' },
          {
            label: 'success',
            value: 'success',
          },
          { label: 'warning', value: 'warning' },
          { label: 'danger', value: 'danger' },
          {
            label: 'info',
            value: 'info',
          },
          { label: 'text', value: 'text' },
        ],
      },
      { type: 'switch', field: 'plain', title: i18n.t('legacyScript.plainButton') },
      {
        type: 'switch',
        field: 'round',
        title: i18n.t('legacyScript.roundedButton'),
      },
      { type: 'switch', field: 'circle', title: i18n.t('legacyScript.circleButton') },
      {
        type: 'switch',
        field: 'loading',
        title: i18n.t('legacyScript.loadingState'),
      },
      { type: 'switch', field: 'disabled', title: i18n.t('legacyScript.disabledState') },
      {
        type: 'input',
        field: 'icon',
        title: i18n.t('legacyScript.iconClassName'),
      },
    ];
  },
};

import { $ } from '@/lang'
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
        title: $('legacyScript.content'),
      },
      {
        type: 'select',
        field: 'size',
        title: $('legacyScript.size'),
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
        title: $('file.type'),
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
      { type: 'switch', field: 'plain', title: $('legacyScript.plainButton') },
      {
        type: 'switch',
        field: 'round',
        title: $('legacyScript.roundedButton'),
      },
      { type: 'switch', field: 'circle', title: $('legacyScript.circleButton') },
      {
        type: 'switch',
        field: 'loading',
        title: $('legacyScript.loadingState'),
      },
      { type: 'switch', field: 'disabled', title: $('legacyScript.disabledState') },
      {
        type: 'input',
        field: 'icon',
        title: $('legacyScript.iconClassName'),
      },
    ];
  },
};

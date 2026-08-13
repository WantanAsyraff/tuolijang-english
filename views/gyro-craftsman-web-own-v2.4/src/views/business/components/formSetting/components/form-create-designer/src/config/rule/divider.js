import { $ } from '@/lang'
const label = '分割线';
const name = 'el-divider';

export default {
  icon: 'icon-divider',
  label,
  name,
  rule() {
    return {
      type: name,
      props: {},
      wrap: { show: false },
      native: false,
      children: [''],
    };
  },
  props() {
    return [
      {
        type: 'select',
        field: 'direction',
        title: $('legacyScript.setDividerDirection'),
        options: [
          { label: 'horizontal', value: 'horizontal' },
          { label: 'vertical', value: 'vertical' },
        ],
      },
      {
        type: 'input',
        field: 'formCreateChild',
        title: $('legacyScript.setDividerText'),
      },
      {
        type: 'select',
        field: 'contentPosition',
        title: $('legacyScript.setDividerTextPosition'),
        options: [
          { label: 'left', value: 'left' },
          { label: 'right', value: 'right' },
          {
            label: 'center',
            value: 'center',
          },
        ],
      },
    ];
  },
};

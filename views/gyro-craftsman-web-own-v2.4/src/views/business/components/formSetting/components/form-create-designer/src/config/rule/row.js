import { $ } from '@/lang'
const label = $('栅格布局')
const name = 'row';

export default {
  icon: 'icon-row',
  label,
  name,
  rule() {
    return {
      type: 'FcRow',
      props: {},
      children: [],
    };
  },
  children: 'col',
  props() {
    return [
      { type: 'inputNumber', field: 'gutter', title: $('legacyScript.gridSpacing') },
      {
        type: 'switch',
        field: 'type',
        title: $('legacyScript.flexLayoutMode'),
        props: { activeValue: 'flex', inactiveValue: 'default' },
      },
      {
        type: 'select',
        field: 'justify',
        title: $('legacyScript.horizontalAlignmentInFlexLayout'),
        options: [
          { label: 'start', value: 'start' },
          { label: 'end', value: 'end' },
          {
            label: 'center',
            value: 'center',
          },
          { label: 'space-around', value: 'space-around' },
          { label: 'space-between', value: 'space-between' },
        ],
      },
      {
        type: 'select',
        field: 'align',
        title: $('legacyScript.verticalAlignmentInFlexLayout'),
        options: [
          { label: 'top', value: 'top' },
          { label: 'middle', value: 'middle' },
          {
            label: 'bottom',
            value: 'bottom',
          },
        ],
      },
    ];
  },
};

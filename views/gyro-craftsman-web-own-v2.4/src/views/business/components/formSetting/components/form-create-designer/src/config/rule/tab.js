import { $ } from '@/lang'
const label = $('标签页')
const name = 'tab';

export default {
  icon: 'icon-tab',
  label,
  name,
  children: 'tab-pane',
  rule() {
    return {
      type: 'el-tabs',
      children: [],
    };
  },
  props() {
    return [
      {
        type: 'select',
        field: 'type',
        title: $('legacyScript.styleType'),
        options: [
          { label: 'default', value: 'default' },
          {
            label: 'card',
            value: 'card',
          },
          { label: 'border-card', value: 'border-card' },
        ],
      },
      { type: 'switch', field: 'closable', title: $('legacyScript.whetherTagsAreClosable') },
      {
        type: 'select',
        field: 'tabPosition',
        title: $('legacyScript.tabPosition'),
        options: [
          { label: 'top', value: 'top' },
          { label: 'right', value: 'right' },
          {
            label: 'left',
            value: 'left',
          },
        ],
      },
      { type: 'switch', field: 'stretch', title: $('legacyScript.whetherTheLabelWidthAutoExpands') },
    ];
  },
};

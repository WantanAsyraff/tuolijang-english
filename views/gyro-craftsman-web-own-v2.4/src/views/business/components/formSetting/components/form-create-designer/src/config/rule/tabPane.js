import { $ } from '@/lang'
const label = $('标签页')
const name = 'tab-pane';

export default {
  label,
  name,
  inside: true,
  drag: true,
  dragBtn: false,
  rule() {
    return {
      type: 'el-tab-pane',
      props: { label: $('legacyScript.newTab') },
      children: [],
    };
  },
  props() {
    return [
      { type: 'input', field: 'label', title: $('legacyScript.tabTitle') },
      {
        type: 'switch',
        field: 'disabled',
        title: $('legacyScript.disable'),
      },
      { type: 'input', field: 'name', title: $('legacyScript.identifierCorrespondingToTheBoundTabValueRepresentingTheTab') },
      {
        type: 'switch',
        field: 'lazy',
        title: $('legacyScript.whetherTheLabelIsRenderedLazily'),
      },
    ];
  },
};

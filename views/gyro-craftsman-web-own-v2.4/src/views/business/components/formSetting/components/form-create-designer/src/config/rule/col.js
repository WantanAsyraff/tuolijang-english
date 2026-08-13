import { $ } from '@/lang'
const name = 'col';

export default {
  name,
  drag: true,
  dragBtn: false,
  inside: true,
  rule() {
    return {
      type: name,
      props: { span: 12 },
      children: [],
    };
  },
  props() {
    return [
      { type: 'slider', field: 'span', title: $('legacyScript.numberOfColumnsOccupiedByTheGrid'), value: 12, props: { min: 0, max: 24 } },
      { type: 'slider', field: 'offset', title: $('legacyScript.numberOfEmptyColumnsToTheLeftOfTheGrid'), props: { min: 0, max: 24 } },
      { type: 'slider', field: 'push', title: $('legacyScript.numberOfColumnsToShiftTheGridRight'), props: { min: 0, max: 24 } },
      { type: 'slider', field: 'pull', title: $('legacyScript.gridShiftLeftByColumns'), props: { min: 0, max: 24 } },
    ];
  },
};

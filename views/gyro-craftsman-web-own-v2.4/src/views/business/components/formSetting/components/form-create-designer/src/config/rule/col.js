import i18n from '@/lang'
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
      { type: 'slider', field: 'span', title: i18n.t('legacyScript.numberOfColumnsOccupiedByTheGrid'), value: 12, props: { min: 0, max: 24 } },
      { type: 'slider', field: 'offset', title: i18n.t('legacyScript.numberOfEmptyColumnsToTheLeftOfTheGrid'), props: { min: 0, max: 24 } },
      { type: 'slider', field: 'push', title: i18n.t('legacyScript.numberOfColumnsToShiftTheGridRight'), props: { min: 0, max: 24 } },
      { type: 'slider', field: 'pull', title: i18n.t('legacyScript.gridShiftLeftByColumns'), props: { min: 0, max: 24 } },
    ];
  },
};

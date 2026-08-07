import i18n from '@/lang'
const label = '标签页';
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
      props: { label: i18n.t('legacyScript.newTab') },
      children: [],
    };
  },
  props() {
    return [
      { type: 'input', field: 'label', title: i18n.t('legacyScript.tabTitle') },
      {
        type: 'switch',
        field: 'disabled',
        title: i18n.t('legacyScript.disable'),
      },
      { type: 'input', field: 'name', title: i18n.t('legacyScript.identifierCorrespondingToTheBoundTabValueRepresentingTheTab') },
      {
        type: 'switch',
        field: 'lazy',
        title: i18n.t('legacyScript.whetherTheLabelIsRenderedLazily'),
      },
    ];
  },
};

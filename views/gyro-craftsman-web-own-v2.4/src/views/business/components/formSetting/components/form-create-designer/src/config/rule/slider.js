import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';

const label = '滑块';
const name = 'slider';

export default {
  icon: 'icon-slider',
  label,
  name,
  rule() {
    return {
      type: name,
      field: uniqueId(),
      title: label,
      info: '',
      props: {},
    };
  },
  props() {
    return [
      { type: 'inputNumber', field: 'min', title: i18n.t('legacyScript.minimumValue') },
      {
        type: 'inputNumber',
        field: 'max',
        title: i18n.t('legacyScript.maximumValue'),
      },
      { type: 'switch', field: 'disabled', title: i18n.t('legacyScript.disable') },
      {
        type: 'inputNumber',
        field: 'step',
        title: i18n.t('legacyScript.stepSize'),
      },
      { type: 'switch', field: 'showInput', title: i18n.t('legacyScript.showInputBoxOnlyValidForNonRangeSelection') },
      {
        type: 'switch',
        field: 'showInputControls',
        title: i18n.t('legacyScript.showControlButtonsForTheInputFieldWhenVisible'),
        value: true,
      },
      { type: 'switch', field: 'showStops', title: i18n.t('legacyScript.showDiscontinuityPoints') },
      {
        type: 'switch',
        field: 'range',
        title: i18n.t('legacyScript.enableRangeSelection'),
      },
      { type: 'switch', field: 'vertical', title: i18n.t('legacyScript.useVerticalOrientation') },
      {
        type: 'input',
        field: 'height',
        title: i18n.t('legacyScript.sliderHeightRequiredInVerticalMode'),
      },
    ];
  },
};

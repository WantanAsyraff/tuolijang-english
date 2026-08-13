import { $ } from '@/lang'
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
      { type: 'inputNumber', field: 'min', title: $('legacyScript.minimumValue') },
      {
        type: 'inputNumber',
        field: 'max',
        title: $('legacyScript.maximumValue'),
      },
      { type: 'switch', field: 'disabled', title: $('legacyScript.disable') },
      {
        type: 'inputNumber',
        field: 'step',
        title: $('legacyScript.stepSize'),
      },
      { type: 'switch', field: 'showInput', title: $('legacyScript.showInputBoxOnlyValidForNonRangeSelection') },
      {
        type: 'switch',
        field: 'showInputControls',
        title: $('legacyScript.showControlButtonsForTheInputFieldWhenVisible'),
        value: true,
      },
      { type: 'switch', field: 'showStops', title: $('legacyScript.showDiscontinuityPoints') },
      {
        type: 'switch',
        field: 'range',
        title: $('legacyScript.enableRangeSelection'),
      },
      { type: 'switch', field: 'vertical', title: $('legacyScript.useVerticalOrientation') },
      {
        type: 'input',
        field: 'height',
        title: $('legacyScript.sliderHeightRequiredInVerticalMode'),
      },
    ];
  },
};

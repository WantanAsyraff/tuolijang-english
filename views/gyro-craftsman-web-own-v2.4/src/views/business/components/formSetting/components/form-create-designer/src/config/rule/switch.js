import { $ } from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';

const label = $('开关')
const name = 'switch';

export default {
  icon: 'icon-switch',
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
      { type: 'switch', field: 'disabled', title: $('legacyScript.disable') },
      {
        type: 'inputNumber',
        field: 'width',
        title: $('legacyScript.widthPx'),
      },
      { type: 'input', field: 'activeText', title: $('legacyScript.textDescriptionWhenSwitchIsOn') },
      {
        type: 'input',
        field: 'inactiveText',
        title: $('legacyScript.textDescriptionWhenSwitchIsOff'),
      },
      { type: 'input', field: 'activeValue', title: $('legacyScript.valueWhenSwitchIsOn') },
      {
        type: 'input',
        field: 'inactiveValue',
        title: $('legacyScript.valueWhenSwitchIsOff'),
      },
      { type: 'input', field: 'activeColor', title: $('legacyScript.backgroundColorWhenTheSwitchIsOn') },
      {
        type: 'input',
        field: 'inactiveColor',
        title: $('legacyScript.backgroundColorWhenTheSwitchIsOff'),
      },
    ];
  },
};

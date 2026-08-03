import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';

const label = '开关';
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
      { type: 'switch', field: 'disabled', title: i18n.t('legacyScript.disable') },
      {
        type: 'inputNumber',
        field: 'width',
        title: i18n.t('legacyScript.widthPx'),
      },
      { type: 'input', field: 'activeText', title: i18n.t('legacyScript.textDescriptionWhenSwitchIsOn') },
      {
        type: 'input',
        field: 'inactiveText',
        title: i18n.t('legacyScript.textDescriptionWhenSwitchIsOff'),
      },
      { type: 'input', field: 'activeValue', title: i18n.t('legacyScript.valueWhenSwitchIsOn') },
      {
        type: 'input',
        field: 'inactiveValue',
        title: i18n.t('legacyScript.valueWhenSwitchIsOff'),
      },
      { type: 'input', field: 'activeColor', title: i18n.t('legacyScript.backgroundColorWhenTheSwitchIsOn') },
      {
        type: 'input',
        field: 'inactiveColor',
        title: i18n.t('legacyScript.backgroundColorWhenTheSwitchIsOff'),
      },
    ];
  },
};

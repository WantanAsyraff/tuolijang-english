import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';
import { makeRequiredRule } from '../../utils';

const label = '金额';
const name = 'moneyFrom';

export default {
  icon: 'iconfont iconjine3',
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
      { type: 'inputNumber', field: 'min', title: i18n.t('legacyScript.setMinimumNumericValue') },
      { type: 'inputNumber', field: 'max', title: i18n.t('legacyScript.setMaximumNumericValue') },
      { type: 'inputNumber', field: 'precision', title: i18n.t('legacyScript.decimalPlaces'), props: { min: 0, max: 2 } },
      makeRequiredRule(),
    ];
  },
  basic () {

  }
};

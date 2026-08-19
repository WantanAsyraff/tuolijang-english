import { $ } from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';
import { makeRequiredRule } from '../../utils';

const label = $('数字')
const name = 'inputNumber';

export default {
  icon: 'iconfont iconshuzi1',
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
      { type: 'inputNumber', field: 'min', title: $('legacyScript.setMinimumNumericValue') },
      { type: 'inputNumber', field: 'max', title: $('legacyScript.setMaximumNumericValue') },
      { type: 'inputNumber', field: 'precision', title: $('legacyScript.decimalPlaces') },
      makeRequiredRule(),
    ];
  },
  basic () {

  }
};

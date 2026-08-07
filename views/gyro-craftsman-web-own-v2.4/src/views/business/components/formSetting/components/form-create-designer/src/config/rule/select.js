import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';
import { makeOptionsRule } from '../../utils/index';
import { makeRequiredRule } from '../../utils';

const label = '选择器';
const name = 'select';

export default {
  icon: 'iconfont iconxuanzeqi',
  label,
  name,
  rule() {
    return {
      type: name,
      field: uniqueId(),
      title: label,
      info: '',
      effect: {
        fetch: '',
      },
      props: {},
      options: [
        { value: i18n.t('legacyScript.option1'), label: i18n.t('legacyScript.option1') },
        { value: i18n.t('legacyScript.option2'), label: i18n.t('legacyScript.option2') },
      ],
      checkType: 0,
    };
  },
  props() {
    return [makeOptionsRule('options'), makeRequiredRule()];
  },
  basic () {

  }
};

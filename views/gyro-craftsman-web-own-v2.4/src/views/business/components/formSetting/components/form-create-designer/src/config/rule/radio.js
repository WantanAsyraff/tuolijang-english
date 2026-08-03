import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';
import { makeOptionsRule } from '../../utils/index';
import { makeRequiredRule } from '../../utils';

const label = '单选框';
const name = 'radio';

export default {
  icon: 'iconfont icondanxuan1',
  label,
  name,
  rule() {
    return {
      type: name,
      field: uniqueId(),
      title: label,
      info: '',

      props: {},
      options: [
        { value: i18n.t('legacyScript.option1'), label: i18n.t('legacyScript.option1') },
        { value: i18n.t('legacyScript.option2'), label: i18n.t('legacyScript.option2') },
      ],
    };
  },
  props() {
    return [makeOptionsRule('options'), makeRequiredRule()];
  },
  basic () {

  }
};

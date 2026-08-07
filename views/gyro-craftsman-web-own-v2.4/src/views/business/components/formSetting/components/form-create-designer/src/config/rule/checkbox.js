import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';
import { makeOptionsRule } from '../../utils/index';
import { makeRequiredRule } from '../../utils';

const label = '多选';
const name = 'checkbox';

export default {
  icon: 'iconfont iconduoxuan1',
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
    };
  },
  props() {
    return [makeOptionsRule('options'), makeRequiredRule()];
  },
  basic () {

  }
};

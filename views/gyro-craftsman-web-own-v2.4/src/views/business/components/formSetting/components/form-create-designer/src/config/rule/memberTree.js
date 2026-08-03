import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';
import { makeRequiredRule } from '../../utils';

const label = '成员';
const name = 'departmentTree';
export default {
  icon: 'iconfont iconchengyuan',
  label,
  name: 'memberTree',
  rule() {
    return {
      type: name,
      field: uniqueId(),
      title: label,
      info: '',
      props: {
        member: true,
        range: ['many', 'oneself'],
      },
    };
  },
  props() {
    return [
      {
        type: 'checkbox',
        field: 'range',
        title: i18n.t('legacyScript.selectRange'),
        options: [
          { label: i18n.t('legacyScript.selfSelectable'), value: 'oneself' },
          { label: i18n.t('legacyScript.multiSelectable'), value: 'many' },
        ],
      },
      makeRequiredRule(),
    ];
  },
  basic () {

  }
};

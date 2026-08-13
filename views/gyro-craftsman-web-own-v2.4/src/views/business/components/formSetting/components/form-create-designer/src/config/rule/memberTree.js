import { $ } from '@/lang'
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
        title: $('legacyScript.selectRange'),
        options: [
          { label: $('legacyScript.selfSelectable'), value: 'oneself' },
          { label: $('legacyScript.multiSelectable'), value: 'many' },
        ],
      },
      makeRequiredRule(),
    ];
  },
  basic () {

  }
};

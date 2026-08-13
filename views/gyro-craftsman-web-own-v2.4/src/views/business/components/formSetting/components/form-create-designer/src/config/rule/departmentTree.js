import { $ } from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';
import { makeRequiredRule } from '../../utils';

const label = '部门';
const name = 'departmentTree';
export default {
  icon: 'iconfont iconbumen',
  label,
  name,
  rule() {
    return {
      type: name,
      field: uniqueId(),
      title: label,
      info: '',
      props: {
        departType: 'many',
      },
    };
  },
  props() {
    return [
      {
        type: 'radio',
        field: 'departType',
        title: $('legacyScript.selectType'),
        options: [
          { label: $('legacyScript.selectOneDepartment'), value: 'oneself' },
          { label: $('legacyScript.selectMultipleDepartments'), value: 'many' },
        ],
      },
      makeRequiredRule(),
    ];
  },
  basic () {

  }
};

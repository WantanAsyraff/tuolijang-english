import i18n from '@/lang'
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
        title: i18n.t('legacyScript.selectType'),
        options: [
          { label: i18n.t('legacyScript.selectOneDepartment'), value: 'oneself' },
          { label: i18n.t('legacyScript.selectMultipleDepartments'), value: 'many' },
        ],
      },
      makeRequiredRule(),
    ];
  },
  basic () {

  }
};

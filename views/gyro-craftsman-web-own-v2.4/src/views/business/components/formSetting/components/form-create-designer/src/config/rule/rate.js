import { $ } from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';

const label = '评分';
const name = 'rate';

export default {
  icon: 'icon-rate',
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
      { type: 'inputNumber', field: 'max', title: $('legacyScript.maximumScore') },
      {
        type: 'switch',
        field: 'disabled',
        title: $('legacyScript.isReadOnly'),
      },
      { type: 'switch', field: 'allowHalf', title: $('legacyScript.allowPartialSelection') },
      {
        type: 'input',
        field: 'voidColor',
        title: $('legacyScript.colorOfUnselectedIcon'),
      },
      { type: 'input', field: 'disabledVoidColor', title: $('legacyScript.colorOfUnselectedIconInReadOnlyMode') },
      {
        type: 'input',
        field: 'voidIconClass',
        title: $('legacyScript.classNameOfUnselectedIcon'),
      },
      { type: 'input', field: 'disabledVoidIconClass', title: $('legacyScript.classNameOfUnselectedIconInReadOnlyMode') },
      {
        type: 'switch',
        field: 'showScore',
        title: $('legacyScript.whetherToDisplayTheCurrentScoreShowScoreAndShow'),
      },
      { type: 'input', field: 'textColor', title: $('legacyScript.colorOfAuxiliaryText') },
      {
        type: 'input',
        field: 'scoreTemplate',
        title: $('legacyScript.scoreDisplayTemplate'),
      },
    ];
  },
};

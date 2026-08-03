import i18n from '@/lang'
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
      { type: 'inputNumber', field: 'max', title: i18n.t('legacyScript.maximumScore') },
      {
        type: 'switch',
        field: 'disabled',
        title: i18n.t('legacyScript.isReadOnly'),
      },
      { type: 'switch', field: 'allowHalf', title: i18n.t('legacyScript.allowPartialSelection') },
      {
        type: 'input',
        field: 'voidColor',
        title: i18n.t('legacyScript.colorOfUnselectedIcon'),
      },
      { type: 'input', field: 'disabledVoidColor', title: i18n.t('legacyScript.colorOfUnselectedIconInReadOnlyMode') },
      {
        type: 'input',
        field: 'voidIconClass',
        title: i18n.t('legacyScript.classNameOfUnselectedIcon'),
      },
      { type: 'input', field: 'disabledVoidIconClass', title: i18n.t('legacyScript.classNameOfUnselectedIconInReadOnlyMode') },
      {
        type: 'switch',
        field: 'showScore',
        title: i18n.t('legacyScript.whetherToDisplayTheCurrentScoreShowScoreAndShow'),
      },
      { type: 'input', field: 'textColor', title: i18n.t('legacyScript.colorOfAuxiliaryText') },
      {
        type: 'input',
        field: 'scoreTemplate',
        title: i18n.t('legacyScript.scoreDisplayTemplate'),
      },
    ];
  },
};

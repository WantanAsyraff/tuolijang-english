import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';

const label = '文字';
const name = 'span';

export default {
  icon: 'iconfont iconshuomingwenzi1',
  label,
  name,
  rule() {
    return {
      type: name,
      field: uniqueId(),
      title: i18n.t('ui.settingEnterpriseSetupStorageText'),
      native: false,
      children: ['这是一段文字'],
    };
  },
  props() {
    return [
      // {
      //     type: 'input',
      //     field: 'formCreateTitle',
      //     title: 'title',
      // },
      {
        type: 'input',
        field: 'formCreateChild',
        title: i18n.t('legacyScript.content'),
        props: {
          type: 'textarea',
        },
      },
    ];
  },
  basic () {

  }
};

import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';

const label = '颜色选择器';
const name = 'colorPicker';

export default {
  icon: 'icon-color',
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
      { type: 'switch', field: 'disabled', title: i18n.t('legacyScript.disable') },
      {
        type: 'switch',
        field: 'showAlpha',
        title: i18n.t('legacyScript.supportTransparencySelection'),
      },
      {
        type: 'select',
        field: 'colorFormat',
        title: i18n.t('legacyScript.colorFormat'),
        options: [
          { label: 'hsl', value: 'hsl' },
          { label: 'hsv', value: 'hsv' },
          {
            label: 'hex',
            value: 'hex',
          },
          { label: 'rgb', value: 'rgb' },
        ],
      },
    ];
  },
};

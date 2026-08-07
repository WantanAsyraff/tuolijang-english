import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';

const label = '穿梭框';
const name = 'el-transfer';

const generateData = (_) => {
  const data = [];
  for (let i = 1; i <= 15; i++) {
    data.push({
      key: i,
      label: `备选项 ${i}`,
      disabled: i % 4 === 0,
    });
  }
  return data;
};

export default {
  icon: 'icon-transfer',
  label,
  name,
  rule() {
    return {
      type: name,
      field: uniqueId(),
      title: label,
      info: '',
      props: {
        data: generateData(),
      },
    };
  },
  props() {
    return [
      {
        type: 'Struct',
        field: 'data',
        title: i18n.t('legacyScript.dataSourceForTransfer'),
        props: { defaultValue: [] },
      },
      { type: 'switch', field: 'filterable', title: i18n.t('legacyScript.searchable') },
      {
        type: 'input',
        field: 'filterPlaceholder',
        title: i18n.t('legacyScript.searchBoxPlaceholder'),
      },
      {
        type: 'select',
        field: 'targetOrder',
        title: i18n.t('legacyScript.sortingStrategyForRightListItems'),
        info: '若为 original，则保持与数据源相同的顺序；若为 push，则新加入的元素排在最后；若为 unshift，则新加入的元素排在最前',
        options: [
          { label: 'original', value: 'original' },
          {
            label: 'push',
            value: 'push',
          },
          { label: 'unshift', value: 'unshift' },
        ],
      },
      {
        type: 'Struct',
        field: 'titles',
        title: i18n.t('legacyScript.customListTitle'),
        props: { defaultValue: [] },
      },
      {
        type: 'Struct',
        field: 'buttonTexts',
        title: i18n.t('legacyScript.customButtonLabel'),
        props: { defaultValue: [] },
      },
      {
        type: 'Struct',
        field: 'format',
        title: i18n.t('legacyScript.selectionStatusAtTopOfList'),
        props: { defaultValue: {} },
      },
      {
        type: 'Struct',
        field: 'props',
        title: i18n.t('legacyScript.fieldAliasForDataSource'),
        props: { defaultValue: {} },
      },
      {
        type: 'Struct',
        field: 'leftDefaultChecked',
        title: i18n.t('legacyScript.arrayOfSelectedKeysInLeftListInitialState'),
        props: { defaultValue: [] },
      },
      {
        type: 'Struct',
        field: 'rightDefaultChecked',
        title: i18n.t('legacyScript.arrayOfSelectedKeysInRightListInitialState'),
        props: { defaultValue: [] },
      },
    ];
  },
};

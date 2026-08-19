import { $ } from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';

const label = $('穿梭框')
const name = 'el-transfer';

const generateData = (_) => {
  const data = [];
  for (let i = 1; i <= 15; i++) {
    data.push({
      key: i,
      label: $('ui.runtimeLeak.optionNumber', { number: i }),
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
        title: $('legacyScript.dataSourceForTransfer'),
        props: { defaultValue: [] },
      },
      { type: 'switch', field: 'filterable', title: $('legacyScript.searchable') },
      {
        type: 'input',
        field: 'filterPlaceholder',
        title: $('legacyScript.searchBoxPlaceholder'),
      },
      {
        type: 'select',
        field: 'targetOrder',
        title: $('legacyScript.sortingStrategyForRightListItems'),
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
        title: $('legacyScript.customListTitle'),
        props: { defaultValue: [] },
      },
      {
        type: 'Struct',
        field: 'buttonTexts',
        title: $('legacyScript.customButtonLabel'),
        props: { defaultValue: [] },
      },
      {
        type: 'Struct',
        field: 'format',
        title: $('legacyScript.selectionStatusAtTopOfList'),
        props: { defaultValue: {} },
      },
      {
        type: 'Struct',
        field: 'props',
        title: $('legacyScript.fieldAliasForDataSource'),
        props: { defaultValue: {} },
      },
      {
        type: 'Struct',
        field: 'leftDefaultChecked',
        title: $('legacyScript.arrayOfSelectedKeysInLeftListInitialState'),
        props: { defaultValue: [] },
      },
      {
        type: 'Struct',
        field: 'rightDefaultChecked',
        title: $('legacyScript.arrayOfSelectedKeysInRightListInitialState'),
        props: { defaultValue: [] },
      },
    ];
  },
};

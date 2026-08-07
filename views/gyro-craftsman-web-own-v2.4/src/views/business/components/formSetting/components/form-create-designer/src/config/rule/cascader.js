import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';
import { makeOptionsRule } from '../../utils/index';

const label = '级联选择器';
const name = 'cascader';

export default {
  icon: 'icon-cascader',
  label,
  name,
  rule() {
    return {
      type: name,
      field: uniqueId(),
      title: label,
      info: '',
      effect: {
        fetch: '',
      },
      props: {
        options: [
          {
            value: 'zhinan',
            label: i18n.t('legacyScript.guide'),
            children: [
              {
                value: 'shejiyuanze',
                label: i18n.t('legacyScript.designPrinciples'),
                children: [
                  {
                    value: 'yizhi',
                    label: i18n.t('legacyScript.consistency'),
                  },
                  {
                    value: 'fankui',
                    label: i18n.t('legacyScript.feedback'),
                  },
                  {
                    value: 'xiaolv',
                    label: i18n.t('legacyScript.efficiency'),
                  },
                  {
                    value: 'kekong',
                    label: i18n.t('legacyScript.controllability'),
                  },
                ],
              },
              {
                value: 'daohang',
                label: i18n.t('legacyScript.navigation'),
                children: [
                  {
                    value: 'cexiangdaohang',
                    label: i18n.t('legacyScript.sidebarNavigation'),
                  },
                  {
                    value: 'dingbudaohang',
                    label: i18n.t('legacyScript.topNavigation'),
                  },
                ],
              },
            ],
          },
          {
            value: 'zujian',
            label: i18n.t('legacyScript.components'),
            children: [
              {
                value: 'basic',
                label: 'Basic',
                children: [
                  {
                    value: 'layout',
                    label: i18n.t('legacyScript.layout'),
                  },
                  {
                    value: 'color',
                    label: i18n.t('legacyScript.color'),
                  },
                  {
                    value: 'typography',
                    label: i18n.t('legacyScript.typography'),
                  },
                  {
                    value: 'icon',
                    label: i18n.t('legacyScript.icon'),
                  },
                  {
                    value: 'button',
                    label: i18n.t('legacyScript.button'),
                  },
                ],
              },
              {
                value: 'form',
                label: 'Form',
                children: [
                  {
                    value: 'radio',
                    label: i18n.t('legacyScript.radio'),
                  },
                  {
                    value: 'checkbox',
                    label: i18n.t('legacyScript.checkbox'),
                  },
                  {
                    value: 'input',
                    label: i18n.t('legacyScript.input'),
                  },
                  {
                    value: 'input-number',
                    label: i18n.t('legacyScript.inputNumber'),
                  },
                  {
                    value: 'select',
                    label: i18n.t('legacyScript.select'),
                  },
                  {
                    value: 'cascader',
                    label: i18n.t('legacyScript.cascader'),
                  },
                  {
                    value: 'switch',
                    label: i18n.t('legacyScript.switch'),
                  },
                  {
                    value: 'slider',
                    label: i18n.t('legacyScript.slider'),
                  },
                  {
                    value: 'time-picker',
                    label: i18n.t('legacyScript.timePicker'),
                  },
                  {
                    value: 'date-picker',
                    label: i18n.t('legacyScript.datePicker'),
                  },
                  {
                    value: 'datetime-picker',
                    label: i18n.t('legacyScript.dateTimePicker'),
                  },
                  {
                    value: 'upload',
                    label: i18n.t('legacyScript.upload'),
                  },
                  {
                    value: 'rate',
                    label: i18n.t('legacyScript.rate'),
                  },
                  {
                    value: 'form',
                    label: i18n.t('legacyScript.form'),
                  },
                ],
              },
            ],
          },
        ],
      },
    };
  },
  props() {
    return [
      makeOptionsRule('props.options'),
      {
        type: 'Object',
        field: 'props',
        title: i18n.t('legacyScript.configurationOptions'),
        props: {
          rule: [
            {
              type: 'select',
              field: 'expandTrigger',
              title: i18n.t('legacyScript.submenuExpandMode'),
              options: [
                { label: 'click', value: 'click' },
                { label: 'hover', value: 'hover' },
              ],
            },
            { type: 'switch', field: 'multiple', title: i18n.t('legacyScript.allowMultipleSelection') },
            {
              type: 'switch',
              field: 'checkStrictly',
              title: i18n.t('legacyScript.strictlyEnforceParentChildNodeIndependence'),
            },
            {
              type: 'switch',
              field: 'emitPath',
              title:
                i18n.t('legacyScript.returnAnArrayOfValuesFromAllAncestorMenuLevels'),
              value: true,
            },
            { type: 'input', field: 'value', title: i18n.t('legacyScript.specifyOptionValueAsAPropertyOfTheOptionObject') },
            {
              type: 'input',
              field: 'label',
              title: i18n.t('legacyScript.specifyTheOptionLabelAsAPropertyValueOfThe'),
            },
            { type: 'input', field: 'children', title: i18n.t('legacyScript.specifyTheSubOptionsAsAPropertyValueOfThe') },
            {
              type: 'input',
              field: 'disabled',
              title: i18n.t('legacyScript.specifyWhetherTheOptionIsDisabledViaAPropertyValue'),
            },
            { type: 'input', field: 'leaf', title: i18n.t('legacyScript.specifyTheLeafNodeFlagForTheOptionAsA') },
          ],
        },
      },
      {
        type: 'select',
        field: 'size',
        title: i18n.t('legacyScript.size'),
        options: [
          { label: 'medium', value: 'medium' },
          { label: 'small', value: 'small' },
          {
            label: 'mini',
            value: 'mini',
          },
        ],
      },
      { type: 'input', field: 'placeholder', title: i18n.t('legacyScript.inputPlaceholderText') },
      {
        type: 'switch',
        field: 'disabled',
        title: i18n.t('legacyScript.disable'),
      },
      { type: 'switch', field: 'clearable', title: i18n.t('legacyScript.supportClearOption') },
      {
        type: 'switch',
        field: 'showAllLevels',
        title: i18n.t('legacyScript.showFullPathOfSelectedValueInInputBox'),
        value: true,
      },
      { type: 'switch', field: 'collapseTags', title: i18n.t('legacyScript.collapseTagsInMultiSelectMode') },
      {
        type: 'input',
        field: 'separator',
        title: i18n.t('legacyScript.optionSeparator'),
      },
    ];
  },
};

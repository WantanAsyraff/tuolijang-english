import { $ } from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';
import { makeOptionsRule } from '../../utils/index';

const label = $('级联选择器')
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
            label: $('legacyScript.guide'),
            children: [
              {
                value: 'shejiyuanze',
                label: $('legacyScript.designPrinciples'),
                children: [
                  {
                    value: 'yizhi',
                    label: $('legacyScript.consistency'),
                  },
                  {
                    value: 'fankui',
                    label: $('legacyScript.feedback'),
                  },
                  {
                    value: 'xiaolv',
                    label: $('legacyScript.efficiency'),
                  },
                  {
                    value: 'kekong',
                    label: $('legacyScript.controllability'),
                  },
                ],
              },
              {
                value: 'daohang',
                label: $('legacyScript.navigation'),
                children: [
                  {
                    value: 'cexiangdaohang',
                    label: $('legacyScript.sidebarNavigation'),
                  },
                  {
                    value: 'dingbudaohang',
                    label: $('legacyScript.topNavigation'),
                  },
                ],
              },
            ],
          },
          {
            value: 'zujian',
            label: $('legacyScript.components'),
            children: [
              {
                value: 'basic',
                label: 'Basic',
                children: [
                  {
                    value: 'layout',
                    label: $('legacyScript.layout'),
                  },
                  {
                    value: 'color',
                    label: $('legacyScript.color'),
                  },
                  {
                    value: 'typography',
                    label: $('legacyScript.typography'),
                  },
                  {
                    value: 'icon',
                    label: $('legacyScript.icon'),
                  },
                  {
                    value: 'button',
                    label: $('legacyScript.button'),
                  },
                ],
              },
              {
                value: 'form',
                label: 'Form',
                children: [
                  {
                    value: 'radio',
                    label: $('legacyScript.radio'),
                  },
                  {
                    value: 'checkbox',
                    label: $('legacyScript.checkbox'),
                  },
                  {
                    value: 'input',
                    label: $('legacyScript.input'),
                  },
                  {
                    value: 'input-number',
                    label: $('legacyScript.inputNumber'),
                  },
                  {
                    value: 'select',
                    label: $('legacyScript.select'),
                  },
                  {
                    value: 'cascader',
                    label: $('legacyScript.cascader'),
                  },
                  {
                    value: 'switch',
                    label: $('legacyScript.switch'),
                  },
                  {
                    value: 'slider',
                    label: $('legacyScript.slider'),
                  },
                  {
                    value: 'time-picker',
                    label: $('legacyScript.timePicker'),
                  },
                  {
                    value: 'date-picker',
                    label: $('legacyScript.datePicker'),
                  },
                  {
                    value: 'datetime-picker',
                    label: $('legacyScript.dateTimePicker'),
                  },
                  {
                    value: 'upload',
                    label: $('legacyScript.upload'),
                  },
                  {
                    value: 'rate',
                    label: $('legacyScript.rate'),
                  },
                  {
                    value: 'form',
                    label: $('legacyScript.form'),
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
        title: $('legacyScript.configurationOptions'),
        props: {
          rule: [
            {
              type: 'select',
              field: 'expandTrigger',
              title: $('legacyScript.submenuExpandMode'),
              options: [
                { label: 'click', value: 'click' },
                { label: 'hover', value: 'hover' },
              ],
            },
            { type: 'switch', field: 'multiple', title: $('legacyScript.allowMultipleSelection') },
            {
              type: 'switch',
              field: 'checkStrictly',
              title: $('legacyScript.strictlyEnforceParentChildNodeIndependence'),
            },
            {
              type: 'switch',
              field: 'emitPath',
              title:
                $('legacyScript.returnAnArrayOfValuesFromAllAncestorMenuLevels'),
              value: true,
            },
            { type: 'input', field: 'value', title: $('legacyScript.specifyOptionValueAsAPropertyOfTheOptionObject') },
            {
              type: 'input',
              field: 'label',
              title: $('legacyScript.specifyTheOptionLabelAsAPropertyValueOfThe'),
            },
            { type: 'input', field: 'children', title: $('legacyScript.specifyTheSubOptionsAsAPropertyValueOfThe') },
            {
              type: 'input',
              field: 'disabled',
              title: $('legacyScript.specifyWhetherTheOptionIsDisabledViaAPropertyValue'),
            },
            { type: 'input', field: 'leaf', title: $('legacyScript.specifyTheLeafNodeFlagForTheOptionAsA') },
          ],
        },
      },
      {
        type: 'select',
        field: 'size',
        title: $('legacyScript.size'),
        options: [
          { label: 'medium', value: 'medium' },
          { label: 'small', value: 'small' },
          {
            label: 'mini',
            value: 'mini',
          },
        ],
      },
      { type: 'input', field: 'placeholder', title: $('legacyScript.inputPlaceholderText') },
      {
        type: 'switch',
        field: 'disabled',
        title: $('legacyScript.disable'),
      },
      { type: 'switch', field: 'clearable', title: $('legacyScript.supportClearOption') },
      {
        type: 'switch',
        field: 'showAllLevels',
        title: $('legacyScript.showFullPathOfSelectedValueInInputBox'),
        value: true,
      },
      { type: 'switch', field: 'collapseTags', title: $('legacyScript.collapseTagsInMultiSelectMode') },
      {
        type: 'input',
        field: 'separator',
        title: $('legacyScript.optionSeparator'),
      },
    ];
  },
};

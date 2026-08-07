import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';
import { makeOptionsRule } from '../../utils/index';

const label = '树形控件';
const name = 'tree';

export default {
  icon: 'icon-tree',
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
        props: {
          label: 'label',
        },
        showCheckbox: true,
        nodeKey: 'id',
        data: [
          {
            id: 1,
            label: i18n.t('legacyScript.level1'),
            children: [
              {
                id: 4,
                label: i18n.t('legacyScript.level2'),
                children: [
                  {
                    id: 9,
                    label: i18n.t('legacyScript.level3'),
                  },
                  {
                    id: 10,
                    label: i18n.t('legacyScript.level32'),
                  },
                ],
              },
            ],
          },
          {
            id: 2,
            label: i18n.t('legacyScript.level12'),
            children: [
              {
                id: 5,
                label: i18n.t('legacyScript.level221'),
              },
              {
                id: 6,
                label: i18n.t('legacyScript.level222'),
              },
            ],
          },
          {
            id: 3,
            label: i18n.t('legacyScript.level13'),
            children: [
              {
                id: 7,
                label: i18n.t('legacyScript.level231'),
              },
              {
                id: 8,
                label: i18n.t('legacyScript.level232'),
              },
            ],
          },
        ],
      },
    };
  },
  props() {
    return [
      makeOptionsRule('props.data'),
      { type: 'input', field: 'emptyText', title: i18n.t('legacyScript.textDisplayedWhenContentIsEmpty') },
      {
        type: 'Struct',
        field: 'props',
        title: i18n.t('legacyScript.configurationOptionsSeeTableBelow'),
        props: { defaultValue: {} },
      },
      { type: 'switch', field: 'renderAfterExpand', title: i18n.t('legacyScript.whetherToRenderChildNodesOnlyAfterTheFirstExpansion'), value: true },
      {
        type: 'switch',
        field: 'defaultExpandAll',
        title: i18n.t('legacyScript.whetherAllNodesAreExpandedByDefault'),
      },
      {
        type: 'switch',
        field: 'expandOnClickNode',
        title:
          i18n.t('legacyScript.whetherToExpandOrCollapseNodesOnClickDefaultIs'),
        value: true,
      },
      {
        type: 'switch',
        field: 'checkOnClickNode',
        title: i18n.t('legacyScript.whetherToSelectTheNodeOnClickDefaultIsFalse'),
      },
      { type: 'switch', field: 'autoExpandParent', title: i18n.t('legacyScript.whetherToAutomaticallyExpandParentNodesWhenChildNodesAre'), value: true },
      {
        type: 'switch',
        field: 'checkStrictly',
        title: i18n.t('legacyScript.whenCheckboxesAreDisplayedWhetherToStrictlyEnforceThatParent'),
      },
      { type: 'switch', field: 'accordion', title: i18n.t('legacyScript.whetherToExpandOnlyOneSiblingTreeNodeAtA') },
      {
        type: 'inputNumber',
        field: 'indent',
        title: i18n.t('legacyScript.horizontalIndentationBetweenAdjacentLevelNodesInPixels'),
      },
      { type: 'input', field: 'iconClass', title: i18n.t('legacyScript.customIconForTreeNodes') },
      {
        type: 'input',
        field: 'nodeKey',
        title: i18n.t('legacyScript.attributeUsedAsUniqueIdentifierForEachTreeNodeMust'),
      },
    ];
  },
};

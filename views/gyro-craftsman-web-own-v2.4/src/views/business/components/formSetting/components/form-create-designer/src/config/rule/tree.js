import { $ } from '@/lang'
import uniqueId from '@form-create/utils/lib/unique';
import { makeOptionsRule } from '../../utils/index';

const label = $('树形控件')
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
            label: $('legacyScript.level1'),
            children: [
              {
                id: 4,
                label: $('legacyScript.level2'),
                children: [
                  {
                    id: 9,
                    label: $('legacyScript.level3'),
                  },
                  {
                    id: 10,
                    label: $('legacyScript.level32'),
                  },
                ],
              },
            ],
          },
          {
            id: 2,
            label: $('legacyScript.level12'),
            children: [
              {
                id: 5,
                label: $('legacyScript.level221'),
              },
              {
                id: 6,
                label: $('legacyScript.level222'),
              },
            ],
          },
          {
            id: 3,
            label: $('legacyScript.level13'),
            children: [
              {
                id: 7,
                label: $('legacyScript.level231'),
              },
              {
                id: 8,
                label: $('legacyScript.level232'),
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
      { type: 'input', field: 'emptyText', title: $('legacyScript.textDisplayedWhenContentIsEmpty') },
      {
        type: 'Struct',
        field: 'props',
        title: $('legacyScript.configurationOptionsSeeTableBelow'),
        props: { defaultValue: {} },
      },
      { type: 'switch', field: 'renderAfterExpand', title: $('legacyScript.whetherToRenderChildNodesOnlyAfterTheFirstExpansion'), value: true },
      {
        type: 'switch',
        field: 'defaultExpandAll',
        title: $('legacyScript.whetherAllNodesAreExpandedByDefault'),
      },
      {
        type: 'switch',
        field: 'expandOnClickNode',
        title:
          $('legacyScript.whetherToExpandOrCollapseNodesOnClickDefaultIs'),
        value: true,
      },
      {
        type: 'switch',
        field: 'checkOnClickNode',
        title: $('legacyScript.whetherToSelectTheNodeOnClickDefaultIsFalse'),
      },
      { type: 'switch', field: 'autoExpandParent', title: $('legacyScript.whetherToAutomaticallyExpandParentNodesWhenChildNodesAre'), value: true },
      {
        type: 'switch',
        field: 'checkStrictly',
        title: $('legacyScript.whenCheckboxesAreDisplayedWhetherToStrictlyEnforceThatParent'),
      },
      { type: 'switch', field: 'accordion', title: $('legacyScript.whetherToExpandOnlyOneSiblingTreeNodeAtA') },
      {
        type: 'inputNumber',
        field: 'indent',
        title: $('legacyScript.horizontalIndentationBetweenAdjacentLevelNodesInPixels'),
      },
      { type: 'input', field: 'iconClass', title: $('legacyScript.customIconForTreeNodes') },
      {
        type: 'input',
        field: 'nodeKey',
        title: $('legacyScript.attributeUsedAsUniqueIdentifierForEachTreeNodeMust'),
      },
    ];
  },
};

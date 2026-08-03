<script>
import i18n from '@/lang'
// 新建实体表单数配置
const formDataInit = {
  table_name: '',
  table_name_en: '',
  crud_type: '0',
  crud_id: '',
  cate_ids: '',
  show_log: '1',
  show_comment: 1,
  info: '',
  path:[],
  icon:'',
  uni_img:'',
}
// 新建实体表单验证
const formRules = {
  table_name: [
    {
      required: true,
      message: i18n.t('legacyScript.pleaseEnterDisplayName'),
      trigger: 'blur'
    },
    {
      validator: function (rule, value, callback) {
        if (/^[\u4e00-\u9fa5a-zA-Z][\u4e00-\u9fa5a-zA-Z_]{0,15}$/.test(value) == false) {
          callback(new Error('以中文，英文字母开头，中间可输入下划线，最多可输入16个字'))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ],
  table_name_en: [
    {
      required: true,
      message: i18n.t('ui.settingEnterpriseAddAdminRolePleaseEnterEntityName'),
      trigger: 'blur'
    },
    {
      validator: function (rule, value, callback) {
        if (/^[a-z][A-Za-z_]*$/.test(value) == false) {
          callback(new Error('英文小写字母开头，不可包含中文，数字，空格，中间可输下划线'))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ]
}

// 新建实体表单内容
const formConfig = [
  {
    type: 'input',
    label: i18n.t('legacyScript.displayName'),
    placeholder: i18n.t('legacyScript.startWithAChineseCharacterOrLetterUnderscoresAre'),
    key: 'table_name'
  },
  {
    type: 'inputEn',
    label: i18n.t('legacyScript.entityName'),
    placeholder: i18n.t('legacyScript.startWithALowercaseEnglishLetterChineseCharactersNumbers'),
    key: 'table_name_en',
    refresh: 'table_name'
  },
  {
    type: 'radio',
    label: i18n.t('legacyScript.entityType'),
    placeholder: '',
    key: 'crud_type',
    options: [
      {
        label: '0',
        value: '主实体'
      },
      {
        label: '1',
        value: '明细实体'
      }
    ],
    tips: i18n.t('legacyScript.aDetailEntityIsAChildTableAndHas')
  },
  {
    type: 'cascaderSelect',
    label: i18n.t('legacyScript.parentEntity'),
    placeholder: i18n.t('legacyScript.searchForAndSelectTheMainEntity'),
    key: 'crud_id',
    isShow: 'crud_type',
    props: { emitPath: false, label: 'label', value: 'value', children: 'children' },
    options: []
  },
  {
    type: 'switch',
    label: i18n.t('legacyScript.operationLogs'),
    key: 'show_log',
    activeValue: '1',
    inactiveValue: '0',
    inactiveText: '关闭',
    activeText: '开启'
  },
  {
    type: 'switch',
    label: i18n.t('legacyScript.comments'),
    key: 'show_comment',
    activeValue: 1,
    inactiveValue: 0,
    inactiveText: '关闭',
    activeText: '开启'
  },
  {
    type: 'input',
    label: i18n.t('legacyScript.renameComments'),
    key: 'comment_title',
    placeholder: i18n.t('legacyScript.enterTheCommentModuleName'),
    maxlength: 5,
    isShow: 'show_comment'
  },

  {
      type: 'multipleSelect',
    label: i18n.t('legacyScript.linkedApplication'),
    placeholder: i18n.t('legacyScript.searchForAndSelectApplicationsMultipleSelectionsAllowed'),
    key: 'cate_ids',
    options: []
  },
  {
    type: 'cascader',
    label: i18n.t('legacyScript.parentMenu'),
    placeholder: i18n.t('legacyScript.selectTheParentMenu'),
    key: 'path',
    props: {label: 'menu_name', value: 'id', children: 'children' ,checkStrictly: true},
    options: [],
    tips: i18n.t('legacyScript.noMenuWillBeGeneratedUnlessAParentMenu')
  },
   {
    type: 'icon',
    label: i18n.t('legacyScript.menuIcon'),
    placeholder: i18n.t('legacyScript.selectAMenuIcon'),
    key: 'icon',
  },
  {
    type: 'uni_img',
    label: i18n.t('legacyScript.mobileIcon'),
    placeholder: i18n.t('legacyScript.selectAMobileIcon'),
    key: 'uni_img',
    options: []
  },
  {
    type: 'textarea',
    label: i18n.t('legacyScript.entityDescription'),
    placeholder: i18n.t('legacyScript.enterTheEntityDescription'),
    key: 'info'
  }
]

// 低代码-新建字段表单配置
const fieldDataInit = {
  crud_id: 0,
  value: '',
  field_name: '',
  field_name_en: '',
  is_default_value_not_null: 1, // 允许空值
  is_table_show_row: 1, // 列表默认显示
  create_modify: 1, // 新增时修改
  update_modify: 1, // 更新时修改
  comment: '',
  data_dict_id: '',
  data_type: '1', // 数据选项
  is_city_show: 'city',
  customizeItems: [], // 自定义选项字段

  association_crud_id: '', // 关联表id
  association_field_names: [],
  association_field_names_list: null
}

// 低代码-新建字段表单验证
const fieldRules = {
  field_name: [
    {
      required: true,
      message: i18n.t('legacyScript.pleaseEnterDisplayName'),
      trigger: 'blur'
    },
    {
      validator: function (rule, value, callback) {
        if (/^[\u4e00-\u9fa5a-zA-Z][\u4e00-\u9fa5a-zA-Z_]{0,15}$/.test(value) == false) {
          callback(new Error('以中文，英文字母开头，中间可输入下划线，最多可输入16个字'))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ],
  field_name_en: [
    {
      required: true,
      message: i18n.t('legacyScript.enterTheFieldName'),
      trigger: 'blur'
    },
    {
      validator: function (rule, value, callback) {
        if (/^[a-z][A-Za-z_]*$/.test(value) == false) {
          callback(new Error('英文小写字母开头，不可包含中文，空格，中间可输入下划线'))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ],
  data_dict_id: [
    {
      required: true,
      message: i18n.t('ui.customerSetupCustomFormIndexSelectLinkedDictionary'),
      trigger: 'change'
    }
  ]
}

const optionsAdd = [
  {
    label: 1,
    value: '允许编辑'
  },
  {
    label: 0,
    value: '不允许编辑'
  }
]
const optionsEdit = [
  {
    label: 1,
    value: '允许编辑'
  },
  {
    label: 0,
    value: '不允许编辑'
  }
]

const dictMax = [
  {
    type: 'radio',
    label: i18n.t('legacyScript.dataOptions'),
    key: 'data_type',
    tips: i18n.t('legacyScript.maintainStaticDataInTheFormDesigner'),

    options: [
      {
        label: 1,
        value: '静态数据'
      },
      {
        label: 0,
        value: '数据字典'
      }
    ]
  },
  {
    type: 'select',
    label: i18n.t('legacyScript.linkedDictionary'),
    placeholder: i18n.t('legacyScript.searchForAndSelectADataDictionary'),
    key: 'data_dict_id',
    sign: 'dict',
    isShow: 'data_type',
    options: []
  }
]

// 字段段表单每个字段对应的中间动态内容
const keyValue = {
  input: [
    // ...inputMax,
    {
      type: 'switch',
      label: i18n.t('legacyScript.uniqueField'),
      key: 'is_uniqid',
      activeValue: 1,
      inactiveValue: 0,
      activeText: '开启',
      inactiveText: '关闭'
    }
  ],
  input_percentage: [],
  textarea: [],
  rich_text: [],
  input_number: [
    // {
    //   type: 'switch',
    //   label: '字段唯一：',
    //   key: 'is_uniqid',
    //   activeValue: 1,
    //   inactiveValue: 0,
    //   activeText: '开启',
    //   inactiveText: '关闭'
    // }
  ],
  input_float: [
    // {
    //   type: 'switch',
    //   label: '字段唯一：',
    //   key: 'is_uniqid',
    //   activeValue: 1,
    //   inactiveValue: 0,
    //   activeText: '开启',
    //   inactiveText: '关闭'
    // }
  ],

  input_price: [
    // {
    //   type: 'switch',
    //   label: '字段唯一：',
    //   key: 'is_uniqid',
    //   activeValue: 1,
    //   inactiveValue: 0,
    //   activeText: '开启',
    //   inactiveText: '关闭'
    // }
  ],
  radio: [...dictMax],
  cascader_radio: dictMax,
  cascader_address: [
    {
      type: 'radio',
      label: i18n.t('legacyScript.regionSelectionData'),
      key: 'is_city_show',
      options: [
        {
          label: 'city',
          value: '省份,城市'
        },
        {
          label: 'region',
          value: '省份，城市，地区'
        }
      ]
    }
  ],
  checkbox: [...dictMax],
  tag: dictMax,
  cascader: dictMax,
  image: [],
  file: [],
  input_select: [
    {
      type: 'input_select',
      label: i18n.t('legacyScript.referencedEntity'),
      key: 'association_field_names'
    }
  ],
  switch: [],
  date_picker: [],
  date_time_picker: []
}

// 流程条件设置字段
const conditionConfig = {
  input: [
    {
      value: 'in',
      label: i18n.t('legacyScript.contains')
    },
    {
      value: 'not_in',
      label: i18n.t('legacyScript.doesNotContain')
    },
    {
      value: 'eq',
      label: i18n.t('ui.workFlowDrawerConditionDrawerEqualTo')
    },
    // {
    //   value: 'regex',
    //   label: '正则'
    // },
    {
      value: 'not_eq',
      label: i18n.t('legacyScript.notEqualTo')
    },
    {
      value: 'is_empty',
      label: i18n.t('legacyScript.isEmpty')
    },
    {
      value: 'not_empty',
      label: i18n.t('legacyScript.isNotEmpty')
    }
  ],
  switch: [
    // 布尔
    {
      value: 'eq',
      label: i18n.t('ui.workFlowDrawerConditionDrawerEqualTo')
    },

    {
      value: 'is_empty',
      label: i18n.t('legacyScript.isEmpty')
    },
    {
      value: 'not_empty',
      label: i18n.t('legacyScript.isNotEmpty')
    }
  ],
  number: [
    // 整数、精度小数、百分比、金额
    {
      value: 'eq',
      label: i18n.t('ui.workFlowDrawerConditionDrawerEqualTo')
    },
    {
      value: 'gt',
      label: i18n.t('legacyScript.greaterThan')
    },
    {
      value: 'lt',
      label: i18n.t('ui.workFlowDrawerConditionDrawerLessThan')
    },
    // {
    //   value: 'regex',
    //   label: '正则'
    // },
    {
      value: 'gt_eq',
      label: i18n.t('ui.workFlowDrawerConditionDrawerGreaterThanOrEqualTo')
    },
    {
      value: 'lt_eq',
      label: i18n.t('ui.workFlowDrawerConditionDrawerLessThanOrEqualTo')
    },
    {
      value: 'between',
      label: i18n.t('legacyScript.range')
    }
  ],
  select: [
    // 单选、多选、级联、地区、复选按钮

    {
      value: 'in',
      label: i18n.t('legacyScript.contains')
    },
    {
      value: 'not_in',
      label: i18n.t('legacyScript.doesNotContain')
    },
    {
      value: 'is_empty',
      label: i18n.t('legacyScript.isEmpty')
    },
    {
      value: 'not_empty',
      label: i18n.t('legacyScript.isNotEmpty')
    }
  ],
  date: [
    {
      value: 'eq',
      label: i18n.t('ui.workFlowDrawerConditionDrawerEqualTo')
    },
    {
      value: 'gt',
      label: i18n.t('legacyScript.greaterThan')
    },
    {
      value: 'lt',
      label: i18n.t('ui.workFlowDrawerConditionDrawerLessThan')
    },
    {
      value: 'between',
      label: i18n.t('legacyScript.range')
    },
    {
      value: 'n_day',
      label: i18n.t('legacyScript.nDaysAgo')
    },
    {
      value: 'last_day',
      label: i18n.t('legacyScript.lastNDays')
    },
    {
      value: 'next_day',
      label: i18n.t('legacyScript.nextNDays')
    },
    {
      value: 'today',
      label: i18n.t('toptable.today')
    },
    {
      value: 'week',
      label: i18n.t('toptable.thisweek')
    },
    {
      value: 'month',
      label: i18n.t('hr.month')
    },
    {
      value: 'quarter',
      label: i18n.t('legacyScript.thisQuarter')
    },
    {
      value: 'year',
      label: i18n.t('toptable.thisyear')
    },
    {
      value: 'last_year',
      label: i18n.t('legacyScript.lastYear')
    }
  ],
  input_select: [
    {
      value: 'in',
      label: i18n.t('legacyScript.contains')
    },
    {
      value: 'not_in',
      label: i18n.t('legacyScript.doesNotContain')
    },
    {
      value: 'is_empty',
      label: i18n.t('legacyScript.isEmpty')
    },
    {
      value: 'not_empty',
      label: i18n.t('legacyScript.isNotEmpty')
    }
  ]
}

const fieldConfig = [
  {
    type: 'input',
    label: i18n.t('legacyScript.displayName'),
    placeholder: i18n.t('legacyScript.startWithAChineseCharacterOrLetterUnderscoresAre'),
    key: 'field_name'
  },
  {
    type: 'inputEn',
    label: i18n.t('legacyScript.fieldName'),
    placeholder: i18n.t('legacyScript.startWithALowercaseLetterChineseCharactersAndSpaces'),
    key: 'field_name_en',
    refresh: 'field_name'
  },

  {
    type: 'radio',
    label: i18n.t('legacyScript.whenAdding'),
    key: 'create_modify',
    options: optionsAdd
  },
  {
    type: 'radio',
    label: i18n.t('legacyScript.whenEditing'),
    key: 'update_modify',
    options: optionsEdit
  }
]

/**页面筛选类型*/

const searchTypeOptions = [
  {
    value: '0',
    label: i18n.t('legacyScript.recordsICanView')
  },
  {
    value: '1',
    label: i18n.t('legacyScript.ownedByMe')
  },
  {
    value: '2',
    label: i18n.t('legacyScript.createdByMe')
  },
  {
    value: '3',
    label: i18n.t('legacyScript.sharedWithMe')
  },
  {
    value: '4',
    label: i18n.t('legacyScript.sharedByMe')
  }
]
export default {
  formDataInit,
  formRules,
  formConfig,
  fieldDataInit,
  fieldRules,
  fieldConfig,
  keyValue,
  conditionConfig,
  searchTypeOptions
}
</script>

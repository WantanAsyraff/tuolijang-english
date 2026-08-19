import { $ } from '@/lang'

function All() {}
All.prototype = {
  timer: '',
  debounce(fn, delay = 500) {
    var _this = this;
    return function (arg) {
      // 获取函数的作用域和变量
      var that = this;
      var args = arg;
      clearTimeout(_this.timer); // 清除定时器
      _this.timer = setTimeout(function () {
        fn.call(that, args);
      }, delay);
    };
  },
  setCookie(val) {
    // cookie设置[{key:value}]、获取key、清除['key1','key2']
    for (var i = 0, len = val.length; i < len; i++) {
      for (var key in val[i]) {
        document.cookie = key + '=' + encodeURIComponent(val[i][key]) + '; path=/';
      }
    }
  },
  getCookie(name) {
    var strCookie = document.cookie;
    var arrCookie = strCookie.split('; ');
    for (var i = 0, len = arrCookie.length; i < len; i++) {
      var arr = arrCookie[i].split('=');
      if (name == arr[0]) {
        return decodeURIComponent(arr[1]);
      }
    }
  },
  clearCookie(name) {
    var myDate = new Date();
    myDate.setTime(-1000); // 设置时间
    for (var i = 0, len = name.length; i < len; i++) {
      document.cookie = '' + name[i] + "=''; path=/; expires=" + myDate.toGMTString();
    }
  },
  arrToStr(arr) {
    if (arr) {
      return arr
        .map((item) => {
          return item.card?item.card.name:item.name;
        })
        .toString();
    }
  },
  toggleClass(arr, elem, key = 'id') {
    return arr.some((item) => {
      return item[key] == elem[key];
    });
  },
  toChecked(arr, elem, key = 'id') {
    var isIncludes = this.toggleClass(arr, elem, key);
    !isIncludes ? arr.push(elem) : this.removeEle(arr, elem, key);
  },
  removeEle(arr, elem, key = 'id') {
    var includesIndex;
    arr.map((item, index) => {
      if (item[key] == elem[key]) {
        includesIndex = index;
      }
    });
    arr.splice(includesIndex, 1);
  },
  setApproverStr(nodeConfig) {
    if (nodeConfig.settype == 1) {
      const names = this.arrToStr(nodeConfig.nodeUserList)
      if (nodeConfig.nodeUserList.length == 1) {
        return $('ui.runtimeLeak.workflow.memberApproval', { names })
      }
      if (nodeConfig.nodeUserList.length > 1) {
        if (nodeConfig.examineMode == 3) return $('ui.runtimeLeak.workflow.memberSequentialApproval', { names })
        if (nodeConfig.examineMode == 2) return $('ui.runtimeLeak.workflow.memberAllApproval', { names })
        if (nodeConfig.examineMode == 1) return $('ui.runtimeLeak.workflow.memberAnyApproval', { names })
      }
    } else if (nodeConfig.settype == 2) {
      const level = nodeConfig.directorLevel == 1
        ? $('ui.runtimeLeak.workflow.directSupervisor')
        : $('ui.runtimeLeak.workflow.levelSupervisor', { level: nodeConfig.directorLevel })
      return $('ui.runtimeLeak.workflow.supervisorApproval', { level })
    } else if (nodeConfig.settype == 4) {
      const parts = [
        $('ui.workFlowDrawerApproverDrawerSelectedByApplicant'),
        nodeConfig.selectRange == 1
          ? $('ui.workFlowDrawerApproverDrawerNoRangeLimit')
          : $('ui.runtimeLeak.workflow.specifiedRange'),
        nodeConfig.selectMode == 1
          ? $('ui.workFlowDrawerApproverDrawerSingleSelect')
          : $('ui.workFlowDrawerApproverDrawerMultipleSelection')
      ]
      if (nodeConfig.selectMode != 1) {
        if (nodeConfig.examineMode == 1) parts.push($('ui.userExamineAddSignatureAnyApprover'))
        if (nodeConfig.examineMode == 2) parts.push($('ui.userExamineAddSignatureAllApprovers'))
        if (nodeConfig.examineMode == 3) parts.push($('ui.userExamineAddSignatureSequentialApproval'))
      }
      return parts.join(' ')
    } else if (nodeConfig.settype == 5) {
      return $('ui.workFlowDrawerApproverDrawerApplicant')
    } else if (nodeConfig.settype == 7) {
      const level = nodeConfig.directorLevel == 1
        ? $('ui.runtimeLeak.workflow.directSupervisor')
        : $('ui.runtimeLeak.workflow.levelSupervisor', { level: nodeConfig.directorLevel })
      return $('ui.runtimeLeak.workflow.approvalTo', {
        order: this.conditionOrder(nodeConfig.directorOrder),
        level
      })
    }
  },
  dealStr(str, obj) {
    var arr = [];
    var list = str.split(',');
    for (var elem in obj) {
      list.map((item) => {
        if (item == elem) {
          arr.push($(obj[elem].value));
        }
      });
    }
    return arr.join($('ui.runtimeLeak.workflow.or'));
  },
  conditionStr(nodeConfig, index, type) {
    const { conditionList, isDefault } = nodeConfig.conditionNodes[index]
    if (isDefault) return $('ui.runtimeLeak.workflow.otherConditions')
    if (conditionList.length === 0) {
      return index == nodeConfig.conditionNodes.length - 1 && nodeConfig.conditionNodes[0].conditionList.length != 0
        ? $('ui.runtimeLeak.workflow.otherConditions')
        : $('ui.runtimeLeak.workflow.setConditions')
    }
    if (type !== '') {
      return $('ui.runtimeLeak.workflow.conditionsSet', { count: conditionList.length })
    }

    const clauses = conditionList.map((item) => {
      const { type: fieldType, option, value, title, options, category, min, max } = item
      const field = $(title)
      if (fieldType === 'inputNumber' || fieldType === 'timeFrom' || fieldType === 'moneyFrom') {
        const displayValue = value == 4
          ? $('ui.runtimeLeak.workflow.valueRange', { min, max })
          : option
        return $('ui.runtimeLeak.workflow.conditionClause', {
          field,
          operator: this.conditionNumber(value),
          value: displayValue
        })
      }
      if (fieldType === 'radio') {
        return $('ui.runtimeLeak.workflow.conditionClause', {
          field,
          operator: $('ui.workFlowDrawerConditionDrawerBelongsTo'),
          value: this.conditionRadio(options, option)
        })
      }
      if (fieldType === 'checkbox' || fieldType === 'select') {
        return $('ui.runtimeLeak.workflow.conditionClause', {
          field,
          operator: this.conditionCheckbox(value),
          value: this.conditionSelect(options, option, value)
        })
      }
      if (fieldType === 'departmentTree') {
        return $('ui.runtimeLeak.workflow.conditionClause', {
          field,
          operator: category == 1 ? $('ui.workFlowDrawerConditionDrawerBelongsTo') : this.departmentSelect(value),
          value: this.conditionDepartment(options, category == 1 ? '1' : value)
        })
      }
      return field
    })
    return clauses.join(` ${$('ui.runtimeLeak.workflow.and')} `)
  },
  conditionFieldsStr(nodeConfig, index) {
    const { conditionList } = nodeConfig.conditionNodes[index];
    let str = '';
    if (conditionList.length > 0) {
      conditionList.map((value) => {
        if (value.field) {
          str += value.field + ',';
        }
      });
    }
    return str ? str.substr(0, str.length - 1) : '';
  },
  conditionNumber(value) {
    let str = '';
    value = Number(value);
    if (value === 0) {
      str = '小于';
    } else if (value === 1) {
      str = '等于';
    } else if (value === 2) {
      str = '小于等于';
    } else if (value === 3) {
      str = '大于等于';
    } else if (value === 4) {
      str = '介于（两个数字之间）';
    }
    return $(str)
  },
  conditionRadio(options, val) {
    return (options || [])
      .filter((item) => item.value == val)
      .map((item) => $(item.label))
      .join(', ')
  },
  conditionDepartment(options, val = '1') {
    const names = []
    if (options.depList && options.depList.length > 0) names.push(...options.depList.map((item) => item.name))
    if (options.userList && options.userList.length > 0) names.push(...options.userList.map((item) => item.name))
    const connector = val == 0 ? $('ui.runtimeLeak.workflow.and') : $('ui.runtimeLeak.workflow.or')
    return names.join(` ${connector} `)
  },
  departmentSelect(value) {
    let str = '';
    value = Number(value);
    if (value === 0) {
      str = '完全属于';
    } else if (value === 1) {
      str = '其一属于';
    }
    return $(str)
  },
  conditionSelect(options, option, val) {
    const selected = (options || [])
      .filter((item) => option.includes(item.value))
      .map((item) => $(item.label))
    const connector = val == 0 ? $('ui.runtimeLeak.workflow.and') : $('ui.runtimeLeak.workflow.or')
    return selected.join(` ${connector} `)
  },
  conditionCheckbox(value) {
    let str = '';
    value = Number(value);
    if (value === 0) {
      str = '完全等于';
    } else if (value === 1) {
      str = '包含任意';
    }
    return $(str)
  },
  conditionOrder(value) {
    let str = '';
    value = Number(value);
    if (value === 0) {
      str = '从上至下';
    } else if (value === 1) {
      str = '从下至上';
    }
    return $(str)
  },
  copyerStr(nodeConfig) {
    const parts = []
    if (nodeConfig.ccSelfSelectFlag == 1) {
      parts.push($('ui.workFlowDrawerApproverDrawerSelectedByApplicant'))
    }
    if (nodeConfig.nodeUserList.length !== 0) {
      parts.push($('ui.runtimeLeak.workflow.specifiedMembers', { names: this.arrToStr(nodeConfig.nodeUserList) }))
    }
    if (nodeConfig.departmentHead && nodeConfig.departmentHead.length > 0) {
      const levels = nodeConfig.departmentHead.map((value) => value == 1
        ? $('ui.runtimeLeak.workflow.directSupervisor')
        : $('ui.runtimeLeak.workflow.levelSupervisor', { level: value }))
      parts.push($('ui.runtimeLeak.workflow.specifiedSupervisors', { levels: levels.join(', ') }))
    }
    return parts.join(', ')
  },
  onlyValue() {
    return 'xxxxxxx4xxxyxxxxx'.replace(/[xy]/g, function (c) {
      const r = (Math.random() * 16) | 0;
      const v = c == 'x' ? r : (r & 0x3) | 0x8;
      return v.toString(16);
    });
  },
  toggleStrClass(item, key) {
    let a = item.zdy1 ? item.zdy1.split(',') : [];
    return a.some((item) => {
      return item == key;
    });
  },
  getExamineStatus(id,data) {
    var str = '';
    if (id === 0) {
      str = '审核中';
    } else if (id === 1&&!data.recall) {
      str = '已通过';
    } else if (id === 2) {
      str = '已拒绝';
    } else if (id === -1) {
      str = '已撤销';
    } else if(id === 1&&data.recall){
      str = '撤销中'
    }
    return $(str)
  },
};

export default new All();

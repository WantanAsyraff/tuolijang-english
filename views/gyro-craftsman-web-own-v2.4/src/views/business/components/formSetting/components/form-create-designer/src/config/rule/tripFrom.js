import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique'
import { makeRequiredRule } from '../../utils'

const label = '出差'
const name = 'tripFrom'
export default {
  icon: 'iconfont iconchucha',
  label,
  name,
  loadChildren: false,
  rule() {
    return {
      type: name,
      field: uniqueId(),

      children: [
        {
          display: true,
          effect: { required: 'timeFrom' },
          field: uniqueId(),
          hidden: false,
          info: '请输入出差时长',
          input: false,
          props: { timeType: 'day', titleIpt: '出差时长' },
          title: '',
          timeType: 'day',
          symbol: 'leaveDuration',
          type: 'timeFrom',
          _fc_drag_tag: 'timeFrom'
        },
        {
          display: true,
          effect: { required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
          input: false,
          props: { type: 'input', placeholder: i18n.t('legacyScript.pleaseEnterDepartureCity') },
          title: i18n.t('legacyScript.departureCity'),
          type: 'input',
          _fc_drag_tag: 'input'
        },
        {
          display: true,
          effect: { required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
          input: false,
          props: { type: 'input', placeholder: i18n.t('legacyScript.pleaseEnterDestinationCity') },
          title: i18n.t('legacyScript.destinationCity'),
          type: 'input',
          _fc_drag_tag: 'input'
        },
        {
          checkType: 0,
          display: true,
          effect: { fetch: '', required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
          input: false,
          title: i18n.t('legacyScript.transportationMode'),
          type: 'select',
          _fc_drag_tag: 'select',
          options: [
            { value: i18n.t('legacyScript.flight'), label: i18n.t('legacyScript.flight') },
            { value: i18n.t('legacyScript.train'), label: i18n.t('legacyScript.train') },
            { value: i18n.t('legacyScript.highSpeedRail'), label: i18n.t('legacyScript.highSpeedRail') },
            { value: i18n.t('legacyScript.car'), label: i18n.t('legacyScript.car') },
            { value: i18n.t('legacyScript.boat'), label: i18n.t('legacyScript.boat') },
            { value: i18n.t('hr.other'), label: i18n.t('hr.other') }
          ]
        },
        {
          checkType: 0,
          display: true,
          effect: { fetch: '', required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
          input: false,
          title: i18n.t('legacyScript.roundTrip'),
          type: 'select',
          _fc_drag_tag: 'select',
          options: [
            { value: i18n.t('legacyScript.oneWay'), label: i18n.t('legacyScript.oneWay') },
            { value: i18n.t('legacyScript.roundTrip2'), label: i18n.t('legacyScript.roundTrip2') }
          ]
        },
        {
          display: true,
          effect: { required: true },
          field: uniqueId(),
          hidden: false,
          info: '',
          input: false,
          props: { type: 'textarea', titleIpt: '出差事由', placeholder: i18n.t('legacyScript.pleaseEnterTheReasonForTheBusinessTrip') },
          title: i18n.t('legacyScript.reasonForBusinessTrip'),
          type: 'input',
          _fc_drag_tag: 'textarea'
        }
      ]
    }
  },
  props() {
    return [
      // {
      //   type: 'switchStatus',
      //   field: 'remark',
      //   title: '',
      //   display: true,
      //   hidden: false,
      //   props: {
      //     activeText: '显示',
      //     inactiveText: '隐藏',
      //     value: true,
      //     name: '出差事由'
      //   },
      //   _fc_drag_tag: 'switchStatus',
      // },
      // {
      //   type: 'switchStatus',
      //   field: 'isMustHave',
      //   display: true,
      //   hidden: false,
      //   props: {
      //     activeText: '必填',
      //     inactiveText: '选填',
      //     value: false,
      //     name: '是否必填'
      //   },
      //   title: '',
      //   _fc_drag_tag: 'switchStatus',
      // },
    ]
  },
  basic() {
    return [
      {
        checkType: 0,
        display: true,
        field: uniqueId(),
        hidden: false,
        info: '',
        props: {
          value:
            '1. 时长根据自然日计算，提交人可修改<br>2. 交通工具：飞机、火车、高铁/动车、汽车、船、其他<br>3. 单程往返：单程或往返<br>4. 出差总时长：所有行程的总时长，自动计算，支持修改；审批通过之后会同步至考勤',
          title: i18n.t('legacyScript.businessTripRules')
        },
        input: false,
        title: '',
        type: 'infoForm',
        _fc_drag_tag: 'infoForm'
      }
    ]
  }
}

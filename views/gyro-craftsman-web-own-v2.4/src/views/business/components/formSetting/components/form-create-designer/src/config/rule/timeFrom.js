import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique'
import { makeRequiredRule } from '../../utils'

const label = '时长'
const name = 'timeFrom'
export default {
  icon: 'iconfont iconshichang1',
  label,
  name,
  rule() {
    return {
      type: name,
      field: uniqueId(),
      props: {
        timeType: 'day',
        titleIpt: '时长'
      },
      // title: '时长',
      input: false,
      info: '',
      symbol: 'leaveDuration',
      effect: { required: 'timeFrom' }
    }
  },
  props() {
    return [
      {
        type: 'select',
        field: 'timeType',
        symbol: 'leaveDuration',
        title: i18n.t('legacyScript.timeScale'),
        value: 'day',
        options: [
          { label: i18n.t('ui.hrHolidaySettingByDay'), value: 'day' },
          { label: i18n.t('ui.hrHolidaySettingByHour'), value: 'time' }
        ]
      },
      {
        type: 'input',
        field: 'titleIpt',
        title: i18n.t('legacyScript.identifierName'),
        value: '时长'
      },
      makeRequiredRule()
    ]
  },
  basic() {}
}

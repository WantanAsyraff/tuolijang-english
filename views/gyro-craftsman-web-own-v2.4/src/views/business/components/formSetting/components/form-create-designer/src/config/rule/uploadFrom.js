import i18n from '@/lang'
import uniqueId from '@form-create/utils/lib/unique'
import { makeRequiredRule } from '../../utils'

const label = '附件'
const name = 'uploadFrom'
export default {
  icon: 'iconfont iconfujian',
  label,
  name,
  rule() {
    return {
      type: name,
      field: uniqueId(),
      props: { maxLength: 5 },
      title: i18n.t('ui.userDailyAddBoxAttachment'),
      info: ''
    }
  },
  props() {
    return [makeRequiredRule()]
  },
  basic() {}
}

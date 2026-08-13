import { $ } from '@/lang'
<script>
import xitongImg from '@/assets/images/empty/xitong.png';
import duanxinImg from '@/assets/images/empty/duanxin.png';
import qiweiImg from '@/assets/images/empty/qiwei.png';
import dingdingImg from '@/assets/images/empty/dingding.png';
import qitaImg from '@/assets/images/empty/qita.png';

const messagePushData = [
  {
    icon: xitongImg,
    name: '系统通知',
    is_edit: false,
    status: 'system_status'
  },
  {
    icon: duanxinImg,
    name: '短信',
    status: 'sms_status',
    is_edit: false,
    key: 'sms_template_id',
    placeholder: $('legacyScript.enterTheSMSTemplateIDFromYihaotong')
  },
  {
    icon: qiweiImg,
    name: '企微BOT',
    is_edit: false,
    status: 'work_webhook_status',
    key: 'work_webhook_url',
    placeholder: $('legacyScript.enterTheWebhookGeneratedByTheWeComBot')
  },
  {
    icon: dingdingImg,
    name: '钉钉BOT',
    is_edit: false,
    status: 'ding_webhook_status',
    key: 'ding_webhook_url',
    placeholder: $('legacyScript.enterTheWebhookGeneratedByTheDingTalkBot')
  },
  {
    icon: qitaImg,
    name: '其他BOT',
    is_edit: false,
    status: 'other_webhook_status',
    key: 'other_webhook_url',
    placeholder: $('legacyScript.enterTheWebhookGeneratedByTheThirdPartyBot')
  }
]
const weekOptions = [
  {
    value: '1',
    label: $('legacyScript.mon')
  },
  {
    value: '2',
    label: $('legacyScript.tue')
  },
  {
    value: '3',
    label: $('legacyScript.wed')
  },
  {
    value: '4',
    label: $('legacyScript.thu')
  },
  {
    value: '5',
    label: $('legacyScript.fri')
  },
  {
    value: '6',
    label: $('legacyScript.sat')
  },
  {
    value: '7',
    label: $('legacyScript.sun')
  }
]
const timeOptions = [
  {
    value: '0',
    label: $('legacyScript.everyNSeconds')
  },
  {
    value: '1',
    label: $('legacyScript.everyNMinutes')
  },
  {
    value: '2',
    label: $('legacyScript.everyNHours')
  },
  {
    value: '3',
    label: $('legacyScript.everyNDays')
  },
  {
    value: '4',
    label: $('legacyScript.daily')
  },
  {
    value: '5',
    label: $('legacyScript.weekly')
  },
  {
    value: '6',
    label: $('calendar.monthly')
  },
  {
    value: '7',
    label: $('legacyScript.annually')
  }
]

const activities = [
  {
    title: $('legacyScript.whenAnActionOccurs'),
    icon: 'iconfont icondangqian',
    color: '#1C9399',
    id: 1
  },
  {
    title: $('legacyScript.executeAction'),
    icon: 'iconfont icondangqian',
    color: '#eebe77',
    id: 2
  },
  {
    title: '',
    icon: '',
    color: ''
  }
]
export default { messagePushData, weekOptions, timeOptions, activities }
</script>

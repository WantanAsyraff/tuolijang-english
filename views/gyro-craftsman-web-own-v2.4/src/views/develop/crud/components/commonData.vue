<script>
import i18n from '@/lang'
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
    placeholder: i18n.t('legacyScript.enterTheSMSTemplateIDFromYihaotong')
  },
  {
    icon: qiweiImg,
    name: '企微BOT',
    is_edit: false,
    status: 'work_webhook_status',
    key: 'work_webhook_url',
    placeholder: i18n.t('legacyScript.enterTheWebhookGeneratedByTheWeComBot')
  },
  {
    icon: dingdingImg,
    name: '钉钉BOT',
    is_edit: false,
    status: 'ding_webhook_status',
    key: 'ding_webhook_url',
    placeholder: i18n.t('legacyScript.enterTheWebhookGeneratedByTheDingTalkBot')
  },
  {
    icon: qitaImg,
    name: '其他BOT',
    is_edit: false,
    status: 'other_webhook_status',
    key: 'other_webhook_url',
    placeholder: i18n.t('legacyScript.enterTheWebhookGeneratedByTheThirdPartyBot')
  }
]
const weekOptions = [
  {
    value: '1',
    label: i18n.t('legacyScript.mon')
  },
  {
    value: '2',
    label: i18n.t('legacyScript.tue')
  },
  {
    value: '3',
    label: i18n.t('legacyScript.wed')
  },
  {
    value: '4',
    label: i18n.t('legacyScript.thu')
  },
  {
    value: '5',
    label: i18n.t('legacyScript.fri')
  },
  {
    value: '6',
    label: i18n.t('legacyScript.sat')
  },
  {
    value: '7',
    label: i18n.t('legacyScript.sun')
  }
]
const timeOptions = [
  {
    value: '0',
    label: i18n.t('legacyScript.everyNSeconds')
  },
  {
    value: '1',
    label: i18n.t('legacyScript.everyNMinutes')
  },
  {
    value: '2',
    label: i18n.t('legacyScript.everyNHours')
  },
  {
    value: '3',
    label: i18n.t('legacyScript.everyNDays')
  },
  {
    value: '4',
    label: i18n.t('legacyScript.daily')
  },
  {
    value: '5',
    label: i18n.t('legacyScript.weekly')
  },
  {
    value: '6',
    label: i18n.t('calendar.monthly')
  },
  {
    value: '7',
    label: i18n.t('legacyScript.annually')
  }
]

const activities = [
  {
    title: i18n.t('legacyScript.whenAnActionOccurs'),
    icon: 'iconfont icondangqian',
    color: '#1C9399',
    id: 1
  },
  {
    title: i18n.t('legacyScript.executeAction'),
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

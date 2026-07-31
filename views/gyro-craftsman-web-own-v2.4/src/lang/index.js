import Vue from 'vue';
import VueI18n from 'vue-i18n';
import Cookies from 'js-cookie';
import elementEnLocale from 'element-ui/lib/locale/lang/en'; // element-ui lang
import elementZhLocale from 'element-ui/lib/locale/lang/zh-CN'; // element-ui lang
import elementEsLocale from 'element-ui/lib/locale/lang/es'; // element-ui lang
import elementJaLocale from 'element-ui/lib/locale/lang/ja'; // element-ui lang
import enLocale from './en';
import zhLocale from './zh';
import esLocale from './es';
import jaLocale from './ja';
import locale from 'element-ui/lib/locale';
import { translateNotificationText } from './notification-text';

Vue.use(VueI18n);

const messages = {
  en: {
    ...enLocale,
    ...elementEnLocale,
  },
  'zh-cn': {
    ...zhLocale,
    ...elementZhLocale,
  },
  es: {
    ...esLocale,
    ...elementEsLocale,
  },
  ja: {
    ...jaLocale,
    ...elementJaLocale,
  },
};
const supportedLocales = ['en', 'zh-cn'];
const apiMessageKeys = {
  '验证码不正确': 'captchaIncorrect',
  '验证码必须填写': 'captchaRequired',
  '短信验证码必须填写': 'smsCodeRequired',
  '短信验证码必须为数字': 'smsCodeNumeric',
  '短信验证码必须为6位': 'smsCodeSize',
  '请输入正确的短信验证码': 'smsCodeIncorrect',
  '登录状态已失效': 'loginExpired',
  '请求失败': 'requestFailed',
  '缺少刷新TOKEN': 'missingRefreshToken',
  '请检查手机号是否正确': 'phoneIncorrect',
  '该账号暂时无法登陆，请联系管理员激活': 'accountInactive',
  '请滑动验证码': 'slideCaptchaRequired',
  '二次验证失败，请重新滑动验证码': 'secondCaptchaFailed',
  '系统提示': 'systemTip',
  '温馨提示': 'friendlyTip',
  '确定': 'confirm',
  '取消': 'cancel'
};

export function normalizeLanguage(language) {
  const lang = String(language || '').toLowerCase();
  if (['zh', 'zh_cn', 'zh-cn', 'zh-hans'].includes(lang)) return 'zh-cn';
  if (['en', 'en_us', 'en-us', 'en-gb'].includes(lang)) return 'en';
  return '';
}

export function getLanguage() {
  const cookieLanguage = normalizeLanguage(Cookies.get('language'));
  if (supportedLocales.includes(cookieLanguage)) return cookieLanguage;

  const storedLanguage = normalizeLanguage(localStorage.getItem('language'));
  if (supportedLocales.includes(storedLanguage)) return storedLanguage;

  return 'zh-cn';
}
export function setLanguage(language) {
  const normalized = normalizeLanguage(language) || 'zh-cn';
  Cookies.set('language', normalized);
  localStorage.setItem('language', normalized);
  i18n.locale = normalized;
  return normalized;
}

const i18n = new VueI18n({
  // set locale
  // options: en | zh | es
  locale: getLanguage(),
  // set locale messages
  messages,
});
locale.i18n((key, value) => i18n.t(key, value));

export function translateMessage(message) {
  if (typeof message !== 'string') return message;
  const key = apiMessageKeys[message];
  const i18nKey = key ? `apiMessages.${key}` : '';
  const translated = i18nKey && i18n.te(i18nKey) ? i18n.t(i18nKey) : message;
  return translateNotificationText(translated, i18n.locale);
}

export default i18n;

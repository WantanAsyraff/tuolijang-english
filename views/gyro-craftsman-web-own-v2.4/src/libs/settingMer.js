import Cookies from 'js-cookie';
import { processResourceUrl } from '@/utils/resourceUtil';
import { $ } from '@/lang';
// 请求接口地址 如果没有配置自动获取当前网址路径
const VUE_APP_API_URL = processResourceUrl(process.env.VUE_APP_BASE_API || `${location.origin}`);

function getDefaultWsUrl() {
  const apiUrl = process.env.VUE_APP_BASE_API || VUE_APP_API_URL;
  if (apiUrl) {
    try {
      const url = new URL(apiUrl, location.origin);
      url.protocol = url.protocol === 'https:' ? 'wss:' : 'ws:';
      return url.origin;
    } catch (e) {
      // ignore malformed env and fallback to current host
    }
  }

  return (location.protocol === 'https:' ? 'wss' : 'ws') + '://' + location.host;
}

const VUE_APP_WS_URL = process.env.VUE_APP_WS_URL || getDefaultWsUrl();
const login_title = Cookies.get('MerInfo') ? JSON.parse(Cookies.get('MerInfo')).login_title : '';
const SettingMer = {
  // 服务器地址
  httpUrl: VUE_APP_API_URL,
  // 接口请求地址
  https: VUE_APP_API_URL + '/api/ent',
  // https: 'http://123.56.28.2:20200/api/ent',
  // socket连接
  wsSocketUrl: VUE_APP_WS_URL,
  // 路由标题
  title: login_title || $('ui.runtimeLeak.adminOaTitle'),
};

export default SettingMer;

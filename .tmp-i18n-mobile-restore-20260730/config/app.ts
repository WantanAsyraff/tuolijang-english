const CHOOSE_API_URL_KEY = "chooseApiUrl";
// 接口请求地址 格式:`https://您的域名`
let httpApi = "";
let httpWs = "";

const buildWsUrl = (apiUrl : string) : string => {
  if (!apiUrl) return "";
  const match = apiUrl.match(/https?:\/\/(?:www\.)?([^/]+)/);
  if (!match) return "";
  const protocol = apiUrl.startsWith("https") ? "wss:" : "ws:";
  return `${protocol}//${match[1]}`;
};

// H5端配置:H5接口是浏览器地址，非单独部署不用修改
// #ifdef H5
httpApi = `${window.location.protocol}//${window.location.host}`;
// #endif

// APP端配置：优先使用缓存的服务域名
// #ifdef APP-PLUS
const cachedApiUrl = uni.getStorageSync(CHOOSE_API_URL_KEY);
if (cachedApiUrl) {
  httpApi = cachedApiUrl;
} else if (httpApi) {
  uni.setStorageSync(CHOOSE_API_URL_KEY, httpApi);
}
// #endif

httpWs = buildWsUrl(httpApi);

export const HTTP_REQUEST_URL = httpApi;
export const VUE_APP_WS_URL = httpWs;

// 以下配置在不做二开的前提下,不需要做任何的修改
// tslint:disable-next-line:interface-name
interface Header {
  "content-type" : string;
  "Form-type" : string;
  [key : string] : any;
}

let formType = "h5";
// #ifdef H5
formType = navigator.userAgent.toLowerCase().indexOf("micromessenger") !== -1 || navigator.userAgent.toLowerCase().indexOf("wxwork") !== -1 ? "wechat" : "h5";
// #endif
// #ifdef APP-PLUS
formType = "app";
// #endif
// #ifdef MP-WEIXIN
formType = "wechat";
// #endif
export const FORM_TYPE = formType;

export const HEADER : Header = {
  "content-type": "application/json",
  "Form-type": FORM_TYPE,
};
// 会话密钥名称 请勿修改此配置
export const TOKENNAME = "Authorization";
export const APPVERSION = "AppVersion";
// 缓存时间 0 永久
export const EXPIRE = 0;
// 分页最多显示条数
export const LIMIT = 10;

// 默认全局api入口，一般不用修改
export const BASEAPI = "/api/uni/";
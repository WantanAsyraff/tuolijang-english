import { HEADER, TOKENNAME, BASEAPI, APPVERSION } from "@/config/app";
import { toLogin } from "@/libs/login";
import type { Res } from "@/utils/typeHelper";
import message from "./message";
import store from "../store";
import { socketService } from "@/app/services/socket";
import { getActiveApiUrl } from "@/utils/serverConfig";

const AUTH_EXPIRED_STATUS = [40000, 410000, 410001, 410002, 410003];
const REFRESH_TOKEN_URL = "user/token/refresh";
const TOKEN_REFRESH_THRESHOLD = 10 * 60 * 1000;

interface RequestOptions {
  noAuth?: boolean;
  noVerify?: boolean;
  skipAuthRefresh?: boolean;
  _retry?: boolean;
}

let refreshTokenPromise: Promise<any> | null = null;

const buildHeader = (noAuth = false) => {
  const header = { ...HEADER };
  if (!noAuth) {
    header[TOKENNAME] = store.state.app.token ? "Bearer " + store.state.app.token : "Bearer ";
  }
  // #ifdef APP-PLUS
  header[APPVERSION] = uni.getSystemInfoSync().appWgtVersion;
  // #endif
  // #ifdef H5
  header[APPVERSION] = uni.getSystemInfoSync().appVersion;

  // 判断是否在问卷调查页面，如果是且本地保存了 UNIQUE，则添加到请求头中
  if (location.pathname.includes("/pages/module/questionnaire")) {
    const CURD_UNIQUE = uni.getStorageSync("QUESTIONNAIRE_CURD_UNIQUE");
    if (CURD_UNIQUE) {
      header["Curd-Unique"] = CURD_UNIQUE;
    }
  }

  // #endif
  return header;
};

export const refreshAccessToken = () => {
  const refreshToken = store.state.app.refreshToken;
  if (!refreshToken) {
    return Promise.reject({ message: "缺少刷新TOKEN" });
  }
  // #ifndef APP-PLUS
  if (store.state.app.refreshExpiresAt && Date.now() >= store.state.app.refreshExpiresAt) {
    return Promise.reject({ message: "登录信息已失效，请重新登录" });
  }
  // #endif

  if (!refreshTokenPromise) {
    refreshTokenPromise = new Promise((resolve, reject) => {
      uni.request({
        url: getActiveApiUrl() + BASEAPI + REFRESH_TOKEN_URL,
        method: "POST",
        timeout: 10000,
        header: buildHeader(true),
        data: { refresh_token: refreshToken },
        success: (res: UniApp.RequestSuccessCallbackResult | Res) => {
          const responseData = res.data as any;
          if (responseData?.status === 200 && responseData.data?.token && responseData.data?.refresh_token) {
            store.commit("refreshLogin", responseData.data);
            socketService.ensureConnected(responseData.data.token);
            resolve(responseData.data);
          } else {
            reject(responseData || { message: "登录状态已失效" });
          }
        },
        fail: () => {
          reject({ message: "刷新登录凭证失败" });
        }
      });
    }).finally(() => {
      refreshTokenPromise = null;
    });
  }

  return refreshTokenPromise;
};

export const ensureValidAccessToken = () => {
  if (!store.state.app.isLogin || !store.state.app.token) {
    return Promise.resolve();
  }

  const tokenExpiresAt = store.state.app.tokenExpiresAt;
  if (!tokenExpiresAt || Date.now() < tokenExpiresAt - TOKEN_REFRESH_THRESHOLD) {
    return Promise.resolve();
  }

  return refreshAccessToken();
};

const handleAuthExpired = (
  url: string,
  method: UniNamespace.RequestOptions["method"],
  data: object,
  opt: RequestOptions
) => {
  if (opt.skipAuthRefresh || opt._retry) {
    toLogin({ forceLogout: true });
    return Promise.reject({ message: "登录已过期，请重新登录" });
  }

  return refreshAccessToken()
    .then(() => baseRequest(url, method, data, { ...opt, _retry: true }))
    .catch((error) => {
      toLogin({ forceLogout: true });
      return Promise.reject(error);
    });
};

// 注册请求拦截器（只注册一次）
uni.addInterceptor("request", {
  invoke(args) {
    // 处理get请求发送数组的问题
    const { data, method } = args;
    if (method === "get" && Object.keys(data).length > 0) {
      const arrKey = Object.keys(data);
      const arrValue = Object.values(data) as any[];
      let str = "";
      for (let i = 0; i < arrValue.length; i++) {
        if (typeof arrValue[i] === "object") {
          for (let j = 0; j < arrValue[i].length; j++) {
            str += `&${arrKey[i]}[]=${arrValue[i][j]}`;
          }
        } else {
          str += `&${arrKey[i]}=${arrValue[i]}`;
        }
      }

      str = str.slice(1);
      args.data = {};
      args.url = `${args.url}?${str}`;
    }
  }
});

/**
 * 发送请求
 */
function baseRequest(url: string, method: UniNamespace.RequestOptions["method"], data: object, opt: RequestOptions = {}) {
  const { noAuth = false, noVerify = false } = opt;
  // let Url = HTTP_REQUEST_URL
  const Url = getActiveApiUrl();
  const header = buildHeader(noAuth);
  let networkType = "";
  uni.getNetworkType({
    success: (res) => {
      networkType = res.networkType;
    }
  });
  return new Promise((resolve, reject) => {
    if (networkType === "none") {
      message.error("网络开小差了！");
      return false;
    }
    uni.request({
      url: Url + BASEAPI + url,
      method: method,
      timeout: 10000,
      header: header,
      data: data || {},
      success: (res: UniApp.RequestSuccessCallbackResult | Res) => {
        if (res.statusCode === 500) {
          reject({ message: "服务器内部错误: 500" });
        } else if (noVerify)
          resolve(res.data);
        else if (res.data.status == 200)
          resolve(res.data);
        else if (AUTH_EXPIRED_STATUS.indexOf(res.data.status) !== -1) {
          handleAuthExpired(url, method, data, opt).then(resolve).catch(reject);
        } else {
          reject(res.data);
        }
      },
      fail: (error) => {
        console.log(error);
        reject({ message: "请求失败" });
      }
    });
  });
}

const request: { [method: string]: (api: string, data?: object, opt?: RequestOptions) => any } = {};

["options", "get", "post", "put", "head", "delete", "trace", "connect"].forEach((method) => {
  request[method] = (api: string, data: object = {}, opt: RequestOptions = {}) => baseRequest(api, method as UniNamespace.RequestOptions["method"], data, opt);
});

export default request;

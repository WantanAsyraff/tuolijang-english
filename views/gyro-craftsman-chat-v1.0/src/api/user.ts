import { http } from "@/utils/http";

interface PwdLoginParams {
  account: string; // 手机号
  password: string; // 密码
  captcha: string; // 验证码
  key: string; // 验证码key
}

/**
 * 密码登录
 */
export const userPwdLoginApi = (data: PwdLoginParams) => {
  return http.post("/user/login", data, { requireAuth: false });
};

interface SmsLoginParams {
  phone: string; // 手机号
  verification_code: string; // 验证码
  key: string; // 验证码key
}

/**
 * 短信登录
 */
export const userSmsLoginApi = (data: SmsLoginParams) => {
  return http.post("/user/phone_login", data, { requireAuth: false });
};

/**
 * 获取验证码
 */
export const getCaptchaApi = () => {
  return http.get("/common/captcha", null, { requireAuth: false });
};

/**
 *  获取短信验证码 Key
 */
export const getCmsKeyApi = () => {
  return http.get("/common/verify/key", null, { requireAuth: false });
};

interface GetCmsApiParams {
  phone: string; // 手机号
  key: string; // 验证码key
  from: number; // 来源
  types: number; // 类型
}

/**
 * 获取短信验证码
 */
export const getCmsApi = (data: GetCmsApiParams) => {
  return http.post("/common/verify", data, { requireAuth: false });
};

/**
 * 获取用户信息
 */
export const getUserInfoApi = () => {
  return http.get("/user/info");
};

/**
 * 获取扫码登录key
 */
export const getQrcodeScanKeyApi = () => {
  return http.get("/user/scan_key", null, { requireAuth: false });
};

interface CheckQrcodeScanStatusParams {
  key: string; // 扫码登录key
}

/**
 * 检查扫码登录状态
 */
export const checkQrcodeScanStatusApi = (data: CheckQrcodeScanStatusParams) => {
  return http.post("/user/scan_status", data, { requireAuth: false });
};

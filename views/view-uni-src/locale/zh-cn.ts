import generatedUi from "./generated-ui-zh";

export default {
  ui: generatedUi,
  common: {
    language: "语言",
    chinese: "中文",
    english: "English",
    translateToEnglish: "翻译成英文",
    confirm: "确定",
    cancel: "取消",
    logout: "退出登录",
    confirmLogout: "确认退出登录",
    copied: "复制成功",
  },
  user: {
    avatar: "头像",
    nickname: "昵称",
    userId: "用户ID",
    phone: "手机",
    email: "邮箱",
    password: "密码",
    enterNickname: "请输入昵称",
    enterEmail: "请输入邮箱",
  },
  status: {
    networkError: "网络开小差了！",
    requestFailed: "请求失败。",
    serverError: "服务器内部错误: 500",
    loginExpired: "登录已过期，请重新登录。",
    refreshTokenMissing: "缺少刷新 TOKEN。",
    refreshTokenFailed: "刷新登录凭证失败。",
  },
  upload: {
    uploading: "上传中",
    uploadFailed: "上传失败，请稍后再试！",
    chooseFileFailed: "文件选择失败。",
    chooseImageFailed: "图片选择失败。",
    fileTooLarge: "文件大小不能超过{size}MB。",
    imageTooLarge: "图片大小不能超过{size}MB。",
  },
};
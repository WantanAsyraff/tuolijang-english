/**
 * 启动参数处理
 * 纯函数，不依赖 UI 生命周期，从 App.vue onLaunch 中抽出
 */

/**
 * 同步问卷调查上下文参数
 * 若从问卷页面启动且携带 unique 参数，将其保存到本地存储
 */
export const syncQuestionnaireContext = (options: any): void => {
  if (options.path === "pages/module/questionnaire") {
    if (options.query?.unique) {
      uni.setStorageSync("QUESTIONNAIRE_CURD_UNIQUE", options.query.unique);
    } else {
      uni.removeStorageSync("QUESTIONNAIRE_CURD_UNIQUE");
    }
  }
};

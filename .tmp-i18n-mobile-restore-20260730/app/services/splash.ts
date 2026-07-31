/**
 * 启动页管理服务
 * 提供立即关闭和兜底延迟关闭两种策略
 */
export const splashService = (() => {
  let fallbackTimer: ReturnType<typeof setTimeout> | null = null;

  /**
   * 立即关闭启动页
   */
  const closeNow = (): void => {
    // #ifdef APP-PLUS
    try {
      plus.navigator.closeSplashscreen();
    } catch (e) {
      console.error("关闭启动页失败", e);
    }
    // #endif
  };

  /**
   * 设置兜底延迟关闭（防止极端情况下启动页卡住）
   * @param delay 延迟毫秒数，默认 2000ms
   */
  const startFallback = (delay: number = 2000): void => {
    // #ifdef APP-PLUS
    if (fallbackTimer) clearTimeout(fallbackTimer);
    fallbackTimer = setTimeout(() => {
      closeNow();
      fallbackTimer = null;
    }, delay);
    // #endif
  };

  return {
    closeNow,
    startFallback,
  };
})();

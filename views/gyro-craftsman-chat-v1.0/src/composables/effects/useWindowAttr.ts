import { useResize } from "../ui/useResize";
import { isInIframe, isAppPreview } from "@/config";

/**
 * 设置窗口属性，将部分信息设置到 document.documentElement 上，供全局使用
 */
export const useWindowAttr = () => {
  const width = ref(window.innerWidth);
  const height = ref(window.innerHeight);

  useResize(() => {
    width.value = window.innerWidth;
    height.value = window.innerHeight;
  });

  if (isInIframe) {
    document.documentElement.setAttribute("data-in-iframe", "");
  }

  if (isAppPreview) {
    document.documentElement.setAttribute("data-app-preview", "");
  }

  watch(
    [width, height],
    ([newWidth, newHeight]) => {
      document.documentElement.style.setProperty("--vw", `${newWidth}px`);
      document.documentElement.style.setProperty("--vh", `${newHeight}px`);
    },
    {
      immediate: true
    }
  );
};

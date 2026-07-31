import { useResize } from "./useResize";

/**
 * 判断是否是移动端小屏幕
 */
export const useMediumScreen = () => {
  const isMediumScreen = ref(false);

  const handleResize = () => {
    isMediumScreen.value = window.innerWidth < 768;
  };

  handleResize();
  useResize(handleResize);

  return {
    isMediumScreen,
  };
};

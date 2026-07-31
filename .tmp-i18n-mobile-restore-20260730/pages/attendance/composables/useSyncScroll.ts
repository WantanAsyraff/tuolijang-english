import { useScrollViewScroll } from "./useScroll";

type ScrollPart = "head" | "body" | "aside";

export const useSyncScroll = () => {
  const scrollPart = ref<ScrollPart>();

  const headScrollLeft = ref(0);
  const bodyScrollLeft = ref(0);
  const bodyScrollTop = ref(0);
  const asideScrollTop = ref(0);

  // 生成组件滚动回调处理程序
  const createScrollAfterCallback = (key: ScrollPart) => {
    return () => {
      if (!scrollPart.value) {
        scrollPart.value = key;
        syncScrollState();
      } else if (scrollPart.value === key) {
        // 同步滚动状态
        syncScrollState();
      } else {
        // 滚动部分不是自己，却触发了滚动事件，说明滚动事件来自于另一个滚动组件 set scrollLeft 引发的滚动
        scrollPart.value = undefined;
      }
    };
  };

  const [headScrollDetail, handleHeadScroll] = useScrollViewScroll(createScrollAfterCallback("head"));

  const [bodyScrollDetail, handleBodyScroll] = useScrollViewScroll(createScrollAfterCallback("body"));

  const [asideScrollDetail, handleAsideScroll] = useScrollViewScroll(createScrollAfterCallback("aside"));

  function syncScrollState() {
    switch (scrollPart.value) {
      case "body":
        headScrollLeft.value = bodyScrollDetail.scrollLeft;
        asideScrollTop.value = bodyScrollDetail.scrollTop;
        break;
      case "head":
        bodyScrollLeft.value = headScrollDetail.scrollLeft;
        break;
      case "aside":
        bodyScrollTop.value = asideScrollDetail.scrollTop;
        break;
    }
  }

  const tableScrollInfo = {
    header: {
      scrollLeft: headScrollLeft,
      handleScroll: handleHeadScroll
    },
    body: {
      scrollLeft: bodyScrollLeft,
      scrollTop: bodyScrollTop,
      handleScroll: handleBodyScroll
    },
    aside: {
      scrollTop: asideScrollTop,
      handleScroll: handleAsideScroll
    }
  };

  provide("tableScrollInfo", tableScrollInfo);
};

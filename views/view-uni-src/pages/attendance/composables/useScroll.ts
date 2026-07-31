interface ScrollEventDetail {
  scrollLeft: number;
  scrollTop: number;
}

interface ScrollEvent extends Event {
  detail: ScrollEventDetail;
}

type EventHandler<T> = (e: T) => void;

type ScrollViewDetailTuple = [ScrollEventDetail, EventHandler<ScrollEvent>];
/**
 *
 * scrollView scroll 滚动处理
 */
export const useScrollViewScroll = (callback?: EventHandler<ScrollEventDetail>): ScrollViewDetailTuple => {
  const scrollDetail = reactive<ScrollEventDetail>({
    scrollLeft: 0,
    scrollTop: 0
  });

  const handleScroll = (e: ScrollEvent) => {
    const { scrollLeft, scrollTop } = e.detail;
    scrollDetail.scrollLeft = scrollLeft;
    scrollDetail.scrollTop = scrollTop;
    callback && callback(e.detail);
  };

  return [
    scrollDetail,
    handleScroll
  ];
};

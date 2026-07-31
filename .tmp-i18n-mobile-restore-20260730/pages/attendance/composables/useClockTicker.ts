import { computed, onDeactivated, onMounted, onUnmounted, ref, type ComputedRef } from "vue";

/** 打卡页时钟 ticker 的依赖配置。 */
interface UseClockTickerOptions {
  /** 是否开启定位；开启时每分钟刷新一次定位，保证范围状态不过期。 */
  isLocationEnable: ComputedRef<boolean>;
  /** 当前打卡状态过期时间戳，超过后触发 onClockExpired。 */
  clockUpdateTime: ComputedRef<number> | { value: number };
  /** 分钟变化回调，考勤页用来刷新定位。 */
  onMinuteChange: () => void;
  /** 打卡状态过期回调，预留给后续自动刷新规则。 */
  onClockExpired: () => void;
}

/** 将时/分/秒补齐为两位显示。 */
function padTime(value: number) {
  return value < 10 ? `0${value}` : `${value}`;
}

/**
 * 页面实时时钟。
 *
 * 负责：
 * - 每秒更新时间文本。
 * - 每分钟触发一次定位刷新，降低打卡范围误差。
 * - 监听 clockUpdateTime，到达状态边界时通知页面。
 */
export function useClockTicker(options: UseClockTickerOptions) {
  /** 当前时间。 */
  const date = ref(new Date());
  /** 上一次 tick 所在分钟，用于判断是否跨分钟。 */
  const lastMinute = ref(date.value.getMinutes());
  /** setTimeout 句柄，页面卸载/失活时清理。 */
  const timer = ref<ReturnType<typeof setTimeout> | null>(null);

  /** 当前小时。 */
  const hour = computed(() => date.value.getHours());
  /** 当前分钟，两位格式。 */
  const minute = computed(() => padTime(date.value.getMinutes()));
  /** 当前秒，两位格式。 */
  const second = computed(() => padTime(date.value.getSeconds()));
  /** 按钮内展示的时间文本。 */
  const timeText = computed(() => `${hour.value}:${minute.value}:${second.value}`);

  /** 单次时钟推进，并安排下一秒 tick。 */
  function tick() {
    date.value = new Date();

    const currentMinute = date.value.getMinutes();
    if (lastMinute.value !== currentMinute) {
      lastMinute.value = currentMinute;
      if (options.isLocationEnable.value) {
        // 定位不需要每秒刷新；每分钟刷新一次能平衡准确性和性能/耗电。
        options.onMinuteChange();
      }
    }

    const currentTimestamp = date.value.getTime() / 1000;
    if (options.clockUpdateTime.value <= currentTimestamp) {
      // 到达后端给出的状态过期点，通知外层决定是否刷新规则。
      options.onClockExpired();
    }

    timer.value = setTimeout(tick, 1000);
  }

  /** 停止计时器，避免页面离开后继续 setState。 */
  function stop() {
    if (!timer.value) return;
    clearTimeout(timer.value);
    timer.value = null;
  }

  onMounted(() => {
    stop();
    tick();
  });

  onDeactivated(stop);
  onUnmounted(stop);

  return {
    timeText,
  };
}

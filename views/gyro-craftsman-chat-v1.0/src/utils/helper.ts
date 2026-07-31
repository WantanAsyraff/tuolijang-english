import { Message } from "@/utils/message";
import { translate, translateSystemText } from "@/locale";

export type AnyFunction = (...args: any[]) => any;

/**
 * 节流
 * @param fn 函数
 * @param delay 延迟时间
 * @returns 节流后的函数
 */
export const throttle = <F extends AnyFunction>(fn: F, delay: number) => {
  let timer: NodeJS.Timeout | null = null;
  return function (this: ThisParameterType<F>, ...args: Parameters<F>) {
    if (timer) return;
    timer = setTimeout(() => {
      fn.apply(this, args);
      timer = null;
    }, delay);
  };
};

/**
 * 防抖
 * @param fn 函数
 * @param delay 延迟时间
 * @returns 防抖后的函数
 */
export const debounce = <F extends AnyFunction>(fn: F, delay: number) => {
  let timer: NodeJS.Timeout | null = null;
  return function (this: ThisParameterType<F>, ...args: Parameters<F>) {
    if (timer) clearTimeout(timer);
    timer = setTimeout(() => {
      fn.apply(this, args);
    }, delay);
  };
};

/**
 * 获取元素的 dataset 属性，如果目标元素没有 dataset 属性，则向上查找父级元素的 dataset 属性
 * @param target 目标元素
 * @param parentSelector 父级选择器
 * @param key 要获取的 key
 * @param fullDataset 是否获取全部 dataset
 */
export function getDataSet(target: Element, parentSelector: string, key: string, fullDataset?: true): DOMStringMap | null;
export function getDataSet(target: Element, parentSelector: string, key: string, fullDataset?: false): string | null;
export function getDataSet(target: Element, parentSelector: string, key: string, fullDataset: boolean = false) {
  let hasDatasetEl: HTMLElement | undefined;
  if (target instanceof HTMLElement && target.dataset[key] !== undefined) {
    hasDatasetEl = target;
  } else if (parentSelector) {
    const parentEl = target.closest(parentSelector);
    if (parentEl && parentEl instanceof HTMLElement && parentEl.dataset[key] !== undefined) {
      hasDatasetEl = parentEl;
    }
  }
  if (hasDatasetEl) {
    if (fullDataset) {
      return hasDatasetEl.dataset;
    }
    return hasDatasetEl.dataset[key] as string;
  }
  return null;
};

/**
 * 复制文本
 * @param text 文本
 */
export const copyText = (text: string) => {
  const showCopySuccess = () => Message.success(translate("common.copied"));
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text);
    showCopySuccess();
  } else {
    const copyInput = document.createElement("textarea");
    copyInput.style.position = "absolute";
    copyInput.style.opacity = "0";
    copyInput.value = text;
    document.body.appendChild(copyInput);
    copyInput.select();
    document.execCommand("copy");
    document.body.removeChild(copyInput);
    showCopySuccess();
  }
};

/**
 * 获取当前时间范围
 * @returns 当前时间范围
 */
export const getTimeRangeByDate = () => {
  const hour = new Date().getHours();
  if (hour >= 0 && hour < 6) {
    return translateSystemText("凌晨") as string;
  } else if (hour >= 6 && hour < 12) {
    return translateSystemText("上午") as string;
  } else if (hour >= 12 && hour < 14) {
    return translateSystemText("中午") as string;
  } else if (hour >= 14 && hour < 18) {
    return translateSystemText("下午") as string;
  } else {
    return translateSystemText("晚上") as string;
  }
};

/**
 * 获取随机数
 * @param max 上限
 * @param count 要生成的数量
 * @returns 随机数
 */
export const getRandomNumbers = (max: number, count: number = 3) => {
  if (max <= count) throw new Error("n must be greater than 3");

  const numbers = new Set<number>();
  while (numbers.size < count) {
    const randomNum = Math.floor(Math.random() * max);
    numbers.add(randomNum);
  }
  return Array.from(numbers);
};

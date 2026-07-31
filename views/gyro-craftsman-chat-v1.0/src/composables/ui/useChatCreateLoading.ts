import { INJECT_KEY } from "@/constants/inject-key";

/**
 * 提供 loading 状态
 * @param key 注入的 key
 * @returns
 */
export const useProvideLoading = (key: INJECT_KEY) => {
  const isLoading = ref(false);

  const setLoading = (value: boolean) => {
    isLoading.value = value;
  };

  provide(key, isLoading);

  return { isLoading, setLoading };
};

/**
 * 注入 loading 状态
 * @param key 注入的 key
 * @param defaultValue 默认值
 * @returns
 */
export const useInjectLoading = (key: INJECT_KEY, defaultValue: boolean = false) => {
  return inject(key, () => ref(defaultValue), true);
};

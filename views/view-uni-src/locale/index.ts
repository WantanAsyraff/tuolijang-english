import { createI18n } from "vue-i18n";
import { messages, translateSystemTextValue } from "./generated-locale";

export type SupportedLocale = "zh-cn" | "en";

const LANGUAGE_KEY = "language";
const supportedLocales: SupportedLocale[] = ["zh-cn", "en"];

export const normalizeLanguage = (language?: string | null): SupportedLocale | "" => {
  const lang = String(language || "").toLowerCase();
  if (["zh", "zh_cn", "zh-cn", "zh-hans"].includes(lang)) return "zh-cn";
  if (["en", "en_us", "en-us", "en-gb"].includes(lang)) return "en";
  return "";
};

export const getLanguage = (): SupportedLocale => {
  const stored = normalizeLanguage(uni.getStorageSync(LANGUAGE_KEY));
  if (stored && supportedLocales.includes(stored)) return stored;

  return "zh-cn";
};

export const setLanguage = (language: SupportedLocale): SupportedLocale => {
  const locale = normalizeLanguage(language) || "zh-cn";
  uni.setStorageSync(LANGUAGE_KEY, locale);
  // #ifdef H5
  document.cookie = `${LANGUAGE_KEY}=${locale}; path=/; max-age=31536000`;
  // #endif
  i18n.global.locale.value = locale;
  uni.$emit("language:changed", locale);
  return locale;
};

const i18n = createI18n({
  legacy: false,
  globalInjection: true,
  locale: getLanguage(),
  fallbackLocale: "zh-cn",
  messages,
});

export const translateSystemText = (value: unknown, englishValue?: string): unknown =>
  translateSystemTextValue(value, { locale: i18n.global.locale.value, englishValue });

const SYSTEM_FORMATTER_KEYS = new Set(["formatter", "labelFormatter", "tooltipFormatter", "valueFormatter"]);

export function localizeSystemObject<T>(value: T, seen = new WeakMap<object, unknown>(), key = ""): T {
  if (typeof value === "string") return translateSystemText(value) as T;
  if (typeof value === "function") {
    if (!SYSTEM_FORMATTER_KEYS.has(key)) return value;
    return function localizedFormatter(this: unknown, ...args: unknown[]) {
      return localizeSystemObject(value.apply(this, args));
    } as T;
  }
  if (!value || typeof value !== "object" || value instanceof Date || value instanceof RegExp) return value;
  if (seen.has(value as object)) return seen.get(value as object) as T;

  const output: unknown = Array.isArray(value) ? [] : {};
  seen.set(value as object, output);
  Object.entries(value as Record<string, unknown>).forEach(([childKey, item]) => {
    (output as Record<string, unknown>)[childKey] = localizeSystemObject(item, seen, childKey);
  });
  return output as T;
}

export default i18n;
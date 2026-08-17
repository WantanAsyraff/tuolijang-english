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

const getQueryLanguage = (): SupportedLocale | "" => {
  if (typeof location === "undefined") return "";
  return normalizeLanguage(new URL(location.href).searchParams.get(LANGUAGE_KEY));
};

const getCookieLanguage = (): SupportedLocale | "" => {
  if (typeof document === "undefined") return "";
  const match = document.cookie.match(/(?:^|;\s*)language=([^;]+)/);
  return normalizeLanguage(match ? decodeURIComponent(match[1]) : "");
};

export const getLanguage = (): SupportedLocale => {
  const query = getQueryLanguage();
  if (query) return query;

  const stored = normalizeLanguage(localStorage.getItem(LANGUAGE_KEY));
  if (stored && supportedLocales.includes(stored)) return stored;

  const cookie = getCookieLanguage();
  if (cookie) return cookie;

  return "zh-cn";
};

const i18n = createI18n({
  legacy: false,
  globalInjection: true,
  locale: getLanguage(),
  fallbackLocale: "zh-cn",
  messages,
});

export const setLanguage = (language: SupportedLocale): SupportedLocale => {
  const locale = normalizeLanguage(language) || "zh-cn";
  localStorage.setItem(LANGUAGE_KEY, locale);
  document.cookie = `${LANGUAGE_KEY}=${locale}; path=/; max-age=31536000`;
  i18n.global.locale.value = locale;
  return locale;
};

export const translate = (key: string): string => i18n.global.t(key);

export const translateSystemText = (value: unknown, englishValue?: string): unknown =>
  translateSystemTextValue(value, { locale: i18n.global.locale.value, englishValue });

export default i18n;

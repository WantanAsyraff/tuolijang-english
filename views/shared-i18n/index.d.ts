export type SupportedLocale = 'zh-cn' | 'en'

export interface TranslateSystemTextOptions {
  locale?: string | null
  englishValue?: string | null
  code?: string | number | null
  route?: string | null
  key?: string | null
}

export declare const SYSTEM_TEXT_EN: Readonly<Record<string, string>>
export declare function normalizeLocale(language?: string | null): SupportedLocale | ''
export declare function translateSystemTextValue<T>(value: T, options?: TranslateSystemTextOptions): T | string
export declare function containsHan(value: unknown): boolean

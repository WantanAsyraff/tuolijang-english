import type { ComponentCustomProperties } from "vue";

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    $ts: (value: unknown, englishValue?: string) => unknown;
    $localize: <T>(value: T) => T;
  }
}

export {};

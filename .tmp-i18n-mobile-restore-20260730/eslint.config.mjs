// @ts-check
import { defineConfig } from "eslint/config";
import globals from "globals";
import pluginVue from "eslint-plugin-vue";
import tseslint from "typescript-eslint";
import stylistic from "@stylistic/eslint-plugin";

export default defineConfig([
  {
    ignores: [
      "unpackage/",
      "uni_modules/",
      "node_modules/"
    ]
  },
  {
    files:
      ["**/*.{js,mjs,cjs,ts,vue}"],
    languageOptions:
    {
      globals: {
        ...globals.browser,
        computed: "readonly",
        ref: "readonly",
        reactive: "readonly",
        onMounted: "readonly",
        onLoad: "readonly",
        onShow: "readonly",
        onHide: "readonly",
        onUnload: "readonly",
        onReachBottom: "readonly",
        onPullDownRefresh: "readonly",
        uni: "readonly",
        getCurrentInstance: "readonly",
        nextTick: "readonly",
        toRefs: "readonly",
        watch: "readonly",
        getCurrentPages: "readonly",
        plus: "readonly"
      }
    }
  },
  ...tseslint.configs.recommended,
  ...pluginVue.configs["flat/essential"],
  {
    files: ["**/*.vue"],
    languageOptions: {
      parserOptions: {
        parser: tseslint.parser
      }
    }
  },
  stylistic.configs.customize({
    indent: 2,
    quotes: "double",
    semi: true,
    commaDangle: "only-multiline",
    braceStyle: "1tbs"
  }),
  {
    rules: {
      "@typescript-eslint/no-explicit-any": "off",
      "@typescript-eslint/no-unused-expressions": "off",
      "vue/multi-word-component-names": "off"
    }
  }
]);

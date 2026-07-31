const globals = require("globals");

// .eslintrc.js
module.exports = {
  root: true,

  env: {
    browser: true,
    node: true,
    es6: true,
  },

  parser: "vue-eslint-parser",

  parserOptions: {
    parser: "babel-eslint",
    ecmaVersion: 2022,
    sourceType: "module",
  },

  globals: {
    ...globals.browser,
    ...globals.es2022,
  },

  extends: [
    "plugin:vue/recommended",
    "eslint:recommended",
  ],

  rules: {
    // ---- 格式强制 ----
    "semi": ["error", "always"],                        // 必须分号结尾
    "quotes": ["error", "double", { avoidEscape: true, allowTemplateLiterals: false }],
    "indent": ["error", 2, { SwitchCase: 1 }],
    "comma-dangle": ["error", "always-multiline"],      // 多行时尾逗号，利于 git diff
    "eol-last": ["error", "always"],                    // 文件末尾换行

    // ---- 空格/换行 ----
    "keyword-spacing": ["error", { before: true, after: true }],
    "space-before-blocks": ["error", "always"],
    "space-infix-ops": "error",
    "space-in-parens": ["error", "never"],
    "object-curly-spacing": ["error", "always"],
    "array-bracket-spacing": ["error", "never"],
    "no-trailing-spaces": "error",
    "no-multiple-empty-lines": ["error", { max: 1, maxEOF: 0 }],

    // ---- 变量/逻辑 ----
    "no-var": "error",                                  // 禁用 var
    "prefer-const": ["error", { destructuring: "all" }],
    "no-unused-vars": ["warn", { vars: "all", args: "none" }],
    "no-console": process.env.NODE_ENV === "production" ? "warn" : "off",
    "no-debugger": process.env.NODE_ENV === "production" ? "error" : "off",
    "eqeqeq": ["error", "always", { null: "ignore" }],
    "no-eval": "error",

    // ---- Vue 2 specific ----
    "vue/html-indent": ["error", 2],
    "vue/html-quotes": ["error", "double"],             // template 属性也用双引号
    "vue/max-attributes-per-line": ["error", {
      singleline: { max: 3 },
      multiline: { max: 1 },
    }],
    "vue/component-name-in-template-casing": ["error", "PascalCase"],
    "vue/no-unused-vars": "warn",
    "vue/no-unused-components": "warn",
    "vue/require-default-prop": "off",                 // Vue2 项目通常不强制
    "vue/multi-word-component-names": "off",           // 存量组件太多，不强制
    "vue/html-self-closing": ["any", {
      html: { void: "always", normal: "never", component: "always" },
    }],
  },
};

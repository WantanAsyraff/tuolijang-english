"use strict";
const path = require("path");
const moment = require("moment");
const defaultSettings = require("./src/settings.js");
const webpack = require("webpack");
const HAS_HAN = /[\u3400-\u9fff]/;
const LOCALIZED_TEMPLATE_ATTRIBUTES = new Set([
  "alt",
  "aria-label",
  "button-text",
  "cancel-text",
  "confirm-text",
  "content",
  "empty-text",
  "label",
  "loading-text",
  "placeholder",
  "text",
  "title",
]);
const SKIPPED_LOCALIZATION_TAGS = new Set(["script", "style", "code", "pre"]);

const templateI18nModule = {
  transformNode(element) {
    if (
      !element ||
      SKIPPED_LOCALIZATION_TAGS.has(element.tag) ||
      (element.attrsMap && element.attrsMap["data-skip-i18n"] !== undefined)
    ) {
      return element;
    }

    (element.children || []).forEach((child) => {
      if (child.type !== 3 || !HAS_HAN.test(child.text || "")) return;
      child.type = 2;
      child.expression = `_s($ts(${JSON.stringify(child.text)}))`;
    });

    (element.attrs || []).forEach((attribute) => {
      if (
        !LOCALIZED_TEMPLATE_ATTRIBUTES.has(attribute.name) ||
        typeof attribute.value !== "string"
      ) {
        return;
      }
      try {
        const source = JSON.parse(attribute.value);
        if (HAS_HAN.test(source)) {
          attribute.value = `_s($ts(${JSON.stringify(source)}))`;
        }
      } catch (error) {
        // Dynamic attributes are already expressions and must remain untouched.
      }
    });

    return element;
  },
};

function resolve(dir) {
  return path.join(__dirname, dir);
}

const name = defaultSettings.title || "vue Element Admin"; // page title
const port = process.env.port || process.env.npm_config_port || 9527; // dev port

module.exports = {
  publicPath: defaultSettings.roterPre + "/",
  outputDir: "dist",
  assetsDir: "system",
  css: {
    extract: {
      ignoreOrder: true,
    },
  },
  // 在 dist/index.html 的输出
  indexPath: "index.html",

  lintOnSave: false,
  productionSourceMap: false,
  devServer: {
    publicPath: defaultSettings.roterPre + "/",
    port: port,
    disableHostCheck: true,
    proxy: {
      "^/api": {
        target: "http://dev.oa.crmeb.net",
        changeOrigin: true,
      },
      "^/uploads": {
        target: "http://dev.oa.crmeb.net",
        changeOrigin: true,
        pathRewrite: {},
      },
      "^/ws": {
        target: "http://dev.oa.crmeb.net",
        changeOrigin: true,
        ws: true,
      },
    },
    overlay: {
      warnings: false,
      errors: true,
    },
    before(app) {
      app.get(/^\/$/, (req, res) => {
        res.redirect(defaultSettings.roterPre);
      });
    },
  },
  configureWebpack: {
    name: name,
    performance: {
      hints: false,
    },
    resolve: {
      alias: {
        "@": resolve("src"),
        "~@": resolve("static"),
      },
    },
  },

  transpileDependencies: [
    // 对 xmind 编辑器中的 quill 做处理
    "quill",

    // 将 xmind 在线预览工具加入 babel 进行处理
    "xmind-embed-viewer",
    /@wecom[\\/]jssdk/,
    // 添加pdfjs-dist到转译依赖中
    "pdfjs-dist",
  ],
  chainWebpack(config) {
    // 打包可视化插件
    if (process.env.ANALYZE) {
      config.plugin("webpack-bundle-analyzer")
        .use(require("webpack-bundle-analyzer").BundleAnalyzerPlugin);
    }
    // config.plugin('compressionPlugin')
    // .use(new CompressionPlugin({
    //     filename: '[path].gz[query]',
    //     algorithm: 'gzip',
    //     test: productionGzipExtensions,
    //     threshold: 10240,
    //     minRatio: 0.8,
    //     deleteOriginalAssets: true
    // }));

    config.plugins.delete("preload"); // TODO: need test
    config.plugins.delete("prefetch"); // TODO: need test

    config.plugin("html").tap(args => {
      args[0].meta = {
        ...(args[0].meta || {}),
        "build-time": moment().format("YYYY-MM-DD HH:mm:ss"),
      };
      return args;
    });

    config
      .plugin("ignore-moment-locale")
      .use(webpack.IgnorePlugin, [{
        resourceRegExp: /^\.\/locale$/,  // 匹配引入 locale 模块的路径
        contextRegExp: /moment$/,  // 匹配 moment 模块
      }]);

    config.module.rule("svg").exclude.add(resolve("src/icons")).end();
    config.module
      .rule("icons")
      .test(/\.svg$/)
      .include.add(resolve("src/icons"))
      .end()
      .use("svg-sprite-loader")
      .loader("svg-sprite-loader")
      .options({
        symbolId: "icon-[name]",
      })
      .end();

    config.module
      .rule("vue")
      .use("vue-loader")
      .loader("vue-loader")
      .tap((options) => {
        options.compilerOptions.preserveWhitespace = false;
        options.compilerOptions.modules = [
          ...(options.compilerOptions.modules || []),
          templateI18nModule,
        ];
        return options;
      })
      .end();
    config
      // https://webpack.js.org/configuration/devtool/#development
      .when(process.env.NODE_ENV === "development", (config) => config.devtool("eval-source-map"));

    config.when(process.env.NODE_ENV !== "development", (config) => {
      config
        .plugin("ScriptExtHtmlWebpackPlugin")
        .after("html")
        .use("script-ext-html-webpack-plugin", [
          {
            // `runtime` must same as runtimeChunk name. default is `runtime`
            inline: /runtime\..*\.js$/,
          },
        ])
        .end();
      config.optimization.splitChunks({
        chunks: "all",
        maxAsyncRequests: 30,
        cacheGroups: {
          asyncShared: {
            name: "chunk-async-shared",
            test: /[\\/]node_modules[\\/]/,
            priority: 8,
            chunks: "async", // 针对异步加载
            minChunks: 2,    // 核心：被至少 2 个异步路由复用时才提取
            minSize: 20000,  // 小于 20kb 的就算了，没必要提取
          },
          wangeditor: {
            name: "chunk-wangeditor",
            test: /[\\/]node_modules[\\/]@wangeditor[\\/]/,
            priority: 25,
            chunks: "async",
          },
          aceBuilds: {
            name: "chunk-ace-builds",
            test: /[\\/]node_modules[\\/]ace-builds[\\/]/,
            priority: 25,
            chunks: "async",
          },
          pinyinPro: {
            name: "chunk-pinyin-pro",
            test: /[\\/]node_modules[\\/]pinyin-pro[\\/]/,
            priority: 25,
            chunks: "async",
          },
          simpleMindMap: {
            name: "chunk-mind-map",
            test: /[\\/]node_modules[\\/]simple-mind-map[\\/]/,
            priority: 25,
            chunks: "async",
          },
          lodash: {
            name: "chunk-lodash",
            test: /[\\/]node_modules[\\/]lodash[\\/]/,
            priority: 25,
            chunks: "async",
          },
          mavonEditor: {
            name: "chunk-mavon-editor",
            test: /[\\/]node_modules[\\/]mavon-editor[\\/]/,
            priority: 25,
            chunks: "async",
          },
          xlsx: {
            name: "chunk-xlsx",
            test: /[\\/]node_modules[\\/]xlsx[\\/]/,
            priority: 25,
            chunks: "async",
          },
          echarts: {
            name: "chunk-echarts",
            test: /[\\/]node_modules[\\/](echarts|zrender|echarts-liquidfill)[\\/]/,
            priority: 25,
            chunks: "async",
          },
          libs: {
            name: "chunk-libs",
            test: /[\\/]node_modules[\\/]/,
            priority: 10,
            chunks: "initial", // only package third parties that are initially dependent
          },
          elementUI: {
            name: "chunk-elementUI", // split elementUI into a single package
            priority: 20, // the weight needs to be larger than libs and app or it will be packaged into libs or app
            test: /[\\/]node_modules[\\/]_?element-ui(.*)/, // in order to adapt to cnpm
          },
          commons: {
            name: "chunk-commons",
            test(module) {
              const resource = module && module.nameForCondition ? module.nameForCondition() : "";
              if (!resource) return false;
              const srcDir = resolve("src") + path.sep;
              const viewsDir = resolve("src/views") + path.sep;
              return resource.startsWith(srcDir) && !resource.startsWith(viewsDir);
            },
            minChunks: 3, //  minimum common number
            priority: 5,
            reuseExistingChunk: true,
          },
        },
      });
      config.optimization.runtimeChunk("single");
    });
  },
};

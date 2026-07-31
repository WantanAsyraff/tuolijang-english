import { defineConfig } from "vite";
import moment from "moment";

// @ts-expect-error: Type definitions for '@dcloudio/vite-plugin-uni' are missing or incomplete
import uni from "@dcloudio/vite-plugin-uni";
// 自动导入vue/uni-app
import AutoImport from "unplugin-auto-import/vite";
import { i18nTemplatePlugin } from "./config/i18nTemplatePlugin";

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    i18nTemplatePlugin(),
    uni(),
    AutoImport({
      include: [
        /\.[tj]sx?$/, // .ts, .tsx, .js, .jsx
        /\.vue$/,
        /\.vue\?vue/, // .vue
      ],
      imports: [
        "vue",
        "uni-app",
      ],
      dts: "typings/auto-imports.d.ts",
    }),
    // 注入打包时间到 HTML
    {
      name: "inject-build-time",
      transformIndexHtml() {
        return [
          {
            tag: "meta",
            attrs: {
              name: "build-time",
              content: moment().format("YYYY-MM-DD HH:mm:ss")
            },
            injectTo: "head"
          }
        ];
      }
    }
  ],
  // 发布时删除 console
  build: {
    minify: "terser",
    terserOptions: {
      compress: {
        drop_console: true,
      },
    },
    rollupOptions: {
      output: {
        entryFileNames: `assets/[name].[hash].js`,
        chunkFileNames: `assets/[name].[hash].js`,
        assetFileNames: `assets/[name].[hash].[ext]`,
        manualChunks(id) {
          if (!id.includes("node_modules")) return;
          if (id.includes("/moment/")) return "vendor-moment";
          if (id.includes("/crypto-js/")) return "vendor-crypto";
          if (id.includes("/@wecom/jssdk")) return "vendor-wxwork";
          if (id.includes("/vue-i18n/") || id.includes("/@intlify/")) return "vendor-i18n";
          if (id.includes("/gcoord/")) return "vendor-gcoord";
          if (id.includes("/chinese-lunar-calendar/")) return "vendor-lunar";
          if (id.includes("/weapp-qrcode/")) return "vendor-qrcode";
        }
      }
    },
  },
  server: {
    /**
     * 本地环境调试企业微信时，使用下方被注释的配置
     * 首先修改本机 host 文件，将可信域名映射到 127.0.0.1，并使用 socat 转发 80 端口到 vite 运行的 5173 端口
     * socat TCP-LISTEN:80,reuseaddr,fork TCP:localhost:5173
     * headers.host 为对应企业微信可信域名， target 为域名实际对应 IP 地址
     */

    // proxy: {
    //   "^/(api|uploads)": {
    //     target: "http://121.199.74.167",
    //     headers: {
    //       host: "dev.oa.crmeb.net"
    //     }
    //   },
    // },
    proxy: {
      "/api": {
        target: "http://dev.oa.crmeb.net",
        changeOrigin: true
      },
      "/ws": {
        target: "ws://dev.oa.crmeb.net",
        ws: true,
        changeOrigin: true
      }
    }
  }
});

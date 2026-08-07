import { defineConfig, loadEnv } from "vite";
import type { PluginOption } from "vite";
import vue from "@vitejs/plugin-vue";
import { createHtmlPlugin } from "vite-plugin-html";
import AutoImport from "unplugin-auto-import/vite";
import Components from "unplugin-vue-components/vite";
import Icons from "unplugin-icons/vite";
import IconsResolver from "unplugin-icons/resolver";
import { ElementPlusResolver } from "unplugin-vue-components/resolvers";
import UnoCSS from "unocss/vite";
import unoConfig from "./uno.config";
import zipPack from "vite-plugin-zip-pack";
import dayjs from "dayjs";
import { visualizer } from "rollup-plugin-visualizer";

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd());
  const cmdArgs = process.argv.slice(2);
  const isZip = cmdArgs.includes("--zip");
  const isAna = cmdArgs.includes("--ana");

  const base = env.VITE_ROUTE_PREFIX || "/";

  const time = new Date();
  const releaseTime = new Date(time.getTime() - (time.getTimezoneOffset() * 60000)).toISOString();

  const plugins: PluginOption[] = [
    vue(),
    createHtmlPlugin({
      minify: true,
      inject: {
        data: {
          base,
          title: "Tuoluojiang ChatAI",
          releaseTime
        }
      }
    }),
    AutoImport({
      dts: "./src/types/auto-import.d.ts",
      resolvers: [
        ElementPlusResolver(),
        IconsResolver({
          prefix: "Icon"
        })
      ],
      imports: [
        // presets
        "vue",
      ]
    }),
    Components({
      dts: "./src/types/components.d.ts",
      resolvers: [
        ElementPlusResolver(),
        IconsResolver({
          enabledCollections: ["ep"]
        })
      ],
    }),
    UnoCSS({ ...unoConfig, configFile: false }),
    Icons({
      autoInstall: true
    }),
  ];

  if (isAna) {
    plugins.push(visualizer({
      gzipSize: true,
      brotliSize: true,
      emitFile: false,
      filename: "stats.html", // 分析图生成的文件名
      open: true // 如果存在本地服务端口，将在打包后自动展示
    }));
  }

  if (isZip) {
    plugins.push(
      zipPack({
        outFileName: `ai-web-release-${dayjs().format("YYYY_MM_DD_HH_mm_ss")}.zip`
      })
    );
  }

  return {
    base,
    plugins,
    resolve: {
      alias: {
        "@": "/src"
      }
    },
    server: {
      host: "0.0.0.0",
      port: 19527,
      proxy: {
        "/api": {
          target: "https://demo.tuoluojiang.com",
          changeOrigin: true
        }
      }
    },
    build: {
      cssCodeSplit: true,
      sourcemap: false,
      rollupOptions: {
        output: {
          manualChunks: {
            vue: ["vue", "vue-router", "pinia"],
            element: ["element-plus"],
            markdown: ["unified", "highlight.js", "remark-gfm", "remark-parse"]
          }
        }
      }
    }
  };
});

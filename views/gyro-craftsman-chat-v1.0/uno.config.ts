import { defineConfig, transformerDirectives } from "unocss";
import presetWind from "@unocss/preset-wind3";

export default defineConfig({
  // ...UnoCSS options
  presets: [presetWind()],
  transformers: [transformerDirectives()],
  rules: [
    [
      "primary-color",
      {
        color: "var(--color-primary)"
      }
    ],
    [
      "single-line",
      {
        "white-space": "nowrap",
        "overflow": "hidden",
        "text-overflow": "ellipsis"
      }
    ]
  ]
});

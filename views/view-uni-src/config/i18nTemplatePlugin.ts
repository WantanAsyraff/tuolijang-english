import { parse, type RootNode, type TemplateChildNode } from "@vue/compiler-dom";

const NodeTypes = { ELEMENT: 1, TEXT: 2, ATTRIBUTE: 6 } as const;
import type { Plugin } from "vite";

const HAS_HAN = /[\u3400-\u9fff]/;
const VISIBLE_ATTRIBUTES = new Set([
  "alt",
  "title",
  "label",
  "text",
  "placeholder",
  "confirm-text",
  "cancel-text",
  "empty-text",
  "loading-text",
  "right-text",
  "default-title",
  "popup-title",
  "button-title",
  "tips",
  "unit"
]);

interface Replacement {
  start: number;
  end: number;
  value: string;
}

function translateTextNode(source: string): string {
  const leading = source.match(/^\s*/)?.[0] || "";
  const trailing = source.match(/\s*$/)?.[0] || "";
  const text = source.slice(leading.length, source.length - trailing.length);
  if (!text || !HAS_HAN.test(text)) return source;
  return `${leading}{{ $ts(${JSON.stringify(text)}) }}${trailing}`;
}

function collectReplacements(node: RootNode | TemplateChildNode, replacements: Replacement[]): void {
  if (node.type === NodeTypes.TEXT && HAS_HAN.test(node.content)) {
    replacements.push({
      start: node.loc.start.offset,
      end: node.loc.end.offset,
      value: translateTextNode(node.loc.source)
    });
  }

  if (node.type === NodeTypes.ELEMENT) {
    node.props.forEach((property) => {
      if (
        property.type === NodeTypes.ATTRIBUTE &&
        property.value &&
        VISIBLE_ATTRIBUTES.has(property.name) &&
        HAS_HAN.test(property.value.content)
      ) {
        replacements.push({
          start: property.loc.start.offset,
          end: property.loc.end.offset,
          value: `:${property.name}='$ts(${JSON.stringify(property.value.content)})'`
        });
      }
    });
  }

  if ("children" in node && Array.isArray(node.children)) {
    node.children.forEach((child) => {
      if (typeof child === "object" && child && "type" in child) {
        collectReplacements(child as TemplateChildNode, replacements);
      }
    });
  }
}

function transformTemplate(template: string): string {
  let ast: RootNode;
  try {
    ast = parse(template, { comments: true });
  } catch {
    return template;
  }

  const replacements: Replacement[] = [];
  collectReplacements(ast, replacements);
  return replacements
    .sort((a, b) => b.start - a.start)
    .reduce((result, replacement) => {
      return result.slice(0, replacement.start) + replacement.value + result.slice(replacement.end);
    }, template);
}

export function i18nTemplatePlugin(): Plugin {
  return {
    name: "tuoluojiang-i18n-template",
    enforce: "pre",
    transform(source, id) {
      if (!id.endsWith(".vue") || id.includes("/uni_modules/") || id.includes("\\uni_modules\\")) return null;
      const match = source.match(/<template(?:\s[^>]*)?>([\s\S]*?)<\/template>/i);
      if (!match || match.index === undefined) return null;

      const templateStart = match.index + match[0].indexOf(match[1]);
      const translatedTemplate = transformTemplate(match[1]);
      if (translatedTemplate === match[1]) return null;

      return {
        code: source.slice(0, templateStart) + translatedTemplate + source.slice(templateStart + match[1].length),
        map: null
      };
    }
  };
}

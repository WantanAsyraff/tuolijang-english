import type { Root, RootContent } from "mdast";
import { h, type VNodeChild } from "vue";
import hljs from "highlight.js/lib/common";
import { unified } from "unified";
import remarkParse from "remark-parse";
import remarkGfm from "remark-gfm";
import { TABLE_EXPAND_EVENT } from "@/constants/dataset-key";

// 解析 markdown 语法树
export const unifiedMd = unified().use(remarkParse).use(remarkGfm);

// 高亮代码
const getHighlightCode = (codeStr: string, lang: string) => {
  try {
    return hljs.highlight(codeStr, { language: lang, ignoreIllegals: true }).value;
  } catch (error: any) {
    console.error(error);
    return codeStr;
  }
};

// 获取表格容器
const getTableContainer = (slot: VNodeChild) => {
  return h("div", {
    class: "table-container relative",
  }, [
    h("i", {
      "class": "ai-icon ai-icon-zhankai2 expand-icon",
      "data-event": TABLE_EXPAND_EVENT,
    }),
    h("div", {
      class: "table-container-content",
    }, [
      slot
    ])
  ]);
};

type RenderOptions = {
  isTableHeader?: boolean;
};

export const renderMd = <T extends Root | RootContent>(ast: T, index?: number, _?: unknown, options?: RenderOptions): VNodeChild => {
  // 根节点，则递归渲染
  if (ast.type === "root") {
    return ast.children.map(renderMd<RootContent>);
  } else if (ast.type === "paragraph") {
    // 段落节点，渲染子节点
    return h("p", {}, ast.children.map(renderMd<RootContent>));
  } else if (ast.type === "text") {
    // 文本节点，返回文本内容
    return ast.value;
  } else if (ast.type === "heading") {
    // 标题节点，渲染子节点
    return h("h" + ast.depth, {}, ast.children.map(renderMd<RootContent>));
  } else if (ast.type === "list") {
    // 列表节点，根据是否有序列表，返回 ol 或 ul 标签
    const tag = ast.ordered ? "ol" : "ul";
    return h(tag, {}, ast.children.map(renderMd<RootContent>));
  } else if (ast.type === "listItem") {
    // 列表项节点，渲染子节点
    return h("li", {}, ast.children.map(renderMd<RootContent>));
  } else if (ast.type === "strong") {
    // 加粗节点，渲染子节点
    return h("strong", {}, ast.children.map(renderMd<RootContent>));
  } else if (ast.type === "table") {
    // 表格节点，渲染子节点
    const tableSlotContent = h("table", {}, [
      // 表头节点，渲染子节点
      h("thead", {}, ast.children.slice(0, 1).map((item) => {
        return renderMd<RootContent>(item, index, _, { isTableHeader: true });
      })),
      // 表体节点，渲染子节点
      h("tbody", {}, ast.children.slice(1).map(renderMd<RootContent>)),
    ]);
    return getTableContainer(tableSlotContent);
  } else if (ast.type === "tableRow") {
    // 表格行节点，渲染子节点
    return h("tr", {}, ast.children.map((item, index, _) => {
      return renderMd<RootContent>(item, index, _, options);
    }));
  } else if (ast.type === "tableCell") {
    // 表格单元格节点，根据是否为表头，返回 th 或 td 标签
    const tag = options?.isTableHeader ? "th" : "td";
    return h(tag, {}, ast.children.map(renderMd<RootContent>));
  } else if (ast.type === "code") {
    // 代码节点，渲染子节点
    return h("pre", {
      "class": "hljs",
      "data-language": ast.lang,
    }, [
      h("code", {
        // 高亮代码
        innerHTML: getHighlightCode(ast.value, ast.lang || "")
      })
    ]);
  } else if (ast.type === "inlineCode") {
    // 内联代码节点，返回内联代码内容
    return h("code", {}, ast.value);
  } else if (ast.type === "link") {
    // 链接节点，渲染子节点
    return h("a", {
      href: ast.url,
      target: "_blank",
    }, ast.children.map(renderMd<RootContent>));
  } else if (ast.type === "emphasis") {
    // 强调节点，渲染子节点
    return h("em", {}, ast.children.map(renderMd<RootContent>));
  } else if (ast.type === "delete") {
    // 删除节点，渲染子节点
    return h("del", {}, ast.children.map(renderMd<RootContent>));
  } else if (ast.type === "blockquote") {
    // 块引用节点，渲染子节点
    return h("blockquote", {}, ast.children.map(renderMd<RootContent>));
  } else if (ast.type === "break") {
    // 换行节点，返回换行符
    return "\n";
  }

  console.warn("未处理的节点类型：", ast.type, ast);
  // 其他节点，返回空字符串
  return "";
};

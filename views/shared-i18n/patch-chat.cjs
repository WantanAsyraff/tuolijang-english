const fs = require('fs')
const path = require('path')

const root = path.resolve(__dirname, '../gyro-craftsman-chat-v1.0')

function patch(relativePath, replacements) {
  const file = path.join(root, relativePath)
  let source = fs.readFileSync(file, 'utf8').replace(/\r\n/g, '\n')
  replacements.forEach(([from, to]) => {
    if (!source.includes(from)) throw new Error(`${relativePath}: expected anchor not found: ${from}`)
    source = source.replace(from, to)
  })
  fs.writeFileSync(file, source, 'utf8')
  console.log(`Updated ${relativePath}`)
}

patch('src/locale/en.ts', [
  ['    tips: "Tips",', '    tips: "Tips",\n    copy: "Copy",\n    rename: "Rename",\n    delete: "Delete",\n    clear: "Clear",\n    recentConversations: "Recent conversations",\n    pin: "Pin",\n    unpin: "Unpin",\n    deleteSuccess: "Deleted successfully",\n    pinSuccess: "Conversation pinned",\n    unpinSuccess: "Conversation unpinned",\n    copied: "Copied successfully",\n    siteName: "Tuoluojiang Assistant",'],
  ['    noApps: "No apps available",', '    noApps: "No apps available",\n    clearConversation: "Clear conversation",\n    conversationCleared: "Conversation cleared",\n    thinking: "Thinking",\n    errorLabel: "Error:",\n    reasoning: "Reasoning",\n    stopGenerating: "Stop generating",\n    moreData: "More data",\n    regenerate: "Regenerate",\n    conversationNotFound: "Conversation not found",\n    deleteConversationConfirm: "Delete this conversation?",'],
  ['    internalError: "Internal error",', '    internalError: "Internal error",\n    appNotFound: "App not found",']
])

patch('src/locale/zh-cn.ts', [
  ['    tips: "提示",', '    tips: "提示",\n    copy: "复制",\n    rename: "重命名",\n    delete: "删除",\n    clear: "清空",\n    recentConversations: "最近对话",\n    pin: "置顶",\n    unpin: "取消置顶",\n    deleteSuccess: "删除成功",\n    pinSuccess: "置顶成功",\n    unpinSuccess: "取消置顶成功",\n    copied: "复制成功",\n    siteName: "陀螺匠小助手",'],
  ['    noApps: "暂无应用",', '    noApps: "暂无应用",\n    clearConversation: "清空对话",\n    conversationCleared: "对话已清空",\n    thinking: "思考中",\n    errorLabel: "错误：",\n    reasoning: "思考过程",\n    stopGenerating: "停止生成",\n    moreData: "更多数据",\n    regenerate: "重新生成",\n    conversationNotFound: "对话不存在",\n    deleteConversationConfirm: "确定要删除此对话吗？",'],
  ['    internalError: "内部错误",', '    internalError: "内部错误",\n    appNotFound: "应用不存在",']
])

patch('src/locale/index.ts', [
  ['import zhCn from "./zh-cn";', 'import zhCn from "./zh-cn";\nimport { translateSystemTextValue } from "../../../shared-i18n/index.js";'],
  ['export const getLanguage = (): SupportedLocale => {\n  const stored', 'const getQueryLanguage = (): SupportedLocale | "" => {\n  if (typeof location === "undefined") return "";\n  return normalizeLanguage(new URL(location.href).searchParams.get(LANGUAGE_KEY));\n};\n\nconst getCookieLanguage = (): SupportedLocale | "" => {\n  if (typeof document === "undefined") return "";\n  const match = document.cookie.match(/(?:^|;\\s*)language=([^;]+)/);\n  return normalizeLanguage(match ? decodeURIComponent(match[1]) : "");\n};\n\nexport const getLanguage = (): SupportedLocale => {\n  const query = getQueryLanguage();\n  if (query) return query;\n\n  const stored'],
  ['  const browser = normalizeLanguage(navigator.language);', '  const cookie = getCookieLanguage();\n  if (cookie) return cookie;\n\n  const browser = normalizeLanguage(navigator.language);'],
  ['export const translate = (key: string): string => i18n.global.t(key);', 'export const translate = (key: string): string => i18n.global.t(key);\n\nexport const translateSystemText = (value: unknown, englishValue?: string): unknown =>\n  translateSystemTextValue(value, { locale: i18n.global.locale.value, englishValue });']
])

patch('src/config/index.ts', [
  ['import defaultLogo from "@/assets/images/logo.png";', 'import defaultLogo from "@/assets/images/logo.png";\nimport { translate } from "@/locale";'],
  ['export const defaultSiteName = "陀螺匠小助手";', 'export const defaultSiteName = translate("common.siteName");']
])

patch('src/utils/message.ts', [
  ['const showMessage = (message: string, type:', 'import { translateSystemText } from "@/locale";\n\nconst showMessage = (message: string, type:'],
  ['    message,', '    message: translateSystemText(message) as string,']
])

patch('src/components/chat-message-input/chat-message-toolbar.vue', [
  ['<span class="single-line max-w-70px">清空对话</span>', '<span class="single-line max-w-70px">{{ t("chat.clearConversation") }}</span>'],
  ['import { Message } from "@/utils/message";', 'import { Message } from "@/utils/message";\nimport { useI18n } from "vue-i18n";'],
  ['const route = useRoute();', 'const { t } = useI18n();\nconst route = useRoute();'],
  ['Message.info("对话已清空!");', 'Message.info(t("chat.conversationCleared"));']
])

patch('src/components/chat-messages/chat-message-ai.vue', [
  ['        思考中', '        {{ t("chat.thinking") }}'],
  ['        错误：{{ message.errorText }}', '        {{ t("chat.errorLabel") }} {{ message.errorText }}'],
  ['<div class="thinking-title">思考过程</div>', '<div class="thinking-title">{{ t("chat.reasoning") }}</div>'],
  [':data-event="STOP_GENERATE_CHAT_EVENT">停止生成</button>', ':data-event="STOP_GENERATE_CHAT_EVENT">{{ t("chat.stopGenerating") }}</button>'],
  ['        更多数据', '        {{ t("chat.moreData") }}'],
  ['import { useRootStore } from "@/pinia/stores/useRootStore";', 'import { useRootStore } from "@/pinia/stores/useRootStore";\nimport { useI18n } from "vue-i18n";'],
  ['const { message, isLast } = defineProps<Props>();', 'const { message, isLast } = defineProps<Props>();\nconst { t } = useI18n();'],
  ['    text: "重新生成",', '    text: t("chat.regenerate"),'],
  ['    text: "复制",', '    text: t("common.copy"),']
])

patch('src/components/chat-messages/chat-message-user.vue', [
  ['import type { ChatMessage, ChatMessageTool } from "@/types/chat";', 'import type { ChatMessage, ChatMessageTool } from "@/types/chat";\nimport { useI18n } from "vue-i18n";'],
  ['const props = defineProps<{', 'const { t } = useI18n();\nconst props = defineProps<{'],
  ['    text: "复制",', '    text: t("common.copy"),']
])

patch('src/components/chat-sidebar/chat-sidebar-recent-chat.vue', [
  ['        最近对话', '        {{ t("common.recentConversations") }}'],
  ['import { handleError } from "@/utils/error-handler";', 'import { handleError } from "@/utils/error-handler";\nimport { useI18n } from "vue-i18n";'],
  ['const { handleCloseSidebar } = useSidebarStore();', 'const { t } = useI18n();\nconst { handleCloseSidebar } = useSidebarStore();'],
  ['    label: "重命名",', '    label: t("common.rename"),'],
  ['    getLabel: (chat: Readonly<Chat>) => chat.isTopUp ? "取消置顶" : "置顶",', '    getLabel: (chat: Readonly<Chat>) => chat.isTopUp ? t("common.unpin") : t("common.pin"),'],
  ['    label: "删除",', '    label: t("common.delete"),'],
  ['await ElMessageBox.confirm("确定要删除此对话吗？", "提示", {\n      confirmButtonText: "确定",\n      cancelButtonText: "取消",', 'await ElMessageBox.confirm(t("chat.deleteConversationConfirm"), t("common.tips"), {\n      confirmButtonText: t("common.confirm"),\n      cancelButtonText: t("common.cancel"),'],
  ['Message.success("删除成功");', 'Message.success(t("common.deleteSuccess"));'],
  ['Message.success(isTopUp ? "取消置顶成功" : "置顶成功");', 'Message.success(isTopUp ? t("common.unpinSuccess") : t("common.pinSuccess"));']
])

for (const relativePath of ['src/pinia/stores/useChatList.ts', 'src/pinia/stores/useChatMessage.ts', 'src/pinia/stores/useChatStore.ts']) {
  patch(relativePath, [
    [relativePath.endsWith('useChatMessage.ts') ? 'import { SSEService, type ThinkingPayload } from "@/services/sse";' : relativePath.endsWith('useChatStore.ts') ? 'import { useChatMessage } from "./useChatMessage";' : 'import type { Chat } from "@/types/chat";', (relativePath.endsWith('useChatMessage.ts') ? 'import { SSEService, type ThinkingPayload } from "@/services/sse";' : relativePath.endsWith('useChatStore.ts') ? 'import { useChatMessage } from "./useChatMessage";' : 'import type { Chat } from "@/types/chat";') + '\nimport { translate } from "@/locale";'],
    ['new Error("对话不存在")', 'new Error(translate("chat.conversationNotFound"))']
  ])
}

patch('src/pinia/stores/useChatMessage.ts', [
  ['error?.message || "连接失败"', 'error?.message || translate("error.connectionFailed")']
])

patch('src/services/app.ts', [
  ['import type { App } from "@/types/app";', 'import type { App } from "@/types/app";\nimport { translate } from "@/locale";'],
  ['throw new Error("应用不存在")', 'throw new Error(translate("error.appNotFound"))']
])

patch('src/utils/helper.ts', [
  ['import { Message } from "@/utils/message";', 'import { Message } from "@/utils/message";\nimport { translate, translateSystemText } from "@/locale";'],
  ['const showCopySuccess = () => Message.success("复制成功");', 'const showCopySuccess = () => Message.success(translate("common.copied"));'],
  ['    return "凌晨";', '    return translateSystemText("凌晨") as string;'],
  ['    return "上午";', '    return translateSystemText("上午") as string;'],
  ['    return "中午";', '    return translateSystemText("中午") as string;'],
  ['    return "下午";', '    return translateSystemText("下午") as string;'],
  ['    return "晚上";', '    return translateSystemText("晚上") as string;']
])

patch('src/views/chat-main/chat-main.vue', [
  ['Message.error("请先登录");', 'Message.error(t("error.loginRequired"));'],
  ['import ChatMainContainer from "@/components/chat-main-container/chat-main-container.vue";', 'import ChatMainContainer from "@/components/chat-main-container/chat-main-container.vue";\nimport { useI18n } from "vue-i18n";'],
  ['const props = defineProps<{', 'const { t } = useI18n();\nconst props = defineProps<{']
])

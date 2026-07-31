import { GENERATED_SYSTEM_TEXT_EN } from "./generated-en-system-text.js";
import { ADMIN_SYSTEM_TEXT_EN } from "./admin-en-system-text.js";
import { ADMIN_DIALOG_SYSTEM_TEXT_EN } from "./admin-dialog-en-system-text.js";
import { ADMIN_DICTIONARY_SYSTEM_TEXT_EN } from "./admin-dictionary-en-system-text.js";
import { ADMIN_SETTINGS_EXTRA_SYSTEM_TEXT_EN } from "./admin-settings-extra-en-system-text.js";
import { REPORTED_UI_EXTRA_SYSTEM_TEXT_EN } from "./reported-ui-extra-en-system-text.js";
import { MANUAL_SYSTEM_TEXT_EN } from "./manual-en-system-text.js";
import { MOBILE_SYSTEM_TEXT_EN } from "./mobile-en-system-text.js";

export const SYSTEM_TEXT_EN = Object.freeze({
    ...GENERATED_SYSTEM_TEXT_EN,
    ...ADMIN_SYSTEM_TEXT_EN,
    ...ADMIN_DIALOG_SYSTEM_TEXT_EN,
    ...ADMIN_DICTIONARY_SYSTEM_TEXT_EN,
    ...ADMIN_SETTINGS_EXTRA_SYSTEM_TEXT_EN,
    ...REPORTED_UI_EXTRA_SYSTEM_TEXT_EN,
    ...MANUAL_SYSTEM_TEXT_EN,
    ...MOBILE_SYSTEM_TEXT_EN,
});

const HAS_HAN = /[\u3400-\u9fff]/;
const TRAILING_COLON = /[:：]$/;

export function normalizeLocale(language) {
    const locale = String(language || "").toLowerCase();
    if (["zh", "zh_cn", "zh-cn", "zh-hans"].includes(locale)) return "zh-cn";
    if (["en", "en_us", "en-us", "en-gb"].includes(locale)) return "en";
    return "";
}

function translateExact(value) {
    const raw = String(value || "").trim();
    const normalized = raw.replace(TRAILING_COLON, "").trim();
    const compact = normalized.replace(/\s+/g, "");
    return SYSTEM_TEXT_EN[normalized] || SYSTEM_TEXT_EN[compact] || SYSTEM_TEXT_EN[raw] || raw;
}

function translateFragment(value, depth = 0) {
    const raw = String(value || "").trim();
    const exact = translateExact(raw);
    if (exact !== raw || depth >= 5) return exact;
    const patterned = translatePattern(raw, depth + 1);
    return HAS_HAN.test(patterned) ? raw : patterned;
}

function translatePattern(text, depth = 0) {
    const normalized = text.replace(TRAILING_COLON, "").trim();
    const suffix = TRAILING_COLON.test(text) ? ":" : "";
    const fragment = (value) => translateFragment(value, depth);
    const patterns = [
        [/^(.+?)[-—]?线性(\d*)$/, (value, match) => `${fragment(value)} (outlined${match[2] ? ` ${match[2]}` : ""})`],
        [/^(.+?)[-—]?面性(\d*)$/, (value, match) => `${fragment(value)} (filled${match[2] ? ` ${match[2]}` : ""})`],
        [/^请输入(.+)$/, (value) => `Please enter ${fragment(value)}`],
        [/^请填写(.+)$/, (value) => `Please enter ${fragment(value)}`],
        [/^请选择(.+)$/, (value) => `Please select ${fragment(value)}`],
        [/^请搜索(.+)$/, (value) => `Please search ${fragment(value)}`],
        [/^暂无(.+?)[~～。！!]?$/, (value) => `No ${fragment(value)}`],
        [/^添加(.+)$/, (value) => `Add ${fragment(value)}`],
        [/^新增(.+)$/, (value) => `Add ${fragment(value)}`],
        [/^编辑(.+)$/, (value) => `Edit ${fragment(value)}`],
        [/^修改(.+)$/, (value) => `Edit ${fragment(value)}`],
        [/^删除(.+)$/, (value) => `Delete ${fragment(value)}`],
        [/^选择(.+)$/, (value) => `Select ${fragment(value)}`],
        [/^搜索(.+)$/, (value) => `Search ${fragment(value)}`],
        [/^查看(.+)$/, (value) => `View ${fragment(value)}`],
        [/^创建(.+)$/, (value) => `Create ${fragment(value)}`],
        [/^提交(.+)$/, (value) => `Submit ${fragment(value)}`],
        [/^导出(.+)$/, (value) => `Export ${fragment(value)}`],
        [/^复制(.+)$/, (value) => `Copy ${fragment(value)}`],
        [/^刷新(.+)$/, (value) => `Refresh ${fragment(value)}`],
        [/^重置(.+)$/, (value) => `Reset ${fragment(value)}`],
        [/^使用(.+)$/, (value) => `Use ${fragment(value)}`],
        [/^打开(.+)$/, (value) => `Open ${fragment(value)}`],
        [/^分享(.+)$/, (value) => `Share ${fragment(value)}`],
        [/^上传(.+)$/, (value) => `Upload ${fragment(value)}`],
        [/^下载(.+)$/, (value) => `Download ${fragment(value)}`],
        [/^移除(.+)$/, (value) => `Remove ${fragment(value)}`],
        [/^插入(.+)$/, (value) => `Insert ${fragment(value)}`],
        [/^恢复(.+)$/, (value) => `Restore ${fragment(value)}`],
        [/^关联(.+)$/, (value) => `Related ${fragment(value)}`],
        [/^基础(.+)$/, (value) => `Basic ${fragment(value)}`],
        [/^其他(.+)$/, (value) => `Other ${fragment(value)}`],
        [/^最后(.+)$/, (value) => `Last ${fragment(value)}`],
        [/^立即(.+)$/, (value) => `${fragment(value)} now`],
        [/^按(.+)$/, (value) => `By ${fragment(value)}`],
        [/^当前(.+)$/, (value) => `Current ${fragment(value)}`],
        [/^累计(.+)$/, (value) => `Total ${fragment(value)}`],
        [/^(.+)名称$/, (value) => `${fragment(value)} name`],
        [/^(.+)编号$/, (value) => `${fragment(value)} number`],
        [/^(.+)类型$/, (value) => `${fragment(value)} type`],
        [/^(.+)分类$/, (value) => `${fragment(value)} category`],
        [/^(.+)状态$/, (value) => `${fragment(value)} status`],
        [/^(.+)数量$/, (value) => `${fragment(value)} quantity`],
        [/^(.+)金额$/, (value) => `${fragment(value)} amount`],
        [/^(.+)价格$/, (value) => `${fragment(value)} price`],
        [/^(.+)日期$/, (value) => `${fragment(value)} date`],
        [/^(.+)时间$/, (value) => `${fragment(value)} time`],
        [/^(.+)原因$/, (value) => `${fragment(value)} reason`],
        [/^(.+)方式$/, (value) => `${fragment(value)} method`],
        [/^(.+)规则$/, (value) => `${fragment(value)} rules`],
        [/^(.+)设置$/, (value) => `${fragment(value)} settings`],
        [/^(.+)配置$/, (value) => `${fragment(value)} configuration`],
        [/^(.+)信息$/, (value) => `${fragment(value)} information`],
        [/^(.+)内容$/, (value) => `${fragment(value)} content`],
        [/^(.+)说明$/, (value) => `${fragment(value)} description`],
        [/^(.+)结果$/, (value) => `${fragment(value)} result`],
        [/^(.+)数据$/, (value) => `${fragment(value)} data`],
        [/^(.+)页面$/, (value) => `${fragment(value)} page`],
        [/^(.+)人员$/, (value) => `${fragment(value)} personnel`],
        [/^(.+)人数$/, (value) => `${fragment(value)} count`],
        [/^(.+)功能$/, (value) => `${fragment(value)} feature`],
        [/^(.+)标题$/, (value) => `${fragment(value)} title`],
        [/^(.+)摘要$/, (value) => `${fragment(value)} summary`],
        [/^(.+)封面$/, (value) => `${fragment(value)} cover`],
        [/^(.+)期限$/, (value) => `${fragment(value)} term`],
        [/^(.+)时长$/, (value) => `${fragment(value)} duration`],
        [/^(.+)行为$/, (value) => `${fragment(value)} activity`],
        [/^(.+)范围$/, (value) => `${fragment(value)} scope`],
        [/^(.+)样式$/, (value) => `${fragment(value)} style`],
        [/^(.+)显示$/, (value) => `${fragment(value)} display`],
        [/^(.+)记录$/, (value) => `${fragment(value)} records`],
        [/^(.+)列表$/, (value) => `${fragment(value)} list`],
        [/^(.+)详情$/, (value) => `${fragment(value)} details`],
        [/^(.+)明细$/, (value) => `${fragment(value)} details`],
        [/^(.+)管理$/, (value) => `${fragment(value)} management`],
        [/^(.+)统计$/, (value) => `${fragment(value)} statistics`],
        [/^(.+)搜索$/, (value) => `${fragment(value)} search`],
        [/^(.+)不能为空$/, (value) => `${fragment(value)} is required`],
        [/^(.+)成功[！!]??$/, (value) => `${fragment(value)} successful`],
        [/^(.+)失败[！!]??$/, (value) => `${fragment(value)} failed`],
        [/^\+\s*(.+)$/, (value) => `+ ${fragment(value)}`],
        [/^(.+?)\s*\*$/, (value) => `${fragment(value)} *`],
        [/^\((.+)\)$/, (value) => `(${fragment(value)})`],
        [/^共\s*(\d+)\s*条$/, (value) => `Total ${value}`],
        [/^共有\s*(\d+)\s*条$/, (value) => `Total ${value}`],
        [/^共\s*(\d+)\s*项$/, (value) => `${value} items`],
        [/^合计\((\d+)\)$/, (value) => `Total (${value})`],
        [
            /^(\d+)\s*个实体$/,
            (value) => `${value} ${value === "1" ? "entity" : "entities"}`,
        ],
        [/^操作日志\((\d+)\)$/, (value) => `Operation logs (${value})`],
        [/^(\d+)\s*条\/页$/, (value) => `${value} / page`],
        [/^(\d{1,2})月$/, (value) => ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"][Number(value) - 1]],
        [/^还有(\d+)个日程$/, (value) => `${value} more schedules`],
        [/^新添加联系人\s*[“"](.+)[”"]$/, (value) => `Added contact “${value}”`],
        [
            /^(.+)-(线性|面性)$/,
            (value, match) =>
                `${fragment(value)} (${match[2] === "线性" ? "outlined" : "filled"})`,
        ],
    ];

    for (const [pattern, format] of patterns) {
        const match = normalized.match(pattern);
        if (!match) continue;
        const translated = format(match[1], match);
        if (!HAS_HAN.test(translated)) return `${translated}${suffix}`;
    }
    return text;
}
export function translateSystemTextValue(value, options = {}) {
    if (value === null || value === undefined || typeof value !== "string")
        return value;
    const locale = normalizeLocale(options.locale);
    if (locale !== "en") return value;

    const backendEnglish = options.englishValue;
    if (
        typeof backendEnglish === "string" &&
        backendEnglish.trim() &&
        !HAS_HAN.test(backendEnglish)
    ) {
        return backendEnglish;
    }

    const raw = value;
    const trimmed = raw.trim();
    if (!trimmed || !HAS_HAN.test(trimmed)) return value;
    const hasColon = TRAILING_COLON.test(trimmed);
    const exact = translateExact(trimmed);
    if (exact !== trimmed && !HAS_HAN.test(exact)) {
        const translated = `${exact}${
            hasColon && !TRAILING_COLON.test(exact) ? ":" : ""
        }`;
        return raw.replace(trimmed, translated);
    }

    const patterned = translatePattern(trimmed);
    return patterned === trimmed ? value : raw.replace(trimmed, patterned);
}

export function containsHan(value) {
    return HAS_HAN.test(String(value || ""));
}

export function createLocalizationRuntime(systemTextEn) {
  const hasHanPattern = /[\u3400-\u9fff]/;
  const trailingColonPattern = /[:：]$/;

  function normalizeLocale(language) {
    const locale = String(language || "").toLowerCase();
    if (["zh", "zh_cn", "zh-cn", "zh-hans"].includes(locale)) return "zh-cn";
    if (["en", "en_us", "en-us", "en-gb"].includes(locale)) return "en";
    return "";
  }

  function containsHan(value) {
    return hasHanPattern.test(String(value || ""));
  }

  function translateExact(value) {
    const raw = String(value || "").trim();
    const normalized = raw.replace(trailingColonPattern, "").trim();
    const compact = normalized.replace(/\s+/g, "");
    return systemTextEn[raw] || systemTextEn[normalized] || systemTextEn[compact] || raw;
  }

  function translateParameterized(value) {
    const text = String(value || "").trim();
    let match = text.match(/^共\s*(\d+)\s*条$/);
    if (match) return `Total ${match[1]}`;
    match = text.match(/^共有\s*(\d+)\s*条$/);
    if (match) return `Total ${match[1]}`;
    match = text.match(/^共\s*(\d+)\s*项$/);
    if (match) return `${match[1]} items`;
    match = text.match(/^(\d+)\s*条\/页$/);
    if (match) return `${match[1]} / page`;
    match = text.match(/^合计\((\d+)\)$/);
    if (match) return `Total (${match[1]})`;
    match = text.match(/^操作日志\((\d+)\)$/);
    if (match) return `Operation logs (${match[1]})`;
    match = text.match(/^(\d+)\s*个实体$/);
    if (match) return `${match[1]} ${match[1] === "1" ? "entity" : "entities"}`;
    match = text.match(/^新添加联系人\s*[“"](.+)[”"]$/);
    if (match) return `Added contact “${match[1]}”`;
    match = text.match(/^(.+?)-?(线性|面性)(\d*)$/);
    if (match) {
      const base = translateExact(match[1]);
      if (!containsHan(base)) {
        return `${base} (${match[2] === "线性" ? "outlined" : "filled"}${match[3] ? ` ${match[3]}` : ""})`;
      }
    }
    match = text.match(/^(\d{1,2})月$/);
    if (match) {
      return [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
      ][Number(match[1]) - 1] || text;
    }
    return text;
  }

  function translateSystemTextValue(value, options = {}) {
    if (value === null || value === undefined || typeof value !== "string") return value;
    if (normalizeLocale(options.locale) !== "en") return value;

    const backendEnglish = options.englishValue;
    if (typeof backendEnglish === "string" && backendEnglish.trim() && !containsHan(backendEnglish)) {
      return backendEnglish;
    }

    const raw = value;
    const trimmed = raw.trim();
    if (!trimmed || !containsHan(trimmed)) return value;

    const exact = translateExact(trimmed);
    if (exact !== trimmed && !containsHan(exact)) {
      const needsColon = trailingColonPattern.test(trimmed) && !trailingColonPattern.test(exact);
      return raw.replace(trimmed, `${exact}${needsColon ? ":" : ""}`);
    }

    const parameterized = translateParameterized(trimmed);
    return parameterized === trimmed ? value : raw.replace(trimmed, parameterized);
  }

  return { normalizeLocale, translateSystemTextValue, containsHan };
}

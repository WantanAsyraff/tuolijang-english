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

    const translateLabel = (label) => {
      const translated = translateExact(label);
      return translated || label;
    };
    const rowMessages = [
      [/^第(\d+)行账目类型只能填写收入或支出$/, (m) => `In row ${m[1]}, the account type must be income or expense`],
      [/^第(\d+)行账目金额必须大于0$/, (m) => `In row ${m[1]}, the amount must be greater than 0`],
      [/^第(\d+)行账目分类不能为空$/, (m) => `In row ${m[1]}, the account category is required`],
      [/^第(\d+)行支付方式不能为空$/, (m) => `In row ${m[1]}, the payment method is required`],
      [/^第(\d+)行支付方式不存在$/, (m) => `In row ${m[1]}, the payment method does not exist`],
      [/^第(\d+)行收支时间格式不正确$/, (m) => `In row ${m[1]}, the transaction time format is invalid`],
      [/^第(\d+)行存在无效的分类$/, (m) => `In row ${m[1]}, the category is invalid`],
      [/^第(\d+)行数据(开始|结束)时间格式无法解析$/, (m) => `The ${m[2] === "开始" ? "start" : "end"} time in row ${m[1]} could not be parsed`],
    ];
    for (const [pattern, format] of rowMessages) {
      const rowMatch = text.match(pattern);
      if (rowMatch) return format(rowMatch);
    }

    // Backend notifications substitute documented placeholders before they reach
    // the client. This explicitly allowlisted system template preserves the
    // reporter value while translating the system-owned notification wording.
    let dynamicMatch = text.match(/^(.+)的(.+)已(提交|更新)，请及时查看！$/);
    if (dynamicMatch) {
      const action = dynamicMatch[3] === "提交" ? "submitted" : "updated";
      return `${dynamicMatch[1]}'s ${translateLabel(dynamicMatch[2])} has been ${action}. Please review it promptly.`;
    }

    // Activity logs are system-owned templates. Only this geographical field
    // format is allowlisted, so arbitrary business-record changes stay raw.
    dynamicMatch = text.match(/^省市区：由【(.*?)】修改为【(.*?)】$/);
    if (dynamicMatch) {
      const translateAreas = (areas) => String(areas).split(/([,，])/).map((part) => {
        if (part === ',' || part === '，') return ', ';
        return translateLabel(part);
      }).join('');
      return `Province/city/district: changed from [${translateAreas(dynamicMatch[1])}] to [${translateAreas(dynamicMatch[2])}]`;
    }
    dynamicMatch = text.match(/^员工导入结果，成功：(\d+)条,失败：(\d+)条\.$/);
    if (dynamicMatch) return `Employee import result — successful: ${dynamicMatch[1]}, failed: ${dynamicMatch[2]}.`;
    dynamicMatch = text.match(/^导入结果，成功:(\d+)条,失败:(\d+)条\.$/);
    if (dynamicMatch) return `Import result — successful: ${dynamicMatch[1]}, failed: ${dynamicMatch[2]}.`;
    dynamicMatch = text.match(/^处理中，已处理(\d+)条数据$/);
    if (dynamicMatch) return `Processing; ${dynamicMatch[1]} records processed`;
    dynamicMatch = text.match(/^已成功删除(\d+)个文件$/);
    if (dynamicMatch) return `Successfully deleted ${dynamicMatch[1]} ${dynamicMatch[1] === "1" ? "file" : "files"}`;
    dynamicMatch = text.match(/^维度总分必须为(.+)分$/);
    if (dynamicMatch) return `The dimension total score must be ${dynamicMatch[1]}`;
    dynamicMatch = text.match(/^维度中指标总分必须为(.+)分$/);
    if (dynamicMatch) return `The total indicator score within the dimension must be ${dynamicMatch[1]}`;
    dynamicMatch = text.match(/^目标字段“(.+)”输入的公式错误,错误原因:(.+)$/);
    if (dynamicMatch) return `The formula for target field “${translateLabel(dynamicMatch[1])}” is invalid. Reason: ${dynamicMatch[2]}`;
    dynamicMatch = text.match(/^请先在应用中解除关联！【(.+)】$/);
    if (dynamicMatch) return `Remove the association from the following application first: ${dynamicMatch[1]}`;
    dynamicMatch = text.match(/^请先前往开发中设置\[(.+)]实体的主字段展示$/);
    if (dynamicMatch) return `Configure the primary display field for the ${translateLabel(dynamicMatch[1])} entity under Development first`;
    dynamicMatch = text.match(/^明细字段(.+)的值不能重复$/);
    if (dynamicMatch) return `The value of detail field ${translateLabel(dynamicMatch[1])} must be unique`;
    dynamicMatch = text.match(/^字段(.+)的值不能重复$/);
    if (dynamicMatch) return `The value of field ${translateLabel(dynamicMatch[1])} must be unique`;
    dynamicMatch = text.match(/^版本信息不匹配，请更新App版本至: v(.+)版本$/);
    if (dynamicMatch) return `Version mismatch. Update the app to version v${dynamicMatch[1]}`;
    dynamicMatch = text.match(/^没有查询到(.+)信息$/);
    if (dynamicMatch) return `No information was found for ${translateLabel(dynamicMatch[1])}`;
    dynamicMatch = text.match(/^暂无权限在(.+)模块中(新增数据|更新该数据|删除该数据|分配该数据|分享该数据)！$/);
    if (dynamicMatch) {
      const action = { 新增数据: "add data", 更新该数据: "update this data", 删除该数据: "delete this data", 分配该数据: "assign this data", 分享该数据: "share this data" }[dynamicMatch[2]];
      return `You do not have permission to ${action} in the ${translateLabel(dynamicMatch[1])} module`;
    }
    dynamicMatch = text.match(/^暂无权限在(.+?)(?:表)?中(删除|修改)数据$/);
    if (dynamicMatch) return `You do not have permission to ${dynamicMatch[2] === "删除" ? "delete" : "edit"} data in ${translateLabel(dynamicMatch[1])}`;
    dynamicMatch = text.match(/^字段名已存在[：:](.+)$/);
    if (dynamicMatch) return `Field name already exists: ${dynamicMatch[1]}`;
    dynamicMatch = text.match(/^批量字段中存在重复字段名：(.+)$/);
    if (dynamicMatch) return `Duplicate field name in the batch: ${dynamicMatch[1]}`;
    dynamicMatch = text.match(/^请先解除"(.+)"实体中的一对一关联字段$/);
    if (dynamicMatch) return `Remove the one-to-one relation field from the “${translateLabel(dynamicMatch[1])}” entity first`;
    dynamicMatch = text.match(/^客户【(.+)】(存在负责人|状态异常), 不能进行取消流失操作$/);
    if (dynamicMatch) return dynamicMatch[2] === "存在负责人"
      ? `Customer ${dynamicMatch[1]} has an owner, so the lost status cannot be cancelled`
      : `Customer ${dynamicMatch[1]} has an invalid status, so the lost status cannot be cancelled`;
    dynamicMatch = text.match(/^【(.+)】(.+)(的记录已存在，请勿重复添加！|的考核记录已存在，无法重复添加！)$/);
    if (dynamicMatch) return `${dynamicMatch[1]} — ${translateLabel(dynamicMatch[2])}: this assessment record already exists and cannot be added again`;
    dynamicMatch = text.match(/^(.+)最多输入(\d+)个(数字|字)$/);
    if (dynamicMatch) return `${translateLabel(dynamicMatch[1])} must contain no more than ${dynamicMatch[2]} ${dynamicMatch[3] === "数字" ? "digits" : "characters"}`;
    dynamicMatch = text.match(/^(.+)最少输入(\d+)个(数字|字)$/);
    if (dynamicMatch) return `${translateLabel(dynamicMatch[1])} must contain at least ${dynamicMatch[2]} ${dynamicMatch[3] === "数字" ? "digits" : "characters"}`;
    dynamicMatch = text.match(/^(.+)最少输入字数(\d+)$/);
    if (dynamicMatch) return `${translateLabel(dynamicMatch[1])} must contain at least ${dynamicMatch[2]} characters`;
    dynamicMatch = text.match(/^最少输入字数(\d+)$/);
    if (dynamicMatch) return `Enter at least ${dynamicMatch[1]} characters`;
    dynamicMatch = text.match(/^(.+)最多选择数量(\d+)$/);
    if (dynamicMatch) return `Select no more than ${dynamicMatch[2]} options for ${translateLabel(dynamicMatch[1])}`;
    dynamicMatch = text.match(/^(.+)最少选择数量(\d+)$/);
    if (dynamicMatch) return `Select at least ${dynamicMatch[2]} options for ${translateLabel(dynamicMatch[1])}`;
    dynamicMatch = text.match(/^(.+)不能晚于(.+)$/);
    if (dynamicMatch) return `${translateLabel(dynamicMatch[1])} cannot be later than ${dynamicMatch[2]}`;
    dynamicMatch = text.match(/^(.+)不能早于(.+)$/);
    if (dynamicMatch) return `${translateLabel(dynamicMatch[1])} cannot be earlier than ${dynamicMatch[2]}`;
    dynamicMatch = text.match(/^请(选择|输入)(.+)$/);
    if (dynamicMatch) return `${dynamicMatch[1] === "选择" ? "Select" : "Enter"} ${translateLabel(dynamicMatch[2])}`;
    dynamicMatch = text.match(/^(.+)不能为空$/);
    if (dynamicMatch) return `${translateLabel(dynamicMatch[1])} is required`;
    dynamicMatch = text.match(/^(.+)已存在$/);
    if (dynamicMatch) return `${translateLabel(dynamicMatch[1])} already exists`;
    dynamicMatch = text.match(/^(直属|一级)分类数量到达上限$/);
    if (dynamicMatch) return `The ${dynamicMatch[1] === "直属" ? "direct" : "top-level"} category limit has been reached`;

    const prefixedErrors = [
      [/^查询失败，请重新对话:(.+)$/, "Query failed. Start a new conversation: "],
      [/^清除角标失败: (.+)$/, "Failed to clear the badge: "],
      [/^导入失败：(.+)$/, "Import failed: "],
      [/^规格错误 ：(.+)$/, "Invalid specification: "],
      [/^创建电子签约订单失败:(.+)$/, "Failed to create the electronic-signing order: "],
      [/^创建电子签约流程失败:(.+)$/, "Failed to create the electronic-signing process: "],
      [/^获取会话存档失败:(.+)$/, "Failed to retrieve the conversation archive: "],
      [/^获取企业微信应用配置失败：(.+)$/, "Failed to retrieve the WeCom application configuration: "],
      [/^获取token失败:(.+)$/, "Failed to retrieve the token: "],
      [/^平台错误：(.+)$/, "Platform error: "],
      [/^七牛云：(.+)$/, "Qiniu Cloud: "],
      [/^失败原因:(.+)$/, "Failure reason: "],
      [/^远程文件下载失败: (.+)$/, "Remote file download failed: "],
      [/^Redis 连接失败: (.+)$/, "Redis connection failed: "],
      [/^JSON 文件不存在：(.+)$/, "JSON file not found: "],
      [/^读取 JSON 文件失败：(.+)$/, "Failed to read JSON file: "],
      [/^JSON 解析失败：(.+)$/, "Failed to parse JSON: "],
      [/^缺少字段:(.+)$/, "Missing field: "],
      [/^(.+)权限有误$/, "Invalid permission: "],
    ];
    for (const [pattern, prefix] of prefixedErrors) {
      const errorMatch = text.match(pattern);
      if (errorMatch) return `${prefix}${errorMatch[1]}`;
    }
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

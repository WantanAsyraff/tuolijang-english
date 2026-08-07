import test from "node:test";
import assert from "node:assert/strict";
import fs from "node:fs";
import os from "node:os";
import path from "node:path";
import { createRequire } from "node:module";
import { fileURLToPath } from "node:url";
import {
    SYSTEM_TEXT_EN,
    normalizeLocale,
    translateSystemTextValue,
} from "./index.js";
import {
    normalizeNotificationInput,
    translateNotificationText,
} from "../gyro-craftsman-web-own-v2.4/src/lang/notification-text.js";

const here = path.dirname(fileURLToPath(import.meta.url));
const views = path.resolve(here, "..");
const require = createRequire(import.meta.url);

test("normalizes supported locale aliases and rejects unsupported locales", () => {
    assert.equal(normalizeLocale("EN_us"), "en");
    assert.equal(normalizeLocale("zh-Hans"), "zh-cn");
    assert.equal(normalizeLocale("fr"), "");
});

test("translates known system metadata and preserves unknown business content", () => {
    assert.equal(
        translateSystemTextValue("添加服务配置", { locale: "en" }),
        "Add service configuration"
    );
    assert.equal(
        translateSystemTextValue("Acme 自定义项目", { locale: "en" }),
        "Acme 自定义项目"
    );
    assert.equal(
        translateSystemTextValue("客户", {
            locale: "en",
            englishValue: "Customer",
        }),
        "Customer"
    );
});

test("translates customer metadata shown in English-mode tables and forms", () => {
    const expected = new Map([
        ["退回次数", "Return count"],
        ["退回原因", "Return reason"],
        ["记录类型", "Record type"],
        ["父级分类", "Parent category"],
        ["顶级分类", "Top-level category"],
        ["业务员排行榜", "Salesperson ranking"],
        ["支出金额（元）", "Expense amount (yuan)"],
        ["净额（元）", "Net amount (yuan)"],
        ["工资结构", "Salary structure"],
        ["考勤管理", "Attendance management"],
        ["考勤设置", "Attendance settings"],
        ["考勤组设置", "Attendance group settings"],
        ["白名单设置", "Whitelist settings"],
        ["假期类型", "Leave types"],
        ["日历配置", "Calendar settings"],
        ["职位管理", "Position management"],
    ]);
    for (const [source, translated] of expected) {
        assert.equal(
            translateSystemTextValue(source, { locale: "en" }),
            translated
        );
    }
});
test("keeps reported English labels readable and free of camelCase leakage", () => {
    const expected = new Map([
        ["\u6682\u65e0\u6570\u636e", "No data"],
        ["\u5ba2\u6237\u7ba1\u7406", "Customer management"],
        ["\u5458\u5de5\u6863\u6848", "Employee archives"],
        ["\u6dfb\u52a0\u6761\u4ef6", "Add condition"],
        ["\u5546\u673a\u7c7b\u578b", "Opportunity type"],
        ["\u6807\u51c6\u4ea7\u54c1", "Standard product"],
        ["\u5e93\u5b58\u5b9a\u4ef7", "Inventory pricing"],
        [
            "\u81ea\u52a8\u9000\u56de\u516c\u6d77",
            "Automatic return to customer pool",
        ],
        ["\u5de5\u8d44\u7ed3\u6784\u5217\u8868", "Salary structure list"],
        ["\u767d\u540d\u5355\u4eba\u5458", "Whitelisted employees"],
        ["\u6392\u73ed\u6708\u4efd", "Schedule month"],
    ]);

    for (const [source, translated] of expected) {
        assert.equal(
            translateSystemTextValue(source, { locale: "en" }),
            translated
        );
    }

    const camelCaseValues = Object.entries(SYSTEM_TEXT_EN).filter(([, value]) =>
        /^[a-z]+(?:[A-Z][A-Za-z0-9]*)+$/.test(value)
    );
    assert.deepEqual(camelCaseValues, []);
});
test("covers the second reported calendar and administration labels", () => {
    const expected = new Map([
        ["最近90天", "Last 90 days"],
        ["财务部", "Finance Department"],
        [
            "日历中的红点表示您当日未提交日报",
            "Red dots indicate days when you did not submit a daily report.",
        ],
        ["季度考核", "Quarterly assessment"],
        ["异常订单", "Abnormal order"],
        ["合计(0)", "Total (0)"],
        ["送达客户群", "Delivered customer groups"],
        ["供应商", "Provider"],
        ["支付凭证", "Payment voucher"],
        ["批量共享协作", "Batch share and collaborate"],
        ["年假（天）", "Annual leave (days)"],
        ["新增考勤组页面", "Add attendance group"],
        [
            "日历配置中黑色为上班日，红色为休息日",
            "In calendar settings, black indicates workdays and red indicates rest days.",
        ],
        ["最近打开", "Recently opened"],
        ["共有6条", "Total 6"],
        ["共0项", "0 items"],
    ]);

    for (const [source, translated] of expected) {
        assert.equal(
            translateSystemTextValue(source, { locale: "en" }),
            translated
        );
    }
});

test("uses English month names and hides lunar annotations in the annual calendar", () => {
    const source = fs.readFileSync(
        path.join(
            views,
            "gyro-craftsman-web-own-v2.4/src/views/hr/attendance/setting/calendarsetUp.vue"
        ),
        "utf8"
    );
    assert.match(source, /lunar: getLanguage\(\) !== ["']en["']/);
    assert.match(
        source,
        /Intl\.DateTimeFormat\(["']en["'], \{ month: ["']long["'] \}\)/
    );
});
test("handles parameterized counts while leaving Chinese mode unchanged", () => {
    assert.equal(
        translateSystemTextValue("共 12 条", { locale: "en" }),
        "Total 12"
    );
    assert.equal(
        translateSystemTextValue("共 12 条", { locale: "zh-cn" }),
        "共 12 条"
    );
});

test("all clients default to Chinese when no preference is available", () => {
    const sources = [
        "gyro-craftsman-web-own-v2.4/src/lang/index.js",
        "gyro-craftsman-chat-v1.0/src/locale/index.ts",
        "view-uni-src/locale/index.ts",
    ].map((file) => fs.readFileSync(path.join(views, file), "utf8"));
    sources.forEach((source) => assert.match(source, /return ["']zh-cn["']/));
    assert.doesNotMatch(sources[0], /navigator\.language/);
    assert.doesNotMatch(sources[1], /navigator\.language/);
    assert.doesNotMatch(sources[2], /systemInfo\.language/);
});

test("embedded chat query locale takes precedence over stored preference", () => {
    const source = fs.readFileSync(
        path.join(views, "gyro-craftsman-chat-v1.0/src/locale/index.ts"),
        "utf8"
    );
    assert.ok(
        source.indexOf("const query = getQueryLanguage()") <
            source.indexOf("localStorage.getItem")
    );
});

test("mobile language changes refresh native navigation and tab labels", () => {
    const locale = fs.readFileSync(
        path.join(views, "view-uni-src/locale/index.ts"),
        "utf8"
    );
    const navigation = fs.readFileSync(
        path.join(views, "view-uni-src/locale/navigation.ts"),
        "utf8"
    );
    assert.match(locale, /uni\.\$emit\("language:changed"/);
    assert.match(navigation, /uni\.\$on\("language:changed"/);
    assert.match(navigation, /setNavigationBarTitle/);
    assert.match(navigation, /setTabBarItem/);
});

test("translates the complete administration navigation and representative nested UI", () => {
    const expected = new Map([
        ["应用开发", "Application development"],
        ["应用搭建", "Application builder"],
        ["应用管理", "Application management"],
        ["数据字典", "Data dictionary"],
        ["流程信息", "Workflow information"],
        ["流程管理", "Workflow management"],
        ["触发器管理", "Trigger management"],
        ["触发列表", "Trigger list"],
        ["触发日志", "Trigger logs"],
        ["图表列表", "Chart list"],
        ["外部接口", "External integrations"],
        ["对外接口", "External APIs"],
        ["授权查询", "Authorization lookup"],
        ["接口文档", "API documentation"],
        ["用户权限", "User permissions"],
        ["高级授权", "Advanced authorization"],
        ["操作日志", "Operation logs"],
        ["协议设置", "Agreement settings"],
        ["快捷入口", "Quick access"],
        ["基础设置", "Basic settings"],
        ["防火墙", "Firewall"],
        ["企微设置", "WeCom settings"],
        ["消息中心", "Message center"],
        ["消息设置", "Message settings"],
        ["推送记录", "Push records"],
        ["新建应用", "New application"],
        ["字典列表", "Dictionary list"],
        ["关联实体", "Linked entity"],
        ["层级", "Level"],
        ["账目收支类型", "Income/expense type"],
        ["财务分类", "Financial category"],
        ["派车状态", "Dispatch status"],
        ["派车记录", "Dispatch records"],
        ["驾照类型", "Driver's license type"],
        ["驾驶员信息", "Driver information"],
        ["车辆类型", "Vehicle type"],
        ["车辆基础信息", "Basic vehicle information"],
        ["2个实体", "2 entities"],
        ["操作日志(7)", "Operation logs (7)"],
        ["菜单名称", "Menu name"],
        ["新建授权密钥", "New authorization key"],
        ["企业微信基础配置", "WeCom basic settings"],
        ["消息内容", "Message content"],
        ["你确定要删除当前应用吗", "Delete this application?"],
    ]);

    for (const [source, translated] of expected) {
        assert.equal(
            translateSystemTextValue(source, { locale: "en" }),
            translated
        );
    }
});

test("covers the latest reported customer, finance, workbench, and attendance text", () => {
    const expected = new Map([
        ["高效团队铸就一流企业！！！", "A high-performing team builds a first-class enterprise!"],
        ["显示字段设置", "Column display settings"],
        ["赢单", "Won"],
        ["选择产品弹窗", "Select products"],
        ["修改财务流水类别", "Edit financial transaction category"],
        ["企业公户", "Corporate bank account"],
        ["工资条结构List", "Salary structure list"],
        ["年假（天）", "Annual leave (days)"],
        ["选择考勤方式", "Select attendance method"],
        ["可申请过去", "Corrections can be requested for the past"],
        ["查看班次", "View shift"],
        ["请选择考勤组(多选)", "Please select attendance groups (multiple)"],
    ]);

    for (const [source, translated] of expected) {
        assert.equal(translateSystemTextValue(source, { locale: "en" }), translated);
    }

    const workbench = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/views/user/workbench/index.vue"),
        "utf8"
    );
    assert.match(workbench, /\$ts\(realName\)/);
    assert.match(workbench, /\$ts\(enterprise\.culture \|\| '--'\)/);

    const shift = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/views/hr/attendance/setting/components/addShift.vue"),
        "utf8"
    );
    assert.match(shift, /formatRuleText\(item, form1?\)/);
    assert.match(shift, /formatOvertimeText\(\)/);
});

test("covers the reported notification, quick-entry, daily-report, and assessment-template labels", () => {
    const expected = new Map([
        ["\u5168\u90e8\u7c7b\u578b", "All types"],
        ["\u8ba2\u9605\u4fe1\u606f", "Subscriptions"],
        ["\u5168\u90e8\u5df2\u8bfb", "Mark all as read"],
        ["\u6807\u8bb0\u4e3a\u5df2\u8bfb", "Mark as read"],
        ["\u5df2\u6dfb\u52a0", "Added"],
        ["\u4e2a\u4eba\u529e\u516c", "Personal office"],
        ["\u529e\u516c\u5de5\u5177", "Office tools"],
        ["\u516c\u53f8\u4ecb\u7ecd", "Company profile"],
        ["\u9879\u76ee\u7ba1\u7406", "Project management"],
        ["\u6211\u7684\u4efb\u52a1", "My tasks"],
        ["\u4e0b\u4e2a\u5de5\u4f5c\u65e5\u8ba1\u5212", "Next workday plan"],
        ["\u6211\u7684\u6a21\u677f", "My templates"],
        ["\u9ad8\u5c42", "Executive management"],
        ["\u8425\u9500\u90e8", "Marketing Department"],
        ["\u4eba\u529b\u8d44\u6e90", "Human Resources"],
        ["\u751f\u4ea7\u90e8", "Production Department"],
        ["\u884c\u653f\u90e8", "Administration Department"],
        ["\u5e02\u573a\u91c7\u8d2d\u90e8", "Marketing and Procurement Department"],
        ["\u8d28\u68c0\u90e8", "Quality Control Department"],
        ["\u4ed3\u50a8\u7269\u6d41\u90e8", "Warehousing and Logistics Department"],
        ["\u540e\u7aef\u7ee9\u6548\u6708\u5ea6\u6a21\u677f", "Backend Monthly Performance Template"],
        ["\u524d\u7aef\u7ee9\u6548\u6708\u5ea6\u6a21\u677f", "Frontend Monthly Performance Template"],
        ["\u9879\u76ee\u8d1f\u8d23\u4eba\u7ee9\u6548\u6708\u5ea6\u6a21\u677f", "Project Manager Monthly Performance Template"],
        ["\u6280\u672f\u603b\u76d1\uff08\u7814\u53d1\uff09\u7ee9\u6548\u8003\u6838\u8868", "Senior Technical Director (R&D) Assessment Form"],
        ["\u7f51\u7edc\u7ba1\u7406\u5458\u8003\u6838\u8bc4\u5206\u8868\uff08\u6708\u5e95\uff09", "Network Administrator Month-End Assessment Form"],
        ["\u6280\u672f\u90e8\u7ecf\u7406\u8003\u6838\u8bc4\u5206\u8868\uff08\u6708\u5ea6\uff09", "Technical Department Manager Monthly Assessment Form"],
        ["\u7814\u53d1\u7ecf\u7406\u8003\u6838\u8bc4\u5206\u8868\uff08\u6708\u5ea6\uff09", "R&D Manager Monthly Assessment Form"],
        ["\u6280\u672f\u603b\u76d1\u8003\u6838\u8bc4\u5206\u8868\uff08\u6708\u5ea6\uff09", "Technical Director Monthly Assessment Form"],
        ["\u9879\u76ee\u5f00\u53d1", "Project development"],
        ["\u9879\u76ee\u5f00\u53d1\u8fdb\u5ea6", "Project development progress"],
        ["bug\u4fee\u590d", "Bug fixes"],
        ["\u5f00\u53d1\u8d28\u91cf", "Development quality"],
        ["\u4ea7\u54c1\u8d28\u91cf", "Product quality"],
        ["\u529f\u80fd\u8fd8\u539f\u5ea6", "Feature fidelity"],
        ["\u4ee3\u7801\u89c4\u8303", "Coding standards"],
        ["\u9879\u76ee\u7ef4\u62a4", "Project maintenance"],
        ["\u552e\u540e\u7ef4\u62a4", "After-sales maintenance"],
        ["GITEE\u7f3a\u9677", "Gitee issues"],
        ["\u989d\u5916\u5206\u503c", "Bonus points"],
        ["\u52a0\u5206\u9879", "Bonus items"],
        ["\u4f18\u5316\u521b\u65b0", "Optimization and innovation"],
    ]);

    for (const [source, translated] of expected) {
        assert.equal(translateSystemTextValue(source, { locale: "en" }), translated);
    }
});


test("translates websocket notifications and normalizes every supported toast input shape", () => {
    const translate = (value) => translateNotificationText(value, "en");

    assert.equal(translate("日报查看提醒"), "Daily report reminder");
    assert.equal(
        translate("Wan的日报已提交，请及时查看！"),
        "Wan's daily report has been submitted. Please review it promptly."
    );
    assert.equal(
        translate("James的周报已修改，请及时查看！"),
        "James' weekly report has been updated. Please review it promptly."
    );
    assert.equal(translate("立即查看"), "View now");
    assert.equal(
        translate("<strong>日报查看提醒</strong><span>Wan的日报已提交，请及时查看！</span>"),
        "<strong>Daily report reminder</strong><span>Wan's daily report has been submitted. Please review it promptly.</span>"
    );
    assert.equal(translateNotificationText("日报查看提醒", "zh-cn"), "日报查看提醒");
    assert.equal(translate("Acme 自定义项目"), "Acme 自定义项目");

    const input = {
        title: "日报查看提醒",
        message: "Wan的日报已提交，请及时查看！",
        description: "请及时查看",
        buttons: [{ title: "立即查看" }, "已删除"],
        duration: 0,
    };
    const normalized = normalizeNotificationInput(input, translate);
    assert.deepEqual(normalized, {
        title: "Daily report reminder",
        message: "Wan's daily report has been submitted. Please review it promptly.",
        description: "Please review it promptly",
        buttons: [{ title: "View now" }, "Deleted"],
        duration: 0,
    });
    assert.equal(input.title, "日报查看提醒");

    assert.equal(normalizeNotificationInput(new Error("请求失败"), translate), "Request failed");
    assert.deepEqual(
        normalizeNotificationInput({ msg: "请求失败", duration: 0 }, translate),
        { msg: "请求失败", message: "Request failed", duration: 0 }
    );
    assert.equal(normalizeNotificationInput(null, translate), null);
    assert.equal(normalizeNotificationInput(7, translate), 7);
    const arrayInput = ["请求失败"];
    assert.equal(normalizeNotificationInput(arrayInput, translate), arrayInput);

    const vnodeMessage = { componentOptions: {} };
    const vnodeInput = normalizeNotificationInput({ message: vnodeMessage }, translate);
    assert.equal(vnodeInput.message, vnodeMessage);
});

test("covers every backend notification title and every frontend notification entry point", () => {
    const noticeEnum = fs.readFileSync(path.join(views, "../app/Constants/NoticeEnum.php"), "utf8");
    const titles = [...noticeEnum.matchAll(/(?:\/\/|\*)\s*([^\r\n.]*提醒)/g)]
        .map((match) => match[1].replace(/^\*\s*/, "").trim())
        .filter((value, index, values) => values.indexOf(value) === index);
    assert.ok(titles.length >= 40);
    for (const title of titles) {
        assert.notEqual(translateNotificationText(title, "en"), title, title);
    }

    const plugins = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/bootstrap/plugins.js"),
        "utf8"
    );
    assert.match(plugins, /Vue\.prototype\.\$notify = wrapElementService/);
    const socketNotice = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/libs/notice.js"),
        "utf8"
    );
    assert.match(socketNotice, /normalizeSocketNotification/);
    assert.match(socketNotice, /escapeHtml/);
    const workbench = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/views/user/workbench/index.vue"),
        "utf8"
    );
    assert.match(workbench, /translateMessage\('\\u6388\\u6743\\u63d0\\u9192'\)/);
    const noticeHandle = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/libs/noticeHandle.js"),
        "utf8"
    );
    assert.doesNotMatch(noticeHandle, /message:\s*(?:res|error)\.message/);
    const rightDrawer = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/components/setting/rightDrawer.vue"),
        "utf8"
    );
    assert.match(rightDrawer, /Message\.error\(translateMessage\(res\.msg\)\)/);
});
test("translates every tooltip on the AI application settings page", () => {
    const expected = new Map([
        ["AI\u5e94\u7528\u5c55\u793a\u540d\u79f0", "The display name of the AI application."],
        ["\u8bf7\u8f93\u5165\u4e00\u6bb5\u7b80\u660e\u627c\u8981\u53c8\u5438\u775b\u7684\u4ecb\u7ecd\uff0c\u53ef\u4ee5\u5feb\u901f\u5438\u5f15\u7528\u6237\u4f7f\u7528\u4f60\u7684\u667a\u80fd\u4f53", "Enter a concise, engaging introduction that quickly encourages users to try your AI assistant."],
        ["\u901a\u8fc7\u63d0\u793a\u8bcd\uff0c\u4f60\u80fd\u7cbe\u786e\u8bbe\u5b9a\u5e94\u7528\u7684\u4f5c\u7528\u8303\u56f4\u3002\u5305\u62ec\u6307\u5b9a\u5e94\u7528\u5c06\u626e\u6f14\u7684\u89d2\u8272\uff0c\u80fd\u591f\u4f7f\u7528\u7684\u7ec4\u4ef6\u4ee5\u53ca\u8f93\u51fa\u7ed3\u679c\u7684\u683c\u5f0f\u4e0e\u98ce\u683c\uff1b\u6b64\u5916\u4f60\u8fd8\u53ef\u4ee5\u89c4\u5b9a\u5e94\u7528\u4e0d\u5f97\u6267\u884c\u54ea\u4e9b\u64cd\u4f5c\u7b49", "Use prompts to precisely define the application's scope, including its role, available components, output format and style, and any actions it must not perform."],
        ["\u62e5\u6709\u7f16\u8f91\u6743\u9650\u7684\u6210\u5458\uff0c\u53ef\u4ee5\u8fdb\u884cAI\u5e94\u7528\u7f16\u6392", "Members with edit permission can configure the AI application."],
        ["\u8bf7\u9009\u62e9\u5141\u8bb8\u4f7f\u7528\u8be5\u5e94\u7528\u7684\u4eba\u5458", "Select the people who are allowed to use this application."],
        ["\u7528\u4e8e\u8bbe\u7f6e\u5141\u8bb8\u7528\u6237\u4f7f\u7528\u9891\u6b21\uff0c\u5230\u8fbe\u9650\u5236\u4ee5\u540e\u4e0d\u5141\u8bb8\u7ee7\u7eed\u8bbf\u95ee\uff0c0\u8868\u793a\u4e0d\u9650\u5236", "Set how often each user can use the application. Once the limit is reached, further access is blocked. Enter 0 for no limit."],
        ["\u5e94\u7528\u987a\u5e8f\u8c03\u6574\uff0c\u6570\u5b57\u8d8a\u5927\u8d8a\u9760\u524d", "Adjust the application order. Higher numbers appear first."],
        ["\u9009\u62e9\u6a21\u578b\u8bbe\u7f6e\u4e2d\u914d\u7f6e\u597d\u7684AI\u6a21\u578b", "Select an AI model configured under Model settings."],
        ["\u5c06\u5728\u7528\u6237\u5f00\u542f\u5bf9\u8bdd\u65f6\u5c55\u793a\uff0c\u5f15\u5bfc\u7528\u6237\u5feb\u901f\u4e86\u89e3\u529f\u80fd\u5e76\u5f00\u542f\u5bf9\u8bdd", "Shown when users start a conversation to introduce the features and help them begin."],
        ["\u81f3\u5c11\u51993\u4e2a\uff0c\u8d85\u51fa\u540e\u968f\u673a\u53d63\u4e2a\u5c55\u793a", "Add at least three questions. If more are added, three will be shown at random."],
        ["\u6269\u5c55\u667a\u80fd\u4f53\u7684\u6570\u636e\u5e93\u77e5\u8bc6\u50a8\u5907\uff0c\u4e3a\u7528\u6237\u63d0\u4f9b\u66f4\u9488\u5bf9\u6027\u7684\u7b54\u6848\uff0c\u667a\u80fd\u4f53\u516c\u5f00\u53d1\u5e03\u540e\u53ef\u751f\u6210\u4f18\u8d28\u95ee\u7b54\uff0c\u7528\u4e8e\u8c03\u4f18", "Expand the assistant's database knowledge to provide more targeted answers. After the assistant is published, high-quality Q&A can be generated for optimization."],
        ["\u60a8\u53ef\u4ee5\u5bf9\u6570\u636e\u5e93\u8868\u67e5\u8be2\u4e4b\u540e\uff0c\u8fd4\u56de\u5185\u5bb9\u7684\u6574\u7406\u683c\u5f0f\u8fdb\u884c\u63cf\u8ff0", "Describe how the content returned from a database query should be organized."],
        ["\u7528\u6237\u63d0\u95ee\u89e6\u53d1\u5173\u952e\u5b57\uff0c\u624d\u4f1a\u6839\u636e\u6570\u636e\u5e93\u8fdb\u884c\u67e5\u8be2", "The database is queried only when a user's question triggers a keyword."],
        ["\u672c\u6b21\u56de\u7b54\u5185\u5bb9\u6839\u636e\u6700\u8fd1\u8f6e\u6b21\u7684\u53c2\u8003\u5bf9\u8bdd", "This response uses the most recent conversation turns as context."],
    ]);

    for (const [source, translated] of expected) {
        assert.equal(translateSystemTextValue(source, { locale: "en" }), translated);
    }

    const popover = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/views/chat/components/popover.vue"),
        "utf8"
    );
    assert.match(popover, /\{\{\s*formattedTips\s*\}\}/);
    assert.match(popover, /this\.\$ts\(this\.tips\)/);
});

test("covers the reported customer, quick-reply, employee, and shift screens", () => {
    const expected = new Map([
        ["企微添加", "Added via WeCom"],
        ["未成交", "Not converted"],
        ["北京市", "Beijing"],
        ["内蒙古自治区", "Inner Mongolia Autonomous Region"],
        ["设置部门目标", "Set department targets"],
        ["目标平均分配到每月", "Distribute target evenly by month"],
        ["同步标签", "Sync labels"],
        ["明星客户", "Star customers"],
        ["复选按钮组", "Checkbox group"],
        ["日期时间控件", "Date and time picker"],
        ["图片选择控件", "Image picker"],
        ["用于设置通知哪些员工进行此消息群发，员工收到群发任务后，可在企业微信上进行群发消息操作", "Select the employees who should receive this mass-send task. They can send the message from WeCom after receiving it."],
        ["内容分组", "Content group"],
        ["回复类型", "Reply type"],
        ["小程序", "Mini Program"],
        ["数字越大越靠前", "Higher numbers appear first"],
        ["导入快捷回复", "Import quick replies"],
        ["负责部门", "Responsible departments"],
        ["试用到期", "Probation end date"],
        ["身份证正面", "Front of ID card"],
        ["毕业院校", "School or university"],
        ["新建班次", "New shift"],
        ["合计", "Total"],
    ]);

    for (const [source, translated] of expected) {
        assert.equal(translateSystemTextValue(source, { locale: "en" }), translated);
    }

    const product = fs.readFileSync(path.join(views, "gyro-craftsman-web-own-v2.4/src/views/customer/components/productList.vue"), "utf8");
    assert.ok(product.includes("this.$ts('合计')"));
    const customerForm = fs.readFileSync(path.join(views, "gyro-craftsman-web-own-v2.4/src/components/customer/oaForm.vue"), "utf8");
    assert.ok(customerForm.includes("localizedOptions(val.options, val.key)"));
    const employeeForm = fs.readFileSync(path.join(views, "gyro-craftsman-web-own-v2.4/src/views/hr/archives/components/formItemDataList.vue"), "utf8");
    assert.ok(employeeForm.includes('label-width="150px"'));
    const shift = fs.readFileSync(path.join(views, "gyro-craftsman-web-own-v2.4/src/views/hr/attendance/setting/components/addShift.vue"), "utf8");
    assert.ok(shift.includes('size="1000px"'));
    assert.ok(shift.includes('class="rule-copy rule-suffix"'));
});


test("covers the latest goal, mass-send, employee archive, and job-level screens", () => {
    const expected = new Map([
        ["请输入有效的年度目标（正数）", "Please enter a valid annual target (positive number)."],
        ["群主调整发送范围", "Owner-adjusted sending scope"],
        ["成员调整发送范围：", "Member-adjusted sending scope:"],
        ["银行卡号：", "Bank card number:"],
        ["请输入银行卡", "Enter bank card number"],
        ["社保账号：", "Social security account:"],
        ["公积金号：", "Housing provident fund account:"],
        ["请输入紧急联系人姓名", "Enter emergency contact name"],
        ["职级体系图", "Job Level System Chart"],
        ["薪资范围", "Salary range"],
        ["管理岗", "Management"],
        ["技术岗", "Technical"],
        ["高级Java工程师", "Senior Java Engineer"],
        ["销售员", "Sales Representative"],
    ]);

    for (const [source, translated] of expected) {
        assert.equal(translateSystemTextValue(source, { locale: "en" }), translated);
    }

    const massSend = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/views/customer/weChatMass/addGroupPosting.vue"),
        "utf8"
    );
    assert.match(massSend, /locale === 'en' \? '230px' : '132px'/);
    assert.match(massSend, /formData\.types == 1 \? \$t\(/);

    const jobLevels = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/views/hr/enterprise/job/positionSystemChart.vue"),
        "utf8"
    );
    assert.match(jobLevels, /\$ts\(info\.name\)/);
    assert.match(jobLevels, /\$ts\(col\.name\)/);
    assert.match(jobLevels, /\$ts\(item\.info\.rank\.alias\)/);
});


test("covers the profile menu, memo, app QR, quick-entry, and workbench notices", () => {
    const expected = new Map([
        ["个人简历", "Personal résumé"],
        ["论坛中心", "Forum Center"],
        ["最近使用", "Recent"],
        ["我的文件夹", "My folders"],
        ["创建于", "Created at"],
        ["保存成功", "Saved successfully"],
        ["修改成功", "Updated successfully"],
        ["扫描配置通用APP", "Scan to configure the mobile app"],
        ["陀螺匠会更努力了解你的需求", "Tuoluojiang will work harder to understand your needs."],
        ["本月", "This month"],
    ]);

    for (const [source, translated] of expected) {
        assert.equal(translateSystemTextValue(source, { locale: "en" }), translated);
    }

    const workbench = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/views/user/workbench/index.vue"),
        "utf8"
    );
    assert.match(workbench, /localizedNoticeText\(item\.title\)/);
    assert.match(workbench, /localizedNoticeText\(item\.message\)/);
    assert.match(workbench, /return translateMessage\(value\)/);

    const quickManage = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/views/user/workbench/components/quickManage.vue"),
        "utf8"
    );
    assert.match(quickManage, /\$t\("ui\.userWorkbenchQuickManageTuoluojiangWillWorkHarderToUnderstandYourNeeds"\)/);

    const memo = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/views/user/memorandum/index.vue"),
        "utf8"
    );
    assert.match(memo, /\$ts\(j\.month\)/);
});
test("covers notification details and every reported workflow designer screen", () => {
    const expected = new Map([
        ["日常汇报", "Daily reports"],
        ["用车申请", "Vehicle request"],
        ["流程名称：", "Workflow name:"],
        ["审批说明：", "Approval description:"],
        ["适用于所有员工的用车申请。", "Applies to vehicle requests from all employees."],
        ["指定上级审批: 直属上级", "Designated manager approval: Direct manager"],
        ["抄送人", "CC recipients"],
        ["申请人自选", "Selected by applicant"],
        ["流程结束", "End of process"],
        ["撤销审批：", "Approval revocation:"],
        ["加签权限：", "Additional approver permission:"],
        ["允许在审批单中增加临时审批人", "Allow temporary approvers to be added to the approval form"],
    ]);

    for (const [source, translated] of expected) {
        assert.equal(translateSystemTextValue(source, { locale: "en" }), translated);
    }

    const messageDetails = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/views/user/news/components/messageDetails.vue"),
        "utf8"
    );
    assert.match(messageDetails, /localizedText\(messageData\.data\.cate_name\)/);
    assert.match(messageDetails, /localizedText\(messageData\.data\.message\)/);
    assert.match(messageDetails, /return translateMessage\(value\)/);

    const workflow = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/views/develop/crud/process.vue"),
        "utf8"
    );
    assert.match(workflow, /:value="\$ts\(baseConfig\.name\)"/);
    assert.match(workflow, /:value="\$ts\(baseConfig\.info\)"/);
    assert.match(workflow, /this\.options = this\.localizedOptions\(data\.data\)/);

    const nodeWrap = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/components/workFlow/nodeWrap.vue"),
        "utf8"
    );
    assert.match(nodeWrap, /\$ts\(\$func\.setApproverStr\(nodeConfig\)\)/);
    assert.match(nodeWrap, /\$ts\(\$func\.copyerStr\(nodeConfig\)\)/);
    assert.match(nodeWrap, /\$ts\(nodeConfig\.nodeName\)/);
});
test("covers the assessment library and the remaining reported English screens", () => {
    const expected = new Map([
        ["类型名称", "Type name"],
        ["餐饮业", "Food & Beverage"],
        ["关键业绩绩效", "Key performance results"],
        ["人工成本率（人力资源投资回报率）", "Labor cost ratio (HR return on investment)"],
        ["员工个人职业发展达成率/员工能力提升", "Individual development completion / employee capability improvement"],
        ["输入评分标准", "Enter scoring criteria"],
        ["新签商机", "New opportunity"],
        ["软件产品", "Software products"],
        ["必填", "Required"],
        ["定时中", "Scheduled"],
        ["LLM(大语言模型)", "LLM (Large Language Model)"],
        ["若不选择上级菜单，则不生成菜单", "No menu will be generated unless a parent menu is selected"],
        ["新增字典", "Add dictionary"],
        ["新添加联系人 “A”", "Added contact “A”"],
        ["商品-线性", "Product (outlined)"],
    ]);

    for (const [source, translated] of expected) {
        assert.equal(translateSystemTextValue(source, { locale: "en" }), translated);
    }

    const templateDialog = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/components/form-common/dialog-template.vue"),
        "utf8"
    );
    assert.match(templateDialog, /:width="DIALOG_SIZE\.XL"/);
    assert.match(templateDialog, /localizeTemplateDetails\(res\.data\.info\)/);
    assert.match(templateDialog, /grid-template-columns: 190px minmax\(0, 1fr\) 300px/);

    const modalForm = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/libs/modal-form.js"),
        "utf8"
    );
    assert.match(modalForm, /this\.\$i18n\.locale === 'en' \? '190px' : '90px'/);

    const reminder = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/views/customer/list/components/remindDialog.vue"),
        "utf8"
    );
    assert.match(reminder, /formLabelWidth\(\)/);
    assert.match(reminder, /:width="DIALOG_SIZE\.SM"/);

    const customForm = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/views/customer/setup/customForm/index.vue"),
        "utf8"
    );
    assert.match(customForm, /:active-text="\$t\('ui\.developForeignDocumentRequired'\)"/);
    assert.match(customForm, /prop="required" min-width="145"/);

    const iconPicker = fs.readFileSync(
        path.join(views, "gyro-craftsman-web-own-v2.4/src/components/form-common/select-icon.vue"),
        "utf8"
    );
    assert.match(iconPicker, /localizedIconName\(item\.name\)/);
});
test("covers whitespace variants, mobile prompts, and remaining composed labels", () => {
    const expected = new Map([
        ["备 注：", "Remarks:"],
        ["前一年", "Previous year"],
        ["后一年", "Next year"],
        ["请输入评分等级", "Please enter the rating level"],
        ["暂无日程安排", "No schedules"],
        ["确定领取该线索吗？", "Claim this lead?"],
    ]);
    for (const [source, translated] of expected) {
        assert.equal(translateSystemTextValue(source, { locale: "en" }), translated);
    }
    assert.equal(
        translateSystemTextValue("张三的自定义备注", { locale: "en" }),
        "张三的自定义备注"
    );
});

test("keeps compile-time template guards and canvas chart localization wired in every client", () => {
    const webConfig = fs.readFileSync(path.join(views, "gyro-craftsman-web-own-v2.4/vue.config.js"), "utf8");
    assert.match(webConfig, /templateI18nModule/);
    assert.match(webConfig, /compilerOptions\.modules/);

    const webCharts = [
        "gyro-craftsman-web-own-v2.4/src/components/common/echarts.vue",
        "gyro-craftsman-web-own-v2.4/src/components/scEcharts/index.vue",
        "gyro-craftsman-web-own-v2.4/src/views/user/workStatistics/components/assessStatistics.vue",
        "gyro-craftsman-web-own-v2.4/src/views/hr/assess/staff/mentStatistics.vue",
    ];
    webCharts.forEach((file) => {
        assert.match(fs.readFileSync(path.join(views, file), "utf8"), /localizeChartOption/);
    });

    const mobileRoot = path.join(views, "view-uni-src");
    const mobileCharts = [];
    const walk = (directory) => {
        for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
            if (["node_modules", "unpackage", "uni_modules"].includes(entry.name)) continue;
            const full = path.join(directory, entry.name);
            if (entry.isDirectory()) walk(full);
            else if (entry.name.endsWith(".vue") && fs.readFileSync(full, "utf8").includes("<qiun-data-charts")) mobileCharts.push(full);
        }
    };
    walk(mobileRoot);
    assert.ok(mobileCharts.length > 0);
    mobileCharts.forEach((file) => assert.match(fs.readFileSync(file, "utf8"), /\$localize\(/));

    const mobileInstall = fs.readFileSync(path.join(mobileRoot, "locale/install.ts"), "utf8");
    assert.match(mobileInstall, /globalProperties\.\$localize = localizeSystemObject/);
    const mobileSources = [
        "components/moduleForm/index.vue",
        "pages/customer/lead/detail.vue",
    ].map((file) => fs.readFileSync(path.join(mobileRoot, file), "utf8")).join("\n");
    assert.doesNotMatch(mobileSources, /setting\.group\.selectLabel|hr\.confirmclaim/);

    const chatTable = fs.readFileSync(path.join(views, "gyro-craftsman-chat-v1.0/src/components/chat-messages/chat-message-table.vue"), "utf8");
    assert.match(chatTable, /translateSystemText\(fieldName\)/);
    const chatTools = fs.readFileSync(path.join(views, "gyro-craftsman-chat-v1.0/src/components/chat-messages/chat-message-ai.vue"), "utf8");
    assert.match(chatTools, /computed<Tool\[\]>\(\(\) =>/);
});
test("localizes mobile system-owned dynamic controls without translating record content", () => {
    const mobileRoot = path.join(views, "view-uni-src");
    const expectedBindings = new Map([
        ["components/BottomActionSheet/index.vue", /\$ts\(item\.label\)/],
        ["components/DropDown/index.vue", /\$ts\(item\.name\)/],
        ["components/moduleForm/index.vue", /\$localize\(val\.data_dict\)/],
        ["components/examineForm/index.vue", /\$localize\(item\.props\.options\)/],
        ["pages/customer/components/common-form.vue", /\$localize\(\(item as FormItemWithOptions\)\.options\)/],
        ["pages/module/components/item.vue", /\$ts\(item\[val\.field_name_en\]\.name\)/],
    ]);
    for (const [file, pattern] of expectedBindings) {
        assert.match(fs.readFileSync(path.join(mobileRoot, file), "utf8"), pattern);
    }

    assert.equal(translateSystemTextValue("待跟进", { locale: "en" }), "To follow up");
    assert.equal(translateSystemTextValue("Alice 的客户备注", { locale: "en" }), "Alice 的客户备注");
});
test("translates every System application-builder icon label", () => {
    const iconSource = fs.readFileSync(
        path.join(
            views,
            "gyro-craftsman-web-own-v2.4/src/libs/iconfont-icons.js"
        ),
        "utf8"
    );
    const iconNames = [...iconSource.matchAll(/name:\s*["']([^"']+)["']/g)].map(
        (match) => match[1]
    );

    assert.ok(iconNames.length > 800);
    for (const iconName of iconNames) {
        assert.doesNotMatch(
            translateSystemTextValue(iconName, { locale: "en" }),
            /[\u3400-\u9fff]/,
            `Untranslated System icon label: ${iconName}`
        );
    }
});

test("keeps first-party Blade and installer UI server-localized", () => {
    const repo = path.resolve(views, "..");
    const bladeFiles = [];
    const visit = (directory) => {
        for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
            const target = path.join(directory, entry.name);
            if (entry.isDirectory()) visit(target);
            else if (entry.name.endsWith(".blade.php")) bladeFiles.push(target);
        }
    };
    visit(path.join(repo, "resources/views"));
    for (const file of bladeFiles) {
        const visible = fs.readFileSync(file, "utf8")
            .replace(/\{\{--[\s\S]*?--\}\}/g, "")
            .replace(/<!--[\s\S]*?-->/g, "")
            .replace(/^\s*\/\/.*$/gm, "");
        assert.doesNotMatch(visible, /[\u3400-\u9fff]/, file);
    }

    const controller = fs.readFileSync(path.join(repo, "app/Http/Controller/Install.php"), "utf8");
    assert.match(controller, /request\(\)->cookie\('language', 'zh-cn'\)/);
    assert.match(controller, /App::setLocale/);
    assert.match(controller, /__\('frontend\.install\.progress_invalid'\)/);

    const selector = fs.readFileSync(path.join(repo, "public/install/js/install-i18n.js"), "utf8");
    assert.doesNotMatch(selector, /textMap|MutationObserver/);
    assert.match(selector, /COOKIE_NAME = 'language'/);
});

test("keeps Laravel frontend catalogs and placeholders paired", () => {
    const repo = path.resolve(views, "..");
    const readPhpCatalog = (file) => {
        const source = fs.readFileSync(file, "utf8");
        const output = new Map();
        const entry = /'([^']+)'\s*=>\s*(?:'((?:\\.|[^'])*)'|"((?:\\.|[^"])*)")/gs;
        for (const match of source.matchAll(entry)) output.set(match[1], match[2] ?? match[3] ?? "");
        return output;
    };
    const en = readPhpCatalog(path.join(repo, "resources/lang/en/frontend.php"));
    const zh = readPhpCatalog(path.join(repo, "resources/lang/zh-cn/frontend.php"));
    assert.deepEqual([...en.keys()].sort(), [...zh.keys()].sort());
    const placeholders = (value) => [...value.matchAll(/:([A-Za-z_][A-Za-z0-9_]*)/g)].map((item) => item[1]).sort();
    for (const key of en.keys()) assert.deepEqual(placeholders(en.get(key)), placeholders(zh.get(key)), key);
});

test("fails the script UI audit when Chinese-bearing source cannot be parsed", () => {
    const { auditScriptUi } = require("./script-ui-audit.cjs");
    const root = fs.mkdtempSync(path.join(os.tmpdir(), "i18n-script-audit-"));
    const sourceFile = path.join(root, "broken.vue");
    try {
        fs.writeFileSync(sourceFile, "<script>const label = '中文'; invalid syntax</script>");
        const issues = auditScriptUi({
            root,
            sourceFiles: [sourceFile],
            parse: () => { throw new SyntaxError("Unexpected token"); },
        });
        assert.deepEqual(issues, [{
            file: "broken.vue",
            line: 1,
            sink: "parse:error",
            text: "Unexpected token",
        }]);
    } finally {
        fs.rmSync(root, { recursive: true, force: true });
    }
});

import type { App } from "vue";
import { translateSystemText } from "@/locale";

export const NAVIGATION_TITLE_ZH: Record<string, string> = {
  "pages/launch/index": "陀螺匠",
  "pages/common/ww-default": "企业微信",
  "pages/index/index": "首页",
  "pages/index/manage": "应用管理",
  "pages/notice/index": "消息",
  "pages/module/list": "低代码应用",
  "pages/module/details": "低代码详情",
  "pages/module/addForm": "低代码添加",
  "pages/module/oneOnOne": "低代码一对一",
  "pages/module/dashboard": "数据统计",
  "pages/module/questionnaire": "问卷调查",
  "pages/notice/work": "工作待办",
  "pages/notice/info": "待办详情",
  "pages/forum/index": "学习",
  "pages/forum/default": "查看文章",
  "pages/forum/search": "文章搜索",
  "pages/forum/history": "阅读记录",
  "pages/workbench/index": "应用",
  "pages/cloudfile/index": "云文件",
  "pages/user/index": "通讯录",
  "pages/user/userPhone": "手机号添加",
  "pages/user/userId": "成员ID添加",
  "pages/user/userRecord": "添加成员记录",
  "pages/user/personal": "个人信息",
  "pages/opportunities/index": "企微商机",
  "pages/users/login/index": "登录",
  "pages/users/login/config": "添加服务配置",
  "pages/users/login/enterprise": "切换企业",
  "pages/users/login/service": "服务协议",
  "pages/users/login/privacy": "隐私协议",
  "pages/users/organization/index": "组织架构",
  "pages/users/report/index": "填写汇报",
  "pages/users/report/mine": "汇报详情",
  "pages/users/report/check": "查看汇报",
  "pages/users/report/census": "汇报统计",
  "pages/users/report/search": "汇报搜索",
  "pages/users/notice/index": "企业动态",
  "pages/users/noticeDefault/index": "企业动态",
  "pages/users/assessment/index": "绩效考核",
  "pages/users/assessment/default": "考核详情",
  "pages/users/schedule/index": "我的日程",
  "pages/users/schedule/create": "新建日程",
  "pages/users/schedule/detail": "日程详情",
  "pages/users/center/index": "个人中心",
  "pages/users/center/password": "修改密码",
  "pages/users/center/phone": "修改手机",
  "pages/users/examine/index": "申请审批",
  "pages/users/examine/center": "已提交",
  "pages/users/examine/approve": "我审批的",
  "pages/users/examine/components/addSignature": "加签",
  "pages/users/examine/components/transfer": "转审",
  "pages/users/examine/default": "添加审批",
  "pages/users/examine/defaults": "审批详情",
  "pages/users/examine/mine": "我的审批",
  "pages/users/examine/search": "审批搜索",
  "pages/users/department/index": "选择成员",
  "pages/users/memorandum/index": "记事本",
  "pages/users/memorandum/create": "创建笔记",
  "pages/users/memorandum/details": "记事本",
  "pages/users/memorandum/search": "条件搜索",
  "pages/users/scanCode/index": "扫码登录",
  "pages/customer/list/index": "客户",
  "pages/customer/list/addLiaison": "添加联系人",
  "pages/customer/quickReply/index": "快捷回复",
  "pages/customer/list/addCustomer": "添加客户",
  "pages/customer/list/statistics": "业绩统计",
  "pages/customer/list/addFollow": "添加跟进记录",
  "pages/customer/list/shift": "转移",
  "pages/customer/list/search": "客户-条件搜索",
  "pages/customer/list/addSpend": "添加支付",
  "pages/customer/list/details": "客户详情",
  "pages/customer/list/contract": "订单",
  "pages/customer/list/opp": "商机",
  "pages/customer/list/invoice": "发票",
  "pages/customer/list/liaison": "联系人",
  "pages/customer/list/file": "资料",
  "pages/customer/signing/index": "合同合约",
  "pages/customer/signing/details": "合同详情",
  "pages/customer/signing/orderList": "选择订单",
  "pages/customer/signing/addForm": "添加合同",
  "pages/customer/addressSearch/index": "地址搜索",
  "pages/customer/contract/index": "订单管理",
  "pages/customer/contract/details": "订单详情",
  "pages/customer/contract/addContract": "添加订单",
  "pages/customer/contract/collectionDetails": "订单回款详情",
  "pages/customer/contract/addPayment": "添加付款",
  "pages/customer/contract/addFile": "添加订单资料",
  "pages/customer/contract/addRemind": "添加付款提醒",
  "pages/customer/contract/search": "订单-条件搜索",
  "pages/customer/invoice/index": "发票",
  "pages/customer/invoice/details": "发票详情",
  "pages/customer/invoice/search": "-发票-条件搜索",
  "pages/customer/invoice/addInvoice": "申请发票",
  "pages/customer/invoice/checkPayment": "选择付款单",
  "pages/customer/turnover/index": "业绩统计",
  "pages/customer/lead/index": "线索",
  "pages/customer/lead/add": "添加线索",
  "pages/customer/lead/detail": "线索详情",
  "pages/customer/lead/search": "线索搜索",
  "pages/customer/opportunity/index": "商机",
  "pages/customer/opportunity/detail": "商机详情",
  "pages/customer/opportunity/add": "添加商机",
  "pages/customer/opportunity/edit-price": "编辑价格",
  "pages/customer/opportunity/add-product": "添加商品",
  "pages/customer/opportunity/search": "商机搜索",
  "pages/finance/invoice/index": "开票管理",
  "pages/finance/invoice/details": "发票详情",
  "pages/finance/invoice/search": "发票审核-搜索",
  "pages/finance/payment/index": "付款审核",
  "pages/finance/payment/search": "付款搜索",
  "pages/finance/payment/details": "付款详情",
  "pages/finance/bill/index": "收支记账",
  "pages/finance/bill/statistic": "收支统计",
  "pages/finance/bill/details": "账目信息详情",
  "pages/finance/bill/add": "添加账目信息",
  "pages/attendance/index": "打卡",
  "pages/attendance/apply": "申请",
  "pages/attendance/statistics": "统计",
  "pages/attendance/teamReport": "团队月报",
  "pages/attendance/personalReport": "个人月报",
  "pages/attendance/rules": "考勤规则",
  "pages/attendance/map": "考勤范围",
  "pages/attendance/detailed/userVacationList": "假期明细",
  "pages/attendance/detailed/userCheckList": "考勤明细",
  "pages/attendance/detailed/userOvertimeList": "加班明细",
  "pages/attendance/detailed/teamCheckList": "团队打卡明细",
  "pages/attendance/schedule": "排班管理",
  "pages/attendance/scheduleDetail": "排班详情",
  "pages/attendance/scheduleAdd": "新增排班",
  "pages/attendance/shift": "班次管理",
  "pages/attendance/shiftAdd": "新建班次"
};

export const TAB_BAR_ZH = [
  {
    "index": 0,
    "pagePath": "pages/index/index",
    "text": "首页"
  },
  {
    "index": 1,
    "pagePath": "pages/notice/index",
    "text": "消息"
  },
  {
    "index": 2,
    "pagePath": "pages/users/schedule/index",
    "text": "日程"
  },
  {
    "index": 3,
    "pagePath": "pages/users/memorandum/index",
    "text": "笔记"
  },
  {
    "index": 4,
    "pagePath": "pages/workbench/index",
    "text": "应用"
  }
] as const;

function currentRoute(): string {
  const pages = getCurrentPages();
  const page = pages[pages.length - 1] as any;
  return String(page?.route || page?.$page?.fullPath || page?.$page?.path || "").replace(/^\//, "").split("?")[0];
}

export function applyLocalizedNavigationTitle(route = currentRoute()): void {
  const title = NAVIGATION_TITLE_ZH[route];
  if (!title) return;
  uni.setNavigationBarTitle({ title: String(translateSystemText(title)) });
}

export function applyLocalizedTabBar(): void {
  TAB_BAR_ZH.forEach((item) => {
    uni.setTabBarItem({ index: item.index, text: String(translateSystemText(item.text)) });
  });
}

export function installLocalizedNavigation(app: App): void {
  uni.$on("language:changed", () => {
    applyLocalizedNavigationTitle();
    applyLocalizedTabBar();
  });
  app.mixin({
    onShow() {
      applyLocalizedNavigationTitle();
      applyLocalizedTabBar();
    }
  });
}

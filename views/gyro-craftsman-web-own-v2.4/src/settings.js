// 路由前缀
const roterPre = '/admin';

// 路由标题
const title = '陀螺匠OA系统';

/**
 * @type {boolean} true | false
 * @description Whether show the
 * settings right-panel
 */
const showSettings = true;

/**
 * @type {boolean} true | false
 * @description Whether need tagsView
 */
const tagsView = false;

/**
 * @type {boolean} true | false
 * @description Whether fix the header
 */
const fixedHeader = true;

/**
 * @type {boolean} true | false
 * @description Whether show the logo in sidebar
 */
const sidebarLogo = true;

/** 显示右侧帮助
 * @type {boolean} true | false
 * @description Whether show the logo in sidebar
 */
const helpShow = true;

/** 显示论坛
 * @type {boolean} true | false
 * @description Whether show the logo in sidebar
 */
const bbsShow = true;

/** 显示知识社区
 * @type {boolean} true | false
 * @description Whether show the logo in sidebar
 */
const forumShow = true;

/**
 * @type {string | array} 'production' | ['production', 'development']
 * @description Need show err logs component.
 * The default is only used in the production env
 * If you want to also use it in dev, you can pass ['production', 'development']
 */
const errorLog = 'production';

const settings = {
  roterPre,
  title,
  showSettings,
  tagsView,
  fixedHeader,
  sidebarLogo,
  helpShow,
  bbsShow,
  forumShow,
  errorLog,
};

// 导出为 CommonJS 模块
module.exports = settings;

// 同时导出各个变量，保持兼容性
module.exports.roterPre = roterPre;
module.exports.title = title;
module.exports.showSettings = showSettings;
module.exports.tagsView = tagsView;
module.exports.fixedHeader = fixedHeader;
module.exports.sidebarLogo = sidebarLogo;
module.exports.helpShow = helpShow;
module.exports.bbsShow = bbsShow;
module.exports.forumShow = forumShow;
module.exports.errorLog = errorLog;

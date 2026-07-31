(function () {
  var SUPPORTED = ['zh-cn', 'en'];
  var COOKIE_NAME = 'language';

  function readCookie(name) {
    var item = document.cookie.split('; ').find(function (row) {
      return row.indexOf(name + '=') === 0;
    });
    return item ? decodeURIComponent(item.split('=').slice(1).join('=')) : '';
  }

  function getLanguage() {
    var saved = localStorage.getItem(COOKIE_NAME) || readCookie(COOKIE_NAME);
    return SUPPORTED.indexOf(saved) > -1 ? saved : 'zh-cn';
  }

  function setLanguage(lang) {
    var next = SUPPORTED.indexOf(lang) > -1 ? lang : 'zh-cn';
    localStorage.setItem(COOKIE_NAME, next);
    document.cookie = COOKIE_NAME + '=' + encodeURIComponent(next) + '; path=/; max-age=31536000';
    window.location.reload();
  }

  var textMap = {
    '安装向导': 'Installation Wizard',
    '欢迎使用 陀螺匠 · 企业助手': 'Welcome to Tuoluojiang Enterprise Assistant',
    '详细阅读并勾选同意': 'I have read and agree to',
    '《软件使用协议》': 'Software License Agreement',
    '开始安装': 'Start Installation',
    '软件许可协议': 'Software License Agreement',
    '我知道了': 'Got it',
    '安装检测': 'Installation Check',
    '重新检测': 'Recheck',
    '安装环境需满足系统运行要求': 'The installation environment must meet the system requirements',
    '环境及配置': 'Environment and Configuration',
    '基础的系统操作环境': 'Basic system runtime environment',
    '权限检测': 'Permission Check',
    '目录及文件权限检测': 'Directory and file permission check',
    '环境检测': 'Environment Check',
    '推荐配置': 'Recommended',
    '最低要求': 'Minimum Requirement',
    '当前状态': 'Current Status',
    '权限检查': 'Permission Check',
    '读取': 'Read',
    '写入': 'Write',
    '读写': 'Read/Write',
    '无需检测': 'Not Required',
    '无需写入': 'Write Not Required',
    '上一步': 'Previous',
    '下一步': 'Next',
    '创建数据': 'Create Data',
    '数据库信息': 'Database',
    '数据库用户名:': 'DB User:',
    '数据库密码:': 'DB Password:',
    '数据库名:': 'DB Name:',
    '高级设置:': 'Advanced:',
    '数据库服务器:': 'DB Host:',
    '数据库端口:': 'DB Port:',
    '数据表前缀:': 'Prefix:',
    '演示数据:': 'Demo Data:',
    '管理员信息': 'Administrator',
    '管理员帐号:': 'Admin Phone:',
    '管理员密码:': 'Password:',
    '重复密码:': 'Confirm:',
    '缓存设置': 'Cache Settings',
    '缓存方式:': 'Cache:',
    'redis缓存': 'Redis Cache',
    '服务器地址:': 'Host:',
    '端口号:': 'Port:',
    '数据库:': 'DB:',
    '请输入数据库用户名': 'Enter database username',
    '请输入数据库密码': 'Enter database password',
    '请输入数据库名': 'Enter database name',
    '请输入数据库地址': 'Enter database host',
    '请输入数据库端口号': 'Enter database port',
    '请输入数据表前缀': 'Enter table prefix',
    '请输入管理员手机号': 'Enter admin mobile number',
    '请输入密码（至少6个字符）': 'Enter password, at least 6 characters',
    '请再次输入密码': 'Enter password again',
    '请输入redis服务器地址': 'Enter Redis server host',
    '请输入redis服务器端口号': 'Enter Redis server port',
    '请输入redis服务器数据库编号': 'Enter Redis database number',
    '请输入redis数据库密码': 'Enter Redis database password',
    '请输入密码': 'Enter password',
    '管理员密码必须6位字符以上': 'Admin password must be at least 6 characters',
    '两次输入密码不一致!': 'The two passwords do not match.',
    '端口必须是1-65535的数字': 'Port must be a number from 1 to 65535',
    '前缀需以字母开头并以下划线结尾': 'Prefix must start with a letter and end with an underscore',
    'Redis数据库编号必须是0-15': 'Redis database number must be 0 to 15',
    '请输入数据库服务器': 'Enter database server',
    '请输入数据库名称': 'Enter database name',
    '请确认管理员密码': 'Confirm admin password',
    '请输入正确的手机号': 'Enter a valid mobile number',
    '管理员手机号格式不正确': 'Enter a valid phone number, for example +601136610878.',
    '数据库地址或端口错误': 'Database host or port is incorrect',
    '数据库用户名或密码错误': 'Database username or password is incorrect',
    '请检查数据库是否存在': 'Check whether the database exists',
    'MySql数据库必须是5.7及以上版本': 'MySQL must be version 5.7 or later',
    '数据库不为空，请更换一个数据库': 'The database is not empty. Use another database.',
    'Redis数据库没有启动或者配置错误': 'Redis is not running or is misconfigured',
    '请填写正确的手机号': 'Enter a valid mobile number',
    'Redis扩展没有安装': 'Redis extension is not installed',
    '请求失败，请检查服务器状态后重试': 'Request failed. Check the server status and try again.',
    '安装进度': 'Installation Progress',
    '正在准备安装...': 'Preparing installation...',
    '安装失败，请根据提示处理后重试': 'Installation failed. Fix the issue and try again.',
    '安装完成，正在跳转...': 'Installation completed. Redirecting...',
    '系统安装中，请稍等片刻...': 'Installing the system. Please wait...',
    '重新安装': 'Reinstall',
    '安装完成': 'Installation Complete',
    '正在安装...': 'Installing...',
    '服务器返回异常，请刷新后重试': 'The server returned an invalid response. Refresh and try again.',
    '安装失败，请检查日志': 'Installation failed. Check the logs.',
    '安装失败，请检查配置': 'Installation failed. Check the configuration.',
    '请求安装接口失败，请检查服务状态后重试': 'Installation API request failed. Check the service status and try again.',
    '安装成功': 'Installation Successful',
    '为了您站点的安全，安装完成后即可将根目录下的“install”文件夹内除install.lock文件删除，防止重复安装。': 'For site security, after installation you can delete the files in the root install folder except install.lock to prevent repeated installation.',
    '进入后台': 'Enter Admin',
    '请先阅读并同意《软件使用协议》再进行下一步操作': 'Please read and accept the Software License Agreement before continuing.',
    '安装环境检测未通过，请检查': 'The installation environment check failed. Please review the items above.',
    '西安众邦网络科技有限公司': 'Xi\'an Zhongbang Network Technology Co., Ltd.',
    '支持': 'Supported',
    '不支持': 'Not supported',
    '已安装': 'Installed',
    '未安装': 'Not installed',
    '禁止上传': 'Upload disabled',
    '开启': 'Enabled',
    '关闭': 'Disabled',
    '数据库连接失败': 'Database connection failed',
    'Redis 连接失败，请检查配置': 'Redis connection failed. Check the configuration.',
    '刷新数据库配置失败': 'Failed to refresh database configuration',
    '安装进度参数异常，请刷新后重试': 'Installation progress parameter is invalid. Refresh and try again.',
    '读取安装SQL文件失败': 'Failed to read installation SQL file',
    '安装SQL为空，请检查安装文件是否完整': 'Installation SQL is empty. Check whether the installation files are complete.',
    '初始化前清理数据表失败': 'Failed to clear data tables before initialization',
    '创建企业用户数据成功': 'Enterprise user data created successfully',
    '安装日志写入失败，请检查根目录写入权限。': 'Failed to write installation log. Check root directory write permission.',
    '安装锁文件写入失败，请检查 public/install 目录写入权限。': 'Failed to write install lock file. Check public/install write permission.',
    '执行数据库结构SQL完成': 'Database schema SQL completed',
    '默认数据文件不可读': 'Default data file is not readable',
    '创建企业信息失败': 'Failed to create enterprise information',
    '创建企业信息完成': 'Enterprise information created',
    '创建默认数据完成': 'Default data created',
    '跳过默认数据创建': 'Skipped default data creation',
    '写入运行环境配置完成': 'Runtime environment configuration written',
    '初始化角色权限完成': 'Role permissions initialized',
    '客户JSON字段规整完成': 'Customer JSON fields normalized',
    '未知安装任务，请刷新后重试': 'Unknown installation task. Refresh and try again.',
    '创建企业信息': 'Create enterprise information',
    '创建默认数据': 'Create default data',
    '写入运行环境配置': 'Write runtime environment configuration',
    '初始化角色权限': 'Initialize role permissions',
    '执行安装任务': 'Run installation task',
    '数据表前缀需以字母开头、以下划线结尾，仅支持字母数字下划线': 'Table prefix must start with a letter, end with an underscore, and contain only letters, numbers, and underscores.',
    '当前安装流程仅支持 Redis 缓存': 'This installer currently supports Redis cache only.',
    '.env 文件不可读写，请检查文件权限': '.env is not readable/writable. Check file permissions.',
    'SQL文件不可读': 'SQL file is not readable',
    'SQL文件读取失败': 'Failed to read SQL file',
    '安装种子SQL文件不可读': 'Installation seed SQL file is not readable',
    '执行SQL失败': 'SQL execution failed',
    '执行初始化SQL失败': 'Failed to execute initialization SQL',
    '客户JSON字段规整命令执行失败': 'Customer JSON field normalization command failed',
    '规整客户JSON字段': 'Normalize customer JSON fields',
    'PHP 版本': 'PHP Version',
    '附件上传': 'Upload Limit',
    'MySQL 版本过低，需要 5.7.0 或以上版本': 'MySQL version is too low. Version 5.7.0 or later is required.',
    'Redis 服务器地址不能为空': 'Redis server address cannot be empty.',
    '默认数据字典': 'Default data dictionary',
    '系统配置数据': 'System configuration data',
    'CRUD配置数据': 'CRUD configuration data',
    '开发配置数据': 'Development configuration data',
    '默认业务数据': 'Default business data',
    '未知SQL文件': 'Unknown SQL file'
  };

  var agreementText = [
    'Software License Agreement',
    '',
    'Important Notice:',
    'This agreement is entered into by you and Xi\'an Zhongbang Network Technology Co., Ltd. Tuoluojiang Enterprise Intelligent Management System is independently developed by Xi\'an Zhongbang Network Technology Co., Ltd. and is protected by copyright and other intellectual property laws.',
    '',
    'Before downloading, copying, installing, or using this software, please read this agreement carefully. By installing or using this software, you confirm that you understand and accept this agreement. If you do not accept these terms, stop installation and remove any installed components.',
    '',
    '1. License Scope',
    'After obtaining a valid commercial license through an authorized channel, you may install and use this software for commercial purposes within the permitted scope. You are responsible for your domain, deployment environment, content, and legal compliance.',
    '',
    '2. Restrictions',
    'You may not sublicense, rent, lend, redistribute, reverse engineer, decompile, disassemble, remove copyright notices, or modify protected marks unless you have written permission. Unauthorized derivative versions or redistribution are prohibited.',
    '',
    '3. Intellectual Property',
    'All software copyrights, trade secrets, documents, logos, icons, and related intellectual property remain owned by Zhongbang Technology or the relevant rights holder. This agreement grants a right to use the software, not a transfer of ownership.',
    '',
    '4. Updates and Support',
    'Commercial license users may receive upgrade and support rights according to the purchased license type. The provider may decide when and how updates are delivered.',
    '',
    '5. Disclaimer and Limitation of Liability',
    'Except where expressly stated, the software is provided without implied warranties. You are responsible for risks caused by unsupported environments, misuse, unauthorized changes, third-party actions, force majeure, or other causes outside the provider\'s control.',
    '',
    '6. Confidentiality and Termination',
    'You must keep source code, documents, and other technical information confidential. If you violate this agreement, the provider may terminate your license and pursue legal responsibility.',
    '',
    '7. Governing Law',
    'This agreement is governed by the laws of the People\'s Republic of China. Disputes should be resolved through friendly negotiation first; if negotiation fails, they may be submitted to the agreed arbitration institution.',
    '',
    'Published by Xi\'an Zhongbang Network Technology Co., Ltd.',
    'Official website: https://www.tuoluojiang.com'
  ].join('\n');

  function translateDynamicValue(value) {
    var tableDone = value.match(/^创建数据表\[(.+)]完成!$/);
    if (tableDone) return 'Created table [' + tableDone[1] + ']';

    var tableFailed = value.match(/^创建数据表\[(.+)]失败：(.+)$/);
    if (tableFailed) return 'Failed to create table [' + tableFailed[1] + ']: ' + tableFailed[2];

    var seedDone = value.match(/^创建\[(.+)]完成$/);
    if (seedDone) return 'Created [' + seedDone[1] + ']';

    var seedFailed = value.match(/^创建\[(.+)]失败：(.+)$/);
    if (seedFailed) return 'Failed to create [' + seedFailed[1] + ']: ' + seedFailed[2];

    var colonFailed = value.match(/^(.+?)失败：(.+)$/);
    if (colonFailed && textMap[colonFailed[1]]) return textMap[colonFailed[1]] + ' failed: ' + colonFailed[2];

    var colonError = value.match(/^(.+?)：(.+)$/);
    if (colonError && textMap[colonError[1]]) return textMap[colonError[1]] + ': ' + colonError[2];

    return value;
  }

  function translateValue(value) {
    if (!value) return value;
    var direct = textMap[value];
    if (direct) return direct;
    var trimmed = value.trim();
    if (textMap[trimmed]) {
      return value.replace(trimmed, textMap[trimmed]);
    }
    var translatedDynamic = translateDynamicValue(trimmed);
    if (translatedDynamic !== trimmed) {
      return value.replace(trimmed, translatedDynamic);
    }
    return value;
  }

  function translateNode(node) {
    if (!node) return;
    if (node.nodeType === Node.TEXT_NODE) {
      var translatedText = translateValue(node.nodeValue);
      if (translatedText !== node.nodeValue) node.nodeValue = translatedText;
      return;
    }
    if (node.nodeType !== Node.ELEMENT_NODE) return;
    if (['SCRIPT', 'STYLE', 'NOSCRIPT'].indexOf(node.tagName) > -1) return;
    ['placeholder', 'title', 'alt', 'aria-label'].forEach(function (attr) {
      if (node.hasAttribute && node.hasAttribute(attr)) {
        var translatedAttr = translateValue(node.getAttribute(attr));
        if (translatedAttr !== node.getAttribute(attr)) node.setAttribute(attr, translatedAttr);
      }
    });
    Array.prototype.forEach.call(node.childNodes || [], translateNode);
  }

  function translatePage() {
    if (getLanguage() !== 'en') return;
    document.documentElement.lang = 'en';
    document.body.classList.add('install-lang-en');
    var pact = document.querySelector('.pact');
    if (pact) {
      pact.textContent = agreementText;
    }
    translateNode(document.body);
  }

  function buildSwitcher() {
    var current = getLanguage();
    var switcher = document.createElement('div');
    switcher.className = 'install-language-switcher';
    switcher.innerHTML = '<button type="button" data-lang="zh-cn">中文</button><button type="button" data-lang="en">English</button>';
    Array.prototype.forEach.call(switcher.querySelectorAll('button'), function (button) {
      var lang = button.getAttribute('data-lang');
      if (lang === current) {
        button.disabled = true;
        button.className = 'active';
      }
      button.addEventListener('click', function () {
        if (lang !== getLanguage()) setLanguage(lang);
      });
    });
    document.body.appendChild(switcher);
  }

  function installStyles() {
    var style = document.createElement('style');
    style.textContent = '.install-language-switcher{position:fixed;right:24px;top:18px;z-index:9999;display:flex;gap:6px;font-family:Arial,sans-serif}.install-language-switcher button{height:30px;padding:0 12px;border:1px solid #1f6fb2;border-radius:4px;background:#fff;color:#1f6fb2;cursor:pointer;font-size:13px}.install-language-switcher button.active,.install-language-switcher button:disabled{background:#1f6fb2;color:#fff;cursor:default}.install-language-switcher button:not(:disabled):hover{background:#eaf4ff}body.install-lang-en #step3 .tip{width:120px!important;white-space:nowrap;overflow:visible;font-size:13px!important}body.install-lang-en #step3 .el-form-item__label{white-space:nowrap!important;overflow:visible!important;font-size:12px!important;line-height:30px!important}body.install-lang-en #step3 .el-input__inner::placeholder{font-size:12px}body.install-lang-en #step3 .el-form-item__error{white-space:nowrap}';
    document.head.appendChild(style);
  }

  function attachAjaxLanguageHeader() {
    if (!window.jQuery) return;
    window.jQuery(document).ajaxSend(function (_event, xhr) {
      xhr.setRequestHeader('laravel_lang', getLanguage());
    });
  }

  function translateMessageOptions(options) {
    if (getLanguage() !== 'en') return options;
    if (typeof options === 'string') return translateValue(options);
    if (options && typeof options === 'object' && typeof options.message === 'string') {
      var cloned = {};
      Object.keys(options).forEach(function (key) {
        cloned[key] = options[key];
      });
      cloned.message = translateValue(options.message);
      return cloned;
    }
    return options;
  }

  function wrapMessageFunction(fn) {
    return function (options) {
      return fn.call(this, translateMessageOptions(options));
    };
  }

  function patchElementMessages() {
    if (!window.Vue || !window.Vue.prototype || !window.Vue.prototype.$message || window.Vue.prototype.$message.__installI18nPatched) return;
    var original = window.Vue.prototype.$message;
    var wrapped = wrapMessageFunction(original);
    Object.keys(original).forEach(function (key) {
      wrapped[key] = typeof original[key] === 'function' ? wrapMessageFunction(original[key]) : original[key];
    });
    wrapped.__installI18nPatched = true;
    window.Vue.prototype.$message = wrapped;
  }

  function observeDynamicContent() {
    if (getLanguage() !== 'en' || !window.MutationObserver) return;
    var pendingNodes = [];
    var scheduled = false;

    function flushPendingNodes() {
      scheduled = false;
      var nodes = pendingNodes.splice(0, pendingNodes.length);
      nodes.forEach(translateNode);
    }

    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        Array.prototype.forEach.call(mutation.addedNodes || [], function (node) {
          if (node.nodeType === Node.TEXT_NODE || node.nodeType === Node.ELEMENT_NODE) {
            pendingNodes.push(node);
          }
        });
      });
      if (pendingNodes.length && !scheduled) {
        scheduled = true;
        window.requestAnimationFrame(flushPendingNodes);
      }
    });

    observer.observe(document.body, { childList: true, subtree: true });
  }

  function boot() {
    installStyles();
    buildSwitcher();
    attachAjaxLanguageHeader();
    patchElementMessages();
    translatePage();
    observeDynamicContent();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
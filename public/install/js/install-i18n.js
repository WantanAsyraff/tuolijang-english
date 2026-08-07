(function () {
  var SUPPORTED = ['zh-cn', 'en'];
  var COOKIE_NAME = 'language';

  function readCookie(name) {
    var prefix = name + '=';
    var item = document.cookie.split('; ').find(function (row) { return row.indexOf(prefix) === 0; });
    return item ? decodeURIComponent(item.slice(prefix.length)) : '';
  }

  function getLanguage() {
    var value = String(readCookie(COOKIE_NAME) || '').toLowerCase();
    return SUPPORTED.indexOf(value) >= 0 ? value : 'zh-cn';
  }

  function setLanguage(language) {
    document.cookie = COOKIE_NAME + '=' + language + '; path=/; max-age=31536000; SameSite=Lax';
    window.location.reload();
  }

  function buildSwitcher() {
    var current = getLanguage();
    var switcher = document.createElement('div');
    switcher.className = 'install-language-switcher';
    switcher.innerHTML = '<button type="button" data-lang="zh-cn">&#20013;&#25991;</button><button type="button" data-lang="en">English</button>';
    Array.prototype.forEach.call(switcher.querySelectorAll('button'), function (button) {
      var language = button.getAttribute('data-lang');
      button.classList.toggle('active', language === current);
      button.disabled = language === current;
      button.addEventListener('click', function () { setLanguage(language); });
    });
    document.body.appendChild(switcher);
  }

  function installStyles() {
    var style = document.createElement('style');
    style.textContent = '.install-language-switcher{position:fixed;right:24px;top:18px;z-index:9999;display:flex;gap:6px;font-family:Arial,sans-serif}.install-language-switcher button{height:30px;padding:0 12px;border:1px solid #1f6fb2;border-radius:4px;background:#fff;color:#1f6fb2;cursor:pointer;font-size:13px}.install-language-switcher button.active,.install-language-switcher button:disabled{background:#1f6fb2;color:#fff;cursor:default}.install-language-switcher button:not(:disabled):hover{background:#eaf4ff}';
    document.head.appendChild(style);
  }

  function init() {
    document.documentElement.lang = getLanguage();
    installStyles();
    buildSwitcher();
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();

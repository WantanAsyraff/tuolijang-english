<template>
  <div class="language-actions">
    <el-button
      size="mini"
      class="translate-en-btn"
      type="primary"
      @click="toggleLanguage"
    >
      {{ toggleLabel }}
    </el-button>
    <el-dropdown trigger="click" class="international" @command="handleSetLanguage">
      <button type="button" class="lang-trigger" :title="$t('navbar.language') || 'Language'">
        <span class="iconfont icondiqiu" />
      </button>
      <el-dropdown-menu slot="dropdown">
        <el-dropdown-item :disabled="language === 'zh-cn'" command="zh-cn">{{ $t('login.chinese') }}</el-dropdown-item>
        <el-dropdown-item :disabled="language === 'en'" command="en">English</el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
  </div>
</template>

<script>
export default {
  computed: {
    language() {
      return this.$store.getters.lang
    },
    targetLanguage() {
      return this.language === 'en' ? 'zh-cn' : 'en'
    },
    toggleLabel() {
      return this.language === 'en' ? this.$t('navbar.translateToChinese') : this.$t('navbar.translateToEnglish')
    }
  },
  methods: {
    toggleLanguage() {
      this.handleSetLanguage(this.targetLanguage, true)
    },
    handleSetLanguage(lang, force = false) {
      if (!force && lang === this.language) return
      this.$i18n.locale = lang
      this.$store.dispatch('app/setLanguage', lang)
      localStorage.setItem('language', lang)
      this.clearLanguageSensitiveCache()
      this.$nextTick(() => window.location.reload())
    },
    clearLanguageSensitiveCache() {
      localStorage.removeItem('menuTabData')
      localStorage.removeItem('sidebarParentCur')
      localStorage.removeItem('navTitle')
      window.sessionStorage.removeItem('parentMenuId')
    }
  }
}
</script>

<style scoped>
.language-actions {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-right: 10px;
}

.translate-en-btn {
  height: 28px;
  min-width: 64px;
  padding: 0 10px;
  border-radius: 4px;
}

.lang-trigger {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  padding: 0;
  border: 0;
  background: transparent;
  cursor: pointer;
}

.icondiqiu {
  font-size: 20px;
  color: #303133;
}
</style>
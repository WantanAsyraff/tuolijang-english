<template>
  <div class="language-actions">
    <button
      type="button"
      class="translate-en-btn"
      :disabled="locale === 'en'"
      :title="t('common.translateToEnglish')"
      @click="handleLanguageChange('en')"
    >
      {{ t("common.translateToEnglish") }}
    </button>
    <el-dropdown trigger="click" @command="handleLanguageChange">
      <button type="button" class="language-btn" :title="t('common.language')">
        {{ locale === "en" ? "EN" : "ZH" }}
      </button>
      <template #dropdown>
        <el-dropdown-menu>
          <el-dropdown-item command="zh-cn" :disabled="locale === 'zh-cn'">{{ t("common.chinese") }}</el-dropdown-item>
          <el-dropdown-item command="en" :disabled="locale === 'en'">{{ t("common.english") }}</el-dropdown-item>
        </el-dropdown-menu>
      </template>
    </el-dropdown>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from "vue-i18n";
import { setLanguage, type SupportedLocale } from "@/locale";

const { t, locale } = useI18n();

const handleLanguageChange = (language: string) => {
  if (language === locale.value) return;
  setLanguage(language as SupportedLocale);
  window.location.reload();
};
</script>

<style scoped lang="scss">
.language-actions {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.translate-en-btn {
  height: 30px;
  padding: 0 8px;
  border: 1px solid #d8d8d8;
  border-radius: 6px;
  background: #fff;
  color: #303133;
  font-size: 12px;
  line-height: 1;
  cursor: pointer;
}

.translate-en-btn:disabled {
  color: #a8abb2;
  cursor: not-allowed;
  background: #f5f7fa;
}

.language-btn {
  width: 30px;
  height: 30px;
  border: 1px solid #d8d8d8;
  border-radius: 6px;
  background: #fff;
  color: #303133;
  font-size: 13px;
  line-height: 1;
  cursor: pointer;
}
</style>
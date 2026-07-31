<template>
  <!--
    SVG 图标组件
    通过 iconClass 动态渲染对应的 SVG 图标
    支持 className 自定义样式和 title 提示文本
  -->
  <svg :class="svgClass" aria-hidden="true">
    <use :xlink:href="iconName"></use>
    <title v-if="!!title">{{ title }}</title>
  </svg>
</template>

<script setup>
/**
 * SVG 图标组件 - 使用 Vue 2.7 Composition API 重构
 *
 * @component SvgIcon
 * @description 用于渲染注册到全局的 SVG 图标
 * @example
 * <svg-icon icon-class="user" class-name="custom-class" title="用户图标" />
 */
import { computed } from 'vue'

// 定义组件属性
const props = defineProps({
  /**
   * 图标类名（必需）
   * 对应 src/icons/svg 目录下的 svg 文件名
   */
  iconClass: {
    type: String,
    required: true
  },
  /**
   * 自定义 CSS 类名
   * 会追加到默认的 'svg-icon' 类名后
   */
  className: {
    type: String,
    default: ''
  },
  /**
   * 图标标题
   * 显示为 SVG 的 title 元素，用于无障碍访问和鼠标悬停提示
   */
  title: {
    type: String,
    default: ''
  }
})

/**
 * 计算完整的图标引用路径
 * @returns {string} 格式: #icon-{iconClass}
 * @example iconClass="user" -> "#icon-user"
 */
const iconName = computed(() => {
  return `#icon-${props.iconClass}`
})

/**
 * 计算最终的 CSS 类名
 * @returns {string} 基础类名 'svg-icon' + 自定义类名
 */
const svgClass = computed(() => {
  if (props.className) {
    return 'svg-icon ' + props.className
  } else {
    return 'svg-icon'
  }
})
</script>

<style lang="scss" scoped>
.svg-icon {
  width: 1.1em;
  height: 1.1em;
  margin-left: 0.35em;
  margin-right: 0.35em;
  vertical-align: -0.15em;
  fill: currentColor;
  overflow: hidden;
}
</style>

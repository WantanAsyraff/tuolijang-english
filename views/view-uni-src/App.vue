<script setup lang="ts">
import { onLaunch, onShow } from '@dcloudio/uni-app'
import { bootstrapOnLaunch } from '@/app/bootstrap/launch'
import { resumeApp, getNum } from '@/app/bootstrap/resume'
import { applyLocalizedNavigationTitle, applyLocalizedTabBar } from '@/locale/navigation'

onLaunch((options) => {
  bootstrapOnLaunch(options)
  applyLocalizedTabBar()
})

onShow(() => {
  resumeApp()
  getNum()
  applyLocalizedNavigationTitle()
  applyLocalizedTabBar()
})
</script>

<style lang="scss">
@import url('@/static/css/base.css');
@import url('@/static/iconfont/iconfont.css');
@import url('@/static/iconfont/iconfont-crmeb.css');
@import '@/static/css/style.scss';
/*每个页面公共css */
</style>

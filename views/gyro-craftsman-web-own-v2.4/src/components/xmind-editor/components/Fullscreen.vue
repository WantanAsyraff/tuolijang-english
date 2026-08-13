<template>
  <div class="fullscreenContainer">
    <el-tooltip class="item" effect="dark" :content='$("legacy.11423aad226b0482")' placement="top">
      <div class="nav-btn xmind-iconfont iconquanping" @click="toFullscreenShow"></div>
    </el-tooltip>
    <el-tooltip class="item" effect="dark" :content='$("legacy.978ec3846aa48964")' placement="top">
      <div class="nav-btn xmind-iconfont iconquanping1" @click="toFullscreenEdit"></div>
    </el-tooltip>
  </div>
</template>

<script>
import { fullscrrenEvent, fullScreen } from "../utils";

export default {
  name: 'Fullscreen',
  props: {
    mindMap: {
      type: Object
    }
  },
  data() {
    return {}
  },
  created() {
    document[fullscrrenEvent] = () => {
      setTimeout(() => {
        this.mindMap.resize()
      }, 1000)
    }
  },
  methods: {
    // 全屏查看
    toFullscreenShow() {
      fullScreen(this.mindMap.el)
    },

    // 全屏编辑
    toFullscreenEdit() {
      fullScreen(document.body)
    }
  }
}
</script>

<style lang="scss" scoped>
.fullscreenContainer {
  display: flex;
  align-items: center;

  .item {
    margin-right: 20px;

    &:last-of-type {
      margin-right: 0;
    }
  }

  .nav-btn {
    cursor: pointer;
  }
}
</style>

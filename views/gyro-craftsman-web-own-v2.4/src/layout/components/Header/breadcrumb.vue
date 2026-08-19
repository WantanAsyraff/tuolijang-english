<template>
  <div class="breadcrumb-content flex lh-center">
    <div v-if="hasBrand" class="logo-wrapper">
      <img v-if="brandLogo" :src="brandLogo" alt="" />
      <span v-if="enterpriseName" class="enterprise-name" v-show="sidebar.opened">{{ enterpriseName }}</span>
    </div>
    <el-breadcrumb class="breadcrumb-list" separator-class="el-icon-arrow-right">
      <el-breadcrumb-item v-for="(item, index) in levaList" :key="index"> {{ item }}</el-breadcrumb-item>
    </el-breadcrumb>
  </div>
</template>

<script>
import { $ } from '@/lang'
import { roterPre } from '@/settings'
import { mapGetters } from 'vuex'
export default {
  name: 'Breadcrumb',
  data() {
    return {
      levaList: [],
      path: ''
    }
  },
   computed: {
    ...mapGetters(['sidebar', 'avatar', 'device', 'sidebarParentCur', 'menuList', 'enterprise']),
    webConfig() {
      return this.$store.getters['appConfig/configData'];
    },
    brandLogo() {
      return this.enterprise?.logo || this.webConfig?.site_logo || '';
    },
    enterpriseName() {
      const name = this.enterprise?.enterprise_name || this.webConfig?.enterprise_name || '';
      const localizedName = this.$(name);
      return localizedName.length > 14 ? localizedName.slice(0, 13) + '…' : localizedName;
    },
    hasBrand() {
      return Boolean(this.brandLogo || this.enterpriseName);
    },
  },
  watch: {
    $route(to, from) {
      this.getBreadcrumb()
    }
  },
  mounted() {
    this.getBreadcrumb()
  },
  methods: {
    menuTitle(title) {
      return this.$(title)
    },
    getBreadcrumb() {
      let newPath = this.$route.fullPath
      newPath = newPath.split('?')[0]
      // 获取缓存菜单
      let routerInfo = this.$store.getters.menuList
      if (newPath == `${roterPre}/search`) {
        this.levaList = [this.menuTitle('搜索')]
      } else {
        if (routerInfo) {
          this.find(routerInfo, newPath)
        }
      }
    },
    find(array, path) {
      let stack = []
      let going = true
      let walker = (array, path) => {
        array.forEach((item) => {
          if (!going) return
          stack.push(this.menuTitle(item['menu_name']))
          if (item['menu_path'] === path) {
            going = false
          } else if (item['children']) {
            walker(item['children'], path)
          } else {
            stack.pop()
          }
        })
        if (going) stack.pop()
      }
      walker(array, path)

      this.levaList = stack
      if (this.levaList.length <= 2) {
        localStorage.setItem('navTitle', JSON.stringify(this.levaList))
      } else {
        localStorage.setItem('navTitle', JSON.stringify(this.levaList))
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.breadcrumb-content {
  flex: 1;
  min-width: 0;
  padding-left: 16px;
  overflow: hidden;
}

::v-deep .el-breadcrumb__item:last-child .el-breadcrumb__inner {
  color: #303133;
  font-family: PingFang SC-Regular, PingFang SC;
  font-weight: 400;
  font-size: 13px;
}
::v-deep .el-breadcrumb__inner {
  font-size: 13px;
  font-family: PingFang SC-Regular, PingFang SC;
  font-weight: 400;
  color:#909399;
}

.breadcrumb-list {
  min-width: 0;
  overflow: hidden;
  white-space: nowrap;
}

.logo-wrapper {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  margin-right: 24px;
  .enterprise-name {
    max-width: 190px;
    margin-left: 10px;
    overflow: hidden;
    color: #000919;
    font-family: PingFang SC-Regular, PingFang SC;
    font-size: 15px;
    font-weight: 500;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  img {
    flex-shrink: 0;
    width: 43px;
    height: 40px;
    object-fit: cover;
  }
}
</style>

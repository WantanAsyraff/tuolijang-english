<template>
  <div class="navbar-news">
    <div class="navbar">
      <div ref="leftBox" class="menu-box">
    
        <el-scrollbar style="height: 100%;">
          <template v-if="showMore">
            <div class="nav-box">
              <template v-for="(item, index) in menuList">
                <div v-if="item.is_show != 0 && item.menu_path !== settingMenuUniquePath" :key="index"
                  :class="{ on: parentcur === index }" class="nav-item" @click="handleParentCur(index, 1, item)">
                  <div class="nav-items">
                    <i :class="getIconCss(item.icon)"></i>
                    <span> {{ menuTitle(item.menu_name, item.menu_name_en) }} </span>
                  </div>
                </div>
              </template>
            </div>
          </template>
          <template v-else>
            <div class="nav-box">
              <template v-for="(item, index) in menuList.slice(0, showMax)">
                <div v-if="item.is_show != 0 && item.menu_path !== settingMenuUniquePath" :key="index"
                  :class="{ on: parentcur === index }" class="nav-item" @click="handleParentCur(index, 1, item)">
                  <div class="nav-items">
                    <i :class="item.icon" class="iconfont"></i>
                    <span> {{ menuTitle(item.menu_name, item.menu_name_en) }}</span>
                  </div>
                </div>
              </template>
              <el-popover v-if="newMenuListArr.length - 1 > showMax" placement="right" trigger="hover" width="176">
                <div class="popover-content">
                  <template v-for="(item, index) in menuList.slice(showMax, menuList.length)">
                    <div v-if="item.is_show !== 0 && item.menu_path !== settingMenuUniquePath" :key="'pop' + index"
                      class="nav-item" @click="handleParentCur(index, 2, item)">
                      <div class="nav-items">
                        <i :class="getIconCss(item.icon)"></i>
                        <span> {{ menuTitle(item.menu_name, item.menu_name_en) }}</span>
                      </div>
                    </div>
                  </template>
                </div>
                <div slot="reference" class="nav-item popover-nav-item">
                  <div class="nav-items">
                    <i class="iconfont icongenjinjilu-gengduo"></i>
                    <span>{{ menuTitle($t('ui.layoutNavbarMore')) }}</span>
                  </div>
                </div>
              </el-popover>
            </div>
          </template>
        </el-scrollbar>
      </div>
      <div class="below-bar" v-if="settingMenuInfo && settingMenuInfo.is_show !== 0">
        <el-tooltip :content="menuTitle(settingMenuInfo.menu_name, settingMenuInfo.menu_name_en)" placement="right">
          <div class="nav-item" @click="handleSettingMenuClick" :class="{ on: parentcur == settingMenuIndex }">
            <i :class="getIconCss(settingMenuInfo.icon)"></i>
            <!-- <i :class="settingMenuInfo.icon" class="iconfont"></i> -->
          </div>
        </el-tooltip>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { roterPre } from '@/settings'
import Cookies from 'js-cookie'
import { generateTitle, translateSystemText } from '@/utils/i18ns'
import defaultSettings from '@/settings'
const settingMenuUniquePath = '/admin/setting';
export default {
  props: ['type'],
  computed: {
    ...mapGetters(['sidebar', 'avatar', 'device', 'sidebarParentCur', 'menuList', 'enterprise']),
    webConfig() {
      return this.$store.getters['appConfig/configData'];
    },
    enterpriseTitle() {
      return this.enterprise?.enterprise_name || this.webConfig?.enterprise_name || '';
    },
    settingMenuIndex() {
      return this.menuList.findIndex(menu => menu.menu_path === settingMenuUniquePath)
    },
    settingMenuInfo() {
      if (this.settingMenuIndex === -1) return;
      return this.menuList[this.settingMenuIndex];
    },
    newMenuListArr() {
      return this.menuList.filter((val) => val.is_show !== 0)
    },
    activeMenus() {
      const route = this.$route
      const { meta, path } = route
      if (meta.activeMenu) {
        return meta.activeMenu
      }
      return path
    }
  },
  data() {
    return {
      settingMenuUniquePath,
      roterPre: roterPre,
      adminInfo: Cookies.set('AdminName'),
      levelList: null,
      parentcur: this.sidebarParentCur,
      showMax: 5,
      newMenuList: [],
      showMore: true,
    }
  },

  watch: {
    $route(route) {
      // if you go to the redirect page, do not update the breadcrumbs
      if (route.path.startsWith('/redirect/')) {
        return
      }
      this.getBreadcrumb()
    },
    enterpriseTitle: {
      handler(title) {
        if (title) {
          defaultSettings.title = title;
          document.title = title;
        }
      },
      immediate: true,
    },
  },
  created() {
    this.getShowMore = this.getShowMore.bind(this);
  },
  mounted() {
    this.getBreadcrumb()
    this.getShowMore()
    window.addEventListener('resize', this.getShowMore)
  },
  destroyed() {
    window.removeEventListener('resize', this.getShowMore)
  },
  methods: {
    menuTitle(title, englishTitle) {
      return translateSystemText(title, this, englishTitle)
    },
    getIconCss(cssName) {
      if (/^el-icon-/.test(cssName)) {
        return [cssName]
      } else {
        return ['iconfont', cssName]
      }
    },
    getShowMore() {
      const navHeight = 70
      const height = this.$refs.leftBox.clientHeight
      // console.log(height,88888888888)
      const len = this.newMenuListArr.length
      this.showMore = navHeight * len <= height
      this.showMax = Math.floor(height / navHeight) - 1
    },
    generateTitle,
    getBreadcrumb() {
      // only show routes with meta.title
      const matched = this.$route.matched.filter((item) => item.meta && item.meta.title)
      this.levelList = matched.filter((item) => item.meta && item.meta.title && item.meta.breadcrumb !== false)
      if (this.activeMenus === `${roterPre}/dashboard`) {
        return this.$store.commit('app/SET_PARENTCUR', 2)
      }
      this.filterArr(this.menuList, this.activeMenus)
    },
    handleSettingMenuClick() {
      this.handleParentCur(this.settingMenuIndex, 1, this.settingMenuInfo);
    },
    // 父级菜单切换
    handleParentCur(index, type, row) {
      if (type === 1) {
        const hasChildren = Array.isArray(row.children) && row.children.length > 0
        this.parentcur = index
        this.$store.commit('app/SET_CLICK_TAB', true)
        this.$store.commit('app/SET_PARENTCUR', this.parentcur)
        if (!hasChildren) {
          this.$store.commit('app/SET_CLICK_TAB', false)
          this.$router.push({
            path: row.menu_path
          })
        }
      } else {
        this.parentcur = this.menuList.findIndex(item => item.id === row.id)
        this.handleParentCur(this.parentcur, 1, row)

        // const len4 = this.menuList[this.showMax - 1]
        // this.menuList[this.showMax - 1] = row
        // this.menuList[this.showMax + index] = len4
        // this.$store.commit('user/SET_MENU_LIST', this.menuList)
        // this.$store.commit('app/SET_CLICK_TAB', true)
        // this.$store.commit('app/SET_PARENTCUR', this.parentcur)
        // if (!row.children) {
        //   this.$router.push({
        //     path: row.menu_path
        //   })

        // }


      }


    },
    filterArr(arr, url) {
      const findInTree = (nodes, target) => {
        for (const node of nodes) {
          if (node.menu_path === target) return true
          if (node.children && node.children.length > 0) {
            if (findInTree(node.children, target)) return true
          }
        }
        return false
      }

      for (let i = 0; i < arr.length; i++) {
        if (arr[i].menu_path === url || (arr[i].children && findInTree(arr[i].children, url))) {
          this.parentcur = i
          this.$store.commit('app/SET_PARENTCUR', i)
          return
        }
      }
    },
  }
}
</script>

<style lang="scss" scoped>
.fontSize {
  font-size: 14px !important;
}

//::v-deep .user-dropdown {
//  //left: 50px !important;
//}
//::v-deep .divBox {
//  padding-top: 0 !important;
//}
//.popover-user {
//  z-index: 200;
//  margin: -12px;
//}
.navbar {
  z-index: 70;
  overflow: hidden;
  position: fixed;
  left: 0;
  top: 65px;
  width:76px;
  bottom: 0;
 background: linear-gradient(172deg, rgba(255, 255, 255, 0.1) 0%, #ffffff 15%, #ffffff 100%);
//  background: linear-gradient( 173deg, rgba(255,255,255,0) 0%, rgba(255, 255, 255, 0) 7%, rgba(255,255,255,0.4752) 21%, #FFFFFF 100%);
  color: #303133;
  min-height: 670px;

  &::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 1px;
    background: linear-gradient(
      to bottom,
      rgba(225, 234, 243, 0) 0%,
      rgba(225, 234, 243, 0.72) 30%,
      rgba(225, 234, 243, 0)  80%,
      rgba(225, 234, 243, 0) 100%
    );
    pointer-events: none;
  }
}

.below-bar {
  position: absolute;
  left: 0;
  bottom: 0;
  width: 100%;
  padding: 0 10px 27px;

  .nav-item {
    width: 100%;
    height: 46px;
    background-color: transparent;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;

    i {
      font-size: 18px;
    }

    &:hover {
      background-color: #EAF0F6;
    }

    &.on {
      // background-color: #fff;

      i {
        color: #1890ff;
      }
    }
  }

}

.menu-box {
  ::v-deep .el-scrollbar__wrap {
    overflow-x: hidden;
  }

  width: 100%;
  height: calc(100% - 107px);
  min-height: 379px;

  .nav-box {
    flex: 1;
    display: flex;
    flex-wrap: wrap;

    .nav-item {
      position: relative;
      cursor: pointer;
      width: 64px;
      height: 66px;
      margin-left: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 4px;
      color: #303133;
      font-family: PingFang SC, PingFang SC;
font-weight: 400;
font-size: 13px;

      &.on {
        // background-color: #fff !important;
        border-radius: 8px;
        font-weight: 600;
        color: #1890ff !important;

        i {
          color: #1890ff;
          font-weight: normal;
        }
      }

      &:last-of-type {
        margin-bottom: 0;
      }

      &:hover {
        background-color: #EAF0F6 ;
        border-radius: 8px;
      }

      .nav-items {
        display: inline;
        text-align: center;
      }

      i,
      span {
        display: block;
        font-size: 13px;
      }

      i {
        font-size: 18px;
        color: #303133;
        margin-bottom: 4px;
      }
    }

    .popover-nav-item {
      margin-top: 10px;
    }
  }
}

.popover-content {
  display: flex;
  align-items: center;
  flex-wrap: wrap;

  .nav-item {
    position: relative;
    cursor: pointer;
    font-size: 13px;
    width: 64px;
    height: 66px;
    display: flex;
    align-items: center;
    justify-content: center;

    .nav-items {
      text-align: center;

      .iconfont {
        font-size: 20px;
        color: #1890ff;
      }

      span {
        display: block;
      }
    }
  }
}

.logo-wrapper {
     margin: 0 auto;
  width: 44px;
  height:43px;
  margin-top: 15px;
  margin-bottom: 16px;

  img {
    width: 44px;
    height: 43px;
    object-fit: cover;
  }
}

//.down-item {
//  text-align: center;
//  line-height: 40px;
//}


//.drop-config {
//  display: flex;
//  flex-direction: column;
//  padding: 18px 14px 14px 18px;
//}
//.drop-txt {
//  padding: 0 17px;
//  font-size: 14px;
//  color: #606266;
//  &:hover,
//  &:focus {
//    background-color: #e8f4ff;
//    color: #46a6ff;
//  }
//}
//.drop-body {
//  border-bottom: 1px solid #f5f5f5;
//}
//.pop-box {
//  padding: 6px 6px 0;
//  font-size: 14px;
//  color: #000;
//  .pop-item {
//    margin-bottom: 18px;
//    cursor: pointer;
//    &:last-child {
//      margin-bottom: 6px;
//    }
//  }
//}
.active {
  color: #1890ff;
}
</style>

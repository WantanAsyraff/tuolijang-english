<template>
  <view class="uni-indexed-list" ref="list" id="list">
    <!-- #ifdef APP-NVUE -->
    <list class="uni-indexed-list__scroll" scrollable="true" show-scrollbar="false">
      <cell v-for="(list, idx) in lists" :key="idx" :ref="getListItemId(idx)">
        <indexed-list-item
          :list="list"
          :loaded="loaded"
          :idx="idx"
          :onlyOne="onlyOne"
          :isChecked="isChecked"
          :showSelect="showSelect"
        ></indexed-list-item>
      </cell>
    </list>
    <!-- #endif -->

    <!-- #ifndef APP-NVUE -->
    <scroll-view :scroll-into-view="scrollViewId" class="uni-indexed-list__scroll" scroll-y :show-scrollbar="false">
      <view v-for="(list, idx) in lists" :key="idx" :id="getListItemId(idx)">
        <indexed-list-item
          :list="list"
          :loaded="loaded"
          :idx="idx"
          :onlyOne="onlyOne"
          :isChecked="isChecked"
          :showSelect="showSelect"
        ></indexed-list-item>
      </view>
    </scroll-view>
    <!-- #endif -->

    <view
      class="uni-indexed-list__menu"
      @touchmove.stop.prevent="touchMove"
      @touchend="touchEnd"
      @mousemove.stop.prevent="mousemove"
      @mouseleave.stop="mouseleave"
    >
      <view
        v-for="(list, key) in lists"
        :key="key"
        class="uni-indexed-list__menu-item"
        :data-index="key"
        :class="touchmoveIndex == key ? 'uni-indexed-list__menu--active' : ''"
        @touchstart.stop.prevent="onMenuItemTouchStart(key)"
        @mousedown.stop.prevent="onMenuItemMouseDown(key)"
      >
        <text class="uni-indexed-list__menu-text" :class="touchmoveIndex == key ? 'uni-indexed-list__menu-text--active' : ''">{{ list.key }}</text>
      </view>
    </view>
    <view v-if="touchmove" class="uni-indexed-list__alert-wrapper">
      <text class="uni-indexed-list__alert">{{ lists[touchmoveIndex].title }}</text>
    </view>
  </view>
</template>
<script>
import indexedListItem from './uni-indexed-list-item.vue'
// #ifdef APP-NVUE
const dom = weex.requireModule('dom')
// #endif
// #ifdef APP-PLUS
function throttle(func, delay) {
  var prev = Date.now()
  return function () {
    var context = this
    var args = arguments
    var now = Date.now()
    if (now - prev >= delay) {
      func.apply(context, args)
      prev = Date.now()
    }
  }
}

function touchMove(e) {
  let pageY = e.touches[0].pageY
  let index = this.getMenuIndexByPageY(pageY)
  if (this.touchmoveIndex === index) {
    return false
  }
  let item = this.lists[index]
  if (item) {
    this.scrollToIndex(index, false)
  }
}
const throttleTouchMove = throttle(touchMove, 40)
// #endif

/**
 * IndexedList 索引列表
 * @description 用于展示索引列表
 * @tutorial https://ext.dcloud.net.cn/plugin?id=375
 * @property {Boolean} showSelect = [true|false] 展示模式
 *   @value true 展示模式
 *   @value false 选择模式
 * @property {Object} options 索引列表需要的数据对象
 * @event {Function} click 点击列表事件 ，返回当前选择项的事件对象
 * @example <uni-indexed-list options="" showSelect="false" @click=""></uni-indexed-list>
 */
export default {
  name: 'UniIndexedList',
  components: {
    indexedListItem,
  },
  emits: ['click'],
  props: {
    options: {
      type: Array,
      default() {
        return []
      },
    },
    showSelect: {
      type: Boolean,
      default: false,
    },
    // 单选
    onlyOne: {
      type: Boolean,
      default: false,
    },
    isChecked: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      lists: [],
      winHeight: 0,
      winOffsetY: 0,
      touchmove: false,
      touchmoveIndex: -1,
      scrollViewId: '',
      touchmovable: true,
      loaded: false,
      isPC: false,
      scrollHeight: 500,
      listIdPrefix: `oa-member-indexed-list-${Math.random().toString(36).slice(2)}-`,
      menuItemRects: [],
    }
  },
  watch: {
    options: {
      handler: function () {
        this.setList()
      },
      deep: true,
    },
  },
  mounted() {
    // #ifdef H5
    this.isPC = this.IsPC()
    // #endif
    setTimeout(() => {
      this.setList()
    }, 50)
    setTimeout(() => {
      this.loaded = true
    }, 300)
  },
  methods: {
    // 更新滚动区域高度
    updateScrollHeight() {
      uni
        .createSelectorQuery()
        .in(this)
        .select('#list')
        .boundingClientRect((ret) => {
          if (ret) {
            this.scrollHeight = ret.height
            this.winOffsetY = ret.top
            this.winHeight = ret.height
          }
        })
        .exec()
      uni
        .createSelectorQuery()
        .in(this)
        .selectAll('.uni-indexed-list__menu-item')
        .boundingClientRect((rects) => {
          this.menuItemRects = Array.isArray(rects) ? rects : []
        })
        .exec()
    },
    refreshLayout() {
      this.$nextTick(() => {
        this.updateScrollHeight()
      })
    },
    setList() {
      let index = 0
      this.lists = []
      this.options.forEach((value, index) => {
        if (value.data.length === 0) {
          return
        }
        let indexBefore = index
        let items = value.data.map((item) => {
          let obj = {}
          obj['name'] = item.name
          obj['id'] = item.id
          obj['avatar'] = item.avatar
          obj['job'] = item.job
          obj['itemIndex'] = index
          index++
          return obj
        })
        this.lists.push({
          title: value.letter,
          key: value.letter,
          items: items,
          itemIndex: indexBefore,
        })
      })
      this.$nextTick(() => {
        this.updateScrollHeight()
      })
    },
    getMenuIndexByPageY(pageY) {
      if (this.lists.length === 0) {
        return -1
      }
      if (this.menuItemRects.length === 0) {
        return -1
      }
      const rectIndex = this.menuItemRects.findIndex((rect) => pageY >= rect.top && pageY <= rect.bottom)
      if (rectIndex > -1) {
        return rectIndex
      }
      const firstRect = this.menuItemRects[0]
      const lastRect = this.menuItemRects[this.menuItemRects.length - 1]
      if (firstRect && pageY < firstRect.top) {
        return 0
      }
      if (lastRect && pageY > lastRect.bottom) {
        return this.lists.length - 1
      }
      const index = this.menuItemRects.reduce((closestIndex, rect, currentIndex) => {
        const currentDistance = Math.abs(pageY - (rect.top + rect.bottom) / 2)
        const closestRect = this.menuItemRects[closestIndex]
        const closestDistance = Math.abs(pageY - (closestRect.top + closestRect.bottom) / 2)
        return currentDistance < closestDistance ? currentIndex : closestIndex
      }, 0)
      return Math.max(0, Math.min(index, this.lists.length - 1))
    },
    getListItemId(index) {
      return `${this.listIdPrefix}${index}`
    },
    scrollToIndex(index, animated = true) {
      const item = this.lists[index]
      if (!item) {
        return
      }
      const scrollViewId = this.getListItemId(index)
      this.touchmoveIndex = index
      // #ifndef APP-NVUE
      if (this.scrollViewId === scrollViewId) {
        this.scrollViewId = ''
        this.$nextTick(() => {
          this.scrollViewId = scrollViewId
        })
      } else {
        this.scrollViewId = scrollViewId
      }
      // #endif
      // #ifdef APP-NVUE
      dom.scrollToElement(this.$refs[scrollViewId][0], {
        animated,
      })
      // #endif
    },
    onMenuItemTouchStart(index) {
      this.touchmove = true
      this.scrollToIndex(index, false)
    },
    onMenuItemMouseDown(index) {
      if (!this.isPC) {
        return
      }
      this.touchmove = true
      this.scrollToIndex(index, false)
    },
    touchMove(e) {
      // #ifndef APP-PLUS
      let pageY = this.isPC ? e.pageY : e.touches[0].pageY
      let index = this.getMenuIndexByPageY(pageY)
      if (this.touchmoveIndex === index) {
        return false
      }
      let item = this.lists[index]
      if (item) {
        this.scrollToIndex(index, false)
      }
      // #endif
      // #ifdef APP-PLUS
      throttleTouchMove.call(this, e)
      // #endif
    },
    touchEnd() {
      this.touchmove = false
      // this.touchmoveIndex = -1
    },

    /**
     * 兼容 PC @tian
     */

    mousemove(e) {
      if (!this.isPC) return
      this.touchMove(e)
    },
    mouseleave(e) {
      if (!this.isPC) return
      this.touchEnd(e)
    },

    // #ifdef H5
    IsPC() {
      var userAgentInfo = typeof navigator !== 'undefined' ? navigator.userAgent : ''
      var Agents = ['Android', 'iPhone', 'SymbianOS', 'Windows Phone', 'iPad', 'iPod']
      var flag = true
      for (let v = 0; v < Agents.length - 1; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
          flag = false
          break
        }
      }
      return flag
    },
    // #endif
  },
}
</script>
<style lang="scss" scoped>
.uni-indexed-list {
  position: absolute;
  left: 0;
  top: 0;
  right: 0;
  bottom: 0;

  display: flex;
  flex-direction: row;
  height: 100%;
  overflow: hidden;
}

.uni-indexed-list__scroll {
  flex: 1;
  height: 100%;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.uni-indexed-list__scroll::-webkit-scrollbar {
  width: 0;
  height: 0;
  display: none;
}

.uni-indexed-list__menu {
  position: fixed;
  top: 350rpx;
  right: 0;
  // margin-top: 10px;
  width: 44px;
  /* #ifndef APP-NVUE */
  display: flex;
  /* #endif */
  //   background: #ffffff;
  flex-direction: column;
}

.uni-indexed-list__menu-item {
  /* #ifndef APP-NVUE */
  display: flex;
  /* #endif */
  flex: 1;
  align-items: center;
  justify-content: center;
  min-height: 16px;
  /* #ifdef H5 */
  cursor: pointer;
  /* #endif */
  margin-bottom: 0.3rem;
}

.uni-indexed-list__menu-text {
  text-align: center;
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 20rpx;
  color: #606266;
}

.uni-indexed-list__menu-text--active {
  border-radius: 16px;
  width: 16px;
  height: 16px;
  line-height: 16px;
  color: #1890ff;
  font-weight: bold;
}

.uni-indexed-list__alert-wrapper {
  position: absolute;
  left: 0;
  top: 0;
  right: 0;
  bottom: 0;
  /* #ifndef APP-NVUE */
  display: flex;
  /* #endif */
  flex-direction: row;
  align-items: center;
  justify-content: center;
}

.uni-indexed-list__alert {
  width: 80px;
  height: 80px;
  border-radius: 10px;
  text-align: center;
  line-height: 80px;
  font-size: 35px;
  color: #fff;
  background-color: rgba(0, 0, 0, 0.5);
}
</style>

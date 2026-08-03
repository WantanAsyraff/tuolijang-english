<template>
  <div>
    <el-popover placement="bottom" trigger="manual" v-model="showPopover" popper-class="time-popover">
      <div
        v-for="(item, index) in list.length > 0 ? list : crudList"
        :key="index"
        class="item-box"
        @click="rowFn(item)"
      >
        {{ $ts(item.label, item.label_en) }}
      </div>
      <template #reference>
        <div @click="showPopover = true">
          <slot>
            <el-button type="text" size="small" v-if="!icon">{{ $t("ui.settingEnterpriseNewsIndexBatchSettings") }}</el-button>
            <span v-else class="iconfont iconxitong-xitongshezhi-cebian"></span>
          </slot>
        </div>
      </template>
    </el-popover>
  </div>
</template>
<script>
import i18n from '@/lang'
export default {
  name: '',
  props: {
    icon: {
      type: Boolean,
      default: false
    },
    list: {
      type: Array,
      default: () => {
        return []
      }
    }
  },
  data() {
    return {
      showPopover: false,
      crudList: [
        {
          value: 0,
          label: i18n.t('ui.customerWeChatMassAddGroupPostingNotAllowed')
        },
        {
          value: 1,
          label: i18n.t('customer.meOnly')
        },
        {
          value: 5,
          label: i18n.t('customer.directSubordinates')
        },
        {
          value: 2,
          label: i18n.t('customer.thisDept')
        },

        {
          value: 4,
          label: i18n.t('customer.allData')
        }
      ]
    }
  },
  mounted() {
    window.addEventListener('click', this.handleClosePopover)
  },
  destroyed() {
    window.removeEventListener('click', this.handleClosePopover)
  },
  methods: {
    handleClosePopover(e) {
      if (this.$el.contains(e.target) || !this.showPopover) return
      this.showPopover = false
    },
    rowFn(item) {
      this.$emit('handClick', item)
      this.showPopover = false
    }
  }
}
</script>
<style scoped lang="scss">
.iconxitong-xitongshezhi-cebian {
  color: #c8c8c8;
  line-height: 23px;
  font-size: 12px;
  cursor: pointer;
  margin-left: 5px;
}

.item-box {
  cursor: pointer;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 13px;
  height: 32px;
  line-height: 32px;
  padding: 0 14px;
}

.item-box:hover {
  background: #f2f3f5;
}
</style>
<style>
.time-popover {
  padding: 0;
}
</style>

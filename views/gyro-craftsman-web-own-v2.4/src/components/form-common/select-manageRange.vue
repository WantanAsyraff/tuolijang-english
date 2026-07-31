<!-- @FileDescription: 下拉选择管理范围组件 -->
<template>
  <el-cascader
    style="width: 200px"
    size="small"
    v-model="frame_id"
    :options="frameTreeData"
    :props="{ checkStrictly: true, value: 'id', label: 'label' }"
    :placeholder='$ts("请选择管理范围")'
    filterable
    :show-all-levels="false"
    @change="changeFrame"
  ></el-cascader>
</template>

<script>
import { frameTreeApi } from '@/api/public'

export default {
  name: 'manageRange',
  props: {
    all: {
      type: String,
      default: 'all'
    },
    scopeFrames: {
      type: Array,
      default: () => {
        return []
      }
    }
  },
  created() {
    if (this.scopeFrames.length > 0) {
      this.frameTreeData = this.localizeFrameTree(this.scopeFrames)
    } else {
      this.getScopeFrame()
    }
  },
  data() {
    return {
      frameTreeData: [],
      frame_id: this.all
    }
  },
  methods: {
    localizeFrameTree(list) {
      if (!Array.isArray(list)) return []
      return list.map((item) => ({
        ...item,
        label: this.$ts(item.label),
        children: item.children ? this.localizeFrameTree(item.children) : item.children
      }))
    },
    changeFrame(e) {
      const id = e[e.length - 1]
      this.$emit('change', id)
    },
    getScopeFrame() {
      let data = {
        scope: 1
      }
      frameTreeApi(data).then((res) => {
        this.frameTreeData = this.localizeFrameTree(res.data)
      })
    },
    reset() {
      this.frame_id = this.all
    }
  }
}
</script>

<style lang="scss" scoped>
::v-deep .tips {
  font-size: 13px;
  color: #999;
  margin-bottom: 20px;
  margin-left: 75px;
}
.dialog-footer {
  padding-top: 20px;
  border-top: 1px solid #e6ebf5;
  text-align: right;
}
::v-deep .el-cascader {
  width: 100%;
}
</style>

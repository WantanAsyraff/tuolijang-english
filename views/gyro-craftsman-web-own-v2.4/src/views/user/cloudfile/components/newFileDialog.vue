<template>
<!--  新建文件或文件夹弹窗-->
<el-dialog :before-close="handleClose" :title="config.title" :visible.sync="dialogVisible" width="480px">
  <div class="mt15">
    <div v-if="config.command !== 5" class="current-dialog mb15">
      <span>{{ $t("ui.userCloudfileNewFileDialogFileType") }}</span>
      <el-select v-model="info.type" :placeholder="$t('ui.userCloudfileNewFileDialogPleaseSelectFileType')">
        <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value" />
      </el-select>
    </div>
    <div class="current-dialog mb15">
      <span>{{ $t("ui.userCloudfileNewFileDialogFileName") }}</span>
      <el-input
        v-model="info.name"
        :placeholder="config.command === 5 ? $t('ui.userCloudfileNewFileDialogEnterFolderName') : $t('ui.userCloudfileNewFileDialogEnterFileName')"
        maxlength="20"
        type="text"
      />
    </div>
  </div>
  <div slot="footer" class="dialog-footer">
    <el-button :loading="loading" size="small" type="primary" @click="handleAdd">{{ $t("ui.formCommonDialogFormOk") }}</el-button>
  </div>
</el-dialog>
</template>

<script>
import i18n from '@/lang'
import { folderCreateApi, folderMakeApi, folderSpaceEntCreateApi, folderSpaceEntMakeApi } from '@/api/cloud'
export default {
  name: 'MyFileDialog',
  props: {
    config: {
      type: Object,
      default: () => {
        return {}
      }
    }
  },
  data() {
    return {
      dialogVisible: false,
      info: {
        name: '',
        type: ''
      },
      options: [
        { label: i18n.t('file.document'), value: 'word' },
        { label: i18n.t('file.table'), value: 'excel' },
        { label: i18n.t('legacyScript.mindMap'), value: 'mindmap' }
        // { label: '幻灯片', value: 'ppt' }
      ],
      loading: false
    }
  },
  computed: {},
  watch: {
    config: {
      handler(nVal) {
        this.info.type = this.config.type
      },
      deep: true
    }
  },
  mounted() {},
  methods: {
    handleOpen() {
      this.dialogVisible = true
    },
    handleClose() {
      this.info.name = ''
      this.info.type = ''
      this.dialogVisible = false
    },
    handleAdd() {
      // 创建文件夹
      if (this.config.command === 5) {
        if (this.info.name === '') {
          this.$message.error(i18n.t('legacyScript.folderNameCannotBeEmpty'))
        } else {
          const data = {
            pid: this.config.pid,
            name: this.info.name
          }
          if (this.config.switch === 6) {
            // 企业空间文件夹创建
            this.loading = true
            folderSpaceEntMakeApi(this.config.spaceId, data)
              .then((err) => {
                this.handleClose()
                this.loading = false
                this.$emit('handleIsOK')
              })
              .catch((error) => {
                this.loading = false
              })
          } else {
            // 我的空间文件夹创建
            this.loading = true
            folderMakeApi(data)
              .then((err) => {
                this.handleClose()
                this.loading = false
                this.$emit('handleIsOK')
              })
              .catch((error) => {
                this.loading = false
              })
          }
        }
      } else {
        if (this.info.name === '') {
          this.$message.error(i18n.t('legacyScript.fileNameCannotBeEmpty'))
        } else {
          const data = {
            pid: this.config.pid,
            name: this.info.name,
            type: this.info.type
          }

          if (data.type === 'mindmap') {
            this.loading = true
            this.$emit('handleCreateXmindFile', {
              data,
              success: () => {
                this.handleClose()
                this.loading = false
                this.$emit('handleIsOK')
              },
              error: () => {
                this.loading = false
              }
            })
            return
          }

          if (this.config.switch === 6) {
            // 企业空间文件创建
            this.loading = true
            folderSpaceEntCreateApi(this.config.spaceId, data)
              .then((err) => {
                this.handleClose()
                this.loading = false
                this.$emit('handleIsOK')
              })
              .catch((error) => {
                this.loading = false
              })
          } else {
            this.loading = true
            // 我的空间文件创建
            folderCreateApi(data)
              .then((err) => {
                this.handleClose()
                this.loading = false
                this.$emit('handleIsOK')
              })
              .catch((error) => {
                this.loading = false
              })
          }
        }
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.current-dialog {
  display: flex;
  align-items: center;
  position: relative;
  span {
    width: 80px;
    text-align: right;
  }
  div {
    width: 80%;
  }
  .current-dialog-icon {
    position: absolute;
    right: 10px;
    width: auto;
    top: 12px;
  }
  ::v-deep .el-input__inner {
    text-align: left;
  }
}
</style>

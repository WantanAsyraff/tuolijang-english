<template>
<div class="material-input-container">
  <!-- 素材内容输入框 -->
  <div class="content-textarea-box">
    <el-input
      ref="contentTextRef"
      type="textarea"
      :placeholder="$('ui.customerWeChatMassMaterialContentEnterMaterialContent')"
      v-model="content"
      rows="8"
      resize="none"
      maxlength="1000"
      @input="contentChange"
    ></el-input>
    <div v-if="rightCustomerName" class="content-textarea-footer" @click="insertCustomerName">
      <i class="el-icon-plus"></i> {{ $("ui.customerWeChatMassMaterialContentInsertCustomerName") }}
    </div>
  </div>

  <!-- 已选文件列表 -->
  <div class="selected-files mt10 mb10">
    <el-popover placement="top-start" trigger="click">
      <div>
        <div class="flex" v-if="filteredTypeList.length > 0">
          <div
            v-for="(item, index) in filteredTypeList"
            :key="index"
            class="type-box"
            @click="typeFn(item)"
            :class="{ active: activeType == item.value }"
          >
            <div class="icon iconfont" :class="item.icon"></div>
            <span class="text">{{ item.label }}</span>
          </div>
        </div>
      </div>
      <div class="add" slot="reference"><i class="iconfont iconfujian1"></i>{{ $("ui.customerWeChatMassMaterialContentAddAttachment") }}</div>
    </el-popover>

    <div class="box mt10" v-if="uploadFileList.length > 0">
      <div v-for="(file, index) in uploadFileList" :key="index" class="item" @close="handleFileClose(index)">
        <div class="flex lh-center">
          <span class="iconfont iconxiaochengxu1" v-if="file.types === 'mini_program'"></span>
          <span class="iconfont iconlianjie1" v-else-if="file.types === 'link'"></span>
          <template v-else>
            <span class="file" v-if="file.file && toSrcIcon(file.file.name) !== 'img'">{{
              getFileTypeFn(file.file.name)
            }}</span>
            <img
              v-if="file.file && file.file.url && toSrcIcon(file.file.name) == 'img'"
              :src="file.file.url"
              alt=""
              class="img"
            />
          </template>
          <span v-if="['mini_program', 'link'].includes(file.types)" style="width: 230px" class="over-text"
            >{{ file.title }}
          </span>
          <div
            v-if="file.file && !['mini_program', 'link'].includes(file.types)"
            style="width: 180px"
            class="over-text"
          >
            {{ file.file.name }}
          </div>
        </div>
        <span class="el-icon-error" @click="delFn(file, index)"></span>
      </div>
    </div>
  </div>

  <!-- 添加网页/小程序 -->
  <el-dialog
    :title="activeType === 'link' ? $('ui.customerWeChatMassMaterialContentAddWebpageMessage') : $('ui.customerWeChatMassMaterialContentAddMiniProgramMessage')"
    :visible.sync="dialogTableVisible"
    :append-to-body="true"
    width="650px"
    :close-on-click-modal="false"
    @close="handleClose"
  >
    <el-form :model="form" label-width="auto" ref="form" :rules="rules">
      <!-- 网页 -->
      <template v-if="activeType === 'link'">
        <el-form-item :label="$('ui.customerQuickReplyAddReplyWebLink')" prop="link">
          <el-input :placeholder="$('ui.customerQuickReplyAddReplyStartWithHttpOrHttps')" v-model="form.link" size="small"> </el-input>
        </el-form-item>
      </template>
      <!-- 小程序 -->
      <template v-if="activeType === 'mini_program'">
        <el-form-item :label="$('ui.customerQuickReplyAddReplyMiniProgramTitle')" prop="title">
          <el-input v-model="form.title" size="small" :placeholder="$('ui.customerQuickReplyAddReplyPleaseEnterMiniProgramTitle')"> </el-input>
        </el-form-item>
        <el-form-item label="AppID：" prop="app_id">
          <el-input v-model="form.app_id" size="small" :placeholder="$('ui.customerQuickReplyAddReplyEnterTheMiniProgramAppIdLinkedToThe')">
          </el-input>
        </el-form-item>
        <el-form-item :label="$('ui.customerQuickReplyAddReplyPagePath')" prop="link">
          <el-input v-model="form.link" :placeholder="$('ui.customerQuickReplyAddReplyEnterAPagePathForExamplePageIndex')" size="small"> </el-input>
        </el-form-item>
        <el-form-item prop="replyType">
          <div slot="label"><span class="required">*</span>{{ $("ui.customerQuickReplyAddReplyMiniProgramCover") }}</div>
          <div class="pic" v-if="file && file.url">
            <img class="img" :src="file.url" />
            <div class="pic-upload">
              <div class="pic-upload-content">
                <i @click="handleAvatar(index)" class="el-icon-delete"></i>
              </div>
            </div>
          </div>

          <el-upload
            v-else
            class="upload-demo mr10"
            action="##"
            :headers="myHeaders"
            :show-file-list="false"
            accept="image/*"
            :http-request="uploadServerLog"
          >
            <div class="upload-btn">
              <span class="el-icon-plus"></span>
            </div>
          </el-upload>
          <div class="sort-tip">{{ $("ui.customerQuickReplyAddReplyImagesMustBe10MbOrSmallerJpgAnd") }}</div>
        </el-form-item>
      </template>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click="handleClose" size="small">{{ $("ui.xmindEditorNodeHyperlinkCancel") }}</el-button>
      <el-button type="primary" @click="handleConfirm" size="small" :loading="dialogLoading">{{ $("ui.xmindEditorNodeHyperlinkOk") }}</el-button>
    </div>
  </el-dialog>
  <!-- 上传文件、视频、文件 -->
  <el-upload
    class="offscreen"
    ref="upload"
    action="##"
    :headers="myHeaders"
    :show-file-list="false"
    :http-request="uploadServerLog"
  >
  </el-upload>
</div>
</template>
<script>
import { $ } from '@/lang'
import { getUploadKeysApi } from '@/api/public'
import SettingMer from '@/libs/settingMer'
import { getFileType, getFileExtension } from '@/libs/public'
import { uploader } from '@/utils/uploadCloud'
import { getUrlMetadataApi } from '@/api/weCom'
export default {
  name: 'MaterialInput',
  props: {
    rightCustomerName: {
      type: Boolean,
      default: false
    },
    types: {
      // types=2是朋友圈素材，只能上传图片/视频/网页
      type: [String, Number],
      default: () => {
        return 0
      }
    }
  },

  data() {
    return {
      dialogTableVisible: false,
      dialogLoading: false,
      content: '', // 素材内容
      uploadFileList: [], // 上传组件的文件列表，用于临时存储
      activeType: '',
      file: {},
      myHeaders: {
        authorization: 'Bearer ' + localStorage.getItem('token')
      },
      form: {
        title: '', // 标题
        info: '', // 摘要
        link: '', // 路径链接
        app_id: ''
      },
      rules: {
        title: [{ required: true, message: $('legacyScript.enterTitle'), trigger: 'blur' }],
        info: [{ required: true, message: $('legacyScript.pleaseEnterSummary'), trigger: 'blur' }],
        link: [{ required: true, message: $('legacyScript.pleaseEnterThePathLink'), trigger: 'blur' }],
        app_id: [{ required: true, message: $('legacyScript.pleaseEnterTheApplicationID'), trigger: 'blur' }]
      },
      acceptType: '', // 上传类型
      type: [
        {
          icon: 'icontupian4',
          label: $('file.picture'),
          value: 'image'
        },
        {
          icon: 'iconshipin1',
          label: $('legacyScript.video'),
          value: 'video'
        },
        {
          icon: 'iconwenjian4',
          label: $('ui.userCloudfileLayoutCloudfileLeftFile'),
          value: 'file'
        },
        {
          icon: 'iconwangye-01',
          label: $('legacyScript.webPage'),
          value: 'link'
        },
        {
          icon: 'iconxiaochengxu',
          label: $('ui.customerWeChatMassAddGroupPostingMiniProgram'),
          value: 'mini_program'
        }
      ]
    }
  },
  computed: {
    filteredTypeList() {
      const FILTER_TYPES = ['mini_program', 'file']
      const FILTER_CONDITION = 2
      if (!Array.isArray(this.type)) return []

      // 3. 简化条件逻辑
      return this.types == FILTER_CONDITION
        ? this.type.filter((item) => !FILTER_TYPES.includes(item.value))
        : [...this.type] // 浅拷贝：避免直接返回原数组导致意外修改
    }
  },

  methods: {
    toSrcIcon(name) {
      return getFileType(name)
    },
    getFileTypeFn(name) {
      return getFileExtension(name)
    },
    delFn(Item, index) {
      this.uploadFileList.splice(index, 1)
      this.$emit('getAttachData', this.uploadFileList)
    },
    getData(data) {
      this.content = data.content
      this.uploadFileList = data.attach || []
    },
    handleClose() {
      this.dialogTableVisible = false
      this.dialogLoading = false
      this.file = {}
      this.activeType = ''
      this.$refs.form.resetFields()
    },
    contentChange() {
      this.$emit('contentChange', this.content)
    },
    // 在光标处插入客户名称占位符
    insertCustomerName() {
      const tag = '{客户名称}'
      const input = this.$refs.contentTextRef && this.$refs.contentTextRef.$refs.textarea
      const text = this.content || ''
      let start = text.length

      if (input) {
        start = input.selectionStart
        const end = input.selectionEnd
        const newText = text.substring(0, start) + tag + text.substring(end)
        if (newText.length > 1000) {
          this.$message.warning($('legacyScript.contentLengthMustNotExceed1000Characters'))
          return
        }
        this.content = newText
      } else {
        const newText = text + tag
        if (newText.length > 1000) {
          this.$message.warning($('legacyScript.contentLengthMustNotExceed1000Characters'))
          return
        }
        this.content = newText
      }

      this.contentChange()
      this.$nextTick(() => {
        if (input) {
          input.focus()
          const pos = start + tag.length
          input.setSelectionRange(pos, pos)
        }
      })
    },
    handleConfirm() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          if (this.activeType === 'link') {
            // 网页消息：调用接口获取元信息
            this.dialogLoading = true
            getUrlMetadataApi({ url: this.form.link })
              .then((res) => {
                // 接收所有元信息
                const { title, description, cover_image } = res.data
                this.form.title = title || ''
                this.form.info = description || ''
                this.form.cover_url = cover_image || ''
                this.form.types = this.activeType
                this.uploadFileList.push(JSON.parse(JSON.stringify(this.form)))
                this.$emit('getAttachData', this.uploadFileList)
                this.handleClose()
              })
              .finally(() => {
                this.dialogLoading = false
              })
          } else {
            // 小程序消息：原有逻辑
            if (this.file) {
              this.form.file_id = this.file.id
            }
            if (this.activeType === 'mini_program' && !this.form.file_id) {
              return this.$message.error($('legacyScript.pleaseUploadTheMiniProgramCoverImage'))
            }
            this.form.types = this.activeType
            this.uploadFileList.push(JSON.parse(JSON.stringify(this.form)))
            this.activeType = ''
            this.$emit('getAttachData', this.uploadFileList)
            this.dialogTableVisible = false
          }
        }
      })
    },
    handleAvatar() {
      this.file = {}
    },

    // 上传文件方法
    async uploadServerLog(params) {
      try {
        // 参数校验：确保文件存在
        const file = params?.file
        if (!file) {
          this.$message.error($('legacyScript.noUploadedFileDetectedPleaseSelectAFileAgain'))
          return
        }

        if (['mini_program', 'image', 'link'].includes(this.activeType)) {
          if (file.size / 1024 / 1024 > 10) {
            this.$message.error($('legacyScript.imageSizeMustNotExceed10MB'))
            return false
          }
          const allowedMimeTypes = ['image/png', 'image/jpeg', 'image/jpg']
          const allowedExtensions = ['.png', '.jpg', '.jpeg']
          const fileExtension = '.' + file.name.split('.').pop()?.toLowerCase()
          // 检查 MIME 类型或文件后缀是否符合要求
          const isAllowed =
            allowedMimeTypes.includes(file.type) || (fileExtension && allowedExtensions.includes(fileExtension))

          if (!isAllowed) {
            this.$message.error($('legacyScript.pleaseUploadAnImageInPNGOrJPGFormat'))
            return false
          }
        }
        if (this.activeType === 'video') {
          if (file.size / 1024 / 1024 > 10) {
            this.$message.error($('legacyScript.videoSizeMustNotExceed10MB'))
            return false
          }
          const fileName = file.name.toLowerCase()
          const isMP4ByExtension = fileName.endsWith('.mp4') // 校验文件后缀
          const isMP4ByType = file.type === 'video/mp4' // 校验 MIME 类型

          // 双重校验：后缀或 MIME 类型任一不符合则拒绝
          if (!isMP4ByExtension || !isMP4ByType) {
            this.$message.error($('legacyScript.pleaseUploadAVideoInMP4Format'))
            return false
          }
        }

        if (this.activeType === 'file') {
          if (file.size / 1024 / 1024 > 200) {
            this.$message.error($('legacyScript.fileSizeMustNotExceed200MB'))
            return false
          }
        }

        let obj = {
          key: file.name,
          contentType: file.type
        }

        const uploadKeysRes = await getUploadKeysApi(obj)

        if (!uploadKeysRes?.data) {
          this.$message.error($('legacyScript.failedToRetrieveUploadConfigurationInvalidResponseFormat'))
        }

        const isLocalUpload = uploadKeysRes.data.type === 'local'
        const uploadUrl = isLocalUpload ? `work/media/upload` : `${SettingMer.https}/work/media/save`
        const uploadRes = await uploader(file, 0, {
          uploadRes: uploadKeysRes,
          url: uploadUrl
        })

        if (uploadRes?.data) {
          const { fileListname, name, size, url, id } = uploadRes.data
          let file = {
            types: this.activeType,
            file_id: id,
            file: {
              size,
              name,
              url,
              id
            }
          }
          if (['image', 'video', 'file'].includes(this.activeType)) {
            this.uploadFileList.push(file)
            this.activeType = ''
            this.$emit('getAttachData', this.uploadFileList)
          } else {
            this.file = { size, name, url, id }
          }
        } else {
          throw new Error($('上传成功，但未返回文件信息'))
        }
      } catch (error) {
        // 统一错误处理
        this.$message.closeAll()
        const errorMsg = error instanceof Error ? error.message : '文件上传失败，请重试'
        this.$message.error(errorMsg)
      }
    },
    typeFn(item) {
      this.activeType = item.value
      if (['image', 'video', 'file'].includes(item.value)) {
        if (item.value === 'image') {
          this.acceptType = 'image/*'
        } else if (item.value === 'video') {
          this.acceptType = 'video/*'
        } else {
          this.acceptType = ''
        }

        if (this.$refs.upload && this.$refs.upload.$el) {
          this.$refs.upload.$el.querySelector('.el-upload__input').click()
        }
      } else {
        this.dialogTableVisible = true
      }
    }
  }
}
</script>

<style scoped lang="scss">
.material-input-container {
  width: 100%;

  border: 1px solid #e4e7ed;
  padding: 0 12px;
  border-radius: 4px;
  ::v-deep .el-textarea__inner,
  .el-input__inner {
    margin-top: 10px;
    border: none;
    padding: 0;
    border-bottom: none;
  }
}

.content-textarea-box {
  position: relative;
  border-bottom: 1px solid #f4f5f7;

  .content-textarea-footer {
    padding: 4px 0 8px;
    font-size: 12px;
    color: #1890ff;
    cursor: pointer;

    position: absolute;
    right: 0;
    bottom: 0;

    .el-icon-plus {
      font-size: 12px;
    }
  }
}
.add {
  cursor: pointer;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 12px;
  color: #282828;
  .iconfujian1 {
    font-size: 12px;
    margin-right: 4px;
  }
}

.type-box {
  cursor: pointer;
  width: 58px;
  height: 58px;
  padding: 10px 0;
  background: #f9f9f9;
  color: #606266;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  margin-right: 14px;
  border-radius: 6px;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  .text {
    font-size: 12px;
    margin-top: 6px;
  }
  .icon {
    flex: 1;

    font-size: 20px;
  }
}
.active {
  color: #1890ff;
  background: #f3f8fe;
}
.box {
  display: flex;
  flex-wrap: wrap;
}
.item {
  width: calc(50% - 10px);
  height: 30px;
  background: #f7f7f7;
  border-radius: 4px 4px 4px 4px;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 12px;
  color: #606266;
  margin-bottom: 10px;
  line-height: 30px;
  padding-right: 12px;
  padding-left: 4px;
  display: flex;
  margin-right: 10px;

  justify-content: space-between;
  align-items: center;
  .el-icon-error {
    cursor: pointer;
    font-size: 12px;
    color: #ccc;
  }
}
.file {
  display: flex;
  width: 23px;
  height: 22px;
  background: url('../../../../assets/images/cloud/file-box.png') no-repeat;
  background-size: 23px 22px;
  color: #fff !important;
  justify-content: center;
  line-height: 22px;
  font-size: 11px;
  margin-right: 4px;
}
.img {
  width: 23px;
  height: 22px;
  border-radius: 4px;
  margin-right: 4px;
}
.upload-btn {
  width: 58px;
  height: 58px;
  line-height: 58px;
  text-align: center;
  border: 1px dashed #dcdfe6;
  border-radius: 6px;
  .el-icon-plus {
    font-size: 18px;
  }
}
.pic {
  width: 58px;
  height: 58px;
  border-radius: 6px;
  position: relative;

  img {
    border-radius: 6px;
    width: 100%;
    height: 100%;
  }
  &:hover {
    .pic-upload {
      display: block;
    }
  }
  .pic-upload {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    right: 0;
    display: none;
    border-radius: 6px;
    background-color: rgba(0, 0, 0, 0.6);
    .pic-upload-content {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      i {
        color: #fff;
        font-size: 18px;
        cursor: pointer;
        margin-right: 10px;
        &:last-of-type {
          margin-right: 0;
        }
      }
    }
  }
}
.sort-tip {
  font-size: 12px;
  color: #909399;
  height: 14px;
  margin-top: 4px;
}
.iconlianjie1 {
  font-size: 18px;
  color: #1890ff;
}
.iconxiaochengxu1 {
  font-size: 18px;
  color: #19be6b;
  margin-right: 4px;
}
.required {
  color: #f56c6c;
  margin-right: 4px;
}
.offscreen {
  position: absolute;
  top: -9999px;
  left: -9999px;
  width: 1px;
  height: 1px;
  overflow: hidden;
}
</style>

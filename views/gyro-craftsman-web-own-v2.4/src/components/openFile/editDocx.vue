<template>
  <div>
    <div class="toolbar">
      <Toolbar :defaultConfig="toolbarConfig" :editor="editor" :mode="mode" />
    </div>
    <div class="main">
      <Editor
        v-model="vHtml"
        :defaultConfig="editorConfig"
        :mode="mode"
        style="overflow-y: hidden; margin-top: 20px"
        @onCreated="onCreated"
        @customPaste="onCustomPaste"
      />
    </div>
  </div>
</template>

<script>
import { $ } from '@/lang'
import '@wangeditor-next/editor/dist/css/style.css'
import { Editor, Toolbar } from '@wangeditor-next/editor-for-vue2'
import { uploader } from '@/utils/uploadCloud'
import { normalizePastedTableHtml } from '@/utils/wangeditor/tablePaste'
import mammoth from 'mammoth'
import { asBlob } from 'html-docx-js-typescript'
import { saveAs } from 'file-saver'
import { fileUpload } from '@/api/public'
export default {
  name: 'fileEdit',
  props: {
    url: {
      type: String,
      default: ''
    },
    fid: {
      type: String,
      default: ''
    },
    file: {
      type: Object,
      default: () => ({})
    }
  },
  components: {
    Editor,
    Toolbar
  },
  data() {
    return {
      vHtml: '',
      editor: null,
      toolbarConfig: {},
      editorConfig: { MENU_CONF: {}, placeholder: $('legacyScript.enterContent') },
      mode: 'default' // or 'simple'
    }
  },
  created() {
    let that = this
    that.editorConfig.MENU_CONF['uploadImage'] = {
      customUpload(file, insertFn) {
        let options = {
          way: 2,
          relation_type: '',
          relation_id: 0,
          eid: 0
        }
        uploader(file, 1, options).then((res) => {
          insertFn(res.data.url, '图片上传', res.data.url)
        })
      }
    }

    that.toolbarConfig.excludeKeys = ['fullScreen', 'insertVideo', 'uploadVideo', 'group-video']

    // that.toolbarConfig.insertKeys = {
    //   index: 2, // 插入的位置，基于当前的 toolbarKeys
    //   keys: ['menu-key1', 'menu-key2']
    // }
  },
  mounted() {
    const xhr = new XMLHttpRequest()
    xhr.open('get', this.$processResourceUrl(this.url), true)
    xhr.responseType = 'arraybuffer'
    xhr.onload = () => {
      if (xhr.status == 200) {
        mammoth
          .convertToHtml(
            { arrayBuffer: new Uint8Array(xhr.response) },
            {
              transformImage: function (image) {
                return image.read('base64').then(function (imageBuffer) {
                  // 获取Word中的图片尺寸
                  const width = image.width
                  const height = image.height

                  // 生成包含尺寸信息的img标签
                  return `<img src="data:${image.contentType};base64,${imageBuffer}" 
                 style="width: ${width}px; height: ${height}px;"
                 alt="文档图片">`
                })
              }
            }
          )
          .then((resultObject) => {
            this.$nextTick(() => {
              this.vHtml = resultObject.value
            })
          })
      }
    }
    xhr.send()
  },
  methods: {
    onCreated(editor) {
      this.editor = Object.seal(editor) // 一定要用 Object.seal() ，否则会报错
    },

    onCustomPaste(editor, event, done) {
      try {
        const clipboardData = event && event.clipboardData
        if (!clipboardData) return done(true)

        const html = clipboardData.getData('text/html')
        if (!html || !/<table[\s>]/i.test(html)) return done(true)

        const { html: cleanHtml, changed } = normalizePastedTableHtml(html)
        if (!changed) return done(true)

        editor.insertHtml(cleanHtml)
        return done(false)
      } catch (e) {
        return done(true)
      }
    },

    wordOption(type) {
      const innerHtml = this.vHtml

        // 替换strong标签
        .replace(/<strong>/g, '<b>')
        .replace(/<\/strong>/g, '</b>')
        // 处理背景色标记
        .replace(/<mark([^>]*)/g, '<span$1 style="background-color: yellow;"')
        .replace(/<\/mark>/g, '</span>')
        // 处理图片大小 - 关键优化部分
        .replace(/<img([^>]+)>/g, (match, attributes) => {
          // 提取现有宽度和高度属性
          const widthMatch = attributes.match(/width="([^"]*)"/i)
          const heightMatch = attributes.match(/height="([^"]*)"/i)

          // 构建基础样式
          let styles = []

          // 如果有宽度属性，使用它；否则设置最大宽度
          if (widthMatch && widthMatch[1]) {
            styles.push(`width: ${widthMatch[1]};`)
          } else {
            styles.push('max-width: 100%;')
          }

          // 如果有高度属性，使用它；否则保持比例
          if (heightMatch && heightMatch[1]) {
            styles.push(`height: ${heightMatch[1]};`)
          } else {
            styles.push('height: auto;')
          }

          // 检查是否已有style属性
          if (/style=/i.test(attributes)) {
            // 在现有样式前添加我们的样式
            return `<img ${attributes.replace(/style="/i, `style="${styles.join(' ')} `)}>`
          } else {
            // 添加新的style属性
            return `<img ${attributes} style="${styles.join(' ')}">`
          }
        })
      asBlob(innerHtml)
        .then((blobData) => {
          if (type === 'export') {
            // 导出为文件
            saveAs(blobData, `${this.file.name}.${this.file.file_ext}`)
          } else {
            // 上传文件内容
            const uploadData = {
              content: innerHtml,
              is_file: 1
            }

            fileUpload(this.fid, this.file.id, uploadData)
              .then((res) => {
                if (res.status === 200) {
                }
              })
              .catch((error) => {})
              .finally(() => {
                this.$emit('closeLoading')
              })
          }
        })
        .catch((error) => {
          console.error($('legacyScript.failedToConvertToBlob'), error)
          this.$emit('closeLoading') // 确保在错误情况下也关闭加载状态
        })
    },

    beforeDestroy() {
      const editor = this.editor
      if (editor == null) return
      editor.destroy() // 组件销毁时，及时销毁编辑器
    }
  }
}
</script>
<style lang="scss" scoped>
.toolbar {
  position: fixed;
  width: 100%;
  z-index: 99;
}
::v-deep .w-e-bar-show {
  display: flex;
  justify-content: center;
}

.main {
  margin-top: 60px;
  width: 70%;
  margin: 0 auto;
  padding-top: 20px;
}
::v-deep .w-e-text-container {
  margin-top: 30px;
  min-height: calc(100vh - 110px) !important;
  // overflow-y: auto;
  border: none !important;
  padding: 40px 60px;
}
::v-deep .w-e-text-placeholder {
  padding: 40px 60px;
}
</style>

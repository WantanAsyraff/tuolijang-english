<template>
  <div class="pdf-preview-container">
    <!-- PDF渲染区域 -->
    <div class="pdf-content" ref="pdfContent"></div>
  </div>
</template>

<script>
import i18n from '@/lang'
import * as PDFJS from 'pdfjs-dist'
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker.entry'

export default {
  name: 'PreviewPdf',
  props: {
    url: {
      type: String,
      required: true,
      validator: (val) => val.trim() !== '' // 校验非空
    },
    // 初始缩放比例（可选）
    initialScale: {
      type: Number,
      default: 1.5
    }
  },
  data() {
    return {
      pdfDoc: null, // PDF文档实例
      totalPages: 0, // 总页数
      scale: this.initialScale, // 当前缩放比例
      isRendering: false, // 是否正在渲染（防止重复渲染）
      renderedPages: [] // 已渲染的页面集合
    }
  },
  created() {
    // 初始化PDF Worker（关键：解决GlobalWorkerOptions undefined问题）
    PDFJS.GlobalWorkerOptions.workerSrc = pdfjsWorker
  },
  watch: {
    // 监听PDF地址变化，重新加载
    url: {
      immediate: true,
      handler() {
        this.loadPdf()
      }
    },
    // 监听缩放比例变化，重新渲染所有页面
    scale() {
      this.renderAllPages()
    }
  },
  mounted() {
    // 监听PDF地址变化，重新加载
    setTimeout(() => {
      this.loadPdf()
    }, 200)
 
  },
  methods: {
    /**
     * 加载PDF文档
     */
    async loadPdf() {
      // this.url='/uploads/cloud/2025/12/11/c38a9202512111505078642.pdf'
      this.totalPages = 0
      this.isRendering = false
      this.renderedPages = []

      this.$refs.pdfContent.innerHTML = '<div class="pdf-loading">PDF加载中...</div>'

      if (!this.url) {
        this.$refs.pdfContent.innerHTML = '<div class="pdf-error">PDF地址不能为空</div>'
        return
      }

      try {
        // 配置PDF加载参数，解决跨域问题
        const loadingParams = {
          url: this.url,
          cMapUrl: 'cmaps/',  // 使用绝对路径
          cMapPacked: true,
          withCredentials: false, // 启用跨域凭证

        }

        // 获取PDF文档实例
        const loadingTask = PDFJS.getDocument(loadingParams)
        this.pdfDoc = await loadingTask.promise
        this.totalPages = this.pdfDoc.numPages
        // 渲染所有页面
        await this.renderAllPages()
      } catch (error) {
        console.error(i18n.t('legacyScript.failedToLoadPDF'), error)
        this.$emit('pdf-error', error)

        let errorMsg = 'PDF加载失败，请检查文件地址或网络'
        if (error.name === 'MissingPDFException') {
          errorMsg = 'PDF文件不存在或已损坏'
        } else if (error.name === 'InvalidPDFException') {
          errorMsg = 'PDF文件格式无效'
        } else if (error.name === 'PDFNetworkError') {
          errorMsg = '网络错误，请检查网络连接或跨域设置'
        } else if (error.name === 'TimeoutError') {
          errorMsg = 'PDF加载超时，请稍后重试'
        } else if (error.message) {
          errorMsg = `PDF加载失败: ${error.message}`
        }
        
        this.$refs.pdfContent.innerHTML = `<div class="pdf-error">${errorMsg}</div>`
      }
    },

    /**
     * 渲染所有页面
     */
    async renderAllPages() {
      if (this.isRendering || !this.pdfDoc) return

      this.isRendering = true
      try {
        // 清空渲染区域
        this.$refs.pdfContent.innerHTML = ''
        this.renderedPages = []

        // 渲染每一页
        for (let pageNum = 1; pageNum <= this.totalPages; pageNum++) {
          const page = await this.pdfDoc.getPage(pageNum)
          await this.renderPage(page, pageNum)
        }
      } catch (error) {
        console.error(i18n.t('legacyScript.failedToRenderPDF'), error)
        this.$emit('pdf-error', error)
      } finally {
        this.isRendering = false
      }
    },

    /**
     * 渲染单页
     */
    async renderPage(page, pageNum) {
      // 获取视口（根据缩放比例调整）
      const viewport = page.getViewport({ scale: this.scale })

      // 创建canvas用于渲染PDF
      const canvas = document.createElement('canvas')
      const ctx = canvas.getContext('2d')
      canvas.width = viewport.width
      canvas.height = viewport.height
      canvas.className = 'pdf-page-canvas'
      canvas.dataset.pageNum = pageNum

      // 添加到渲染区域
      this.$refs.pdfContent.appendChild(canvas)
      this.renderedPages.push(canvas)

      // 渲染PDF页面
      await page.render({
        canvasContext: ctx,
        viewport: viewport
      }).promise
    }
  }
}
</script>

<style scoped>
.pdf-preview-container {
  width: 100%;
  min-height: 600px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  padding: 16px;
  box-sizing: border-box;
}

.pdf-content {
  flex: 1;
  overflow: auto;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  padding: 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.pdf-page-canvas {
  margin-bottom: 10px;
  border: 1px solid #f0f0f0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.pdf-toolbar {
  display: flex;
  align-items: center;
  gap: 12px;
  justify-content: center;
  flex-wrap: wrap;
}

.pdf-btn {
  padding: 6px 12px;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  background: #ffffff;
  cursor: pointer;
  transition: all 0.2s;
}

.pdf-btn:hover:not(:disabled) {
  background: #f3f4f6;
  border-color: #9ca3af;
}

.pdf-btn:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.pdf-page-info {
  color: #374151;
  font-size: 14px;
}

.pdf-error {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #ef4444;
  font-size: 16px;
}
</style>

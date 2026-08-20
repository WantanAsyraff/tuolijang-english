import { $ } from '@/lang'
import { Message, MessageBox, Alert, Notification } from 'element-ui'
import { normalizeNotificationInput } from '@/lang/notification-text'

const normalizeTip = (input) => normalizeNotificationInput(input, $)
const withDefaultDuration = (input) => {
  const normalized = normalizeTip(input)
  if (normalized && typeof normalized === 'object' && !Array.isArray(normalized)) {
    return {
      ...normalized,
      duration: normalized.duration === undefined ? 5000 : normalized.duration
    }
  }
  return { message: normalized, duration: 5000 }
}
class msgTips {
  static getInstance() {
    return new msgTips()
  }
  // 消息提示
  msg(msg) {
    Message.info(withDefaultDuration(msg))
  }

  // 错误消息
  msgError(msg) {
    Message.error(withDefaultDuration(msg))
  }

  // 成功消息
  msgSuccess(msg) {
    Message.success(withDefaultDuration(msg))
  }

  // 警告消息
  msgWarning(msg) {
    Message.warning(withDefaultDuration(msg))
  }

  // 弹出提示
  alert(msg) {
    Alert($(msg), $('系统提示'))
  }

  // 通知提示
  notify(msg) {
    Notification.info(normalizeTip(msg))
  }

  // 错误通知
  notifyError(msg) {
    Notification.error(normalizeTip(msg))
  }

  // 成功通知
  notifySuccess(msg) {
    Notification.success(normalizeTip(msg))
  }

  // 警告通知
  notifyWarning(msg) {
    Notification.warning(normalizeTip(msg))
  }

  // 确认窗体
  confirm(options) {
    return new Promise((resolve, reject) => {
      MessageBox.confirm($(options.message), $(options.title || '温馨提示'), {
        confirmButtonText: $(options.confirmButtonText || '确定'),
        confirmButtonClass: options.confirmButtonClass || '',
        cancelButtonText: $(options.cancelButtonText || '取消'),
        cancelButtonClass: options.cancelButtonClass || '',
        type: options.type || 'warning'
      })
        .then(() => {
          resolve()
        })
        .catch(() => undefined)
    })
  }

  // 提交内容
  // prompt(content: string, title: string, options?: ElMessageBoxOptions) {
  //   return this.$prompt(content, title, {
  //     confirmButtonText: '确定',
  //     cancelButtonText: '取消',
  //     ...options
  //   })
  // }
}

const Tips = msgTips.getInstance()
export default Tips

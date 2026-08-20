import { $ } from '@/lang'
import noticeHandle from '@/libs/noticeHandle'
import ElementUI from 'element-ui'
import { roterPre } from '@/settings'
import { messageUnreadCountApi } from '@/api/public'
import store from '@/store'
import { noticeMessageReadApi } from '@/api/user'
import { EventBus } from '@/libs/bus'
import { getMenus } from '@/utils/auth'
import { broadcastMenuInvalidated, clearMenuCache } from '@/utils/menu-cache'
import SettingMer from '@/libs/settingMer'
import { normalizeNotificationInput } from '@/lang/notification-text'
let limitConnect = 40 // 断线重连次数
let timeToken = ''
import audioUrl from '../assets/audio/tip.mp3'
const audioTip = new Audio(audioUrl)
const notifications = {}
let permissionRefreshTask = null
function escapeHtml(value) {
  return String(value == null ? '' : value).replace(/[&<>"']/g, (character) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;'
  }[character]))
}

function normalizeSocketNotification(input) {
  const normalized = normalizeNotificationInput(input || {}, $)
  const payload = normalized && typeof normalized === 'object' && !Array.isArray(normalized)
    ? normalized
    : { message: normalized }

  return {
    ...payload,
    title: typeof payload.title === 'string' ? payload.title : '',
    message: typeof payload.message === 'string' ? payload.message : '',
    image: typeof payload.image === 'string' ? payload.image : '',
    url: typeof payload.url === 'string' ? payload.url : '',
    buttons: Array.isArray(payload.buttons) ? payload.buttons : []
  }
}

// 提取消息内容生成函数
function generateMessageContent(message) {
  const imgShow = escapeHtml(message.image)
  let content = `
    <a onclick="onNotice()" href="${escapeHtml(roterPre + message.url)}">
      <div class='el-row display-align'>
        <div class='el-col el-col-24 left' ${imgShow == '' ? 'style="display:none"' : 'style="display:block"'}>
           <img src='${imgShow}' alt='' style="width:55px;height:55px" >
        </div>
        <div ${imgShow == '' ? 'class="el-col el-col-24 right width100"' : 'class="el-col el-col-24 right"'}>
          <p class='title over-text'>${escapeHtml(message.title)}</p>
          <p class='caption over-text2'>${escapeHtml(message.message)}</p>
        </div>
      </div>
    </a>`

  if (message.buttons.length > 0) {
    content += message.buttons
      .map(
        (value, i) => `
      <div class='text-right'>
        <button type="button" class="el-button el-button--text el-button--small" onclick="onConfirm(${i})">
          <span>${escapeHtml(value && (value.title || value.label || value.text))}</span>
        </button>
      </div>`
      )
      .join('')
  }

  return content
}

// 提取通知显示函数
function showNotification(input) {
  const message = normalizeSocketNotification(input)
  const notify = ElementUI.Notification({
    title: $('\u6d88\u606f'),
    dangerouslyUseHTMLString: true,
    message: generateMessageContent(message),
    duration: 10000,
    offset: 60,
    iconClass: 'iconfont iconxiaoxi',
    customClass: 'message-socket'
  })

  notifications[message.uniqid] = notify
  getMessage()

  // 设置全局回调函数
  window.onConfirm = (index) => {
    const item = message.buttons[index]
    EventBus.$emit('messageSuccess', { item, detail: message })
    closeNotification(message)
  }

  window.onCancel = () => {
    noticeHandle(message, 0)
    closeNotification(message)
  }

  window.onNotice = () => closeNotification(message)
}

// 提取关闭通知函数
function closeNotification(message) {
  if (notifications[message.uniqid]) {
    notifications[message.uniqid].close()
    delete notifications[message.uniqid]
    batchMessageRead(1, { ids: [message.id] })
  }
}

// 主函数优化
function notice(token) {
  const wsBaseUrl = (SettingMer.wsSocketUrl || `${window.location.protocol === 'https:' ? 'wss:' : 'ws:'}//${window.location.host}`).replace(/\/$/, '')
  const wsUrl = `${wsBaseUrl}/ws?type=ent&token=${token}`
  const ws = new WebSocket(wsUrl)
  timeToken = token
  let pingInterval
  let manuallyClosed = false

  const send = (type, data) => ws.send(JSON.stringify({ type, data }))

  ws.onopen = () => {
    limitConnect = 40
    getMessage()
    pingInterval = setInterval(() => send('ping'), 10000)
  }

  ws.onmessage = (res) => {
    const data = parseSocketMessage(res.data)
    if (!data) {
      return
    }
    const type = data.type || data.event
    if (type === 'notice') {
      const playback = audioTip.play()
      if (playback && typeof playback.catch === 'function') {
        playback.catch(() => undefined)
      }
      showNotification(data.data)
    } else if (type === 'permission_changed') {
      handlePermissionChanged(data.data)
    }
  }

  ws.onclose = (e) => {
    EventBus.$emit('close', e)
    clearInterval(pingInterval)
    if (manuallyClosed) {
      return
    }
    reconnect()
  }

  ws.onerror = () => {
    // 部分浏览器/网络场景 onerror 后不一定触发 onclose,需主动清理避免 pingInterval 残留
    clearInterval(pingInterval)
    if (manuallyClosed) {
      return
    }
    reconnect()
  }

  return () => {
    manuallyClosed = true
    ws.close()
  }
}

function parseSocketMessage(message) {
  if (!message) {
    return null
  }
  try {
    return typeof message === 'string' ? JSON.parse(message) : message
  } catch (e) {
    console.warn('webSocket message parse failed', message)
    return null
  }
}

function handlePermissionChanged(payload = {}) {
  if (permissionRefreshTask) {
    return permissionRefreshTask
  }

  console.info('permission changed, refreshing menus', payload)
  clearMenuCache()
  permissionRefreshTask = getMenus({ force: true, checkCurrentRoute: true })
    .then(() => {
      broadcastMenuInvalidated()
    })
    .finally(() => {
      permissionRefreshTask = null
    })

  return permissionRefreshTask
}

// 重连
function reconnect() {
  // lockReconnect加锁，防止onclose、onerror两次重连
  if (limitConnect > 0) {
    if (!localStorage.getItem('lockReconnect')) {
      localStorage.setItem('lockReconnect', 1)
      limitConnect--
      console.log('第' + (40 - limitConnect + 1) + '次重连')
      // 进行重连
      setTimeout(function () {
        notice(timeToken)
        localStorage.removeItem('lockReconnect')
      }, 10000)
    }
  } else {
    console.log('webSocket连接已超时')
  }
}
// 批量标记未已读
function batchMessageRead(status, data) {
  noticeMessageReadApi(status, data)
    .then((res) => {
      getMessage()
    })
    .catch((error) => {
      // console.log(error.message);
    })
}
// 消息数量
function getMessage() {
  messageUnreadCountApi()
    .then((res) => {
      const num = res.data.messageNum ? res.data.messageNum : 0
      store.commit('user/SET_MESSAGE', num)
    })
    .catch((error) => {
      ElementUI.Message({
        message: $(error && error.message ? error.message : error),
        type: 'error'
      })
    })
}

export default notice

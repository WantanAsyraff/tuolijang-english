<template>
  <BaseContainer class="base-container">
    <view class="content">
      <!-- 搜索 -->

      <view class="default-search">
        <uni-search-bar
          @blur="searchData"
          :placeholder="$t('ui.customerListSelectedLabelSearch')"
          :focus="true"
          cancelButton="none"
          bgColor="#F0F1F5"
          v-model="where.name"
          @clear="clearSearchData"
        >
        </uni-search-bar>
      </view>

      <!-- 素材类型 -->
      <view class="tab-bar">
        <view
          v-for="(tab, tabIndex) in tabList"
          :key="tabIndex"
          :class="['tab-item', { active: currentTab == tab.value }]"
          @click="handleTabClick(tab)"
        >
          {{ tab.label }}
        </view>
      </view>
      <view class="quick-reply-page">
        <!-- 左侧分类栏 -->
        <view class="left-category">
          <view
            v-for="(item, index) in categoryList"
            :key="index"
            :class="['category-item', { active: currentCategory === item.id }]"
            @click="handleCategoryClick(item)"
          >
            {{ item.name }}
          </view>
        </view>

        <!-- 右侧内容区 -->
        <view class="right-content">
          <!-- 内容列表 -->
          <view class="content-list" v-if="contentList.length > 0">
            <view
              v-for="(item, itemIndex) in contentList"
              :key="itemIndex"
              class="content-item"
              :class="item.types == 'text' ? 'text-item' : ''"
              @click.stop="openReply(item)"
            >
              <!-- 文本/链接 -->
              <view v-if="item.types === 'text'" class="flex">
                <view class="text-content-wrapper">
                  <text class="text-content">{{ item.content }}</text>
                </view>
                <view class="text-box">
                  <text @click.stop="openReply(item)" class="action-btn">{{ $t('ui.customerQuickReplyIndexSelect') }}</text>

                  <text @click.stop="handleEdit(item)" class="action-btn">{{ $t('ui.customerQuickReplyIndexEdit') }}</text>

                  <text @click.stop="handleDelete(item)" class="action-btn delete-btn">{{ $t('ui.examineFormApprovalBillDelete') }}</text>
                </view>
              </view>
              <view v-if="item.types === 'link'" class="link-flex">
                <view class="file-name link-ellipsis">{{ item.link }}</view>
                <image v-if="item.file_url" :src="item.file_url" class="images" mode=""></image>
              </view>
              <!-- Word/PPT 文件 -->
              <view v-else-if="item.types === 'file'" class="flex">
                <text class="file-name">{{ item.file.file_name }}</text>
                <image v-if="['xls', 'xlsx'].includes(item.file.file_ext)" :src="getImageUrl('excel.png')" class="file-icon" mode="" />
                <image v-else-if="item.file.file_ext == 'pdf'" :src="getImageUrl('pdf.png')" class="file-icon" mode="" />
                <image v-else-if="item.file.file_ext == 'pptx'" :src="getImageUrl('ppt.png')" class="file-icon" mode="" />
                <image v-else-if="['doc', 'docx', 'txt'].includes(item.file.file_ext)" :src="getImageUrl('word.png')" class="file-icon" mode="" />
                <image v-else-if="['jpg', 'png'].includes(item.file.file_ext)" :src="item.file.file_url" class="file-icon" mode="" />
              </view>

              <!-- 图片 -->
              <view v-else-if="item.types === 'image'" style="height: 128rpx">
                <image :src="item.file_url" class="images" mode="" @click="preview(item)"></image>
              </view>

              <!-- 视频 -->
              <view v-else-if="item.types === 'video'" class="video">
                <!-- @fullscreenchange="screenChange" -->
                <video :src="item.file_url" class="video-content"></video>
                <view class="mask" @click.stop="handleWrapperClick(item)"></view>
              </view>
              <view v-else-if="item.types === 'mini_program'">
                <view class="flex">
                  <text class="file-name over-text2">{{ item.title }}</text>
                  <image :src="item.file_url" class="images" mode=""></image>
                </view>
                <view class="tips"> <text class="iconfont icon-xiaochengxu"></text>{{ $t('ui.customerQuickReplyIndexMiniProgram') }}</view>
              </view>
            </view>
          </view>
          <!-- 缺省页 -->
          <view v-else class="default-box">
            <image :src="getImageUrl('default-quick.png')" mode="" class="default-img"></image>
            <view>{{ $t('ui.customerQuickReplyIndexNoContent') }}</view>
          </view>
        </view>
      </view>
    </view>
    <!-- 视频弹窗 -->
    <uni-popup ref="popup" background-color="#fff" @change="change">
      <view class="popup-content">
        <video :src="fileUrl" id="myvideo" @fullscreenchange="screenChange"></video>
      </view>
    </uni-popup>

    <!-- 新增 -->
    <view class="add-btn" @click="addReplyRef.popupOpen()">
      <text class="iconfont icon-biaodan-chengyuantianjia"></text>
    </view>
    <addReply ref="addReplyRef" @submit="handleAddReply" />
  </BaseContainer>
</template>

<script setup lang="ts">import appI18n from '@/locale';

import { ref, onMounted, reactive, computed } from 'vue'
import { WxWork, isWxWorkEnv } from '@/libs/wxwork'
import { lookPreview } from '@/utils/helper'
import addReply from './components/addReply.vue'
import message from '@/utils/message'
import {
  getWorkReplyGroupApi,
  getWorkReplyTempApi,
  replyTempMyListApi,
  replyTempSaveApi,
  replyTempEditSaveApi,
  replyTempDeleteApi,
} from '@/api/customer'
import { useStore } from 'vuex'
import BaseContainer from '@/components/BaseContainer/index.vue'
import { wxworkAuth } from '@/libs/wxwork-auth'
const store = useStore()
const isLogin = computed(() => store.state.app.isLogin)
const popup = ref(null)
const fileUrl = ref(null)
const addReplyRef = ref(null)
const swipeActions = ref([
  {
    text: appI18n.global.t('ui.customerQuickReplyIndexSelect'),
    value: 1,
  },
  {
    text: appI18n.global.t('ui.customerQuickReplyIndexEdit'),
    value: 2,
  },
  {
    text: appI18n.global.t('ui.examineFormApprovalBillDelete'),
    value: 3,
  },
])
const where = reactive({
  name: '',
  group_id: '',
  types: '',
})
// 内容列表
const contentList = ref([])
// 分类列表
const categoryList = ref([])
// 当前选中分类
const currentCategory = ref(null)
// 当前选中标签
const currentTab = ref('')
// 悬浮/触摸激活的项目
const hoveredItem = ref(null)

onLoad((e) => {
  replyGroupList()
})

const replyGroupList = () => {
  getWorkReplyGroupApi().then((res) => {
    categoryList.value = res.data.list
    categoryList.value.unshift({
      id: 'my',
      name: '我的',
    })
    where.group_id = res.data.list[0].id
    currentCategory.value = res.data.list[0].id
    loadCustomerData()
  })
}

// 加载列表数据
const loadCustomerData = () => {
  if (where.group_id === 'my') {
    replyTempMyListApi({ name: where.name, types: where.types }).then((res) => {
      uni.showLoading({
        title: appI18n.global.t('ui.customerContractIndexLoading'),
      })
      contentList.value = res.data.list
      uni.hideLoading()
    })
  } else {
    getWorkReplyTempApi(where).then((res) => {
      uni.showLoading({
        title: appI18n.global.t('ui.customerContractIndexLoading'),
      })
      contentList.value = res.data.list
      uni.hideLoading()
    })
  }
}

// 图片与文档预览
const preview = (item: any) => {
  lookPreview(item.file_url, item.name, [item.file_url])
}
// 打开客户聊天对话框
const openReply = async (item) => {
  let messageType = 'text'
  let messageContent = { text: { content: appI18n.global.t('ui.customerQuickReplyIndexDefaultReplyContent') } }
  let wxWork = null

  try {
    if (!item) {
      throw new Error('缺少消息源数据')
    }

    // 根据类型动态生成消息内容（仅修改这部分的代码即可）
    if (item.types === 'link') {
      // 🔧 修复1：news 类型需要包含在 news 对象内
      messageType = 'news'
      messageContent = {
        news: {
          link: item.link || '', // ✅ 修正：news 用 'link'，不是 'url'
          title: item.title || '',
          desc: item.info || '',
          imgUrl: item.file_url || '', // ✅ 修正：news 用 'imgUrl'，不是 'picurl'
        },
      }
    } else if (item.types === 'text') {
      messageContent = {
        text: { content: item.content || '默认文本内容' },
      }
    } else if (item.types === 'mini_program') {
      // 🔧 修复2：miniprogram 类型需要包含在 miniprogram 对象内
      messageType = 'miniprogram'
      messageContent = {
        miniprogram: {
          appid: item.app_id, // 小程序AppID，必须已在企业微信后台关联
          title: item.title || '小程序',
          imgUrl: item.file_url || '', // ✅ 修正：miniprogram 用 'imgUrl'，不是 'imgUrl'里外不一致
          page: item.link || '', // 小程序页面路径
        },
      }
    } else {
      // 图片/文件类型
      messageType = item.types
      messageContent = {
        [messageType]: { mediaid: item.media_id },
      }
    }

    if (!wxWork) {
      wxWork = await WxWork.getInstance()
    }

    console.log('发送类型：', messageType)
    console.log('发送内容：', messageContent)

    await new Promise((resolve, reject) => {
      wxWork.ww.sendChatMessage({
        msgtype: messageType,
        ...messageContent,
        success: (res) => {
          console.log('发送成功', res)
          resolve(res)
        },
        fail: (err) => {
          console.error('发送失败', err)
          reject(err)
        },
      })
    })

    message.success(appI18n.global.t('ui.customerQuickReplyIndexQuickRepliesSuccessful'))
  } catch (err) {
    const errorMsg = err.message || err.errMsg || '操作失败'
    console.error('快捷回复异常:', err)
    message.error(`快捷回复失败: ${errorMsg}`)
  }
}

const videoPlay = ref(false)

// 播放视频
const handleWrapperClick = (item) => {
  popup.value.open()
  videoPlay.value = true
  fileUrl.value = item.file_url
  let videoContext = uni.createVideoContext('myvideo', this) // this这个是实例对象 必传

  videoContext.requestFullScreen({ direction: 90 })
  videoContext.play()
}
const screenChange = (e) => {
  let fullScreen = e.detail.fullScreen
  if (!fullScreen) {
    popup.value.close()
    //退出全屏
    let videoContext = uni.createVideoContext('myvideo', this) // this这个是实例对象 必传
    videoContext.pause()
    videoContext.seek(0)
    videoPlay.value = false // 隐藏播放盒子
  }
}

// 标签列表
const tabList = ref([
  {
    label: appI18n.global.t('ui.attendanceDetailedUserCheckListAll'),
    value: '',
  },
  {
    label: appI18n.global.t('ui.customerQuickReplyIndexText'),
    value: 'text',
  },
  {
    label: appI18n.global.t('ui.customerQuickReplyIndexFile'),
    value: 'file',
  },
  {
    label: appI18n.global.t('ui.customerContractUploadFileImageImage'),
    value: 'image',
  },
  {
    label: appI18n.global.t('ui.customerQuickReplyIndexVideo'),
    value: 'video',
  },

  {
    label: appI18n.global.t('ui.customerQuickReplyIndexLink'),
    value: 'link',
  },
  {
    label: appI18n.global.t('ui.customerQuickReplyIndexMiniProgram'),
    value: 'mini_program',
  },
])

const getImageUrl = (imageName) => {
  // 动态构造图片路径
  return new URL(`/static/image/list/${imageName}`, import.meta.url).href
}

// 分类点击事件
const handleCategoryClick = (category: { id: number | string }) => {
  currentCategory.value = category.id
  where.group_id = category.id
  loadCustomerData()
}

// 标签点击事件
const handleTabClick = (tab: any) => {
  currentTab.value = tab.value
  where.types = tab.value
  loadCustomerData()
}
const clearSearchData = () => {
  where.name = ''

  loadCustomerData()
}

const searchData = () => {
  loadCustomerData()
}

// 新增快捷回复
const handleAddReply = async (formData: any) => {
  try {
    uni.showLoading({
      title: appI18n.global.t('ui.attendanceShiftAddSaving'),
    })
    if (formData.id) {
      // 编辑模式
      await replyTempEditSaveApi(formData.id, formData)
    } else {
      // 新增模式
      await replyTempSaveApi(formData)
    }
    uni.hideLoading()
    message.success(appI18n.global.t('ui.navigationBarSiderbarSavedSuccessfully'))
    loadCustomerData()
  } catch (error) {
    uni.hideLoading()
    message.error(appI18n.global.t('ui.customerQuickReplyIndexSaveFailed'))
  }
}

// 编辑快捷回复
const handleEdit = (item: any) => {
  addReplyRef.value.popupOpen({
    id: item.id,
    content: item.content,
    sort: item.sort,
  })
}

// 删除快捷回复
const handleDelete = (item: any) => {
  uni.showModal({
    title: appI18n.global.t('ui.customerLeadDetailHint'),
    content: appI18n.global.t('ui.customerQuickReplyIndexDeleteThisQuickReply'),
    success: async (res) => {
      if (res.confirm) {
        try {
          uni.showLoading({
            title: appI18n.global.t('ui.customerQuickReplyIndexDeleting'),
          })
          await replyTempDeleteApi(item.id)
          uni.hideLoading()
          message.success(appI18n.global.t('ui.customerQuickReplyIndexSuccessfullyDeleted'))
          loadCustomerData()
        } catch (error) {
          uni.hideLoading()
          message.error(appI18n.global.t('ui.customerQuickReplyIndexDeleteFail'))
        }
      }
    },
  })
}
</script>

<style scoped lang="scss">
.content {
  width: 100%;

  background-color: #fff;

  .quick-reply-page {
    border-top: 2rpx solid #f5f5f5;
    display: flex;
  }

  /* 左侧分类栏 */
  .left-category {
    width: 194rpx;
    padding: 16rpx 12rpx;
    border-right: 2rpx solid #f5f5f5;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 26rpx;
    color: #606266;
    white-space: nowrap;
    overflow: auto;
    text-overflow: ellipsis;
    height: calc(100vh - 130px);
  }

  .category-item {
    padding: 10px;
    cursor: pointer;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }

  .category-item.active {
    background: #f5f5f5;
    border-radius: 8rpx;
  }

  /* 右侧内容区 */
  .right-content {
    height: calc(100vh - 100px);
    flex: 1;

    display: flex;
    flex-direction: column;

    .default-box {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      font-family:
        PingFang SC,
        PingFang SC;
      font-weight: 400;
      font-size: 12px;
      color: #999999;
      transform: translateY(-70px);

      .default-img {
        display: block;
        width: 148px;
        height: 121px;
      }
    }
  }

  .link-ellipsis {
    line-height: 1.5;
    max-height: 3em;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    word-break: break-all;
    width: 100%;
    /* 可选：设置容器宽度，触发文本换行 */
  }

  /* 搜索栏 */
  .search-bar {
    margin-bottom: 10px;
  }

  /* 顶部标签栏 */
  .tab-bar {
    height: 70rpx;
    border-top: 2rpx solid #f5f5f5;
    display: flex;
    align-items: center;
    padding: 0 24rpx;
  }

  .tab-item {
    margin-right: 36rpx;
    cursor: pointer;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 26rpx;
    color: #606266;
    display: flex;
    align-items: center;
    justify-content: center;

    z-index: 66;
  }

  .tab-item.active {
    color: #409eff;
    font-weight: 500;
  }

  /* 内容列表 */
  .content-list {
    flex: 1;
    overflow-y: auto;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 26rpx;
    color: #303133;
  }

  .video {
    position: relative;

    .mask {
      position: absolute;
      top: 0;
      left: 0;
      width: 128rpx;
      height: 128rpx;
      border-radius: 8rpx 8rpx 8rpx 8rpx;
      background-color: transparent;
      z-index: 80;
    }

    ::v-deep .uni-video-cover-duration {
      display: none;
    }

    ::v-deep .uni-video-cover-play-button {
      width: 41rpx;
      height: 41rpx;
      border: 1rpx solid #ffffff;
      border-radius: 50%;
      text-align: center;
      background-size: 45%;
      background-position: 60% 50%;
    }
  }

  .images {
    display: block;
    width: 112rpx;
    height: 112rpx;
    border-radius: 8rpx 8rpx 8rpx 8rpx;
    flex-shrink: 0;
  }

  .tips {
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 20rpx;
    color: #999999;

    .icon-xiaochengxu {
      font-size: 12px;
      margin-right: 4px;
    }
  }

  .content-item {
    position: relative;
    padding: 24rpx 20rpx;
    border-bottom: 2rpx solid #f5f5f5;
    transition: background-color 0.2s ease;
  }

  .category-item:hover {
    width: 100%;
    background-color: #f5f5f5;
  }

  .text-item {
    // height: 128rpx;
    display: flex;
    align-items: center;

    .text-content-wrapper {
      width: 100%;
      flex: 1;
      overflow: hidden;
    }

    &:hover {
      padding-right: 260rpx;

      .text-box {
        opacity: 1;
      }
    }
  }

  .text-content {
    display: -webkit-box; //使用了flex，需要加
    overflow: hidden; //超出隐藏
    word-break: break-all; //纯英文、数字、中文
    text-overflow: ellipsis; //省略号
    -webkit-box-orient: vertical; //垂直
    -webkit-line-clamp: 4; //显示一行
    white-space: pre-line;
  }

  .link-text {
    line-height: 40rpx;
  }

  .flex {
    height: 100%;
    display: flex;
    justify-content: space-between;
  }

  .file-name {
    display: block;
    // margin-right: 36rpx;
  }

  .file-icon {
    width: 76rpx;
    height: 90rpx;
    flex-shrink: 0;
    margin-right: 10rpx;
    border-radius: 8rpx 8rpx 8rpx 8rpx;
    object-fit: cover;
  }

  .image-content {
    width: 128rpx;
    height: 128rpx;
    border-radius: 8rpx 8rpx 8rpx 8rpx;
  }

  .video-content {
    width: 128rpx;
    height: 128rpx;
    border-radius: 8rpx 8rpx 8rpx 8rpx;

    ::v-deep .uni-video-current-time {
      display: none !important;
    }

    ::v-deep .uni-video-duration {
      display: none !important;
    }

    ::v-deep .uni-video-controls {
      /* 隐藏进度条容器 */
      .uni-video-progress {
        display: none !important;
      }

      /* 隐藏进度时间（如 01:23/05:00） */
      .uni-video-time {
        display: none !important;
      }
    }
  }
}

.popup-content {
  width: 100%;
  height: 100%;
}
.text-box {
  position: absolute;
  top: 0;
  right: 0rpx;
  opacity: 0;
  display: flex;
  height: 100%;
  align-items: center;

  flex-shrink: 0;
  margin-left: 20rpx;
  padding: 24rpx;
  background-color: #fafafa;

  .action-btn {
    // height: 100%;

    text-align: center;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 26rpx;
    color: #303133;
    cursor: pointer;

    margin-right: 24rpx;
  }
  // .line {
  //   display: inline-block;
  //   width: 1px;
  //   height: 16rpx;
  //   background-color: #dcdfe6;
  //   margin-right: 20rpx;
  // }
  .action-btn:last-child {
    margin-right: 0;
  }
}

.content-item:hover {
  // background-color: #fafafa;
}

.link-flex {
  width: 100%;

  display: flex;
  justify-content: space-between !important;
  align-items: center;
}
::v-deep .uni-swipe_button {
  padding: 0 20rpx;
}

.add-btn {
  position: fixed;
  bottom: 100rpx;
  right: 20rpx;
  width: 84rpx;
  height: 84rpx;
  background-color: #308bf8;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0px 4px 4px 0px rgba(28, 146, 248, 0.1145);
  .icon-biaodan-chengyuantianjia {
    font-size: 38rpx;
    font-weight: 500;
    color: #fff;
  }
}
</style>

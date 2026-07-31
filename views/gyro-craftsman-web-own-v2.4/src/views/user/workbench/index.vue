<template>
  <div class="box">
    <div
      class="box-height workbench-page"
      v-loading="pageLoading"
      :element-loading-text="$t('workbench.loading')"
      element-loading-background="rgba(255, 255, 255, 0.85)"
      :class="{ 'workbench-ready': !pageLoading }"
    >
      <!--顶部-->
      <el-row :gutter="14" type="flex">
        <el-col :span="16">
          <el-card
            body-style="padding:0;"
            style="
              background: linear-gradient(165deg, rgba(255, 255, 255, 0.23) 0%, #ffffff 40%);
              border: 1px solid #ffffff;
            "
          >
            <div class="header-need">
              <div class="header-need-left">
                <span class="name">{{ $ts(realName) }}, {{ greetingText }}!</span>

                <div class="header-need-left-info mt14">
                  <img :src="getWeatherIcon()" alt="" />
                  <div>
                    <div class="top">{{ currentWeather.temperature }}℃</div>
                    <div class="bottom">{{ currentDateStr }}</div>
                  </div>
                </div>
              </div>
              <div class="header-need-right">
                <div
                  class="header-need-right-item"
                  v-for="(item, index) in needInfo"
                  :key="'needInfo' + index"
                  @click="needInfoItem(item)"
                >
                  <p class="num">{{ item.num }}</p>
                  <p class="text">{{ $t(item.textKey) }}</p>
                </div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="8">
          <el-card body-style="padding:0">
            <div class="header-pic">
              <div class="header-pic-content">
                <img class="image" :src="personalIcon" alt="" />
                <div class="text">
                  <div class="over-text1">{{ $t('workbench.welcomeLogin') }}</div>
                  <div class="info over-text2">{{ $ts(enterprise.culture || '--') }}</div>
                </div>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
      <!-- 业绩统计 -->
      <el-row class="mt14" type="flex" :gutter="14">
        <el-col :span="16">
          <el-card body-style="padding:20px;">
            <el-row class="calendar_title achievementPb">
              <el-col :span="12" class="row-middle">
                <div class="dynamics">{{ $t('workbench.achievementStatistics') }}</div>
              </el-col>
              <el-col :span="12" class="text-right calendar_title-left">
                <span @click="workStatisticsList(0)" :class="achievementTypes === 0 ? 'active' : ''">{{ $t('workbench.departmentPerformance') }}</span>
                <span class="line">|</span>
                <span @click="workStatisticsList(1)" :class="achievementTypes === 1 ? 'active' : ''">{{ $t('workbench.personalPerformance') }}</span>
              </el-col>
            </el-row>
            <div class="achievementContent">
              <div class="item" v-for="(item, index) in achievementList.slice(0, 4)" :key="index">
                <span class="text">{{ localizedAchievementTitle(item.title) }}</span>
                <span class="num">{{ item.value }}</span>
              </div>
            </div>
            <div class="achievementContent activeContent">
              <div class="item" v-for="(item, index) in achievementList.slice(4)" :key="index">
                <span class="text">{{ localizedAchievementTitle(item.title) }}</span>
                <span class="num">{{ item.value }}</span>
              </div>
            </div>
          </el-card>

          <el-card body-style="padding:0" class="mt14">
            <el-row class="calendar_title">
              <el-col :span="12" class="row-middle">
                <div class="dynamics">{{ $t('workbench.commonFunctions') }}</div>
              </el-col>
              <el-col :span="12" class="text-right calendar_title-left">
                <span class="display-align" @click="handleQuick">
                  <i class="iconfont iconxitong-xitongshezhi-cebian"></i>
                </span>
              </el-col>
            </el-row>
            <div class="quick-content">
              <div class="quick-list" v-if="quickVal.length > 0">
                <div
                  class="quick-list-item"
                  v-for="(item, index) in quickVal"
                  @click="quickItem(item)"
                  :key="'quick' + index"
                >
                  <div class="pointer">
                    <el-image :src="item.image" class="image"></el-image>
                    <div class="name">{{ item.name }}</div>
                  </div>
                </div>
              </div>
              <default-page v-else :min-height="47" :imgWidth="`65px`" :top="`30px`" :index="16" />
            </div>
          </el-card>
        </el-col>

        <el-col :span="8">
          <el-card body-style="padding:0">
            <div class="week-calendar" v-loading="timeTabLoading">
              <div class="calendar-header">
                <div class="calendar-title">
                  <span>{{ $t('workbench.todoItems') }}</span>
                </div>
                <div class="calendar-actions">
                  <span class="today-btn" :class="{ active: isCurrentWeek }" @click="goToToday">{{ $t('workbench.today') }}</span>
                </div>
              </div>
              <div class="week-nav">
                <div class="nav-btn prev" @click="prevWeek">
                  <i class="el-icon-arrow-left"></i>
                </div>
                <div class="week-days">
                  <div
                    v-for="(day, index) in weekDays"
                    :key="index"
                    class="day-item"
                    :class="{ today: day.isToday, selected: day.isSelected }"
                    @click="selectDay(day)"
                  >
                    <span class="week-name">{{ day.weekName }}</span>
                    <span class="day-num">{{ day.dayNum }}</span>
                  </div>
                </div>
                <div class="nav-btn next" @click="nextWeek">
                  <i class="el-icon-arrow-right"></i>
                </div>
              </div>

              <!-- 日程 -->
              <div style="height: 294px; overflow: auto; padding-top: 20px">
                <el-timeline v-if="needList.length > 0">
                  <el-timeline-item v-for="(activity, index) in needList" size="normal" :key="index">
                    <div class="need-item">
                      <!-- <span class="time">{{ normalizeTime(activity.created_at) }}</span> -->
                      <span class="text"> {{ activity.title }}</span>
                    </div>
                  </el-timeline-item>
                </el-timeline>
                <div v-else>
                  <default-page :min-height="234" :index="21" :imgWidth="`120px`" />
                </div>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>

      <!--绩效考核-->
      <el-row :gutter="14" class="mt14">
        <template v-if="assessLoading && assessNowData.length > 0">
          <el-col :span="8">
            <el-card body-style="padding:0" style="height: 386px">
              <div class="calendar_title clearfix">
                <div class="pull-left acea-row row-middle">
                  <div class="dynamics">{{ $t('workbench.currentAssessment') }}</div>
                </div>
                <div class="pull-right acea-row row-middle">
                  <div class="notice-more" @click="handleAssessMore()">
                    {{ $t('workbench.more') }}
                    <i class="el-icon-arrow-right"></i>
                  </div>
                </div>
              </div>
              <echarts :id="assessNowData[0].id" />
            </el-card>
          </el-col>
        </template>
        <el-col
          :span="
            nweNoticeData.length === 0
              ? assessLoading && assessNowData.length > 0
                ? 16
                : 24
              : assessLoading && assessNowData.length > 0
              ? 8
              : 16
          "
        >
          <el-card body-style="padding:0" class="table-box note" style="height: 386px">
            <div class="calendar_title clearfix">
              <div class="pull-left acea-row row-middle">
                <div class="dynamics">{{ $t('workbench.systemNotice') }}</div>
              </div>
              <div class="pull-right acea-row row-middle">
                <div class="notice-more" @click="handleNewMore()">
                  {{ $t('workbench.more') }}
                  <i class="el-icon-arrow-right"></i>
                </div>
              </div>
            </div>
            <div v-if="systemNote.length > 0" class="list">
              <div
                v-for="(item, index) in systemNote"
                :key="index"
                class="item acea-row row-between-wrapper pointer"
                @click="handleDetails(item)"
                :class="item.is_read ? 'finish' : ''"
              >
                <div class="item-list system-note">
                  <div class="line1">
                    <span class="label">【{{ localizedNoticeText(item.title) }}】</span>
                    <span>{{ localizedNoticeText(item.message) }}</span>
                  </div>
                  <div class="time">{{ $moment(item.created_at).format('MM-DD HH:mm') }}</div>
                </div>
              </div>
            </div>
            <default-page v-else :min-height="406" :top="`60px`" :index="14" />
          </el-card>
        </el-col>
        <el-col :span="8" v-if="nweNoticeData.length > 0">
          <el-card body-style="padding:0" style="height: 386px">
            <div class="calendar_title clearfix">
              <div class="pull-left acea-row row-middle">
                <div class="dynamics">{{ $t('workbench.enterpriseNews') }}</div>
              </div>
              <div class="pull-right acea-row row-middle">
                <div class="notice-more" @click="handleNoticeMore('')">
                  {{ $t('workbench.more') }}
                  <i class="el-icon-arrow-right"></i>
                </div>
              </div>
            </div>
            <ul class="news-content" v-if="nweNoticeData.length > 0">
              <li v-for="item in nweNoticeData.slice(0, 5)" :key="'new' + item.id" @click="handleNoticeMore(item.id)">
                <div class="notice-left" :class="{ width100: !item.cover }">
                  <p class="title over-text">{{ item.title }}</p>
                  <div class="bottom">
                    <span>
                      <i class="iconfont iconyiyuedu"></i>
                      {{ item.visit }}
                    </span>
                    <span>
                      <i class="iconfont iconriqishijian"></i>
                      {{ item.push_time.split(' ')[0] }}
                    </span>
                  </div>
                </div>
                <div class="notice-right" v-if="item.cover">
                  <img v-if="item.cover" class="img" :src="item.cover" alt="" />
                </div>
              </li>
            </ul>
            <default-page v-else :min-height="386" :index="14" />
          </el-card>
        </el-col>
      </el-row>
      <password ref="password" :form-data="passwordData"></password>
      <!-- 系统通知 -->
      <noticeList ref="noticeList" v-if="noticeListVisible"></noticeList>

      <message-details ref="messageDetails" :message-data="messageData" />
      <quick-manage ref="quickManage" @isSuccess="quickSuccess" :config="configData"></quick-manage>

      <!-- 跟进弹窗 -->
      <copyright />
    </div>
  </div>
</template>
<script>
import {
  dealtScheduleListApi,
  noticeMessageListApi,
  userAssessSubord,
  userWorkMenusApi,
  userWorkCountApi,
  scheduleListApi,
  scheduleTypesApi,
  scheduleStatusApi,
  enterpriseUserJoinApi,
  workStatisticsApi,
  workStatisticsApiAll,
  getWeather,
  todoListApi
} from '@/api/user'
import { auth } from '@/api/setting'
import { noticeListApi } from '@/api/administration'
import { getStorageJson } from '@/utils/storage'
import { pageJumpTo } from '@/libs/public'
import ElementUI from 'element-ui'
import { roterPre } from '@/settings'
import { configRuleApproveApi } from '@/api/config'
import { translateMessage } from '@/lang'

// 图片资源导入

import personalIcon from '@/assets/images/personal-icon.png'

export default {
  name: 'Workbench',
  components: {
    echarts: () => import('./components/echart'),
    password: () => import('./components/password'),

    defaultPage: () => import('@/components/common/defaultPage'),
    messageDetails: () => import('@/views/user/news/components/messageDetails'),
    quickManage: () => import('./components/quickManage'),
    noticeList: () => import('@/layout/components/Notice/noticeList'),
    copyright: () => import('@/layout/components/copyright.vue')
  },
  data() {
    const currentDate = new Date()
    return {
      personalIcon,
      pageLoading: true,
      noticeListVisible: false,
      addTodoVisible: false,
      calendarDetailsVisible: false,

      realName: '',
      greetingText: this.getGreeting(),
      currentWeather: {
        temperature: 0
      },

      dialogVisible: false,
      configContract: {},
      formInfo: {
        avatar: '',
        type: 'add',
        show: 1,
        data: {},
        follow_id: 0
      },
      needList: [],
      value: currentDate,
      calendar: {
        time: this.$moment(currentDate).format('YYYY-MM')
      },
      dailyList: {},
      currentDay: this.$moment(currentDate).format('DD'),
      dailyDay: [],
      imgList: [],
      achievementList: [],

      types: [],
      scheduleTypes: [],
      time: this.$moment(currentDate).format('YYYY-MM-DD'),

      weekDays: [],
      weekTitle: '',
      currentWeekStart: null,

      passwordData: {},

      userId: '',
      nweNoticeData: [],
      userInfo: {},
      systemNote: [], // 系统通知
      timeTab: 1,
      timeTabData: [
        { text: this.$t('workbench.previousDay'), label: 0 },
        { text: this.$t('workbench.today'), label: 1 },
        { text: this.$t('workbench.nextDay'), label: 2 }
      ],
      timeTabIndex: 1,
      timeTabLoading: false,
      timeTabNum: 0,
      needInfo: [
        {
          textKey: 'workbench.needTodo',
          num: 0,
          path: '/user/todo',
          type: -1
        },
        {
          textKey: 'workbench.myApplications',
          num: 0,
          path: '/user/examine/mine',
          type: 1
        },
        {
          textKey: 'workbench.myApprovals',
          num: 0,
          path: '/user/examine/approval',
          type: 0
        },
        {
          textKey: 'workbench.enterpriseNews',
          num: 0,
          path: '/user/notice/index',
          type: 2
        }
      ],
      options: [
        {
          value: 0,
          label: this.$t('workbench.departmentPerformance')
        },
        {
          value: 1,
          label: this.$t('workbench.personalPerformance')
        }
      ],
      achievementTypes: 0,
      quickData: [],
      quickVal: [],
      assessNowData: [],
      assessLoading: false,
      messageData: {},
      configData: {},

      enterprise: {},
      buildData: [],
      item: {},
      status: 0
    }
  },
  computed: {
    currentDateStr() {
      const currentDate = new Date()
      const weekDayNames = [
        this.$t('workbench.weekDayNames.sun'),
        this.$t('workbench.weekDayNames.mon'),
        this.$t('workbench.weekDayNames.tue'),
        this.$t('workbench.weekDayNames.wed'),
        this.$t('workbench.weekDayNames.thu'),
        this.$t('workbench.weekDayNames.fri'),
        this.$t('workbench.weekDayNames.sat')
      ]
      return this.$moment(currentDate).format('YYYY-MM-DD') + ' ' + weekDayNames[currentDate.getDay()]
    },
    isCurrentWeek() {
      const today = this.$moment().format('YYYY-MM-DD')
      return this.weekDays.some((day) => day.dateStr === today)
    }
    // ...mapGetters(['enterprise'])
  },
  watch: {},

  mounted() {
    // 初始化周视图
    this.initWeekView()

    // 集中获取用户信息，避免多次解析localStorage
    const userInfo = getStorageJson('userInfo')
    this.enterprise = getStorageJson('enterprise')
    // const sitedata = JSON.parse(localStorage.getItem('sitedata'))
    // let city = this.getWeatherLocation(sitedata.site_address)

    if (userInfo) {
      this.userId = userInfo.id
      this.userInfo = userInfo
      this.formInfo.avatar = userInfo.avatar
      this.realName = userInfo.name
    }

    // 天气数据不阻塞工作台首屏加载，避免接口响应慢时整页一直 loading
    this.getCurrentWeather()

    // 并行执行首屏核心异步任务
    Promise.all([
      this.workStatisticsList(0),
      this.getAssessMine(),
      this.getNewTableData(),
      this.getTypes(),
      this.getNewListData(),
      this.getUserWorkMenus(),
      this.entAuth(),
      this.getUserWorkCount(),
      this.getConfigApprove()
    ])
      .catch((error) => {
        console.error('异步任务执行出错:', error)
      })
      .finally(() => {
        this.pageLoading = false
      })

    // 同步执行的操作
    this.getInvitation()
    this.getPassword()
  },
  methods: {
    localizedNoticeText(value) {
      return translateMessage(value)
    },
    // 根据当前时间获取问候语
    getGreeting() {
      const hour = new Date().getHours()
      if (hour >= 5 && hour < 9) {
        return this.$t('workbench.greetings.earlyMorning')
      } else if (hour >= 9 && hour < 12) {
        return this.$t('workbench.greetings.morning')
      } else if (hour >= 12 && hour < 14) {
        return this.$t('workbench.greetings.noon')
      } else if (hour >= 14 && hour < 18) {
        return this.$t('workbench.greetings.afternoon')
      } else if (hour >= 18 && hour < 22) {
        return this.$t('workbench.greetings.evening')
      } else {
        return this.$t('workbench.greetings.lateNight')
      }
    },
    localizedAchievementTitle(title) {
      const keyMap = {
        本月业绩: 'thisMonthPerformance',
        今日业绩: 'todayPerformance',
        昨日业绩: 'yesterdayPerformance',
        本月新增客户: 'newCustomersThisMonth',
        今日新增客户: 'newCustomersToday',
        跟进未完成: 'unfinishedFollowUps',
        今日跟进记录: 'followUpRecordsToday',
        今日新增订单: 'newOrdersToday'
      }
      const key = keyMap[title]
      return key ? this.$t(`workbench.achievement.${key}`) : title
    },

    // 初始化周视图
    initWeekView(date = new Date()) {
      const startOfWeek = this.$moment(date).startOf('isoWeek')
      this.currentWeekStart = startOfWeek.toDate()
      this.updateWeekDays()
    },
    // 根据天气获取对应的图片
    getWeatherIcon() {
      const weatherMap = {
        晴: require('@/assets/images/tianqi/qing.png'),
        多云: require('@/assets/images/tianqi/duoyun.png'),
        小雨: require('@/assets/images/tianqi/xiaoyu.png'),
        大雨: require('@/assets/images/tianqi/dayu.png'),
        雪: require('@/assets/images/tianqi/xue.png')
      }
      return weatherMap[this.currentWeather.text] || require('@/assets/images/tianqi/duoyun.png')
    },
    getCurrentWeather(cityName) {
      return getWeather()
        .then((res) => {
          const now = res.data
          this.currentWeather.temperature = now.temp
          let str = ''
          if (now.weathercode >= 1) {
            str = '晴'
          } else if (2 <= now.weathercode <= 48) {
            str = '多云'
          } else if (51 <= now.weathercode <= 61) {
            str = '小雨'
          } else if (63 <= now.weathercode <= 67) {
            str = '大雨'
          } else if (71 <= now.weathercode <= 86) {
            str = '雪'
          }
          this.currentWeather.text = str
        })
        .catch((error) => {
          console.error('天气数据获取失败:', error)
        })
    },

    // 更新周视图数据
    updateWeekDays() {
      const today = this.$moment().format('YYYY-MM-DD')
      const selectedDate = this.$moment(this.time).format('YYYY-MM-DD')
      this.weekDays = []
      const weekNames = [
        this.$t('workbench.weekNames.mon'),
        this.$t('workbench.weekNames.tue'),
        this.$t('workbench.weekNames.wed'),
        this.$t('workbench.weekNames.thu'),
        this.$t('workbench.weekNames.fri'),
        this.$t('workbench.weekNames.sat'),
        this.$t('workbench.weekNames.sun')
      ]
      for (let i = 0; i < 7; i++) {
        const dayDate = this.$moment(this.currentWeekStart).add(i, 'days')
        this.weekDays.push({
          weekName: weekNames[i],
          dayNum: dayDate.format('D'),
          dateStr: dayDate.format('YYYY-MM-DD'),
          isToday: dayDate.format('YYYY-MM-DD') === today,
          isSelected: dayDate.format('YYYY-MM-DD') === selectedDate
        })
      }

      // 更新标题
      const startDate = this.$moment(this.currentWeekStart)
      const endDate = this.$moment(this.currentWeekStart).add(6, 'days')
      this.weekTitle = `${startDate.format('MM月DD日')}-${endDate.format('MM月DD日')}`
    },

    // 上一周
    prevWeek() {
      this.currentWeekStart = this.$moment(this.currentWeekStart).subtract(7, 'days').toDate()
      this.updateWeekDays()
    },

    // 下一周
    nextWeek() {
      this.currentWeekStart = this.$moment(this.currentWeekStart).add(7, 'days').toDate()
      this.updateWeekDays()
    },

    // 跳转到今天
    goToToday() {
      this.time = this.$moment().format('YYYY-MM-DD')
      this.initWeekView()
      this.getDailyTodoInfo(this.time, true)
    },

    // 选择日期
    selectDay(day) {
      this.time = day.dateStr
      this.updateWeekDays()
      this.getDailyTodoInfo(day.dateStr, true)
    },

    // 业绩统计
    async workStatisticsList(type) {
      this.achievementTypes = type

      const result = await workStatisticsApi(type)

      this.achievementList = result.data
    },

    // 跳转到业绩统计
    performanceFn() {
      this.$router.push({
        path: `${roterPre}/customer/turnover/index`,
        query: {}
      })
    },

    // 打开业绩统计弹窗
    async handlePerformance() {
      const result = await workStatisticsApiAll()
      let otherData = [
        {
          cate_name: this.$t('workbench.performanceBrief'),
          fast_entry: result.data.list
        }
      ]
      this.configData = {
        title: this.$t('workbench.performanceManage'),
        type: 'statistics',
        width: '600px',
        data: result.data.select,
        otherArr: otherData
      }
      await this.$refs.quickManage.handleOpen(this.configData)
    },

    // 关闭跟进弹窗
    recordChange() {
      this.dialogVisible = false
      this.getDailyTodo()
    },

    // 获取授权信息
    async entAuth() {
      const obj = await auth()
      const data = obj.data
      if (data.status === -1) {
        await this.getAuthMessage(`您的授权证书还有${data.day}天过期,请及时前往陀螺匠官方进行授权认证!`)
      }
    },

    // 提醒授权弹窗
    getAuthMessage(message) {
      const title = translateMessage('\u6388\u6743\u63d0\u9192')
      const description = translateMessage(message)
      const action = translateMessage('\u7acb\u5373\u6388\u6743')
      const content = `<div class='el-row display-align'>
        <div class="el-col el-col-24 right width100">
          <p class='title over-text'>${title}</p>
          <p class='caption over-text2'>${description}</p>
        </div>
      </div>
      <div class='text-right'>
        <button id="messageOpen" type="button" class="el-button el-button--text el-button--small"><span>${action}</span></button>
      </div>`

      const notify = ElementUI.Notification({
        title: translateMessage('\u6d88\u606f'),
        dangerouslyUseHTMLString: true,
        message: content,
        duration: 10000,
        offset: 60,
        iconClass: 'iconfont iconxiaoxi',
        customClass: 'message-socket'
      })

      const oBtn = document.getElementById('messageOpen')
      if (oBtn) {
        const handleClick = () => {
          pageJumpTo('/setting/auth/auth/index')
          notify.close()
        }
        oBtn.addEventListener('click', handleClick)
        notify.$once('close', () => {
          oBtn.removeEventListener('click', handleClick)
        })
      }
    },

    // 编辑代办日程
    editFn(id, type, date) {
      let data = {
        id,
        type,
        edit: true,
        date
      }
      this.addTodoVisible = true
      setTimeout(() => {
        this.$refs.addTodo.openBox(data)
      }, 200)
    },

    // 打开系统消息弹窗
    handleDetails(row) {
      this.messageData = {
        width: '560px',
        data: row
      }
      this.$refs.messageDetails.handleOpen()
    },

    // 处理数据
    findItem(arr, key, val) {
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].id === val || arr[i].id == this.userId) {
          return 2
        }
      }
      return -1
    },

    getDailyTodo() {
      this.getDailyTodoInfo(this.time)
    },

    getDailyTodoInfo(time, load = false) {
      if (load) {
        this.timeTabLoading = true
      }
      const data = {
        time,
        page: 1,
        limit: 4
      }
      todoListApi(data)
        .then((res) => {
          this.needList = res.data.list
        })
        .finally(() => {
          if (load) {
            this.timeTabLoading = false
          }
        })
    },

    normalizeTime(time) {
      return this.$moment(time).format('A h:mm') // 获取时间的上午/下午标识
    },

    async getTypes() {
      const result = await scheduleTypesApi()
      this.scheduleTypes = result.data
      result.data.forEach((value) => {
        this.types.push(value.id)
      })
      await this.getDailyTodoInfo(this.$moment(new Date()).format('YYYY-MM-DD'))
    },

    async getScheduleDay(time, type) {
      const data = {
        time: time,
        types: this.types,
        period: 0
      }
      const result = await dealtScheduleListApi(data)
      const res = result.data[0].list
      if (type === 1) {
        res.sort((a, b) => a.finish - b.finish)
        this.needList = res
      }
    },

    async getConfigApprove() {
      const result = await configRuleApproveApi(0)
      this.buildData = result.data
    },

    // 初始密码修改
    getPassword() {
      if (this.userInfo && this.userInfo.is_init === 1) {
        this.passwordData = {
          title: '修改密码',
          width: '540px'
        }
        setTimeout(() => {
          this.$refs.password.handleOpen()
        }, 300)
      }
    },

    // 链接邀请
    getInvitation() {
      const invitationStorage = localStorage.getItem('invitationStorage')
      if (invitationStorage) {
        try {
          const invitation = JSON.parse(invitationStorage)
          this.getEnterpriseInfo(invitation)
        } catch (e) {
          console.error('解析邀请信息失败:', e)
        }
      }
    },

    async getEnterpriseInfo(invitation) {
      await enterpriseUserJoinApi({
        invitation: invitation.invitation
      })
      await localStorage.removeItem('invitationStorage')
    },

    // 跳转到企业动态
    handleNoticeMore(id = '') {
      const data = id ? { id: id } : {}
      pageJumpTo('/user/notice/index', data)
    },

    // 打开系统通知弹窗
    handleNewMore() {
      this.noticeListVisible = true
      this.$nextTick(() => {
        this.$refs.noticeList.openBox()
      })
    },

    // 点击快捷菜单跳转
    quickItem(item) {
      pageJumpTo(item.pc_url)
    },

    // 跳转到绩效考核
    handleAssessMore() {
      pageJumpTo('/user/assessment/my')
    },

    // 业绩考核
    async getAssessMine() {
      let data = {
        handle: 1,
        time: '',
        status: 1
      }
      const result = await userAssessSubord(data)
      this.assessLoading = true
      this.assessNowData = result.data.list ? result.data.list : []
    },

    // 顶部页面跳转
    needInfoItem(item) {
      if (!item.path) return
      pageJumpTo(item.path)
    },
    //  获取当前天气和温度

    // 获取系统消息列表
    async getNewListData() {
      const result = await noticeMessageListApi({ page: 1, limit: 11, cate_id: '', is_read: '' })
      this.systemNote = result.data.list
    },

    // 获取企业动态列表
    async getNewTableData() {
      const result = await noticeListApi({ is_new: 1, status: 1 })
      this.nweNoticeData = result.data.list || []
    },

    // 获取快捷入口列表
    async getUserWorkMenus() {
      this.quickVal = []
      const result = await userWorkMenusApi()
      this.quickData = result.data
      this.quickVal = result.data.checkd
    },

    // 业绩统计
    quickSuccess() {
      this.workStatisticsList()
      this.getUserWorkMenus()
    },

    // 获取顶部四列数据
    async getUserWorkCount() {
      const result = await userWorkCountApi()
      const data = result.data
      this.needInfo[0].num = data.scheduleCount
      this.needInfo[1].num = data.applyCount
      this.needInfo[2].num = data.approveCount
      if (data.noticeCount > 0) {
        this.needInfo[3].num = data.noticeCount
      } else {
        this.needInfo.splice(3, 1)
      }
    },

    // 日期切换
    timeTabInput(e, index) {
      this.timeTabIndex = index
      if (e.label === 1 && this.timeTabNum === 0) return
      if (e.label === 0) {
        this.timeTabNum--
      } else if (e.label === 1) {
        this.timeTabNum = 0
      } else {
        this.timeTabNum++
      }
      this.time = this.$moment(new Date()).add(this.timeTabNum, 'day').format('YYYY-MM-DD')
      this.getDailyTodoInfo(this.time, true)
    },

    // 打开快捷管理弹窗
    handleQuick() {
      let arr = []
      arr = JSON.parse(JSON.stringify(this.quickVal))
      let otherArr = this.quickData.cates
      this.configData = {
        title: this.$t('workbench.quickEntryManagement'),
        width: '600px',
        data: arr,
        otherArr: otherArr
      }

      this.$refs.quickManage.handleOpen(this.configData)
    }
  }
}
</script>
<style lang="scss" scoped>
::v-deep .el-calendar__header {
  align-items: center;
  height: 58px;
  border-bottom: 0;
}

.box {
  margin: 14px;
  margin-top: 1px;
  box-sizing: border-box;
  ::v-deep .divBox {
    // margin: 0;
    padding: 0;
  }
}

.workbench-page {
  min-height: calc(100vh - 92px);
  transition: opacity 0.35s ease;

  &.workbench-ready {
    animation: workbenchFadeIn 0.35s ease;
  }
}

@keyframes workbenchFadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.dynamics {
  font-family: PingFang SC, PingFang SC;
  font-weight: 500;
  font-size: 16px;
  color: #303133;
  display: flex;
  align-items: center;
  ::v-deep .el-input--suffix .el-input__inner {
    border: none;
    font-size: 12px;
    font-family: PingFang SC-Regular, PingFang SC;
    font-weight: 400;
    color: #1890ff;
    margin-left: 15px;
  }
  ::v-deep .el-input .el-select__caret {
    font-size: 12px;
    margin-top: 1px;
    color: #606266;
  }
}
.achievementContent {
  margin-top: 20px;
  display: flex;
  flex-wrap: wrap;
  padding: 0 38px;
  justify-content: space-between;
  gap: 20px;
  .item {
    flex: 0 0 calc(18% - 38px);
    display: flex;
    flex-direction: column;

    .num {
      font-family: D-DIN-PRO, D-DIN-PRO;
      font-weight: 600;
      font-size: 24px;
      color: #303133;
      margin: 10px 0;
    }
    .text {
      font-family: PingFang SC, PingFang SC;
      font-weight: 400;
      font-size: 13px;
      color: #606266;
    }
  }
}
.activeContent {
  background: #f7fbff;
  padding: 14px 38px;
  display: flex;
  align-items: center;
  // .item {
  //   flex: 0 0 calc(20% - 15px);
  // }
}
.achievementInfo {
  padding: 14px 38px;
  padding-bottom: 4px;
  background-color: #f7fbff;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  -webkit-box-shadow: inset 0 0 6px #ccc;
  display: none;
}
::-webkit-scrollbar {
  width: 4px !important; /*对垂直流动条有效*/
}
.table-box .list:hover::-webkit-scrollbar-thumb,
.enterprise:hover::-webkit-scrollbar-thumb {
  display: block;
}

::v-deep .el-calendar__body {
  padding: 10px 20px 10px;
}
::v-deep .el-calendar-table td {
  border: 0;
}
::v-deep .el-calendar-table tr:first-child td {
  border: 0;
}
::v-deep .el-calendar-table tr td:first-child {
  border: 0;
}
::v-deep .el-calendar-table .el-calendar-day {
  text-align: center;
  height: 36px;
  line-height: 36px;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
}
::v-deep .el-calendar-table td p {
  width: 28px;
  height: 28px;
  line-height: 28px;
  position: relative;
  i {
    position: absolute;
    top: 10px;
    left: 4px;
    font-size: 20px;
    font-weight: bold;
    color: rgba(0, 192, 80, 0.6);
  }
}
::v-deep .el-calendar-table td.is-selected {
  .title {
    position: absolute;
    z-index: 10;
  }
  .dealt-content {
    background: #1890ff;
    border-radius: 50%;
  }
}
.notice-more {
  color: #999;
  cursor: pointer;
  font-size: 13px;
  margin-top: 4px;
}
::v-deep .el-tabs__active-bar {
  height: 0;
}
::v-deep .el-tabs__header {
  margin-bottom: 0;
}
::v-deep .el-tabs__nav-wrap::after {
  display: none;
}
::v-deep .el-divider--vertical {
  margin: 0 5px;
}
.enterprise {
  height: 280px;
  padding: 5px 20px 0 20px;
  overflow-x: hidden;
  overflow-y: auto;
  ::v-deep .el-button--text {
    padding: 0 !important;
  }
  .item {
    margin-top: 16px;
    font-size: 13px;
    .line1 {
      width: 75%;
    }
    .label {
      color: #1890ff;
      margin-right: 5px;
    }
  }
}
.calendar_area {
  width: 100%;
  text-align: center;
  display: flex;
  justify-content: center;
  height: 36px;
  align-items: center;
  position: relative;
}
::v-deep .el-calendar-table td .dealt-content {
  line-height: 28px;
  i {
    position: absolute;
    right: 0;
    top: 0;
    font-size: 14px;
    font-weight: bold;
  }
}
.calendar_title {
  padding: 20px;
  .icon {
    color: #1890ff;
    margin-right: 5px;
    .iconfont {
      font-size: 20px;
    }
  }
  .calendar_title-left {
    span {
      cursor: pointer;
      justify-content: flex-end;
      font-family: PingFang SC, PingFang SC;
      font-weight: 400;
      font-size: 13px;
      color: #606266;
      i {
        margin-right: 2px;
      }
    }
    .line {
      color: #eeeeee;
      margin: 0 10px;
    }
    .active {
      color: #1890ff !important;
    }
  }
}
.calendar-dealt {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 0;
  .active {
    background-color: #1890ff;
    color: #fff;
    border: 1px solid transparent;
  }
  .dynamics-icon {
    color: #dcdfe6;
    margin-left: 4px;
  }
}
::v-deep .el-scrollbar__wrap {
  overflow-x: hidden;
}
::v-deep .el-card {
  border-radius: 6px !important;
}

.quick-content {
  width: 100%;

  .quick-list {
    display: flex;
    // padding: 0 61px;
    padding-bottom: 20px;
    .quick-list-item {
      flex: 1;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      .image {
        width: 38px;
        height: 38px;
        display: inline-block;
      }
      .name {
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        font-size: 13px;
        color: #606266;
        margin-top: 10px;
      }
    }
  }
}
.iconqiyewendang {
  color: #1890ff;
  font-size: 19px;
  margin-right: 5px;
}

.need-item {
  display: flex;
  flex-direction: column;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  padding-right: 4px;

  .text {
    line-height: 1.5;
    font-size: 13px;
    color: #303133;
    margin-top: -6px;
  }
}

::v-deep .el-timeline {
  padding: 0;
  .el-timeline-item__node--normal {
    position: absolute;
    width: 6px;
    height: 6px;
    background-color: #dcdfe6;
    left: 0;
  }
  .el-timeline-item__tail {
    position: absolute;
    left: 2px;
    border-left: 1px dashed #dfe4ed;
  }
  .el-timeline-item__wrapper {
    position: relative;
    top: -1px;
    padding-left: 18px !important;
  }
}
.calendar_list {
  padding: 0 20px 0 30px;
  height: 112px;
  overflow-x: hidden;
  margin-bottom: 20px;
  overflow-y: auto;
  .item {
    font-size: 13px;
    color: rgba(0, 0, 0, 0.85);
    line-height: 20px;
    margin-bottom: 12px;
    .label {
      margin-right: 5px;
    }
  }
}
.table-box {
  //   padding-bottom: 24px;
  ::v-deep .table-box-title {
    padding-left: 21px;
    height: 58px;
    // border-bottom: 1px solid rgba(216, 216, 216, 0.3);
    border-bottom: 0;
    display: flex;
    align-items: center;
    color: #000000;
  }
  .el_tabs {
    padding: 5px 30px;
  }
  .list {
    padding: 0 20px 0 16px;
    height: 280px;
    overflow-x: hidden;
    overflow-y: auto;
    .item {
      margin-top: 23px;
      font-size: 13px;
      font-weight: 400;
      color: rgba(0, 0, 0, 0.85) !important;
      &:first-of-type {
        margin-top: 0;
      }
      &.finish {
        color: #909399 !important;
        .time {
          color: #909399;
        }
        .el-icon-check {
          margin-right: 6px;
          vertical-align: bottom;
          font-size: 14px;
        }
        .line1 {
          color: #909399 !important;
          .label {
            color: #909399 !important;
          }
        }
      }
      .item-list {
        width: 100%;
        display: flex;
      }
      .line1 {
        width: 70%;
        color: #303133;
      }
      .system-note {
        .line1 {
          width: calc(100% - 80px);
        }
        .time {
          width: 80px;
        }
      }
      .label {
        color: #1890ff;
        margin-right: 4px;
      }
      .time {
        width: 30%;
        text-align: right;
      }
      .el-icon-remove-outline {
        // margin-right: 6px;
        // vertical-align: bottom;
        // font-size: 14px;
        color: #1890ff;
      }
    }
  }

  &.note .list .item .label {
    color: rgba(0, 0, 0, 0.85) !important;
  }
}
.achievementPb {
  padding: 0;
}

.header-pic {
  width: 100%;
  height: 140px;
  padding: 16px;

  .header-pic-content {
    width: 100%;
    height: 100%;
    position: relative;
    background-image: url('../../../assets/images/personal-logo.png');
    background-repeat: no-repeat;
    background-position: top center;
    background-size: 100% 100%;
  }
  .image {
    position: absolute;
    top: 15px;
    left: 24px;
    width: 96px;
    height: 69px;
    object-fit: cover;
  }
  .text {
    position: absolute;
    top: 33px;
    left: 141px;

    padding-right: 14px;
    font-family: PingFang SC, PingFang SC;
    font-weight: 500;
    font-size: 16px;
    color: #303133;
    .time {
      margin-left: 6px;
    }
    .info {
      font-size: 12px;
      margin-top: 10px;
      color: #909399;
      line-height: 1.5;
    }
  }
}

.header-need {
  width: 100%;
  height: 140px;
  display: flex;
  padding: 20px;
  justify-content: space-between;
  align-items: center;
  .header-need-left {
    width: 35%;
    .name {
      font-family: PingFang SC, PingFang SC;
      font-weight: 500;
      font-size: 16px;
      color: #303133;
    }
    .header-need-left-info {
      display: flex;
      height: 64px;
      img {
        display: block;
        width: 64px;
        height: 64px;
      }
      .top {
        font-family: D-DIN-PRO, D-DIN-PRO;
        font-weight: 600;
        font-size: 22px;
        color: #303133;
        margin-bottom: 8px;
        margin-top: 7px;
        margin-left: 12px;
      }
      .bottom {
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        font-size: 13px;
        color: #909399;
        margin-left: 12px;
      }
    }
  }
  .header-need-right {
    flex: 1;
    display: flex;
    justify-content: space-between;
    .header-need-right-item {
      position: relative;
      float: left;
      cursor: pointer;
      width: 100%;
      text-align: center;
      margin-top: 6px;
      &::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 1px;
        height: 30px;
        background-color: #eeeeee;
      }
      &:last-child::after {
        display: none;
      }
      .num {
        font-family: D-DIN-PRO, D-DIN-PRO;
        font-weight: 600;
        font-size: 32px;
        color: #303133;
      }
      .text {
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        font-size: 13px;
        color: #606266;
        margin-top: 6px;
      }
    }
    .header-need-right-item:last-of-type {
      border-right: none;
    }
  }
}
.news-content {
  height: 200px;
  overflow: auto;
  margin: 0;
  padding: 0 20px;
  list-style: none;
  li {
    width: 100%;
    margin-top: 18px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    &:last-of-type {
      padding-bottom: 22px;
    }
    .notice-left {
      width: calc(100% - 110px);
      margin-right: 28px;
      .title {
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        font-size: 13px;
        color: #303133;
        line-height: 21px;
      }
      .bottom {
        margin-top: 10px;
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        font-size: 13px;
        color: #999999;
        i {
          font-size: 12px;
        }
        span:last-of-type {
          padding-left: 10px;
        }
      }
    }

    &:first-of-type {
      margin-top: 0;
    }
  }
}
.width100 {
  width: 100% !important;
}
.left-line {
  position: relative;
}
.left-line::before {
  content: '';
  position: absolute;
  top: 22px;
  bottom: 22px;
  left: 0;
  width: 1px;
  background-color: #f2f6fc;
}
.icondakai,
.iconyincang {
  cursor: pointer;
}
.dealt-date {
  ::v-deep .el-calendar__header {
    padding: 20px;
  }
  .calendar_area {
    width: 100%;
    text-align: center;
    display: flex;
    justify-content: center;
    height: 40px;
    align-items: center;
    position: relative;
  }
  ::v-deep .el-calendar-table td .dealt-content {
    i {
      position: absolute;
      right: 0;
      top: 0;
      font-size: 14px;
      font-weight: bold;
    }
  }
  .dealt-date-item {
    width: 100%;
    height: 100%;
  }
  .is-selected {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #1890ff;
  }
  position: relative;
  ::v-deep .el-calendar-table .el-calendar-day {
    height: 40px;
    padding: 0;
  }
  ::v-deep .el-calendar__body {
    padding-bottom: 20px;
  }
  ::v-deep .el-calendar-table__row td {
    text-align: center;
  }
  ::v-deep .el-calendar__header {
    justify-content: center;
  }
  ::v-deep .el-calendar__button-group {
    .el-button-group {
      button {
        color: #000000;
        border: none;
      }
      button:nth-of-type(1) {
        position: absolute;
        left: 20px;
        top: 18px;
      }
      button:nth-of-type(2) {
        display: none;
      }
      button:nth-of-type(3) {
        position: absolute;
        right: 20px;
        top: 18px;
      }
    }
  }
}
.iconxitong-xitongshezhi-cebian {
  color: #606266;
  cursor: pointer;
  font-size: 20px;
}
.notice-right {
  width: 78px;
  flex-shrink: 0;
  .img {
    width: 78px;
    height: 52px;
    border-radius: 4px;
    object-fit: cover;
  }
}

// 周视图日历样式
.week-calendar {
  padding: 16px;
  height: 414px;
  .calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;

    .calendar-title {
      display: flex;
      align-items: center;
      font-family: PingFang SC, PingFang SC;
      font-weight: 500;
      font-size: 16px;
      color: #303133;

      .iconfont {
        margin-right: 6px;
        color: #1890ff;
      }
    }

    .calendar-actions {
      .today-btn {
        font-size: 12px;
        color: #909399;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        transition: all 0.3s;

        &:hover {
          color: #1890ff;
          background: #f0f7ff;
        }

        &.active {
          color: #1890ff;
          font-weight: 500;
        }
      }
    }
  }

  .week-nav {
    display: flex;
    align-items: center;

    .nav-btn {
      width: 20px;
      height: 20px;
      background: #f5f5f5;
      border-radius: 4px 4px 4px 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: #909399;
      border-radius: 4px;
      transition: all 0.3s;
      flex-shrink: 0;

      &:hover {
        color: #1890ff;
        background: #f0f7ff;
      }

      &.disabled {
        color: #c0c4cc;
        cursor: not-allowed;

        &:hover {
          background: none;
        }
      }
    }

    .week-days {
      flex: 1;
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 4px;

      .day-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 8px 4px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;

        .week-name {
          font-family: PingFang SC, PingFang SC;
          font-weight: 400;
          font-size: 13px;
          color: #9e9e9e;
          margin-bottom: 4px;
        }

        .day-num {
          width: 22px;
          height: 22px;
          border-radius: 4px 4px 4px 4px;
          font-family: D-DIN-PRO, D-DIN-PRO;
          font-weight: 600;
          font-size: 12px;
          color: #303133;
          text-align: center;
          line-height: 22px;
          margin-top: 6px;
        }

        &:hover {
          background: #f5f7fa;
        }
        &.selected {
          .day-num {
            color: #1890ff;
          }
        }

        &.today {
          // background: #e6f7ff;

          .day-num {
            background: #1890ff;
            color: #fff !important;
          }
        }
      }
    }
  }
}
</style>

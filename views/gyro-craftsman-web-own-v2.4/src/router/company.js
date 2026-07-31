import newLayout from '@/layout'
import { roterPre } from '@/settings'
const pathUrl = `${roterPre}/user`
const companyRouter = {
  path: pathUrl,
  name: 'users',
  meta: {
    title: '用户管理'
  },
  alwaysShow: true,
  component: newLayout,
  children: [
    {
      path: 'resume',
      component: () => import('@/views/user/resume/index'),
      name: 'resume',
      meta: { title: '我的简历', noCache: true }
    },
    {
      path: roterPre + '/program/programList/taskDetails',
      component: () => import('@/views/program/programList/taskDetails.vue'),
      name: 'taskDetails',
      meta: { title: '项目详情' }
    },
    {
      path: roterPre + '/program/programList/dynamics',
      component: () => import('@/views/program/programList/dynamics.vue'),
      name: 'dynamics',
      meta: { title: '项目动态' }
    },
    {
      path: roterPre + '/hr/attendance/setting/addConent',
      component: () => import('@/views/hr/attendance/setting/addConent.vue'),
      name: 'addConent',
      meta: { title: '新增考勤设置' }
    },
    {
      path: roterPre + '/develop/dictionary/management',
      component: () => import('@/views/develop/dictionary/management'),
      name: 'management',
      meta: { title: '字典管理' }
    },
    {
      path: roterPre + '/develop/dictionary/optionSetting',
      component: () => import('@/views/develop/dictionary/optionSetting'),
      name: 'optionSetting',
      meta: { title: '字典设置' }
    },
    {
      path: roterPre + '/customer/clue/dictSetting',
      component: () => import('@/views/customer/clue/dictSetting'),
      name: 'clueDictSetting',
      meta: { title: '线索字典设置' }
    },
    {
      path: roterPre + '/customer/list/dictSetting',
      component: () => import('@/views/customer/list/dictSetting'),
      name: 'customerDictSetting',
      meta: { title: '客户字典设置' }
    },
    {
      path: roterPre + '/customer/opportunityManagement/dictSetting',
      component: () => import('@/views/customer/opportunityManagement/dictSetting'),
      name: 'opportunityDictSetting',
      meta: { title: '商机字典设置' }
    },
    {
      path: roterPre + '/customer/contract/dictSetting',
      component: () => import('@/views/customer/contract/dictSetting'),
      name: 'contractDictSetting',
      meta: { title: '订单字典设置' }
    },
    {
      path: roterPre + '/customer/liaison/dictSetting',
      component: () => import('@/views/customer/liaison/dictSetting'),
      name: 'liaisonDictSetting',
      meta: { title: '联系人字典设置' }
    },
    {
      path: roterPre + '/customer/product/dictSetting',
      component: () => import('@/views/customer/product/dictSetting'),
      name: 'productDictSetting',
      meta: { title: '产品字典设置' }
    },
    {
      path: roterPre + '/customer/product/addProduct',
      component: () => import('@/views/customer/product/addProduct.vue'),
      name: 'addProduct',
      meta: { title: '添加产品' }
    },
    {
      path: roterPre + '/develop/crud/design',
      component: () => import('@/views/develop/crud/design'),
      name: 'design',
      meta: { title: '实体设计' }
    },
    {
      path: `${roterPre}/user/cloudfile/index`,
      name: 'cloudfile',
      component: () => import('@/views/user/cloudfile/index'),
      meta: { title: '云盘' }
    },
    {
      path: `${roterPre}/user/todo`,
      name: 'todo',
      component: () => import('@/views/user/todo/index'),
      meta: { title: '待办' }
    },
    {
      path: `${roterPre}/user/calendar`,
      name: 'calendar',
      component: () => import('@/views/user/calendar/index'),
      meta: { title: '日程' }
    },
    {
      path: 'news/subscribe',
      component: () => import('@/views/user/news/subscribe'),
      name: 'subscribe',
      meta: { title: '订阅消息', noCache: true }
    }
  ]
}

export default companyRouter

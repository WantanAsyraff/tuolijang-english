import i18n from '@/lang'
import newLayout from '@/layout'
import { roterPre } from '@/settings'
const pathUrl = `${roterPre}/user`
const companyRouter = {
  path: pathUrl,
  name: 'users',
  meta: {
    title: i18n.t('systemText.userManagement')
  },
  alwaysShow: true,
  component: newLayout,
  children: [
    {
      path: 'resume',
      component: () => import('@/views/user/resume/index'),
      name: 'resume',
      meta: { title: i18n.t('legacyScript.myResume'), noCache: true }
    },
    {
      path: roterPre + '/program/programList/taskDetails',
      component: () => import('@/views/program/programList/taskDetails.vue'),
      name: 'taskDetails',
      meta: { title: i18n.t('legacyScript.projectDetails') }
    },
    {
      path: roterPre + '/program/programList/dynamics',
      component: () => import('@/views/program/programList/dynamics.vue'),
      name: 'dynamics',
      meta: { title: i18n.t('legacyScript.projectActivity') }
    },
    {
      path: roterPre + '/hr/attendance/setting/addConent',
      component: () => import('@/views/hr/attendance/setting/addConent.vue'),
      name: 'addConent',
      meta: { title: i18n.t('legacyScript.addAttendanceSettings') }
    },
    {
      path: roterPre + '/develop/dictionary/management',
      component: () => import('@/views/develop/dictionary/management'),
      name: 'management',
      meta: { title: i18n.t('legacyScript.dictionaryManagement') }
    },
    {
      path: roterPre + '/develop/dictionary/optionSetting',
      component: () => import('@/views/develop/dictionary/optionSetting'),
      name: 'optionSetting',
      meta: { title: i18n.t('legacyScript.dictionarySettings') }
    },
    {
      path: roterPre + '/customer/clue/dictSetting',
      component: () => import('@/views/customer/clue/dictSetting'),
      name: 'clueDictSetting',
      meta: { title: i18n.t('legacyScript.leadDictionarySettings') }
    },
    {
      path: roterPre + '/customer/list/dictSetting',
      component: () => import('@/views/customer/list/dictSetting'),
      name: 'customerDictSetting',
      meta: { title: i18n.t('legacyScript.customerDictionarySettings') }
    },
    {
      path: roterPre + '/customer/opportunityManagement/dictSetting',
      component: () => import('@/views/customer/opportunityManagement/dictSetting'),
      name: 'opportunityDictSetting',
      meta: { title: i18n.t('legacyScript.opportunityDictionarySettings') }
    },
    {
      path: roterPre + '/customer/contract/dictSetting',
      component: () => import('@/views/customer/contract/dictSetting'),
      name: 'contractDictSetting',
      meta: { title: i18n.t('legacyScript.orderDictionarySettings') }
    },
    {
      path: roterPre + '/customer/liaison/dictSetting',
      component: () => import('@/views/customer/liaison/dictSetting'),
      name: 'liaisonDictSetting',
      meta: { title: i18n.t('legacyScript.contactDictionarySettings') }
    },
    {
      path: roterPre + '/customer/product/dictSetting',
      component: () => import('@/views/customer/product/dictSetting'),
      name: 'productDictSetting',
      meta: { title: i18n.t('legacyScript.productDictionarySettings') }
    },
    {
      path: roterPre + '/customer/product/addProduct',
      component: () => import('@/views/customer/product/addProduct.vue'),
      name: 'addProduct',
      meta: { title: i18n.t('legacyScript.addProduct') }
    },
    {
      path: roterPre + '/develop/crud/design',
      component: () => import('@/views/develop/crud/design'),
      name: 'design',
      meta: { title: i18n.t('ui.developCrudEntityTableEntityDesign') }
    },
    {
      path: `${roterPre}/user/cloudfile/index`,
      name: 'cloudfile',
      component: () => import('@/views/user/cloudfile/index'),
      meta: { title: i18n.t('systemText.cloudDrive') }
    },
    {
      path: `${roterPre}/user/todo`,
      name: 'todo',
      component: () => import('@/views/user/todo/index'),
      meta: { title: i18n.t('systemText.todo') }
    },
    {
      path: `${roterPre}/user/calendar`,
      name: 'calendar',
      component: () => import('@/views/user/calendar/index'),
      meta: { title: i18n.t('legacyScript.schedule') }
    },
    {
      path: 'news/subscribe',
      component: () => import('@/views/user/news/subscribe'),
      name: 'subscribe',
      meta: { title: i18n.t('legacyScript.subscriptionMessages'), noCache: true }
    }
  ]
}

export default companyRouter

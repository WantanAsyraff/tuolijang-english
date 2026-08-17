<template>
  <BaseContainer class="base-container">
    <!-- 顶部 -->
    <view class="cr-position-header">
      <default-nav-bar :is-right="true" :defaultTitle="data.id > 0 ? $t('mobile.ui.navigation.editContract') : $t('mobile.navigation.pages/customer/signing/addForm')"> </default-nav-bar>
      <!-- tab切换 -->
      <view class="tab-box">
        <view class="tab-item" :class="{ active: currentTab === '1' }" @click="currentTab = '1'">{{ $t('ui.customerOpportunityProductPanelProductList') }}</view>
        <view class="tab-item" :class="{ active: currentTab === '2' }" @click="currentTab = '2'">{{ $t('ui.customerSigningDetailItemSigningInformation') }}</view>
      </view>
    </view>

    <view class="search">
      <view class="required-box"><text>{{ $t('ui.customerContractPayDetailCustomerName') }}</text><text class="required">*</text></view>
      <picker
        class="picker-selector"
        mode="selector"
        @change="bindPickerChange"
        :value="selectedIndex"
        :disabled="formData.eid"
        :range="options"
        range-key="customer_name"
      >
        <view class="search-default-label">{{ data.customerInfo.customer_name || '--' }} </view>
      </picker>
    </view>

    <!-- 产品清单 -->
    <orderItem :eid="formData.eid" id="product-list" ref="orderItemRef" />
    <!-- 表单内容 -->
    <view class="form-card" id="sign-form">
      <uni-forms :border="false" :modelValue="formData" ref="form" label-width="80px" :rules="data.rules">
        <!-- 合同信息 -->
        <view class="form-item">
          <view class="title">{{ $t('ui.customerSigningDetailItemSigningInformation') }}</view>
          <uni-forms-item class="label-box">
            <template v-slot:label>
              <view class="label">{{ $t('ui.customerSigningAddFormContractName') }}<text class="iconfont">*</text> </view>
            </template>
            <uni-easyinput
              :inputBorder="false"
              v-model="formData.doc_name"
              type="text"
              :clearable="false"
              @change="getProcessInfo"
              :autoHeight="true"
              :styles="styles"
              :placeholder-style="placeholderStyle"
              :placeholder="$t('ui.examineFormTimeFromPleaseEnter')"
            >
            </uni-easyinput>
          </uni-forms-item>
          <uni-forms-item class="label-box">
            <template v-slot:label>
              <view class="label">{{ $t('ui.customerSigningDetailItemSigningMethod') }}<text class="iconfont">*</text> </view>
            </template>
            <uni-data-checkbox v-model="formData.sign_type" @change="getProcessInfo" :localdata="data.typesOptions" />
          </uni-forms-item>
          <uni-forms-item class="label-box">
            <template v-slot:label>
              <view class="label">{{ $t('ui.customerSigningAddFormContractTerm') }}<text class="iconfont">*</text> </view>
            </template>
            <uni-data-checkbox v-model="formData.term_type" @change="getProcessInfo" :localdata="data.termOptions" />
          </uni-forms-item>
          <uni-forms-item class="label-box" v-if="formData.term_type == 2">
            <template v-slot:label>
              <view class="label">{{ $t('ui.customerSigningAddFormContractDurationDays') }}<text class="iconfont">*</text> </view>
            </template>
            <uni-easyinput
              :inputBorder="false"
              v-model="formData.date_count"
              @change="getProcessInfo"
              type="text"
              :clearable="false"
              :autoHeight="true"
              :styles="styles"
              :placeholder-style="placeholderStyle"
              :placeholder="$t('ui.attendanceShiftAddEnter')"
            >
            </uni-easyinput>
          </uni-forms-item>
          <template v-if="formData.term_type == 1">
            <uni-forms-item class="label-box">
              <template v-slot:label>
                <view class="label">{{ $t('ui.customerSigningDetailItemStartDate') }}<text class="iconfont">*</text> </view>
              </template>
              <uni-datetime-picker
                v-model="formData.start_date"
                @change="getProcessInfo"
                :border="false"
                :clear-icon="false"
                type="date"
                return-type="string"
                format="YYYY-MM-DD"
                :placeholder="$t('ui.examineFormCustomCheckboxPleaseSelect')"
                :placeholder-style="placeholderStyle"
              />
            </uni-forms-item>
            <uni-forms-item class="label-box">
              <template v-slot:label>
                <view class="label">{{ $t('ui.customerSigningDetailItemEndDate') }}<text class="iconfont">*</text> </view>
              </template>
              <uni-datetime-picker
                v-model="formData.end_date"
                @change="getProcessInfo"
                :border="false"
                :clear-icon="false"
                type="date"
                return-type="string"
                format="YYYY-MM-DD"
                :placeholder="$t('ui.examineFormCustomCheckboxPleaseSelect')"
                :placeholder-style="placeholderStyle"
              />
            </uni-forms-item>
          </template>

          <uni-forms-item class="is-direction-top">
            <template v-slot:label>
              <view class="file-title">
                <view>{{ $t('ui.customerSigningAddFormUploadSignedFile') }} </view>
                <text class="iconfont icon-biaodan-tianjia" @click="handleAddFile" v-if="!formData.sign_file" />
              </view>
              <view class="tips">{{ $t('ui.customerSigningAddFormPdfPngJpgWordExcelTxtAndOtherFormats') }}</view>
            </template>
            <oa-uploadList v-if="formData.sign_file" clearBtn="true" @deleteFile="deleteFile" :listData="[formData.sign_file]"></oa-uploadList>
          </uni-forms-item>
        </view>
        <!-- 签署信息 -->
        <view class="form-item">
          <view class="title">
            <text class="tag">{{ $t('ui.customerSigningDetailItemOurCompany') }}</text>
            <text class="company-name"> {{ formData.signatory[0].company_name }}</text>
          </view>
          <uni-forms-item class="label-box">
            <template v-slot:label>
              <view class="label">{{ $t('ui.customerSigningAddFormHandler') }}<text class="iconfont">*</text> </view>
            </template>
            <view class="placeholder" @click="selectMember(99)" v-if="!formData.signatory[0].user_id">
              {{ $t('ui.examineFormCustomCheckboxPleaseSelect') }} <text class="iconfont icon-jinru-copy"></text>
            </view>
            <view @click="selectMember(99, formData.signatory[0])" v-if="formData.signatory[0].user_id">
              {{ formData.signatory[0].name }} <text class="iconfont icon-jinru-copy"></text>
            </view>
          </uni-forms-item>
          <uni-forms-item class="label-box">
            <template v-slot:label>
              <view class="label">{{ $t('ui.userUserPhonePhoneNumber') }}<text class="iconfont">*</text> </view>
            </template>
            <uni-easyinput
              :inputBorder="false"
              v-model="formData.signatory[0].phone"
              type="text"
              :clearable="false"
              :autoHeight="true"
              :styles="styles"
              :placeholder-style="placeholderStyle"
              :placeholder="$t('ui.attendanceShiftAddEnter')"
            >
            </uni-easyinput>
          </uni-forms-item>
        </view>
        <!-- 动态签署方 -->
        <view v-for="(item, index) in formData.signatory.slice(1)" :key="index">
          <view class="del-text">
            {{ $t('ui.customerSigningDetailItemSigner') }}{{ index + 1 }}
            <text class="iconfont icon-danchuang-shanchu" @click="handleDelete(index)"></text>
          </view>
          <view class="form-item">
            <uni-forms-item class="label-box">
              <template v-slot:label>
                <view class="label">{{ $t('ui.customerSigningDetailItemSigner') }}<text class="iconfont">*</text> </view>
              </template>
              <uni-data-checkbox v-model="item.types" :localdata="data.signatoryOptions" />
            </uni-forms-item>
            <uni-forms-item class="label-box" v-if="item.types == 2">
              <template v-slot:label>
                <view class="label">{{ $t('ui.customerSigningAddFormEnterpriseName') }}<text class="iconfont">*</text> </view>
              </template>
              <uni-easyinput
                :inputBorder="false"
                v-model="item.company_name"
                type="text"
                :clearable="false"
                :autoHeight="true"
                :styles="styles"
                :placeholder-style="placeholderStyle"
                :placeholder="$t('ui.examineFormTimeFromPleaseEnter')"
              >
              </uni-easyinput>
            </uni-forms-item>
            <uni-forms-item class="label-box">
              <template v-slot:label>
                <view class="label">{{ $t('ui.customerSigningAddFormHandler') }}<text class="iconfont">*</text> </view>
              </template>
              <uni-easyinput
                :inputBorder="false"
                v-model="item.name"
                type="text"
                :clearable="false"
                :autoHeight="true"
                :styles="styles"
                :placeholder-style="placeholderStyle"
                :placeholder="$t('ui.attendanceShiftAddEnter')"
              >
              </uni-easyinput>
            </uni-forms-item>
            <uni-forms-item class="label-box">
              <template v-slot:label>
                <view class="label">{{ $t('ui.userUserPhonePhoneNumber') }}<text class="iconfont">*</text></view>
              </template>
              <uni-easyinput
                :inputBorder="false"
                v-model="item.phone"
                type="text"
                :clearable="false"
                :autoHeight="true"
                :styles="styles"
                :placeholder-style="placeholderStyle"
                :placeholder="$t('ui.attendanceShiftAddEnter')"
              >
              </uni-easyinput>
            </uni-forms-item>
            <view class="label-box" v-if="formData.signatory.length - 1 == index + 1">
              <view class="addText" @click="addSignatory">
                <text class="iconfont icon-biaodan-tianjia" />
                {{ $t('ui.customerSigningAddFormAddSigner') }}
              </view>
            </view>
          </view>
        </view>
        <!-- 备注 -->
        <view class="form-item">
          <uni-forms-item class="is-direction-top">
            <template v-slot:label>
              <view class="label">{{ $t('ui.customerSigningDetailItemRemarkInformation') }} </view>
            </template>
            <uni-easyinput
              :inputBorder="false"
              v-model="formData.mark"
              type="textarea"
              :clearable="false"
              :autoHeight="true"
              :styles="styles"
              :placeholder-style="placeholderStyle"
              :placeholder="$t('ui.customerSigningAddFormPleaseFillInTheRemarks')"
            >
            </uni-easyinput>
          </uni-forms-item>
        </view>

        <!-- 审批流程 -->
        <view class="form-item">
          <process :examine-data="data.examineData" />
        </view>
      </uni-forms>
    </view>
    <view>
      <BaseBottomBtn :text="$t('ui.replyComponentIndexSubmit')" :disabled="data.disabled" @click="clickSubmit" />
    </view>
  </BaseContainer>
</template>
<script setup>import appI18n from '@/locale';

import BaseContainer from '@/components/BaseContainer/index.vue'
import BaseBottomBtn from '@/components/BaseBottomBtn/index.vue'
import defaultNavBar from '@/components/defaultNavBar/index.vue'
import oaUploadList from '@/components/oa-uploadList/index.vue'
import orderItem from './components/orderItem.vue'
import { formatBytes } from '@/utils/file'
import process from '@/pages/users/examine/components/process.vue'
import { ref, reactive, watch, computed } from 'vue'
import message from '@/utils/message'
import { clickNavigateTo, delayedReLaunch, isFileTypeIcon } from '@/utils/helper'
import { navigateToDepartment, resetExamineIndex, resetSelectDepartment } from '@/utils/autoload'
import {
  getContractDocProcessApi,
  getContractDocTaskApi,
  contractDocAddApi,
  contractDocUpdateApi,
  getCustomerDetailApi,
  getContractDocEditApi,
} from '@/api/signing'
import { isWxWorkEnv } from '@/libs/wxwork'
import { useStore } from 'vuex'
const store = useStore()
const placeholderStyle = ref('color: #C0C4CC;font-size: 30rpx')
const styles = reactive({
  color: '#303133',
  disableColor: '#ffffff',
  fontSize: '30rpx',
})
const orderItemRef = ref(null)
const currentTab = ref('1')
const formData = ref({
  eid: '',
  cid: [],
  link_type: 5,
  productInfo: [],
  doc_name: '',
  sign_type: 2,
  term_type: 2, //
  date_count: '', // 合同时期（天）
  start_date: '',
  end_date: '',
  sign_file: null,
  file_id: '',
  mark: '', // 备注信息
  processInfo: [], // 审批流程节点
  signatory: [
    {
      types: 0,
      user_id: 0,
      company_name: JSON.parse(uni.getStorageSync('storageUserData')).enterprise.enterprise_name,
      phone: '',
      name: '',
    },
    {
      types: 2,
      user_id: 0,
      name: '',
      company_name: '',
      phone: '',
    },
  ],
})
// 定义表单
const data = reactive({
  id: '',
  disabled: false,
  customerInfo: {},
  isShow: false,
  examineData: {},
  fileLoading: false,
  timer: null,
  selectMemberIndex: -1,
  isSelectMember: false,
  typesOptions: [
    {
      text: appI18n.global.t('ui.customerSigningDetailItemESign'),
      value: 2,
    },
    {
      text: appI18n.global.t('ui.customerSigningDetailItemOfflineSigning'),
      value: 1,
    },
  ],
  termOptions: [
    {
      text: appI18n.global.t('ui.customerSigningAddFormStartFromSigningDate'),
      value: 2,
    },
    {
      text: appI18n.global.t('ui.customerSigningAddFormFixedDate'),
      value: 1,
    },
    {
      text: appI18n.global.t('ui.customerSigningAddFormNoFixedTerm'),
      value: 0,
    },
  ],
  signatoryOptions: [
    {
      text: appI18n.global.t('ui.customerSigningDetailItemEnterprise'),
      value: 2,
    },
    {
      text: appI18n.global.t('ui.customerSigningDetailItemPersonal'),
      value: 1,
    },
  ],
})
watch(currentTab, (newvalue) => {
  if (newvalue == '1') {
    // 根据id滚动到产品清单
    uni
      .createSelectorQuery()
      .select('#product-list')
      .boundingClientRect()
      .exec((res) => {
        if (res[0]) {
          uni.pageScrollTo({
            scrollTop: res[0].top - 100, // 减去顶部导航栏高度
            duration: 300,
          })
        }
      })
  } else {
    // 根据id滚动到签约表单
    uni
      .createSelectorQuery()
      .select('#sign-form')
      .boundingClientRect()
      .exec((res) => {
        if (res[0]) {
          uni.pageScrollTo({
            scrollTop: res[0].top - 100, // 减去顶部导航栏高度
            duration: 300,
          })
        }
      })
  }
})

const leadData = ref(null)
onLoad((e) => {
  // 取缓存的合同签约信息赋值

  if (!e.id) {
    let info = uni.getStorageSync('contractSignForm')
    if (info) {
      formData.value = JSON.parse(info)
      formData.value.sign_file = ''
      formData.value.file_id = ''
    } else {
      // 设置选中成员
      let userInfo = JSON.parse(uni.getStorageSync('storageUserData'))
      formData.value.signatory[0].user_id = userInfo.userInfo.id
      formData.value.signatory[0].name = userInfo.userInfo.name
      formData.value.signatory[0].phone = userInfo.userInfo.phone

      let options = [{ id: userInfo.userInfo.id, name: userInfo.userInfo.name }]
      resetSelectDepartment(options, [userInfo.userInfo.id])
    }
  }

  if (e.id) {
    data.id = e.id
    getDetail(e.id)
  }
  if (e.cid) {
    // 从订单进来默认选中当前订单和产品
    formData.value.cid = []
    data.isShow = true
    formData.value.link_type = 2
    formData.value.cid.push(Number(e.cid))
  }
  if (e.oid) {
    // 从商机进来默认选种当前商机和产品
    formData.value.cid = []
    data.isShow = true
    formData.value.link_type = 5
    formData.value.cid.push(Number(e.oid))
  }
  if (e.eid) {
    getCustomerDetailApi(e.eid).then((res) => {
      if (res.status == 200) {
        data.customerInfo = res.data
        formData.value.signatory[1].company_name = res.data.customer_name
        formData.value.signatory[1].phone = res.data.customer_tel
        console.log(res, 8888)
      }
    })
    formData.value.eid = e.eid
  }

  if (e.type === 'add') {
    data.id = 0
  }

  setTimeout(() => {
    getProcessInfo()
  }, 200)
})
const getDetail = (id) => {
  getContractDocEditApi(id, {}).then((res) => {
    if (res.status == 200) {
      formData.value = res.data
      formData.value.sign_file = ''
      formData.value.file_id = ''
      orderItemRef.value.setProductList(formData.value.cid || [], formData.value.link_type || 5, data.isShow, 'noFilter')
    }
  })
}
const getSelectPeople = computed(() => {
  return store.state.app.depSelectPeople
})
onMounted(() => {
  orderItemRef.value.setProductList(formData.value.cid || [], formData.value.link_type || 5, data.isShow)
})

// 监听选择人员
watch(
  getSelectPeople,
  (newvalue, oldvalue) => {
    if (newvalue.length > 0 && store.state.app.depUserIndex == 99) {
      formData.value.signatory[0].user_id = newvalue[0].value || newvalue[0].id
      formData.value.signatory[0].name = newvalue[0].name
      data.selectMemberIndex = -1
    }
  },
  {
    deep: true,
  },
)

import { uploadFlie } from '@/utils/file'
// 选择人员
const selectMember = (index, row) => {
  resetExamineIndex()
  store.commit('setDepUserIndex', index)
  let checkIds = []
  let ids = []
  data.selectMemberIndex = 99
  let query = 'mode=selector'
  if (formData.value.signatory[0].user_id) {
    // 设置选中成员
    let options = [{ id: formData.value.signatory[0].user_id, name: formData.value.signatory[0].name }]
    resetSelectDepartment(options, [options[0].id])
    navigateToDepartment(query, 'pages/customer/signing/addForm', '/pages/users/department/index')
  } else {
    store.commit('setSelectCustomUsers', [])
    navigateToDepartment(query, 'pages/customer/signing/addForm', '/pages/users/department/index')
  }
}

// 删除附件
const deleteFile = (indexs) => {
  formData.value.sign_file = ''
}

// 上传附件
const handleAddFile = () => {
  uploadFlie('common/upload', {}, 100).then((res) => {
    if (res.status == 200) {
      formData.value.sign_file = res.data
      setTimeout(() => {
        getProcessInfo()
      }, 100)
    } else {
      message.error(res.message || '上传失败')
    }
  })
}

// 获取审批流程数据
const getProcessInfo = async () => {
  getContractDocProcessApi(formData.value)
    .then((res) => {
      if (res.status == 200) {
        data.examineData = res.data
        formData.value.processInfo = res.data.list
        // 获取合同文件转换结果
        if (formData.value.sign_type == '2' && formData.value.sign_file && res.data.file && !res.data.file.file_id) {
          data.fileLoading = true

          data.disabled = true
          data.timer = setInterval(() => {
            getContractDocTaskApi(res.data.file.task_id)
              .then((result) => {
                if (result.data.convert_file_id) {
                  formData.value.file_id = result.data.convert_file_id
                  clearInterval(data.timer)
                  data.fileLoading = false
                  message.success(result.data.message || '文件转换成功')
                  data.timer = null
                  data.disabled = false
                } else {
                  message.error(result.data.message || '文件转换失败')
                }
              })
              .catch((error) => {
                message.error(error.message || '文件转换失败')
              })
          }, 2000)
        }
      }
    })
    .catch((error) => {
      message.error(error.message)
    })
}

const addSignatory = () => {
  if (formData.value.signatory.length >= 4) {
    message.error(appI18n.global.t('ui.customerSigningAddFormUpToThreeSignersCanBeAdded'))
    return
  }
  formData.value.signatory.push({
    types: 1,
    user_id: 0,
    name: '',
    company_name: '',
    phone: '',
  })
}
const handleDelete = (index) => {
  if (formData.value.signatory.length <= 2) {
    message.error(appI18n.global.t('ui.customerSigningAddFormTheFirstSignerCannotBeDeleted'))
    return false
  }
  formData.value.signatory.splice(index + 1, 1)
}

const clickSubmit = () => {
  let productInfo = orderItemRef.value.getProductForm()
  formData.value.productInfo = productInfo.list
  formData.value.link_type = productInfo.link_type
  formData.value.cid = productInfo.selectedIds
  if (data.disabled) {
    return false
  }

  let processInfo = data.examineData.list

  if (processInfo && processInfo.length) {
    let len = 0
    for (let i = 0; i < processInfo.length; i++) {
      const value = processInfo[i]
      if (!data.approverDelete && value.types == 1 && value.users.length <= 0) {
        message.error(appI18n.global.t('ui.customerSigningAddFormTheSelectableNodeCannotBeEmpty'))
        return
      }
      if (data.approverDelete && value.types == 1 && (value.settype == 4 || value.settype == 1) && value.users.length <= 0) {
        message.error(appI18n.global.t('ui.customerSigningAddFormTheSelectableNodeCannotBeEmpty'))
        return
      }
      if (value.users.length <= 0) {
        len++
      }
    }
    if (len === processInfo.length) {
      message.error(appI18n.global.t('ui.customerSigningAddFormTheSelectableNodeCannotBeEmpty'))
      return
    }
    processInfo.forEach((value, index) => {
      if (value.users.length <= 0) {
        processInfo.splice(index, 1)
      }
    })
  }

  if (!validateForm()) {
    return false
  }
  if (data.id > 0) {
    // 编辑
    contractDocUpdateApi(data.id, formData.value)
      .then((res) => {
        if (res.status == 200) {
          message.success(appI18n.global.t('ui.customerSigningAddFormEditSuccess'))
          clickNavigateTo(`/pages/customer/signing/details?id=${data.id}`)
        }
      })
      .catch((error) => {
        message.error(error.message || '添加失败')
      })
  } else {
    uni.setStorageSync('contractSignForm', JSON.stringify(formData.value))
    contractDocAddApi(formData.value)
      .then((res) => {
        if (res.status == 200) {
          message.success(appI18n.global.t('ui.customerListSuccessPopupAddedSuccessfully'))
          uni.navigateBack()
        }
      })
      .catch((error) => {
        message.error(error.message || '添加失败')
      })
  }
}
const validateForm = () => {
  let valid = true
  if (!formData.value.doc_name) {
    message.error(appI18n.global.t('ui.customerSigningAddFormPleaseEnterContractName'))
    valid = false
  }
  if (!formData.value.sign_file) {
    message.error(appI18n.global.t('ui.customerSigningAddFormUploadTheContractFile'))
    valid = false
  }
  if (formData.value.term_type == 2 && !formData.value.date_count) {
    message.error(appI18n.global.t('ui.customerSigningAddFormEnterTheContractPeriodDays'))
    valid = false
  }
  if (formData.value.term_type == 1 && (!formData.value.start_date || !formData.value.end_date)) {
    message.error(appI18n.global.t('ui.customerSigningAddFormEnterTheFixedDate'))
    valid = false
  }
  return valid
}
</script>
<style lang="scss" scoped>
.cr-position-header {
  padding-top: var(--status-bar-height);
  // background-color: #fff;
  position: sticky;
  top: 0;
  z-index: 1;
}

::v-deep .uni-input-input {
  font-size: 28rpx !important;
}

.tab-box {
  width: 100%;
  height: 72rpx;
  background: #ffffff;
  display: flex;
  justify-content: center;
  align-items: center;
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 24rpx !important;
  color: #606266;

  .tab-item {
    width: 100%;
    height: 72rpx;
    line-height: 72rpx;
    text-align: center;
  }

  .active {
    color: #303133;
  }
}

.form-card {
  margin-bottom: calc(var(--bottom-area-height) + 180rpx);

  .form-item {
    background-color: #fff;
    padding: 30rpx;
    padding-bottom: 0;
    margin-bottom: 16rpx;
    font-family:
      PingFang SC,
      PingFang SC;

    ::v-deep .uni-easyinput__content-input {
      text-align: right;
      padding-right: 0 !important;
    }

    .title {
      height: 24px;
      font-weight: 500;
      font-size: 30rpx;
      color: #303133;
      display: flex;
      align-items: start;

      .tag {
        display: inline-block;
        height: 42rpx;
        width: 82rpx;
        font-weight: 400;
        font-size: 22rpx;
        color: #ffffff;
        background: #1890ff;
        line-height: 42rpx;
        text-align: center;
        border-radius: 8rpx 8rpx 8rpx 8rpx;
        margin-top: 2px;
      }
      .company-name {
        margin-left: 10rpx;
      }
    }

    ::v-deep .uni-forms-item__content {
      text-align: right;
      font-weight: 400;
      font-size: 30rpx;
      color: #303133;
    }

    ::v-deep .uni-forms-item {
      margin-bottom: 0;
    }

    .label-box {
      align-items: center;
      min-height: 108rpx;
      border-bottom: 1rpx solid #ebeef5;

      .label {
        height: auto;
        font-weight: 400;
        font-size: 30rpx;
        color: #303133;
        display: flex;
        align-items: center;

        .iconfont {
          margin-top: 8rpx;
          margin-left: 5rpx;
          color: #ff2529;
        }
      }
    }
  }
}

::v-deep .uni-data-checklist .checklist-group {
  display: flex;
  justify-content: flex-end;

  .checklist-box {
    margin-left: 40rpx;
    margin-right: 0 !important;
  }

  .checklist-text {
    font-size: 30rpx !important;
    color: #303133 !important;
  }
}

::v-deep .uni-date__x-input {
  font-size: 30rpx !important;
  color: #303133 !important;
}

::v-deep .uni-date-x .icon-calendar {
  display: none;
}

.addText {
  cursor: pointer;
  text-align: center;
  line-height: 110rpx;
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 30rpx;
  color: #1890ff !important;
}

.del-text {
  padding: 0 30rpx;
  display: flex;
  justify-content: space-between;
  font-size: 26rpx;
  color: #666666;
  margin-bottom: 16rpx;
}

.tips {
  font-weight: 400;
  font-size: 20rpx;
  color: #999999;
}

// 上传附件
.flie {
  padding: 24rpx 24rpx 24rpx 0;

  .box {
    width: 100%;
    height: 40px;
    background: #f6f7f9;
    border-radius: 4px 4px 4px 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12rpx;
    padding-right: 20rpx;

    .icon-guanbi-yangshiyi1 {
      color: #999999;
    }

    .left {
      width: 100%;
      display: flex;
      font-family:
        PingFang SC,
        PingFang SC;
      font-weight: 400;

      .slot-image {
        // display: inline-block;
        flex-shrink: 0; // flex布局下图片挤压变形
        width: 52rpx;
        height: 52rpx;
        margin-right: 10rpx;
      }

      .name {
        width: calc(100% - 40px);
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 24rpx;
        color: #303133;
      }

      .size {
        font-size: 20rpx;
        color: #909399;
        margin-top: 2rpx;
      }
    }
  }
}

.file-title {
  margin-top: 30rpx;
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;

  .icon-biaodan-tianjia {
    font-size: 34rpx;
    color: #c0c4cc;
  }
}

.tips {
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 20rpx;
  color: #999999;
  margin-top: 12rpx;
  margin-bottom: 30rpx;
}

.placeholder {
  cursor: pointer;
  color: #c0c4cc;
}

.search {
  width: 100%;
  height: 100rpx;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 30rpx;
  margin: 16rpx 0;
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 30rpx;
  color: #303133;
  background-color: #ffffff;

  .required-box {
    position: relative;
  }

  .required {
    position: absolute;
    right: -10px;
    top: 12rpx;
    color: #ff4d4f;
    font-size: 36rpx;
  }
}
</style>

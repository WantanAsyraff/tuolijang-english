<template>
<div>
  <el-form ref="contractForm" label-width="auto" class="contract-form">
    <!-- 客户信息 -->
    <div class="form-section">
      <div class="section-title">{{ $("ui.customerSigningAddContractSignCustomerInformation") }}</div>
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item :label="$('ui.customerDetailsCustomerName')">
            <span class="info-text">{{
              dataInfo.customer && dataInfo.customer.customer_name ? dataInfo.customer.customer_name : '--'
            }}</span>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item :label="$('ui.customerSigningAddContractSignContactPhone')">
            <span class="info-text">{{
              dataInfo.customer && dataInfo.customer.customer_tel ? dataInfo.customer.customer_tel : '--'
            }}</span>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item :label="$('ui.customerSigningAddContractSignProvinceCityDistrict')">
            <span class="info-text">{{
              dataInfo.customer && dataInfo.customer.area_cascade ? $(dataInfo.customer.area_cascade) : '--'
            }}</span>
          </el-form-item>
        </el-col>
      </el-row>
    </div>

    <!-- 签约信息 -->
    <div class="form-section">
      <div class="section-title">{{ $("ui.customerSigningInfoItemSigningInformation") }}</div>
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item :label="$('ui.customerSigningInfoItemContractName')">
            <span class="info-text">{{ dataInfo.doc_name || '--' }}</span>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item :label="$('ui.customerSigningInfoItemSigningMethod')">
            <span class="info-text">{{ dataInfo.sign_type == 2 ? $('ui.customerSigningInfoItemESign') : $('ui.customerSigningInfoItemOfflineSigning') }}</span>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item :label="$('ui.customerSigningInfoItemElectronicContract')">
            <span v-if="dataInfo.app_url" class="sign-url over-text" @click="handleClick(dataInfo.app_url)">{{
              dataInfo.app_url
            }}</span>
            <span class="info-text" v-else>--</span>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item :label="$('ui.userCalendarAddTodoStartTime')">
            <span class="info-text">{{ dataInfo.start_date || '--' }}</span>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item :label="$('ui.userCalendarAddTodoEndTime')">
            <span class="info-text">{{ dataInfo.end_date || '--' }}</span>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item :label="$('ui.customerSigningInfoItemSigner')">
            <div v-for="(item, index) in dataInfo.signatory" :key="index">
              <div class="signer-info" v-if="item.types == 0">
                <span class="status" v-if="item.sign_status == 1">{{ $("ui.customerSigningInfoItemSigned") }}</span>
                <span class="status" v-if="item.sign_status == 2">{{ $("ui.userExamineExamineRejected") }}</span>
                <span class="status" v-if="item.sign_status == 0">{{ $("ui.customerSigningInfoItemPendingSigning") }}</span>
                <div class="signer-name">
                  {{ item.company_name || '--' }}
                  <span class="company-icon">{{ $("ui.customerSigningInfoItemOurCompany") }}</span>
                </div>
                <div class="signer-name mt6">
                  <span class="mr25">{{ $("ui.customerSigningInfoItemHandler") }}{{ item.name || '--' }}</span>
                  <span>{{ $("ui.customerSigningInfoItemPhoneNumber") }}{{ item.phone || '--' }}</span>
                </div>
              </div>
              <div class="signer-info" v-else>
                <span class="status" v-if="item.sign_status == 1">{{ $("ui.customerSigningInfoItemSigned") }}</span>
                <span class="status" v-if="item.sign_status == 2">{{ $("ui.userExamineExamineRejected") }}</span>
                <span class="status" v-if="item.sign_status == 0">{{ $("ui.customerSigningInfoItemPendingSigning") }}</span>
                <div class="signer-name">
                  {{ item.types == 1 ? item.name || '--' : item.company_name || '--' }}
                  <span class="company-icon individual" v-if="item.types == 1">{{ $("ui.commonOaFromBoxPersonal") }}</span>
                  <span class="company-icon individual" v-else>{{ $("ui.customerSigningInfoItemEnterprise") }}</span>
                </div>
                <div class="signer-name mt6">
                  <span>{{ $("ui.customerSigningInfoItemPhoneNumber") }}{{ item.phone || '--' }}</span>
                </div>
              </div>
            </div>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item :label="$('ui.customerSigningInfoItemFileBeforeSigning')">
            <oa-uploadList v-if="dataInfo.sign_file.length > 0" :fileList="dataInfo.sign_file"></oa-uploadList>
            <span v-else>--</span>
          </el-form-item>
          <el-form-item :label="$('ui.customerSigningInfoItemFileAfterSigning')">
            <oa-uploadList
              v-if="dataInfo.result && dataInfo.result != ''"
              :fileList="dataInfo.result"
            ></oa-uploadList>
            <span v-else>--</span>
          </el-form-item>
        </el-col>

        <el-col :span="12">
          <!-- <el-form-item :label="$('备注：')">
            <span class="info-text">{{ dataInfo.mark||'--' }}</span>
          </el-form-item> -->
        </el-col>
      </el-row>
    </div>

    <div class="form-section">
      <div class="section-title">{{ $("ui.customerSigningAddContractSignProductList") }}</div>

      <productList ref="productList" :type="`edit`" :product="dataInfo.products"></productList>
    </div>

    <div class="form-section">
      <div class="section-title">{{ $("ui.xmindEditorToolbarNodeBtnListRemarks") }}</div>
      <el-row :gutter="20">
        <el-col :span="12">
          <el-form-item :label="$('ui.fdEnterpriseListViewDetailsRemarks')">
            <span class="info-text">{{ dataInfo.mark || '--' }}</span>
          </el-form-item>
        </el-col>
      </el-row>
    </div>
  </el-form>
</div>
</template>
<script>
export default {
  props: {
    dataInfo: {
      type: Object,
      default: {}
    }
  },
  components: {
    oaUploadList: () => import('@/components/form-common/oa-uploadList.vue'),
    productList: () => import('@/views/customer/components/productList.vue')
  },
  methods: {
    handleClick(url) {
      window.open(url)
    }
  }
}
</script>
<style scoped lang="scss">
.contract-form {
  font-family: PingFang SC, PingFang SC;

  .form-section {
    margin-bottom: 10px;
    .section-title {
      margin: 0 0 20px 0;
      font-weight: 500;
      font-size: 14px;
      color: #303133;
      border-left: 3px solid #1890ff;
      padding-left: 10px;
    }
  }
  ::v-deep .el-form-item__label {
    font-weight: 400;
    font-size: 13px;
    color: #606266;
  }
  ::v-deep .el-form-item {
    margin-bottom: 10px;
  }

  .info-text {
    font-weight: 400;
    font-size: 13px;
    color: #303133;
  }
}
.signer-info {
  width: 370px;
  height: 67px;
  background: #f7f7f7;
  border-radius: 8px;
  padding: 12px;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  margin-bottom: 8px;
  position: relative;
  .status {
    display: flex;
    position: absolute;
    top: 12px;
    right: 12px;
    height: 19px;
    line-height: 19px;
    padding: 0 4px;
    justify-content: center;
    align-items: center;

    background: rgba(24, 144, 255, 0.05);
    border-radius: 4px;
    font-size: 11px;
    color: #1890ff;
    border: 0.5px solid #1890ff;
  }

  .signer-name {
    font-size: 13px;
    height: 18px;
    line-height: 18px;
    color: #303133;
  }
  .company-icon {
    display: inline-block;
    padding: 2px 4px;
    background: rgba(24, 144, 255, 0.08);
    border-radius: 4px;
    font-size: 11px;
    color: #1890ff;
    margin-left: 2px;
  }
  .individual {
    color: #ff9900;
    font-size: 11px;
    background-color: rgba(255, 153, 0, 0.08);
  }
  .mt6 {
    margin-top: 6px;
  }
  .mr25 {
    margin-right: 25px;
  }
}
.sign-url {
  display: inline-block;
  width: 350px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  cursor: pointer;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 13px;
  color: #1890ff;
}
</style>

<template>
<div class="divBox">
  <el-card class="card-box mt20">
    <div class="title-16">{{ $t("ui.settingWecomIndexWeComSettings") }}</div>
    <el-form class="main" label-width="auto">
      <div class="from-item-title mb15">
        <span>{{ $t("ui.settingWecomIndexWeComBasicSettings") }}</span>
      </div>

      <el-form-item :label="$t('ui.settingWecomIndexEnterpriseId')">
        <el-input v-model="form.wechat_work_corpid" size="small" :placeholder="$t('ui.settingWecomIndexPleaseEnterTheCorpId')" />
        <span class="tips">{{ $t("ui.settingWecomIndexViewTheWeComCorpIdUnderMyCompany") }}</span>
      </el-form-item>
      <el-form-item :label="$t('ui.settingWecomIndexRequireWeComBinding')">
        <el-switch
          v-model="form.wechat_work_forced_build"
          :active-value="1"
          :inactive-value="0"
          active-text="开启"
          inactive-text="关闭"
        >
        </el-switch>
        <div class="tips">{{ $t("ui.settingWecomIndexWhenEnabledCustomersMustBindAWeComAccount") }}</div>
      </el-form-item>
      <div class="dashed-divider" />

      <div class="from-item-title mb15">
        <span>{{ $t("ui.settingWecomIndexWeComContactsSettings") }}</span>
      </div>
      <el-form-item :label="$t('ui.settingWecomIndexContactsSynchronization')">
        <el-switch
          v-model="form.wechat_work_user_switch"
          :active-value="1"
          :inactive-value="0"
          active-text="开启"
          inactive-text="关闭"
        >
        </el-switch>
      </el-form-item>
      <template v-if="form.wechat_work_user_switch">
        <el-form-item :label="$t('ui.settingWecomIndexServerUrl')">
          <div class="flex">
            <el-input v-model="form.work_server_url" size="small" disabled :placeholder="$t('ui.settingWecomIndexPleaseEnterToken')" />
            <el-button size="small" type="primary" class="ml10" @click="copyFn(form.work_server_url)">{{ $t("ui.settingWecomIndexCopy") }}</el-button>
          </div>
          <span class="tips">{{ $t("ui.settingWecomIndexServerUrl2") }}</span>
        </el-form-item>
        <el-form-item label="Secret：">
          <el-input v-model="form.wechat_work_address_secret" size="small" :placeholder="$t('ui.settingWecomIndexPleaseEnterSecret')" />
          <span class="tips">{{ $t("ui.settingWecomIndexContactsSecretFindItInWeComUnderManagement") }}</span>
        </el-form-item>
        <el-form-item label="Token：">
          <div class="flex">
            <el-input v-model="form.wechat_work_token" size="small" :placeholder="$t('ui.settingWecomIndexPleaseEnterToken')" />
            <el-button size="small" type="primary" class="ml10" @click="copyFn(form.wechat_work_token)"
              >{{ $t("ui.settingWecomIndexCopy") }}</el-button
            >
            <el-button size="small" @click="token()">{{ $t("ui.settingWecomIndexGenerateRandomly") }}</el-button>
          </div>
          <span class="tips">{{ $t("ui.settingWecomIndexContactsEventServerToken") }}</span>
        </el-form-item>
        <el-form-item label="EncodingAESKey：">
          <div class="flex">
            <el-input v-model="form.wechat_work_aes_key" size="small" :placeholder="$t('ui.settingWecomIndexPleaseEnterEncodingAeskey')" />
            <el-button size="small" type="primary" class="ml10" @click="copyFn(form.wechat_work_aes_key)"
              >{{ $t("ui.settingWecomIndexCopy") }}</el-button
            >
            <el-button size="small" @click="encodingAESKeyGen">{{ $t("ui.settingWecomIndexGenerateRandomly") }}</el-button>
          </div>
          <span class="tips">{{ $t("ui.settingWecomIndexWeComContactsEncodingAeskey") }}</span>
        </el-form-item>
      </template>

      <div class="dashed-divider" />
      <div class="from-item-title mb15">
        <span>{{ $t("ui.settingWecomIndexWeComCustomerSettings") }}</span>
      </div>
      <el-form-item :label="$t('ui.settingWecomIndexCustomerSynchronization')">
        <el-switch
          v-model="form.wechat_work_client_switch"
          :active-value="1"
          :inactive-value="0"
          active-text="开启"
          inactive-text="关闭"
        >
        </el-switch>
      </el-form-item>
      <template v-if="form.wechat_work_client_switch">
        <el-form-item :label="$t('ui.settingWecomIndexDataSynchronization')">
          <el-radio-group v-model="form.wechat_work_client_radio">
            <el-radio label="clue">{{ $t("ui.settingWecomIndexLead") }}</el-radio>
            <el-radio label="customer">{{ $t("ui.settingWecomIndexCustomersAndContacts") }}</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="Secret：">
          <el-input v-model="form.wechat_work_user_secret" size="small" :placeholder="$t('ui.settingWecomIndexPleaseEnterSecret2')" />
          <!-- <span class="tips">企业微信客户secret，可前往【客户与上下游】查看secret</span> -->
        </el-form-item>
      </template>
      <div class="dashed-divider" />
      <div class="from-item-title mb15">
        <span>{{ $t("ui.settingWecomIndexWeComCustomAppSettings") }}</span>
      </div>

      <el-form-item :label="$t('ui.settingWecomIndexApplicationAgentId')">
        <el-input v-model="form.wechat_work_build_agent_id" size="small" :placeholder="$t('ui.settingWecomIndexPleaseEnterTheApplicationAgentId')" />
        <span class="tips">{{ $t("ui.settingWecomIndexWeComCustomAppAgentIdCreateAnApp") }}</span>
      </el-form-item>
      <el-form-item :label="$t('ui.settingWecomIndexApplicationSecret')">
        <el-input v-model="form.wechat_work_build_secret" size="small" :placeholder="$t('ui.settingWecomIndexPleaseEnterTheApplicationSecret')" />
        <span class="tips">{{ $t("ui.settingWecomIndexWeComCustomAppSecretViewItInThe") }}</span>
      </el-form-item>
      <div class="dashed-divider" />
      <div class="from-item-title mb15">
        <span>{{ $t("ui.settingWecomIndexWeComConversationArchive") }}</span>
      </div>
      <el-form-item :label="$t('ui.settingWecomIndexWeComConversationArchive2')">
        <el-switch
          v-model="form.wechat_work_session_switch"
          :active-value="1"
          :inactive-value="0"
          active-text="开启"
          inactive-text="关闭"
        >
        </el-switch>
      </el-form-item>
      <template v-if="form.wechat_work_session_switch">
        <el-form-item :label="$t('ui.settingWecomIndexConversationArchiveSecret')">
          <el-input v-model="form.wechat_work_session_secret" size="small" :placeholder="$t('ui.settingWecomIndexPleaseEnterTheConversationSecret')" />
          <span class="tips">{{ $t("ui.settingWecomIndexWeComCustomAppSecretViewItInThe") }}</span>
        </el-form-item>
        <el-form-item :label="$t('ui.settingWecomIndexVersion')">
          <el-input v-model="form.wechat_work_session_public_key_version" size="small" :placeholder="$t('ui.settingWecomIndexPleaseEnterTheVersion')" />
          <span class="tips">{{ $t("ui.settingWecomIndexConversationArchiveKeyVersion") }}</span>
        </el-form-item>
        <el-form-item :label="$t('ui.settingWecomIndexKey')">
          <el-input
            v-model="form.wechat_work_session_private_key"
            type="textarea"
            rows="8"
            size="small"
            :placeholder="$t('ui.settingWecomIndexPleaseEnterTheKey')"
          />
          <span class="tips">{{ $t("ui.settingWecomIndexConversationArchiveKey") }}</span>
        </el-form-item>
        <el-form-item :label="$t('ui.settingWecomIndexPublicKey')">
          <div class="flex">
            <el-input
              v-model="form.wechat_work_session_public_key"
              size="small"
              :placeholder="$t('ui.settingWecomIndexPleaseEnterThePublicKey')"
              type="textarea"
              rows="8"
            />
            <el-button
              style="height: 32px"
              size="small"
              type="primary"
              class="ml10"
              @click="copyFn(form.wechat_work_session_public_key_version)"
              >{{ $t("ui.settingWecomIndexCopy") }}</el-button
            >
            <el-button size="small" style="height: 32px" @click="generatePublicKey">{{ $t("ui.customerWeChatMassGroupDetailsGenerate") }}</el-button>
          </div>
        </el-form-item>
      </template>
    </el-form>
  </el-card>
  <div class="cr-bottom-button btn-shadow">
    <el-button size="small" :loading="loading" type="primary" @click="handleConfirm()">{{ $t("ui.formDesignerFormWidgetFieldWidgetRichTextWidgetSave") }}</el-button>
  </div>
</div>
</template>
<script>
import { getWorkConfigApi, saveWorkConfigApi, getWorkRsaApi } from '@/api/setting'
export default {
  data() {
    return {
      name: 'World',
      form: {
        wechat_work_corpid: '',
        wechat_work_user_switch: 1,
        wechat_work_forced_build: 0,
        wechat_work_address_secret: '',
        wechat_work_token: '',
        wechat_work_aes_key: '',
        work_server_url: '',
        wechat_work_client_switch: 1,
        wechat_work_client_radio: 'clue',
        wechat_work_user_secret: '',
        wechat_work_build_agent_id: '',
        wechat_work_build_secret: '',
        wechat_work_session_switch: 1,
        wechat_work_session_secret: '',
        wechat_work_session_private_key: '',
        wechat_work_session_public_key_version: '',
        wechat_work_session_public_key: '',
        rsa_public_key: ''
      },
      loading: false
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    getData() {
      getWorkConfigApi().then((res) => {
        for (let key in this.form) {
          this.form[key] = res.data[key]
        }
      })
    },
    encodingAESKeyGen() {
      let letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
      let token = ''
      for (let i = 0; i < 43; i++) {
        const j = parseInt(Math.random() * 61 + 1)
        token += letters[j]
      }
      this.form.wechat_work_aes_key = token
    },
    token() {
      let letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
      let token = ''
      for (let i = 0; i < 32; i++) {
        const j = parseInt(Math.random() * 61 + 1)
        token += letters[j]
      }
      this.form.wechat_work_token = token
    },
    handleConfirm() {
      this.loading = true
      saveWorkConfigApi(this.form)
        .then((res) => {
          this.loading = false
        })
        .catch((err) => {
          this.loading = false
        })
    },
    async generatePublicKey() {
      const res = await getWorkRsaApi()
      this.form.wechat_work_session_public_key_version = res.data.rsa_public_key
    },
    copyFn(val) {
      if (!val) return this.$message.error('请输入需要复制的内容')
      clipboard.writeText(val)
      this.$message.success('复制成功')
    }
  }
}
</script>

<style lang="scss" scoped>
.divBox {
  position: relative;
}
.card-box {
  height: calc(100vh - 77px);
  padding-bottom: 80px;
  overflow-y: auto;
}
.main {
  width: 800px;
  margin: 0 auto;

  .tips {
    font-size: 12px;
    color: #909399;
  }

  .from-item-title {
    border-left: 3px solid #1890ff;

    span {
      padding-left: 10px;
      font-weight: bold;
      font-size: 14px;
    }
  }

  .dashed-divider {
    width: 100%;
    border-top: 1px dashed #ccc;
    margin-bottom: 25px;
  }
}
.cr-bottom-button {
  position: absolute;
  left: 0px;
  right: 0;
  bottom: 0;
  width: 100%;
}
::v-deep .el-card__body {
  padding: 20px;
  padding-top: 0;
}
</style>

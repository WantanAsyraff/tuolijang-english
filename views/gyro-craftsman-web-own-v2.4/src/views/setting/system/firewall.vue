<template>
  <div class="divBox firewall-box">
    <div class="box-height">
      <el-card class="card-head normal-page" shadow="never" :body-style="{ padding: '20px 20px 0 20px' }">
        <el-form :model="formData" :inline="true" class="form-box" size="small" label-width="84px">
          <el-form-item :label='$("legacy.af5e5104719ce6a1")'>
            <el-radio-group v-model="formData.status">
              <el-radio :label="0" :value="0">{{ $("hr.close") }}</el-radio>
              <el-radio :label="1" :value="1">{{ $("legacy.bf33347e3c857274") }}</el-radio>
              <el-radio :label="2" :value="2">{{ $("legacy.9cb5e2a94ed56be2") }}</el-radio>
            </el-radio-group>
            <div class="form-item-tips">
              {{ $("legacy.67ea41f271c7201d") }}
            </div>
          </el-form-item>
          <el-form-item :label='$("legacy.0df3a2f3ba5e52fb")'>
            <div class="firewall-rule-item" v-for="(item, index) of formData.ruleList" :key="index">
              <el-input v-model="formData.ruleList[index]" :placeholder='$("legacy.209bfa3c22b7ef3d")' clearable />

              <div class="delete-btn-box" v-if="index">
                <el-button type="text" class="delete-btn" @click="handleDeleteRule(index)">{{ $("public.delete") }}</el-button>
              </div>
            </div>
            <el-button type="text" icon="el-icon-plus" @click="handleAddRule" style="margin: 10px 0"
              >{{ $("legacy.c9f9e18b06233971") }}</el-button
            >
            <div class="firewall-rule-item">
              <el-button type="primary" @click="handleSaveRule">{{ $("public.save") }}</el-button>
            </div>
          </el-form-item>
        </el-form>
      </el-card>
    </div>
  </div>
</template>

<script>
import { getFirewallConfigApi, saveFirewallConfigApi } from '@/api/setting'

export default {
  data() {
    return {
      formData: {
        status: 0, // 0 -> 关闭，1 -> 拦截, 2 -> 过滤
        ruleList: ['']
      }
    }
  },
  created() {
    this.getFirewallConfig()
  },
  methods: {
    async getFirewallConfig() {
      const res = await getFirewallConfigApi()
      const { firewall_switch, firewall_content } = res.data
      this.formData = {
        status: firewall_switch,
        ruleList: firewall_content?.length ? firewall_content : ['']
      }
    },
    handleAddRule() {
      this.formData.ruleList.push('')
    },
    handleSaveRule() {
      const data = {
        firewall_switch: this.formData.status,
        firewall_content: this.formData.ruleList
      }
      saveFirewallConfigApi(data)
    },
    handleDeleteRule(index) {
      this.formData.ruleList.splice(index, 1)
    }
  }
}
</script>

<style scoped lang="scss">
.form-box {
  width: 650px;
  margin: 20px auto 0;
}

.form-item-tips {
  font-size: 13px;
  color: #909399;
}

.firewall-box {
  ::v-deep .el-form-item {
    width: 100%;
    display: flex;
  }

  ::v-deep .el-form-item__content {
    flex: 1;
  }

  ::v-deep .el-icon-circle-close::before {
    content: '\e79d';
  }

  ::v-deep .el-form-item__label {
    color: #606266;
  }

  .firewall-rule-item {
    position: relative;

    & + .firewall-rule-item {
      margin-top: 20px;
    }

    .delete-btn-box {
      position: absolute;
      left: 100%;
      padding-left: 10px;
      top: 0;
      font-size: 14px;
      display: none;
      .delete-btn {
        color: red;
      }
    }

    &:hover {
      .delete-btn-box {
        display: block;
      }
    }
  }
}
</style>

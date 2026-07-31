<!-- 项目详情侧滑弹窗  -->
<template>
<div class="station">
  <el-form ref="formData" :model="formData" label-width="110px">
    <el-form-item :label="$t('ui.programProgramListAddProgramProjectName')" prop="name" class="lh13">
      <span>{{ formData.name || '- -' }}</span>
    </el-form-item>

    <el-form-item :label="$t('ui.programProgramListAddProgramOwner')" prop="uid" class="select-bar">
      <div class="mb-10">
        <div class="avatar-box members-box" v-for="(item, index) in formData.admins" :key="index">
          <img :src="item.avatar" alt="" />
          <span>{{ item.name || '- -' }}</span>
        </div>
      </div>
    </el-form-item>

    <el-form-item :label="$t('ui.programProgramListAddProgramProjectMembers')" class="select-bar">
      <div class="mb-10">
        <div class="avatar-box members-box" v-for="(item, index) in formData.members" :key="index">
          <img :src="item.avatar" alt="" />
          <span class="members-box-content">{{ item.name }}</span>
        </div>
        <span v-if="!formData.members">- -</span>
      </div>
    </el-form-item>

    <el-form-item :label="$t('ui.programProgramListAddProgramPlannedStart')" class="select-bar" prop="start_date">
      <span>{{ formData.start_date || '- -' }}</span>
    </el-form-item>

    <el-form-item :label="$t('ui.programProgramListAddProgramPlannedEnd')" class="select-bar" prop="end_date">
      <span>{{ formData.end_date || '- -' }}</span>
    </el-form-item>

    <el-form-item :label="$t('ui.programProgramListAddProgramRelatedCustomer')" prop="eid">
      <div>
        <span>{{ formData.customer ? formData.customer.customer_name : '- -' }}</span>
      </div>
    </el-form-item>
    <el-form-item v-if="formData.eid" :label="$t('ui.programProgramListAddProgramLinkOrder')" prop="cid">
      <div>
        <span>{{ formData.contract ? formData.contract.contract_name : '--' }}</span>
      </div>
    </el-form-item>

    <el-form-item :label="$t('ui.programProgramListAddProgramProjectStatus')" prop="status">
      <span>
        {{ formData.status == 0 ? $t('ui.programProgramListDetailsProgramNormal') : formData.status == 1 ? $t('ui.programProgramListDetailsProgramPause') : $t('ui.customerWeChatMassGroupDetailsClose') }}
      </span>
    </el-form-item>

    <el-form-item :label="$t('ui.programProgramListAddProgramProjectDescription')">
      <span>{{ formData.describe || '- -' }}</span>
    </el-form-item>
  </el-form>
</div>
</template>

<script>
export default {
  name: 'detailsForm',
  props: {
    formData: {
      type: Object,
      default: () => {}
    }
  },
  data() {
    return {
      drawer: false,
      id: 0,
      type: '',
      edit: false
    }
  },
  methods: {}
}
</script>

<style lang="scss" scoped>
.station ::v-deep .edui-editor-iframeholder {
  height: 300px !important;
}
.mb-10 {
  margin-bottom: -10px;
}
.station {
  ::v-deep .el-form-item__label {
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    font-size: 13px;
    color: #606266;
    line-height: 22px;
  }
  ::v-deep .el-form-item__content {
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    font-size: 13px;
    color: #303133;
    line-height: 22px;
  }
}

::v-deep .el-cascader,
::v-deep .el-input-number,
::v-deep .el-select,
::v-deep .el-date-editor {
  width: 100%;
}
.avatar-box {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-right: 12px;
  background: #f7f7f7;
  padding: 5px 10px;
  border-radius: 4px;
  margin-bottom: 10px;
  img {
    flex: none;
    width: 16px;
    height: 16px;
    border-radius: 50%;
  }
}
.members-box {
  display: inline-flex;
}
.members-box-content {
  margin: 0 3px 0 0;
}
::v-deep .el-form-item {
  align-items: center;
  margin-bottom: 30px;
}
</style>

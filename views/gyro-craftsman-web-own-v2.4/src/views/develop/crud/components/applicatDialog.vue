<!--
  @FileDescription: 低代码应用管理弹窗组件
  功能：提供应用分类的增删改查和拖拽排序功能
-->
<template>
  <div class="oa-dialog">
    <!-- 弹窗主体 -->
    <el-dialog :close-on-click-modal="false" :show-close="false" :visible.sync="show" :width="`516px`">
      <!-- 弹窗标题 -->
      <div slot="title" class="header">
        <span class="title">{{ id ? $("public.edit") : $("file.newlybuild") }} {{ $("ui.developCrudIndexApplicationManagement") }}</span>
        <span class="el-icon-close" @click="handleClose"></span>
      </div>
      <div class="pl20 pr20 mt20">
        <el-form :model="fromItem" :rules="rules" ref="formRef" label-width="auto">
          <el-form-item :label='$("legacyScript.applicationName")' prop="name">
            <el-input v-model="fromItem.name" :placeholder='$("legacy.1eeabd1ffe2a1a0a")' size="small" />
          </el-form-item>
          <el-form-item :label='$("legacy.c36ae26f005e3fdf")' prop="icon">
            <el-input :placeholder='$("ui.formCommonOaFormPleaseSelectAnIcon")' v-model="fromItem.icon" readonly @click.native="showIconDialog = true"
              clearable>
              <i v-if="!fromItem.icon" slot="suffix" class="el-icon-circle-plus-outline" style="cursor: pointer;"></i>
              <i v-else slot="suffix" class="el-icon-circle-close" style="cursor: pointer;"
                @click.stop="handleClearIcon"></i>
            </el-input>
          </el-form-item>
          <el-form-item :label='$("legacyScript.applicationDescription")' prop="info">
            <el-input v-model="fromItem.info" type="textarea" rows="4" resize="none" :placeholder='$("legacy.8197186e83d662d3")'
              size="small" />
          </el-form-item>
          <el-form-item :label='$("ui.businessHolidayTypeIndexSort")' prop="sort">
            <el-input v-model="fromItem.sort" :placeholder='$("hr.placeholder15")' size="small" />
          </el-form-item>
          <el-form-item :label='$("legacyScript.parentMenu")' prop="menu">
            <el-cascader v-model="fromItem.path" size="small" :options="menuList"
              :props="{ checkStrictly: true, label: 'menu_name', value: 'id', children: 'children' }" clearable
              style="width: 100%;">
              <template slot-scope="{ data }">
                <span>{{ $(data.menu_name, data.menu_name_en) }}</span>
              </template>
            </el-cascader>
            <span class="tips">{{ $("legacyScript.noMenuWillBeGeneratedUnlessAParentMenu") }}</span>
          </el-form-item>
        </el-form>
      </div>


      <div slot="footer">
        <div class="dialog-footer">
          <el-button size="small" @click="handleClose">{{ $("public.cancel") }}</el-button>
          <el-button size="small" type="primary" @click="submit">{{ $("public.ok") }}</el-button>

        </div>
      </div>
    </el-dialog>
    <!-- 选择图标 -->
    <el-dialog :title='$("ui.formCommonOaFormSelectMenuIcon")' :visible.sync="showIconDialog" width="50%">
      <div class="icon-box">
        <select-icon ref="selectIconRef" :isEmit="true" @select="handleSelectIcon"></select-icon>
      </div>

    </el-dialog>

  </div>
</template>

<script>

import { savecrudCateApi, delcrudCateApi, saveCrudApi, getCrudInfoApi } from '@/api/develop'
import { menuListApi } from '@/api/system'
export default {
  name: 'ApplicatDialog',
  components: {
    oaSystemImage: () => import('@/components/form-common/oa-systemImage.vue'),
    selectIcon: () => import('@/components/form-common/select-icon.vue'),
  },

  props: {
    // 应用分类列表
    list: {
      type: Array,
      default: () => []
    }
  },

  data() {
    return {
      show: false, // 控制弹窗显示
      showIconDialog: false,
      fromItem: this.generateDefaultFormValues(),
      id: 0,

      menuList: [],
      rules: {
        name: [
          { required: true, message: this.$("legacy.1eeabd1ffe2a1a0a"), trigger: 'blur' }
        ],
        icon: [
          { required: true, message: this.$("legacy.8a89a993327e2997"), trigger: 'blur' }
        ],
        info: [
          { required: true, message: this.$("legacy.8197186e83d662d3"), trigger: 'blur' }
        ],

      }
    }
  },

  methods: {
    /**
     * 关闭弹窗
     */
    handleClose() {
      this.id = 0
      this.show = false
      this.fromItem = this.generateDefaultFormValues()
    },
    generateDefaultFormValues() {
      return {
        name: '',
        icon: '',
        info: '',
        sort: 0,
        path: [],
      };
    },
    handleSelectIcon(data) {
      this.fromItem.icon = data
      this.showIconDialog = false
      this.$refs.formRef.validateField('icon')
    },
    handleClearIcon() {
      this.$set(this.fromItem, 'icon', '')
    },


    openBox(data) {
      if (data) {
        this.id = data.id
        getCrudInfoApi(this.id)
          .then((res) => {
            Object.keys(this.generateDefaultFormValues())
              .forEach(key => {
                if (['icon', 'path'].includes(key) && res.data.menu) {
                  this.fromItem[key] = res.data.menu[key];
                } else {
                  this.fromItem[key] = res.data[key];
                }
              });


          })

      }
      this.show = true
      this.getAllMenus()
    },
    async getAllMenus() {
      let obj = {
        menu_name: "顶级菜单", menu_name_en: "Top-level menu", id: 0
      }
      const result = await menuListApi()
      this.menuList = result.data
      this.menuList.unshift(obj)
    },



    /**
     * 提交表单数据
     */
    submit() {
      this.$refs.formRef.validate((valid) => {
        if (valid) {
          const payload = Object.keys(this.generateDefaultFormValues())
            .reduce((acc, key) => {
              if (key === 'path') {
                acc.menu.path = this.fromItem.path
              } else {
                acc[key] = this.fromItem[key]
              }
              return acc
            }, {
              menu: {}
            });
          saveCrudApi(this.id, payload)
            .then(() => {
              this.handleClose()
              this.$emit('getList')
            })
            .catch((err) => {
              this.$message.error(err.message)
            })
        } else {
          this.$message.error(this.$("legacy.47c728a9f3ef01de"))
        }
      })
    },



  }
}
</script>

<style lang="scss" scoped>
.oa-dialog {
  .header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    .title {
      font-family: PingFang SC, PingFang SC;
      font-weight: 500;
      font-size: 14px;
      color: #303133;
    }

    .el-icon-close {
      color: #c0c4cc;
      font-weight: 500;
      font-size: 14px;
    }
  }

  .content {
    max-height: calc(100vh - 520px);
    overflow-y: scroll;
  }

  .content::-webkit-scrollbar {
    height: 0;
    width: 0;
  }

  .content:first-child {
    padding: 0 20px;
  }

  .vertical {
    display: flex;
    flex-direction: column;
  }

  .add-type {
    display: flex;
    justify-content: flex-start;
  }

  .dialog-footer {
    display: flex;
    justify-content: flex-end;
  }

  ::v-deep .el-dialog {
    border-radius: 6px;
  }

  ::v-deep .el-dialog__body {
    margin-bottom: 0;
    padding: 0;
  }

  ::v-deep .el-button--medium {
    padding: 10px 15px;
  }
}

.el-icon-circle-plus-outline {
  cursor: pointer;
}

.icon-box {
  height: 700px;
  padding: 20px;
  padding-bottom: 100px;
}


.pr20 {
  padding-right: 20px;
}

.tips {
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 13px;
  color: #909399;
}
</style>

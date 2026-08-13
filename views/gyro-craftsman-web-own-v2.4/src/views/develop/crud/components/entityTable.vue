import { $ } from '@/lang'
<template>
<div>
    <div class="header">
        <div class="title"><span class="el-icon-arrow-left" @click="goBack"></span>{{ $("ui.developCrudEntityTableEntityList") }} </div>
        <div class="right lh-center ">
            <el-button type="primary" icon="el-icon-plus" size="small" @click="createEntity('', 'add')">
                {{ $("ui.developCrudEntityTableNewEntity") }}</el-button>
            <el-dropdown>
                <span class="iconfont icongengduo2 pointer ml10"></span>
                <el-dropdown-menu style="text-align: left">
                    <el-dropdown-item @click.native="importEntity"> {{ $("ui.developCrudEntityTableImportEntities") }}</el-dropdown-item>
                  
                    <el-dropdown-item @click.native="downloadFile"> {{ $("ui.developCrudEntityTableDownloadTemplate") }}</el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
        </div>
    </div>

    <div class="flex h32 lh-center  mb10">
        <div class="inTotal">{{ $("ui.developModuleFormBoxTotal") }} {{ total }} {{ $("ui.commonOaFromBoxItems") }}</div>
        <div class="ml20">
            <el-input v-model="where.table_name" prefix-icon="el-icon-search" size="small" :placeholder="$('ui.commonFormListPleaseEnterKeyword')"
                clearable style="width: 250px" @change="getList(1)" @keyup.native.stop.prevent.enter="getList(1)"
                class="input"></el-input>
        </div>
    </div>

    <div class="table-box">
        <el-table v-loading="loading" row-key="id" :data="tableData" :height="height" style="width: 100%"
            :tree-props="{ children: 'children' }">
            <el-table-column prop="table_name_en" :label="$('ui.developCrudEntityTableEntityName')" min-width="180">
                <template slot-scope="scope">
                    <span class="color-doc pointer" @click="designFn(scope.row)">{{ scope.row.table_name_en
                        }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="table_name" :label="$('ui.developCrudEntityTableDisplayName')" min-width="150">
                <template slot-scope="scope">
                    {{ scope.row.table_name }}
                    <span :class="scope.row.crud_id == 0 ? 'zhu' : 'cong'">{{
                        scope.row.crud_id == 0 ? $('ui.formCommonSelectDepartmentMain') : $('ui.developCrudEntityTableFrom')
                        }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="info" :label="$('ui.developApproveIndexLinkedApplication')" min-width="250" show-overflow-tooltip>
                <template slot-scope="scope">
                    <span v-if="scope.row.cate && scope.row.cate.length > 0">{{ scope.row.cate.join('、') }}</span>
                    <span v-else>--</span>
                </template>
            </el-table-column>

            <el-table-column prop="info" :label="$('ui.developCrudEntityTableEntityDescription')" min-width="250" show-overflow-tooltip>
                <template slot-scope="scope">{{ scope.row.info || '--' }}</template>
            </el-table-column>
            <el-table-column prop="user.name" :label="$('ui.hrAssessCheckIndexCreator')" width="230"> </el-table-column>
            <el-table-column prop="created_at" :label="$('ui.invoiceInvoiceDetailsCreatedTime')" width="230"> </el-table-column>
            <el-table-column prop="address" :label="$('ui.formDesignerFormWidgetContainerWidgetDetailsItemOperation')" fixed="right" width="150">
                <template slot-scope="scope">
                    <el-button type="text" @click="designFn(scope.row)">{{ $("ui.developCrudEntityTableEntityDesign") }}</el-button>
                    <el-dropdown>
                        <span class="el-dropdown-link el-button--text el-button more">
                            {{ $("ui.layoutNavbarMore") }}
                            <i class="el-icon-arrow-down" />
                        </span>
                        <el-dropdown-menu style="text-align: left">
                            <el-dropdown-item @click.native="designFn(scope.row,1)"> {{ $("ui.developCrudEntityTableEntityProperties") }} </el-dropdown-item>
                            <el-dropdown-item @click.native="designFn(scope.row, 2)"> {{ $("ui.developCrudEntityTableFieldDesign") }} </el-dropdown-item>
                            <template v-if="scope.row.crud_id == 0">
                                <el-dropdown-item @click.native="designFn(scope.row, 3)"> {{ $("ui.developCrudEntityTableFormDesign") }} </el-dropdown-item>
                                <el-dropdown-item @click.native="designFn(scope.row, 4)"> {{ $("ui.developCrudEntityTableListDesign") }} </el-dropdown-item>
                                <el-dropdown-item @click.native="designFn(scope.row, 5)"> {{ $("ui.workFlowDialogErrorDialogWorkflowDesign") }} </el-dropdown-item>
                                <el-dropdown-item @click.native="designFn(scope.row, 6)"> {{ $("ui.developCrudEntityTableTriggerDesign") }} </el-dropdown-item>
                            </template>
                            <el-dropdown-item divided
                                v-if="scope.row.children && scope.row.children.length == 0 && scope.row.crud_id == 0"
                                @click.native="createEntity(scope.row)">
                                {{ $("ui.developCrudEntityTableAddChildEntity") }}
                            </el-dropdown-item>
                            <el-dropdown-item divided @click.native="deleteEntity(scope.row)"> {{ $("ui.developCrudEntityTableDeleteEntity") }}
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </template>
            </el-table-column>
        </el-table>
    </div>
    <div class="page-fixed">
        <el-pagination :page-size="where.limit" :current-page="where.page" :page-sizes="[15, 20, 30]"
            layout="total,sizes, prev, pager, next, jumper" :total="total" @size-change="handleSizeChange"
            @current-change="pageChange" />
    </div>

    <!-- 新建实体弹窗 -->
    <oa-dialog ref="oaDialog" v-if="dialogShow" :fromData="fromData" :formConfig="formConfig" :formRules="formRules"
        :formDataInit="formDataInit" @submit="submit"></oa-dialog>
    <!-- 导入 -->
    <importExcel ref="importExcel" :distinguish="distinguish" :column-number="columnNumber"
        @importExcelData="importExcelData" />
    <!-- ai新建字段 -->
    <fieldsettingAi v-if="fieldsettingAiShow" ref="fieldsettingAi" :info="info" @getList="getList"></fieldsettingAi>

</div>

</template>
<script>
import {
    databaseSaveApi,
    databaseListApi,
    databaseDelApi,
    databaseCopyApi,
    getDatabaseApi,
    getImportTempApi
} from '@/api/develop'
import { pinyin } from 'pinyin-pro'
import { roterPre } from '@/settings'
import { menuListApi } from '@/api/system'
import fieldsettingAi from './fieldsettingAi'
import oaDialog from '@/components/form-common/dialog-form'
import Commnt from '@/components/develop/commonData'
import importExcel from '@/components/common/importExcel'
export default {
    components: {
        fieldsettingAi,
        oaDialog,
        Commnt,
        importExcel,
    },
    props: {
        cate_id: {
            type: [String, Number],
            default: ''
        },
        cateItem: {
            type: Object,
            default: () => {}
        },
        applicationTabData: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            tableData: [],
            height: window.innerHeight - 280 + 'px',
            where: {
                table_name: '',
                page: 1,
                limit: 15,
                cate_id: this.cate_id
            },
            dialogShow: false,
            fieldsettingAiShow: false,
            info: {
                crud_id: '',
                table_name: '',
                table_name_en: '',
                cate_ids: []
            },
            menuList: [],
            id: 0,
            total: 0,
            loading:false,
            fromData: {
                width: '663px',
                title: $('ui.developCrudEntityTableNewEntity'),
                btnText: '确定',
                labelWidth: '100px',
                type: ''
            },
            formDataInit: Commnt.formDataInit,
            formRules: Commnt.formRules,
            formConfig: Commnt.formConfig,
            distinguish: 0,
            columnNumber: 0,
        }
    },
    watch: {
        cateItem: {
            handler(newVal, oldVal) {
            },
            immediate: true
        }
    },
    


    mounted() {
        this.where.cate_id = this.cate_id
        this.getList()
    },
    methods: {
        goBack() {
            this.$emit('goBack')
        },


        importEntity() {
            this.$refs.importExcel.btnClick()
        },
        // 下载模板
        downloadFile() {
            getImportTempApi().then((res) => {
                this.fileLinkDownLoad(res.data.url, '实体导入模板.xlsx')
            })
        },

        // 导入
        importExcelData(data, name) {
            this.fieldsettingAiShow = true;

            // 过滤掉值为 ID/id/"ID" 的列
            data.forEach((row, idx) => {
                data[idx] = row.filter(v => !['id', 'ID', '"ID"'].includes(String(v).trim()));
            });

            // 解析实体名：去掉扩展名及括号内容
            const crudName = name.split('.')[0].replace(/\([^)]*\)/g, '');
            this.info = {
                ...this.info,
                table_name: crudName,
                table_name_en: this.refreshFn(crudName)
            };

            const header = data[0] || [];
            if (!header.length || header.every(v => !v)) {
                return this.$message.error($('legacyScript.importedDataCannotBeEmpty'));
            }

            // 生成字段列表
            const list = header
                .filter(Boolean)
                .map(text => {
                    const fieldName = text.replace(/\([^)]*\)/g, '');
                    return {
                        field_name: fieldName,
                        field_name_en: this.refreshFn(fieldName),
                        value: 'input',
                        comment: '',
                        is_default_value_not_null: 1,
                        is_table_show_row: 1,
                        create_modify: 1,
                        update_modify: 1,
                        data_dict_id: '',
                        association_crud_id: '',
                        association_field_names: [],
                        is_uniqid: 0,
                        options: []
                    };
                });
setTimeout(() => {
    this.$refs.fieldsettingAi.openBox(list)
}, 300);
        },
        // 刷新转拼音小写
        refreshFn(key) {
            const regex = /^[\u4e00-\u9fa5a-zA-Z][\u4e00-\u9fa5a-zA-Z_]{0,15}$/
            if (!regex.test(key)) {
                return false
            }
            let str = pinyin(key, { toneType: 'none' })
            const reg = /[\t\r\f\n\s]*/g
            if (typeof str === 'string') {
                str = str.replace(reg, '')
            }
            return str
        },


        // 新建实体
        async createEntity(row, type) {
            // 获取菜单列表并追加“顶级菜单”
             // 打开弹窗
            this.dialogShow = true
            const { data: menus } = await menuListApi()
            
            this.menuList = [{ menu_name: '顶级菜单', id: 0 }, ...menus]

            // 重置表单初始值
            this.formDataInit = {
                table_name: '',
                table_name_en: '',
                crud_id: '',
                cate_ids: this.where.cate_id > 0 && !row ? [this.where.cate_id] : [],
                crud_type: '0',
                info: '',
                show_log: '1',
                show_comment: 1,
                path: this.cateItem?.menu?.path || [],
                icon: '',
                uni_img: ''
            }

            // 复制模式或编辑模式
            if (type === 'copy') {
                this.fromData = { title: $('legacyScript.duplicateEntity'), type: 'copy' }
                this.formDataInit.info = row.info
                this.formDataInit.cate_ids = row.cate_ids
            } else {
                this.fromData.type = ''
                if (row?.id) {
                    this.id = row.id
                    this.formDataInit.crud_id = row.id
                }
            }

            // 拉取下拉选项并赋值
            const [{ data: dbList }] = await Promise.all([getDatabaseApi()])
            this.formConfig[3].options = dbList
            this.formConfig[8].options = this.menuList
            
            // this.formConfig[7].options = this.applicationTabData.slice(1)
            this.formConfig[7].options = this.applicationTabData

           
            this.$refs.oaDialog.openBox()
        },

        // 新建实体回调
        submit(data, type) {
            if (data.crud_id && Array.isArray(data.crud_id)) {
                data.crud_id = data.crud_id[1]
            }

            if (type == 'copy') {
                databaseCopyApi(this.id, data)
                    .then((res) => {
                        if (res.status == 200) {
                            this.$refs.oaDialog.handleClose()
                            this.getList()
                        }
                    })
                    .catch((err) => {
                        this.$message.error(err.message)
                    })
            } else {
                databaseSaveApi(data)
                    .then((res) => {
                        if (res.status == 200) {
                            this.$refs.oaDialog.handleClose()
                            this.getList()
                        }
                    })
                    .catch((err) => {
                        this.$message.error(err.message)
                    })
            }
        },
        // 删除实体
        async deleteEntity(row) {
            await this.$modalSure('你确定要删除这条实体吗')
            await databaseDelApi(row.id)
            const totalPage = Math.ceil((this.total - 1) / this.where.limit)
            this.where.page = this.where.page > totalPage ? totalPage : this.where.page
            this.where.page = this.where.page < 1 ? 1 : this.where.page
            await this.getList()
        },

        designFn(row, tabIndex) {
            this.$router.push({
                path: `${roterPre}/develop/crud/design`,
                query: {
                    id: row.id,
                    tabIndex: tabIndex || 4,
                    tab: this.activeName,
                    cate_id: this.where.cate_id
                }
            })
        },
        // 获取实体列表
        async getList() {
         
       
            this.loading = true
            const data = await databaseListApi(this.where)
            this.total = data.data.count
            this.tableData = data.data.list
            this.loading = false
        },

        pageChange(page) {
            this.where.page = page
            this.getList()
        },

        handleSizeChange(val) {
            this.where.limit = val
            this.getList()
        },
    }
}
</script>
<style lang="scss" scoped>
.header {
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.title {
    font-family: PingFang SC, PingFang SC;
    font-weight: 500;
    font-size: 18px;
    height: 18px;
    color: #303133;
    cursor: pointer;
    display: flex;
    align-items: center;

    .el-icon-arrow-left {
        color: #606266;
        font-size: 14px;
        margin-right: 5px;
        font-weight: 500;
        
    }
}
</style>

---
name: create-crud-page
description: 在 src/views/<scope>/ 下生成标准 CRUD 列表页（查询头 + 表格 + 新增/编辑弹窗 + 删除确认 + 详情抽屉），完全套用本项目 oaFromBox + customizeTable 模式。当用户说"加一个列表页/CRUD 页/管理页"、"做一个增删改查页面"时触发。
---

# create-crud-page

为本项目（Vue 2.7 + Element UI）生成一个标准的业务 CRUD 列表页，复用项目内已封装的 `oaFromBox`（搜索/标题/操作按钮）+ `customizeTable`（可定制列、跨页选中、表头拖拽）的统一壳子。

## 必须遵循的项目约定

1. **新建页面统一用 Options API + `setup()` + Composables 写法**（AGENTS.md 明确要求，禁止使用 `<script setup>` 语法糖），混入 `defineComponent` 等用法仅在小改动里出现。
2. **目录布局**：`src/views/<scope>/<feature>/`
   ```
   index.vue           列表主页面
   components/
     editDialog.vue    新增/编辑弹窗（如需要）
     details.vue       详情抽屉（如需要）
   ```
3. **顶层容器**：必须 `<div class="divBox"> + <el-card class="normal-page el-card-flex">`，让自带样式生效（高度撑满、内边距等已由全局 SCSS 处理）。
4. **搜索头**：必须使用 `@/components/common/oaFromBox`，常用 props：
   - `:title="$route.meta.title"` 标题取自路由 meta
   - `:total` 数量统计
   - `:search` 搜索字段定义数组（每项 `{ form_value: 'input'|'select'|'date'..., field_name: '中文', field_name_en: '英文 key' }`）
   - `:dropdownList` 右上角"更多"下拉，约定 `value` 用整型，常见项目里 1=筛选条件设置 2=表头显示设置 3=字典选项设置 4=导出 ...
   - `:viewSearch` / `:treeData` / `:timeSearchObj` 取自 `customizeTable` 的 `@getSearch` 事件
   - `:category="keyword"` 业务标识（决定字典/表头配置的命名空间）
   - `btnText`、`@addDataFn`、`@confirmData`、`@dropdownFn` 必接
5. **列表**：必须使用 `@/views/customer/components/customizeTable`（如不在 customer 域，可保留同一相对路径或在新模块下重新引入相同组件），固定 props：
   - `flexLayout` 必加，启用 flex 高度自适应
   - `:keyword`、`:tableData`、`:where`、`:total`、`:loading`
   - `@getTableData`、`@handleSelectionChange`、`@getSearch`
   - 操作列用具名插槽 `<template #options="{ data }">`
6. **权限**：操作按钮用 `v-hasPermi="['<scope>:<feature>:<action>']"` 指令控制（指令已全局注册）。
7. **删除确认**：使用 `await this.$modalSure('xxx 提示文案')`（Composition 写法里 `proxy.$modalSure`），后接 API。
8. **轻提示**：成功提示由 `request.js` 根据接口返回 `tips/message` 自动弹出，**业务代码不要再** `this.$message.success`。
9. **跳转**：`import { roterPre } from '@/settings'` + `this.$router.push({ path: `${roterPre}/...` })`。
10. **API 调用**：`import { xxxListApi, xxxDeleteApi, xxxExportApi } from '@/api/<scope>'`（参考 `create-api-module` skill 的命名）。
11. **弹窗/侧滑尺寸**：生成新增/编辑弹窗、详情抽屉前必须读取 `docs/ai-ui-popup-guidelines.md`，并从 `@/constants/popupSize` 引入 `DIALOG_SIZE` / `DRAWER_SIZE`，禁止新增散落的魔法尺寸。

## 工作流程

1. **收集信息**：
   - 模块名（中文 + scope，例如"售后工单" `aftersale/ticket`）
   - 是否已有对应 API（没有就先用 `create-api-module`）
   - 列字段（哪些列、是否含人员头像、字典、状态 Tag、标签等）
   - 搜索字段
   - 是否需要详情抽屉、新增/编辑弹窗、导入导出
   - 路由路径（之后用 `create-route` 注册）
2. **生成 `index.vue` 主文件**，骨架（Options API + `setup()` 写法）：

   ```vue
   <template>
     <div class="divBox">
       <el-card :body-style="{ padding: '20px' }" class="normal-page el-card-flex">
         <oaFromBox
           :title="$route.meta.title"
           :total="total"
           :search="search"
           :viewSearch="viewSearch"
           :timeSearchObj="timeSearchObj"
           :dropdownList="dropdownList"
           :category="keyword"
           btnText="新增"
           ref="fromBox"
           @addDataFn="handleAdd"
           @confirmData="handleConfirm"
           @dropdownFn="handleDropdown"
         />

         <customizeTable
           flexLayout
           ref="tableData"
           :keyword="keyword"
           :tableData="tableData"
           :where="where"
           :total="total"
           :loading="loading"
           @getSearch="handleGetSearch"
           @handleSelectionChange="handleSelectionChange"
           @getTableData="getTableData"
         >
           <template #options="{ data }">
             <el-button v-hasPermi="['<scope>:<feature>:view']" type="text" @click="handleView(data)">查看</el-button>
             <el-button v-hasPermi="['<scope>:<feature>:edit']" type="text" @click="handleEdit(data)">编辑</el-button>
             <el-button v-hasPermi="['<scope>:<feature>:delete']" type="text" @click="handleDelete(data)">删除</el-button>
           </template>
         </customizeTable>
       </el-card>

       <edit-dialog ref="editDialog" @ok="getTableData" />
       <details-drawer ref="detailsDrawer" />
     </div>
   </template>

   <script>
   import { ref, reactive, getCurrentInstance, onMounted } from 'vue'
   import oaFromBox from '@/components/common/oaFromBox'
   import customizeTable from '@/views/customer/components/customizeTable'
   import editDialog from './components/editDialog.vue'
   import detailsDrawer from './components/details.vue'
   import { xxxListApi, xxxDeleteApi, xxxExportApi } from '@/api/<scope>'
   import { roterPre } from '@/settings'

   export default {
     name: 'XxxFeatureList',
     components: { oaFromBox, customizeTable, editDialog, detailsDrawer },
     setup() {
       const { proxy } = getCurrentInstance()

       const keyword = '<scope>_<feature>'      // 业务标识（决定字典/表头命名空间）
       const tableData = ref([])
       const total = ref(0)
       const loading = ref(false)
       const ids = ref([])
       const search = ref([])
       const viewSearch = ref([])
       const timeSearchObj = ref({})
       const where = reactive({ page: 1, limit: 15 })
       const dropdownList = ref([
         { label: '筛选条件设置', value: 1 },
         { label: '表头显示设置', value: 2 },
         { label: '字典选项设置', value: 3 },
         { label: '导出', value: 4 }
       ])

       // 模板 ref 用 ref(null) + 同名变量，模板里直接 ref="editDialogRef"
       const editDialogRef = ref(null)
       const detailsDrawerRef = ref(null)
       const tableRef = ref(null)
       const fromBox = ref(null)

       async function getTableData() {
         if (loading.value) return
         loading.value = true
         try {
           const res = await xxxListApi(where)
           tableData.value = res.data.list
           total.value = res.data.count
         } finally {
           loading.value = false
         }
       }

       function handleGetSearch(val) {
         search.value = val.search
         viewSearch.value = val.viewSearch
         timeSearchObj.value = val.timeSearchObj
       }

       function handleConfirm(data) {
         if (data === 'reset') {
           Object.assign(where, { page: 1, limit: 15 })
         } else {
           Object.assign(where, { page: 1, limit: 15 }, data)
         }
         getTableData()
       }

       function handleSelectionChange(rows) {
         ids.value = rows.map((r) => r.id)
       }

       function handleAdd() {
         editDialogRef.value.openBox()
       }

       function handleEdit(row) {
         editDialogRef.value.openBox(row)
       }

       function handleView(row) {
         detailsDrawerRef.value.open(row)
       }

       async function handleDelete(row) {
         await proxy.$modalSure('确认删除该条数据？')
         await xxxDeleteApi(row.id)
         if (where.page > 1 && tableData.value.length <= 1) where.page--
         getTableData()
       }

       function handleDropdown(item) {
         switch (item.value) {
           case 1:
             tableRef.value.customSearchEvt(1); break
           case 2:
             tableRef.value.customSearchEvt(2); break
           case 3:
             proxy.$router.push({ path: `${roterPre}/<scope>/<feature>/dictSetting` }); break
           case 4:
             xxxExportApi({ ...where, page: 0, limit: 0 }); break
         }
       }

       onMounted(getTableData)

       // setup 返回的内容才能在模板访问；模板 ref 也需在此返回
       return {
         keyword, tableData, total, loading, search, viewSearch, timeSearchObj,
         where, dropdownList, editDialogRef, detailsDrawerRef, tableRef, fromBox,
         getTableData, handleGetSearch, handleConfirm, handleSelectionChange,
         handleAdd, handleEdit, handleView, handleDelete, handleDropdown
       }
     }
   }
   </script>
   ```

   > 模板里的 `ref="editDialog"`/`ref="detailsDrawer"`/`ref="tableData"` 需相应改为 `ref="editDialogRef"`/`ref="detailsDrawerRef"`/`ref="tableRef"`，与 `setup()` 返回的同名变量对齐。

3. **生成 `components/editDialog.vue`**（若需要新增/编辑）骨架：

   ```vue
   <template>
     <el-dialog
       :visible.sync="visible"
       :title="form.id ? '编辑' : '新增'"
       :width="DIALOG_SIZE.SM"
       :close-on-click-modal="false"
       @close="reset"
     >
       <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
         <el-form-item label="名称" prop="name">
           <el-input v-model="form.name" placeholder="请输入" maxlength="50" show-word-limit />
         </el-form-item>
       </el-form>
       <span slot="footer">
         <el-button size="small" @click="visible = false">取 消</el-button>
         <el-button size="small" type="primary" :loading="loading" @click="submit">确 定</el-button>
       </span>
     </el-dialog>
   </template>

   <script>
   import { ref, reactive } from 'vue'
   import { DIALOG_SIZE } from '@/constants/popupSize'
   import { xxxCreateApi, xxxUpdateApi } from '@/api/<scope>'

   export default {
     name: 'XxxEditDialog',
     emits: ['ok'],
     setup(props, { emit, expose }) {
       const visible = ref(false)
       const loading = ref(false)
       const formRef = ref(null)
       const form = reactive({ id: 0, name: '' })
       const rules = { name: [{ required: true, message: '请输入名称', trigger: 'blur' }] }

       function openBox(row) {
         reset()
         if (row) Object.assign(form, row)
         visible.value = true
       }

       function reset() {
         form.id = 0
         form.name = ''
         formRef.value && formRef.value.clearValidate()
       }

       function submit() {
         formRef.value.validate(async (ok) => {
           if (!ok) return
           loading.value = true
           try {
             if (form.id) await xxxUpdateApi(form.id, form)
             else await xxxCreateApi(form)
             visible.value = false
             emit('ok')
           } finally {
             loading.value = false
           }
         })
       }

       // 暴露给父组件 ref 调用
       expose({ openBox })

       return { DIALOG_SIZE, visible, loading, formRef, form, rules, openBox, reset, submit }
     }
   }
   </script>
   ```

4. **生成 `components/details.vue`**（若需要详情抽屉）骨架：

   ```vue
   <template>
     <el-drawer :visible.sync="visible" title="详情" :size="DRAWER_SIZE.SM" :wrapper-closable="false">
       <div v-loading="loading" class="p20">
         <el-descriptions :column="1" border>
           <el-descriptions-item label="名称">{{ data.name || '--' }}</el-descriptions-item>
         </el-descriptions>
       </div>
     </el-drawer>
   </template>

   <script>
   import { ref } from 'vue'
   import { DRAWER_SIZE } from '@/constants/popupSize'
   import { xxxDetailApi } from '@/api/<scope>'

   export default {
     name: 'XxxDetailsDrawer',
     setup(props, { expose }) {
       const visible = ref(false)
       const loading = ref(false)
       const data = ref({})

       async function open(row) {
         visible.value = true
         loading.value = true
         try {
           const res = await xxxDetailApi(row.id)
           data.value = res.data
         } finally {
           loading.value = false
         }
       }

       expose({ open })

       return { DRAWER_SIZE, visible, loading, data, open }
     }
   }
   </script>
   ```

5. **接线**：
   - 提示用户用 `create-route` skill 注册路由（或在后台菜单中绑定 `views/<scope>/<feature>/index`）
   - 提示对应权限码（`<scope>:<feature>:view/edit/add/delete`）需要在后台权限菜单中存在

## 兼容老页面（重要）

如果用户要求"参照客户列表/线索列表的写法"，可以退回到 Options API 写法（`export default { data() {...}, methods: {...} }`）并保持 `name: 'PascalCase'`。判定标准：
- 用户明确说"用现有写法"
- 用户在改老页面附近的功能（小改动，无需迁移）
- 老页面里已经大量混用 mixin / 全局 bus，迁移成本高

其余场景（全新页面）**默认使用 Options API + `setup()`**。

## 不要做的事

- 不要在列表页里直接 `axios.get`，必须用 `@/api/...`
- 不要重新实现搜索头/表格容器，**复用** `oaFromBox` + `customizeTable`
- 不要在成功后再写 `$message.success`，request 拦截器已统一处理
- 不要把弹窗、详情抽屉写在 `index.vue` 同一文件里，统一放进 `components/`
- 不要添加 `console.log` 与 `alert`

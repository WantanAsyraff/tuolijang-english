<template>
<div>

    <!-- 下拉选择 -->
    <el-select v-if="item.form_value === 'select' && selectList.includes(item.type)" v-model="item.option"
        size="small" :multiple="item.field === 'repeat' ? false : true" style="width: 100%" filterable>
        <el-option v-for="items in item.options" :key="items.value" :value="items.value"
            :label="$(items.label || items.name, items.label_en || items.name_en)"></el-option>
    </el-select>
    <el-select style="width: 100%" v-model="item.option" v-if="item.form_value === 'switch'" size="small">
        <el-option value="1" :label="$('ui.developFieldComponentYes')"></el-option>
        <el-option value="0" :label="$('ui.developFieldComponentNo')"></el-option>
    </el-select>

    <!-- 输入框类型 -->
    <el-input v-model="item.option" size="small"
        v-if="item.form_value == 'input' && ['input', 'textarea'].includes(item.type)"></el-input>

    <!-- 数字类型 -->
    <template v-if="item.form_value == 'number'">
        <el-input v-model="item.option" size="small" style="width: 100%" v-if="item.value == 'regex'">
        </el-input>
        <el-input-number v-model="item.option" :controls="false" size="small"
            :precision="item.type == 'input_number' ? 0 : undefined" style="width: 100%"
            v-if="!['between', 'regex'].includes(item.value)"></el-input-number>
        <!-- 区间 -->
        <div v-if="item.value == 'between'" class="flex">
            <el-input-number v-model="item.min" :controls="false" style="width: 50%" size="small"
                :precision="item.type == 'input_number' ? 0 : undefined"></el-input-number>
            <el-input-number v-model="item.max" :controls="false" style="width: 50%" class="ml10" size="small"
                :precision="item.type == 'input_number' ? 0 : undefined"></el-input-number>
        </div>
    </template>

    <!-- 级联选择 -->
    <el-cascader
        v-show="['cascader', 'cascader_radio'].includes(item.type) && item.options && item.option !== undefined"
        v-model="item.option" :options="getOptions()" :props="{
            label: 'name',
            value: 'value',
            expandTrigger: 'hover',
            emitPath: item.emitPath === undefined ? true : item.emitPath,
            multiple: true
        }" style="width: 100%" size="small" filterable></el-cascader>

    <!-- 管理范围 -->
    <el-cascader v-if="!noRule && item.type == 'scope_frame'" style="width: 100%" size="small" v-model="item.option"
        :options="frameTreeData" :props="{ checkStrictly: true, emitPath: false, value: 'id', label: 'label' }"
        :placeholder="$('ui.fdExamineFormBoxManagementScope')" filterable clearable :show-all-levels="false"></el-cascader>
    <!-- 级联选择省市区 -->
    <el-cascader v-model="item.option" :options="item.options || []" :props="{
        checkStrictly: true,
        label: 'name',
        value: 'value',
        multiple: true
    }" style="width: 100%" filterable size="small" v-if="['cascader_address'].includes(item.type)"></el-cascader>

    <!-- 日期选择 -->
    <div v-if="item.type == 'date_picker' && !dateList.includes(item.value)">
        <div v-if="item.value == 'between' || !noRule" class="flex">
      <el-date-picker v-model="item.option" type="daterange" :picker-options="pickerOptions" :range-separator="$('ui.commonFormListTo')" :start-placeholder="$('ui.customerSigningIndexStartDate')" :end-placeholder="$('ui.customerSigningIndexEndDate')" style="width: 100%" size="small"
                format=" yyyy/MM/dd" value-format="yyyy/MM/dd">
            </el-date-picker>
        </div>
        <el-input-number v-else-if="['n_day', 'last_day', 'next_day'].includes(item.value)" v-model="item.option"
            :controls="false" :min="0" style="width: 100%" size="small"></el-input-number>
        <el-date-picker v-else style="width: 100%" v-model="item.option" type="date" :placeholder="$('ui.userCalendarAddTodoSelectDate')"
            format=" yyyy/MM/dd" value-format="yyyy/MM/dd" size="small">
        </el-date-picker>
    </div>

    <!-- 日期时间选择 -->
    <div v-if="item.type == 'date_time_picker' && !lastYearList.includes(item.value)">
        <div v-if="item.value == 'between'" class="flex">
      <el-date-picker v-model="item.option" type="datetimerange" :range-separator="$('ui.commonFormListTo')" :start-placeholder="$('ui.customerSigningIndexStartDate')" :end-placeholder="$('ui.customerSigningIndexEndDate')" style="width: 100%" size="small" format=" yyyy/MM/dd HH:mm:ss"
                value-format="yyyy/MM/dd HH:mm:ss" :picker-options="pickerOptions">
            </el-date-picker>
        </div>
        <el-input-number v-else-if="['n_day', 'last_day', 'next_day'].includes(item.value)" v-model="item.option"
            :controls="false" :min="0" style="width: 100%" size="small"></el-input-number>
        <el-date-picker v-else style="width: 100%" v-model="item.option" type="datetime"
            format=" yyyy/MM/dd HH:mm:ss" value-format="yyyy/MM/dd HH:mm:ss" :placeholder="$('ui.administrationNoticeAddNoticeSelectDateTime')" size="small">
        </el-date-picker>
    </div>

    <!-- 一对一关联字段 -->
    <select-one v-if="item.type == 'input_select' && !item.category" :value="item.options[0] || {}" :id="item.id"
        :showType="item.association_show_type" style="width: 100%"
        @getSelection="getSelection($event, item)"></select-one>

    <!-- 选择标签 -->
    <select-label v-if="item.type == 'tag'" :list="item.options || []" :value="item.optionsList || []"
        style="width: 100%" :props="{ children: 'children', label: 'name' }"
        @handleLabelConf="handleLabelConf($event)"></select-label>

    <!-- 选择人员 -->
    <select-member v-if="item.category == 2" :onlyOne="['in', 'not_in'].includes(item.value) ? false : true"
        :value="item.options.userList || []" :selectIdData="item.option || []"
        @getSelectList="getSelectList($event, item)" style="width: 100%"></select-member>

    <!-- 选择部门 -->
    <select-department v-if="item.category == 1" :onlyOne="['in', 'not_in'].includes(item.value) ? false : true"
        :value="item.options.depList || []" @changeMastart="changeMastart($event, item)"
        style="width: 100%"></select-department>
</div>
</template>
<script>
export default {
    name: "FieldComponent",
    components: {
        selectMember: () => import('@/components/form-common/select-member'),
        selectDepartment: () => import('@/components/form-common/select-department'),
        selectOne: () => import('@/components/form-common/select-one'),
        selectLabel: () => import('@/components/form-common/select-label')
    },
    props: {
        item: Object,
        noRule: Boolean,
        activeIndex: Number,
        conditionConfig: Object,
        list: Array,
        index: Number,
        type: String // 图表设计'dashboard'  
    },


    data() {
        return {
            pickerOptions: this.$pickerOptionsTimeEle,
            selectList: ['select', 'radio', 'checkbox'],
            dateList: ['today', 'week', 'month', 'quarter', 'year'],
            lastYearList: ['today', 'week', 'month', 'quarter', 'year', 'last_year'],
        }
    },
    methods: {
        getOptions() {
            const localizeOptions = (options) => Array.isArray(options) ? options.map((option) => ({
                ...option,
                label: option.label == null ? option.label : this.$(option.label, option.label_en),
                name: option.name == null ? option.name : this.$(option.name, option.name_en),
                children: localizeOptions(option.children)
            })) : [];
            return localizeOptions(this.item.options);
        },
        // 选择成员回调
        getSelectList(data, item) {
            if (data.length > 0) {
                data.forEach((item) => {
                    item.id = item.value
                })
            }
            item.options.userList = data
            let arr = []
            data.map((item) => {
                arr.push(item.id)
            })
            item.option = arr
            if (this.activeIndex) {
                this.activeIndex = -1
            }

        },
        // 选择部门完成回调
        changeMastart(data, item) {
            item.options.depList = data
            let arr = []
            data.map((item) => {
                arr.push(item.id)
            })
            item.option = arr
            if (this.activeIndex) {
                this.activeIndex = -1
            }
        },
        // 选中客户标签成功回调
        handleLabelConf(res) {
            if (this.type !== 'dashboard') {
                this.list[this.index].options = res.list
            }
            this.list[this.index].optionsList = res.ids
            this.list[this.index].option = res.ids
            this.item.option = res.ids
        },
        getSelection(data, item) {
            item.options = [data]
            item.option = data.id
            if (this.activeIndex) {
                this.activeIndex = -1
            }
        },


    }
}
</script>

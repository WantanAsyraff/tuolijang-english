<template>
<div class="divBox" v-loading="loading">
  <el-card class="mb12 box-height">
    <el-tabs v-model="where.link_type" @tab-click="handleClick">
      <el-tab-pane :label="$t('ui.customerKpiIndexDepartmentTarget')" name="1" />
      <el-tab-pane :label="$t('ui.customerKpiIndexSalespersonTarget')" name="0" />
    </el-tabs>
    <!-- 筛选 -->
    <div class="flex mb10 mt20">
      <el-date-picker
        v-model="where.year"
        type="year"
        size="small"
        format="yyyy"
        value-format="yyyy"
        :placeholder="$t('ui.customerKpiIndexSelectYear')"
        @change="getTableData"
      >
      </el-date-picker>
      <select-department
        v-if="where.link_type == 1"
        :onlyOne="false"
        :value="userList"
        :isSearch="true"
        @changeMastart="changeMastart"
        style="width: 250px"
        class="ml10 mr10"
      ></select-department>
      <select-member
        v-if="where.link_type == 0"
        class="ml10 mr10"
        :onlyOne="false"
        :value="userList"
        :isSearch="true"
        @getSelectList="getSelectList"
        style="width: 250px"
      >
      </select-member>
      <el-tooltip effect="dark" :content="$t('ui.administrationMaterialFixedRecordResetSearchConditions')" placement="top">
        <div class="reset" @click="reset"><i class="iconfont iconqingchu"></i></div>
      </el-tooltip>
    </div>
    <!-- 统计图 -->
    <div class="mt10">
      <echartBox :option-data="optionData" :styles="styles" />
    </div>

    <!-- 表格 -->
    <el-table :data="tableData" border style="width: 100%" :cell-style="tableCellStyle" class="mt30">
      <el-table-column :label="$t('ui.businessHolidayQueryIndexDepartment')" prop="user.name" fixed> </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexFullYear')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="annual.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="annual.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="annual.ratio" />
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexQ1')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="q1.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="q1.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="q1.ratio" />
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexJanuary')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="month1.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="month1.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="month1.ratio">
          <template slot-scope="scope"> {{ scope.row.month1.ratio }}% </template>
        </el-table-column>
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexFebruary')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="month2.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="month2.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="month2.ratio">
          <template slot-scope="scope"> {{ scope.row.month2.ratio }}% </template>
        </el-table-column>
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexMarch')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="month3.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="month3.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="month3.ratio">
          <template slot-scope="scope"> {{ scope.row.month3.ratio }}% </template>
        </el-table-column>
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexQ2')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="q2.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="q2.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="q2.ratio" />
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexApril')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="month4.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="month4.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="month4.ratio">
          <template slot-scope="scope"> {{ scope.row.month4.ratio }}% </template>
        </el-table-column>
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexMay')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="month5.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="month5.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="month5.ratio">
          <template slot-scope="scope"> {{ scope.row.month5.ratio }}% </template>
        </el-table-column>
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexJune')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="month6.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="month6.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="month6.ratio">
          <template slot-scope="scope"> {{ scope.row.month6.ratio }}% </template>
        </el-table-column>
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexQ3')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="q3.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="q3.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="q3.ratio" />
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexJuly')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="month7.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="month7.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="month7.ratio">
          <template slot-scope="scope"> {{ scope.row.month7.ratio }}% </template>
        </el-table-column>
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexAugust')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="month8.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="month8.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="month8.ratio">
          <template slot-scope="scope"> {{ scope.row.month8.ratio }}% </template>
        </el-table-column>
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexSeptember')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="month9.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="month9.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="month9.ratio">
          <template slot-scope="scope"> {{ scope.row.month9.ratio }}% </template>
        </el-table-column>
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexQ4')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="q4.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="q4.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="q4.ratio" />
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexOctober')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="month10.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="month10.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="month10.ratio" />
      </el-table-column>
      <el-table-column :label="$t('ui.customerTargetStatisticsIndexNovember')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="month11.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="month11.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="month11.ratio">
          <template slot-scope="scope"> {{ scope.row.month11.ratio }}% </template>
        </el-table-column>
      </el-table-column>

      <el-table-column :label="$t('ui.customerTargetStatisticsIndexDecember')" align="center">
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompleted')" prop="month12.amount" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexTarget')" prop="month12.target" width="100" />
        <el-table-column :label="$t('ui.customerTargetStatisticsIndexCompletionRate')" prop="month12.ratio">
          <template slot-scope="scope"> {{ scope.row.month12.ratio }}% </template>
        </el-table-column>
      </el-table-column>
    </el-table>
  </el-card>
</div>
</template>
<script>
import { clientTargetRateApi, clientTargetCensusApi } from '@/api/client'
import { translateRuntimeText } from '@/utils/i18ns'
export default {
  name: '',
  components: {
    echartBox: () => import('@/components/common/echarts'),
    selectMember: () => import('@/components/form-common/select-member'),
    selectDepartment: () => import('@/components/form-common/select-department')
  },
  props: {},
  data() {
    return {
      userList: [],
      styles: {
        height: '280px',
        width: '100%',
        margin: 'auto'
      },
      loading: false,
      optionData: {},
      where: { year: '', frame_id: [], user_id: [], link_type: '1' },

      tableData: []
    }
  },

  mounted() {
    this.where.year = this.$moment().format('YYYY')
    this.initChart()
    this.getTableData()
  },

  methods: {
    tChartText(text) {
      return translateRuntimeText(text, this)
    },
    // 获取表格数据
    getListData() {
      clientTargetRateApi(this.where).then((res) => {
        res.data.forEach((item) => {
          item.q1 = this.calculateQuarterData(item, ['month1', 'month2', 'month3'])
          item.q2 = this.calculateQuarterData(item, ['month4', 'month5', 'month6'])
          item.q3 = this.calculateQuarterData(item, ['month7', 'month8', 'month9'])
          item.q4 = this.calculateQuarterData(item, ['month10', 'month11', 'month12'])
          item.annual = this.calculateQuarterData(item, ['q1', 'q2', 'q3', 'q4'])
          const ratio = (item.annual.amount / item.annual.target) * 100 // 先转百分比数值
          item.annual.ratio = ratio.toFixed(2) + '%'
          // item.annual.ratio = Number(item.annual.amount / item.annual.target).toFixed(2) * 100 + '%'
          item.q1.ratio = Number(item.q1.amount / item.q1.target).toFixed(2) * 100 + '%'
          item.q2.ratio = Number(item.q2.amount / item.q2.target).toFixed(2) * 100 + '%'
          item.q3.ratio = Number(item.q3.amount / item.q3.target).toFixed(2) * 100 + '%'
          item.q4.ratio = Number(item.q3.amount / item.q4.target).toFixed(2) * 100 + '%'
        })
        this.tableData = res.data
      })
    },

    // 设置单元格背景色
    tableCellStyle({ row, column, rowIndex, columnIndex }) {
      if (column.label == '完成' || column.label == this.tChartText('完成')) {
        return 'color: #1980ff;'
      }
    },
    calculateQuarterData(item, months) {
      // 计算总和并保留两位小数
      const sumValues = (prop) => {
        return Number(
          months
            .reduce((sum, month) => {
              // 处理可能的空值或非数字情况
              const value = Number(item[month]?.[prop] || 0)
              return sum + value
            }, 0)
            .toFixed(2)
        )
      }

      return {
        target: sumValues('target'),
        amount: sumValues('amount'),
        ratio: sumValues('ratio')
      }
    },

    getStatisticalData() {
      this.loading = true
      clientTargetCensusApi(this.where).then((res) => {
        this.loading = false
        this.optionData.xAxis.data = Array.isArray(res.data.xAxis) ? res.data.xAxis.map((item) => this.tChartText(item)) : res.data.xAxis
        this.optionData.series[0].data = res.data.series[1].data
        this.optionData.series[1].data = res.data.series[0].data
        this.optionData.series[2].data = res.data.series[2].data
        const numbers = res.data.series[2].data.filter((item) => !isNaN(Number(item))).map(Number)
        this.optionData.yAxis[1].max = Math.max(...numbers)
      })
    },
    initChart() {
      this.optionData = {
        tooltip: {
          trigger: 'axis',
          backgroundColor: 'rgba(50, 49, 49, 0.60)',
          borderColor: 'transparent',
          borderWidth: 1,
          textStyle: { color: '#FFFFFF' },
          formatter: (params) => {
            let res = `<div style="font-weight: bold;">${params[0].name}</div>`
            params.forEach((item) => {
              let value = item.value
              let unit = item.seriesName === this.tChartText('完成率') ? '%' : this.tChartText('元')
              res += `<div>${item.marker} ${item.seriesName}: ${value}${unit}</div>`
            })
            return res
          }
        },
        legend: {
          data: ['完成业绩', '目标', '完成率'].map((item) => this.tChartText(item)),
          top: 0,
          textStyle: { color: '#4E5969' },
          icon: 'circle'
        },
        grid: {
          left: '0%',
          right: '0%',
          bottom: '0%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          data: [],
          axisLine: {
            lineStyle: { color: '#ddd' }
          },
          axisLabel: {
            color: '#666'
          }
        },
        yAxis: [
          {
            type: 'value',
            name: this.tChartText('金额(元)'),
            nameTextStyle: { color: '#666' },
            axisLine: {
              lineStyle: { color: '#ddd' }
            },
            axisLabel: {
              color: '#666',
              formatter: '{value}'
            },
            splitLine: {
              lineStyle: { color: '#f5f5f5' }
            }
          },
          {
            type: 'value',
            name: this.tChartText('完成率'),
            nameTextStyle: { color: '#666' },
            axisLine: {
              lineStyle: { color: '#ddd' }
            },
            axisLabel: {
              color: '#666',
              formatter: '{value}%'
            },
            splitLine: {
              show: false
            },
            max: 200
          }
        ],
        series: [
          {
            name: this.tChartText('完成业绩'),
            type: 'bar',
            data: [],
            itemStyle: {
              color: '#1890FF'
            },
            barWidth: 20
          },
          {
            name: this.tChartText('目标'),
            type: 'bar',
            data: [],
            itemStyle: {
              color: '#4BCAD5'
            },
            barWidth: 20
          },
          {
            name: this.tChartText('完成率'),
            type: 'line',
            yAxisIndex: 1,
            itemStyle: {
              color: '#FFCD27'
            },

            data: []
          }
        ]
      }
    },

    getTableData() {
      this.getListData()
      this.getStatisticalData()
    },
    getSelectList(val) {
      this.userList = val
      this.where.user_id = []
      val.map((item) => {
        this.where.user_id.push(item.value)
      })
      this.getTableData()
    },
    changeMastart(val) {
      this.userList = val
      this.where.frame_id = []
      val.map((item) => {
        this.where.frame_id.push(item.id)
      })
      this.getTableData()
    },
    reset() {
      this.where.year = this.$moment().format('YYYY')
      this.where.frame_id = []
      this.where.user_id = []
      this.userList = []
      this.getTableData()
    },
    handleClick() {
      this.reset()
      this.getTableData()
    }
  }
}
</script>
<style scoped lang="scss"></style>

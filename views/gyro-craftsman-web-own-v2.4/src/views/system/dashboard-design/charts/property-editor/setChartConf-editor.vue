<template>
<div class="chart-styles">
  <div class="c-s-t">{{ $("ui.systemDashboardDesignChartsPropertyEditorSetChartConfEditorChartSettings") }}</div>
  <!-- 表格 -->
  <template v-if="selectedWidget.type == 'listTable' || selectedWidget.type == 'pivotTable'">
    <el-form-item :label="$('ui.systemDashboardDesignChartsPropertyEditorSetChartConfEditorMaximumDisplayedRows')" v-if="selectedWidget.type == 'listTable'">
      <el-input-number v-model="optionModel.setChartConf.pageSize" :min="1" :max="999" />
    </el-form-item>
    <el-form-item :label="$('ui.systemDashboardDesignChartsPropertyEditorSetChartConfEditorShowSummaryRow')">
      <el-switch v-model="optionModel.setChartConf.showSummary"></el-switch>
    </el-form-item>
    <el-form-item :label="$('ui.systemDashboardDesignChartsPropertyEditorSetChartConfEditorShowSummaryColumn')">
      <el-switch v-model="optionModel.setChartConf.showSumcol"></el-switch>
    </el-form-item>
  </template>
  <template v-else>
    <el-form-item :label="$('ui.systemDashboardDesignChartsPropertyEditorSetChartConfEditorShowValues')" v-if="selectedWidget.type !== 'statistic'">
      <el-switch v-model="optionModel.setChartConf.numShow"></el-switch>
    </el-form-item>
    <el-form-item :label="$('ui.systemDashboardDesignChartsPropertyEditorSetChartConfEditorShowLegend')" v-if="selectedWidget.type !== 'statistic'">
      <el-switch
        v-model="optionModel.setChartConf.chartShow"
        :disabled="selectedWidget.type == 'progressbar' && optionModel.chartStyle != 1"
      ></el-switch>
    </el-form-item>
  </template>
  <el-form-item :label="$('ui.systemDashboardDesignChartsPropertyEditorSetChartConfEditorUseAllData')">
    <el-switch v-model="optionModel.setChartConf.useAllData"></el-switch>
    <el-tooltip effect="dark" :content="$('ui.systemDashboardDesignChartsPropertyEditorSetChartConfEditorWhenDisabledUsersCanViewOnlyDataWithinTheir')" placement="top">
      <span class="question-icon">
        <el-icon class="el-icon-warning-outline" size="16"> </el-icon>
      </span>
    </el-tooltip>
  </el-form-item>
</div>
</template>
<script>
export default {
  name: 'setChartConf-editor',
  props: {
    designer: Object,
    selectedWidget: Object,
    optionModel: Object
  }
}
</script>
<style lang="scss" scoped>
.chart-styles {
  padding-top: 20px;
  .c-s-t {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 5px;
  }
  .question-icon {
    cursor: pointer;
    margin-left: 5px;
    position: relative;
    top: 1px;
  }
}
</style>

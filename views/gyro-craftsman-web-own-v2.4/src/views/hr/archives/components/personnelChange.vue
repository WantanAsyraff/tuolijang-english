<template>
  <div class="table-box">
    <el-timeline v-if="tabsName === 'personnelChange'">
      <div class="default" v-if="list.length == 0">
        <img src="../../../../assets/images/emptyState.png" alt="" class="img" />
        <div class="text">{{ $("legacy.6859e9fd91e0d29e") }}</div>
      </div>
      <el-timeline-item v-for="(item, index) in list" :key="index">
        <!-- 离职 -->
        <div class="quit" v-if="item.types === 3">
          <div class="time">
            {{ item.date ? item.date : item.created_at }} <el-tag type="warning" size="mini" class="ml15">{{ $("hr.dimission") }}</el-tag>
          </div>
          <div>{{ $("legacy.c99ee0bfc0ab5570") }} {{ item.o_frame ? item.o_frame.name : '' }}</div>
          <div>{{ $("legacy.e5b99942c5cffdd1") }}{{ item.o_position ? item.o_position.name : '' }}</div>
          <div>{{ $("legacyScript.reasonsForLeaving") }} {{ item.info }}</div>
          <div>{{ $("legacyScript.resignationNotes") }} {{ item.mark }}</div>
        </div>
        <!-- 转正 -->
        <div class="quit" v-if="item.types === 1">
          <div class="time">
            {{ item.date ? item.date : item.created_at }}
            <el-tag type="warning" size="mini" class="ml15">{{ $("ui.hrArchivesIndexEmployeesConfirmEmployment") }}</el-tag>
          </div>
          <div>{{ $("legacy.ec0b8969a5aec13c") }} {{ item.mark || '--' }}</div>
        </div>

        <!-- 调岗 -->
        <div class="post-transfer quit" v-if="item.types === 2">
          <div class="time">{{ item.created_at }} <el-tag type="success" size="mini" class="ml15">{{ $("legacy.57f915235dd9963e") }}</el-tag></div>
          <div>{{ $("legacy.34e01784752a551a") }}</div>
          <div>{{ $("legacy.9f5ae46bc0e62190") }}</div>
          <div>{{ $("legacy.6352f99c4a7e1af0") }}</div>
        </div>
        <!-- 入职 -->
        <div class="post-transfer quit" v-if="item.types === 0">
          <div class="time">
            {{ item.date ? item.date : item.created_at }} <el-tag size="mini" class="ml15">{{ $("ui.hrArchivesUserDetailsOnboard") }}</el-tag>
          </div>

          <div>{{ $("legacyScript.employmentType") }} {{ item.is_part || '--' }}</div>
          <div>{{ $("legacy.e8a7a0d864f0f990") }}{{ item.n_frame ? item.n_frame.name : '--' }}</div>
          <div>{{ $("ui.userTrainingPromotionPosition") }} {{ item.n_position ? item.n_position.name : '--' }}</div>
        </div>
      </el-timeline-item>
    </el-timeline>
    <el-timeline v-if="tabsName == 'salaryAdjustmentRecord'">
      <div class="default" v-if="list.length == 0">
        <img src="../../../../assets/images/emptyState.png" alt="" class="img" />
        <div class="text">{{ $("legacy.8afdee6e56cc99ba") }}</div>
      </div>
      <el-timeline-item
        v-for="(activity, index) in list"
        :key="index"
        :icon="activity.icon"
        :type="activity.type"
        :color="activity.color"
        :size="activity.size"
      >
        <!-- 调薪记录 -->
        <div class="quit">
          <div class="time">
            {{ activity.take_date }}
            <el-tag type="success" size="mini" class="ml15" v-if="index + 1 !== list.length">{{ $("legacy.c0f8f9b95322edae") }}</el-tag>
            <el-tag size="mini" class="ml15" v-else>{{ $("legacyScript.setSalary") }}</el-tag>
          </div>
          <div class="icon"></div>
          <div>{{ $("legacy.a6a39a5d14328f8a") }} {{ activity.total }}</div>
          <div class="salary">
            <span v-for="(item, id) in activity.content" :key="id"
              >{{ item.label }}&nbsp;&nbsp;&nbsp;{{ item.value }}</span
            >
          </div>

          <div>{{ $("legacy.440b3849620dc018") }} {{ activity.created_at }}</div>
        </div>
      </el-timeline-item>
    </el-timeline>
  </div>
</template>
<script>
export default {
  name: 'dfd',

  props: {
    tabsName: {
      type: String,
      default: ''
    },
    list: {
      type: Array,
      default: []
    }
  },
  data() {
    return {}
  },

  methods: {}
}
</script>
<style scoped lang="scss">
.table-box {
  width: 100%;
  margin-top: 30px;
  margin-bottom: 200px;
  height: calc(100vh - 140px);

  overflow-y: auto;
}
.iconfont {
  color: #1890ff;
}
.iconbianji1 {
  margin-right: 10px;
}

.table-box::-webkit-scrollbar {
  height: 0;
  width: 0;
}
.salary {
  width: 100%;
  display: flex;
  justify-content: space-between;
}

.quit {
  font-size: 11px;
  color: #666;
  position: relative;
  margin-top: 3px;
  > div {
    margin-bottom: 15px;
    &:last-of-type {
      margin-bottom: 0;
    }
  }
}
.icon {
  position: absolute;
  top: 2px;
  right: 20px;
  display: flex;
  font-size: 18px;
}
.time {
  font-size: 13px;
  font-weight: 700;
  color: #666;
  display: flex;
  align-items: center;
}
.ml15 {
  margin-left: 15px;
}
.default {
  position: absolute;
  left: 50%;
  top: 50%;
  display: flex;
  transform: translate(-50%, -50%);
  flex-direction: column;
  justify-content: center;
  align-items: center;

  .img {
    width: 100px;
    height: 90px;
  }
  .text {
    color: #666;
  }
}
</style>

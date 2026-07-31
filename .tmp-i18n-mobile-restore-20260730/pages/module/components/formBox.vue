<template>
  <view class="content">
    <picker class="picker-selector" mode="selector" value="0" :range="data.usersData" range-key="name"
      @change="changeUsers">
      <view class="search-default-label">
        {{ data.usersText }}
        <text class="iconfont icon-zhankai1"></text>
      </view>
    </picker>

    <!-- 管理范围 -->
    <view class="picker-selector">
      <uni-data-picker v-model="data.where.scope_frame" :localdata="data.frameList"
        :map="{ text: 'name', value: 'value' }" @change="radioChange">
        <view class="ellipsis">
          {{ data.where.scope_frame ? data.manageRangeText : data.manageRange }}
          <text class="date-open-icon iconfont icon-zhankai1"></text>
        </view>
      </uni-data-picker>
    </view>

    <view class="picker-selector" v-for="(item, index) in info.seniorSearch" :key="index">
      <!-- 时间日期筛选 -->
        <!--- 标签---->
      <view class="search-default-label" @click="handleDaterange(item, index)" v-if="item.form_value == 'date_time_picker' || item.form_value == 'date_picker'">
        <text class="ellipsis">{{ item.text ? item.text : item.field_name }}</text>
        <text v-if="!data.where[item.field_name_en]" class="date-open-icon iconfont icon-zhankai1"></text>
        <text v-if="data.where[item.field_name_en]" class="iconfont date-clear icon-shenpizhongxin-jujue"
          @click.stop="clickClear(item)"></text>
      </view>

      <!-- 省市区 -->
      <uni-data-picker v-if="['cascader_address'].includes(item.form_value)" v-model="data.where[item.field_name_en]"
        :localdata="data.addressData" :map="{ text: 'name', value: 'value' }" @change="cityChange($event, item, index)">
        <template v-slot:default>
          <view>
            <text class="ellipsis">{{ item.text || item.field_name }}</text>
            <text v-if="!data.where[item.field_name_en]" class="date-open-icon iconfont icon-zhankai1"></text>
            <text v-if="data.where[item.field_name_en]" class="iconfont date-clear icon-shenpizhongxin-jujue"
              @click.stop="clickClear(item)"></text>
          </view>
        </template>
      </uni-data-picker>

      <!----级联单选--->
      <uni-data-picker v-if="['cascader_radio'].includes(item.form_value)" :localdata="item.data_dict"
        :map="{ text: 'name', value: 'value' }" @change="radioChange($event, item)">
        <view>
          <text class="ellipsis">{{ item.text || item.field_name }}</text>
          <text v-if="!data.where[item.field_name_en]" class="date-open-icon iconfont icon-zhankai1"></text>
          <text v-if="data.where[item.field_name_en]" class="iconfont date-clear icon-shenpizhongxin-jujue"
            @click.stop="clickClear(item)"></text>
        </view>
      </uni-data-picker>

      <!----开关---->
      <picker mode="selector" v-if="item.form_value == 'switch'" :value="item.value" :range="data.switchList"
        range-key="name" @change="pickerChange($event, item, 'switch')">
        <view>
          <text class="ellipsis">{{ item.text || item.field_name }}</text>
          <text v-if="!data.where[item.field_name_en]" class="date-open-icon iconfont icon-zhankai1"></text>
          <text v-if="data.where[item.field_name_en]" class="iconfont date-clear icon-shenpizhongxin-jujue"
            @click.stop="clickClear(item)"></text>
        </view>
      </picker>

      <!----单选---->
      <picker mode="selector" v-if="['radio'].includes(item.form_value)" :value="item.value" :range="item.data_dict"
        range-key="name" @change="pickerChange($event, item)">
        <view>
          <text class="ellipsis">{{ item.text || item.field_name }}</text>
          <text v-if="!data.where[item.field_name_en]" class="date-open-icon iconfont icon-zhankai1"></text>
          <text v-if="data.where[item.field_name_en]" class="iconfont date-clear icon-shenpizhongxin-jujue"
            @click.stop="clickClear(item)"></text>
        </view>
      </picker>

      <!--- 标签---->
      <view class="search-default-label" @click="changeLabel(item, index)" v-if="item.form_value == 'tag'">
        <text class="ellipsis">{{ item.text ? item.text[0] : item.field_name }}</text>
        <text v-if="!data.where[item.field_name_en]" class="date-open-icon iconfont icon-zhankai1"></text>
        <text v-if="data.where[item.field_name_en]" class="iconfont date-clear icon-shenpizhongxin-jujue"
          @click.stop="clickClear(item)"></text>
      </view>

      <!--- 一对一---->
      <view class="search-default-label" @click="openOne(item, index)" v-if="
            item.form_value == 'input_select' && !data.member.includes(item.field_name_en) && item.field_name_en !== 'frame_id'&&item.association_show_type==1
          ">
        <text class="ellipsis">{{ getOneOnOne.name ? getOneOnOne.name : item.field_name }}</text>
        <text v-if="!data.where[item.field_name_en]" class="date-open-icon iconfont icon-zhankai1"></text>
        <text v-if="data.where[item.field_name_en]" class="iconfont date-clear icon-shenpizhongxin-jujue"
          @click.stop="clickClear(item,getOneOnOne)"></text>
      </view>
      <!-- 一对一下拉选择 -->
      <picker mode="selector"
        v-if="
            item.form_value == 'input_select' && !data.member.includes(item.field_name_en) && item.field_name_en !== 'frame_id'&&item.association_show_type==0"
        :value="item.id" :range="data.index_field_list" :range-key="data.index_field_name"
        @change="pickerChange($event, item,'one')">
        <view>
          <text class="ellipsis">{{ item.text || item.field_name }}</text>

          <text v-if="!data.where[item.field_name_en]" class="date-open-icon iconfont icon-zhankai1"></text>
          <text v-if="data.where[item.field_name_en]" class="iconfont date-clear icon-shenpizhongxin-jujue"
            @click.stop="clickClear(item)"></text>
        </view>
      </picker>

      <!-- 选择部门 -->
      <view class="search-default-label" @click="openFrame(item, index)" v-if="item.field_name_en === 'frame_id'">
        <text class="ellipsis">{{ item.text ? item.text : item.field_name }}</text>
        <text v-if="!data.where[item.field_name_en]" class="date-open-icon iconfont icon-zhankai1"></text>
        <text v-if="data.where[item.field_name_en]" class="iconfont date-clear icon-shenpizhongxin-jujue"
          @click.stop="clickClear(item)"></text>
      </view>
      <!-- 选择人员-->
      <view class="search-default-label" @click="openMember(item, index)"
        v-if="data.member.includes(item.field_name_en)">
        <text class="ellipsis">{{ item.text ? item.text : item.field_name }}</text>
        <text v-if="!data.where[item.field_name_en]" class="date-open-icon iconfont icon-zhankai1"></text>
        <text v-if="data.where[item.field_name_en]" class="iconfont date-clear icon-shenpizhongxin-jujue"
          @click.stop="clickClear(item)"></text>
      </view>

      <!--- 级联复选---->
      <view class="search-default-label" @click="changeCascde(item, index)" v-if="item.form_value == 'cascader'">
        <text class="ellipsis">{{ item.text ? item.text : item.field_name }}</text>
        <text v-if="!data.where[item.field_name_en]" class="date-open-icon iconfont icon-zhankai1"></text>
        <text v-if="data.where[item.field_name_en]" class="iconfont date-clear icon-shenpizhongxin-jujue"
          @click.stop="clickClear(item)"></text>
      </view>

      <!--- 复选---->
      <view class="search-default-label" @click="changeCheck(item, index)" v-if="item.form_value == 'checkbox'">
        <text class="ellipsis">{{ item.text ? item.text[0].text : item.field_name }}</text>
        <text v-if="!data.where[item.field_name_en]" class="date-open-icon iconfont icon-zhankai1"></text>
        <text v-if="data.where[item.field_name_en]" class="iconfont date-clear icon-shenpizhongxin-jujue"
          @click.stop="clickClear(item)"></text>
        <multiplePicker :show="data.show" :columns="item.data_dict" @change="multiplePickerChange($event, item)"
          @cancel="ontouchcancel"></multiplePicker>
      </view>
    </view>
  </view>
  <selected-label title="选择标签" ref="selectedLabelRef" :listData="data.labelData" @changeItem="changeItem"
    @resetLabel="resetLabel"></selected-label>
  <!-- 级联复选组件 -->
  <cascade ref="cascdeRef" title="选择分类" :listData="data.cascadeData" @reset="resetCascade" @change="changeCascade"
    @submitOk="cascadeSubmit"></cascade>
     <timePopup ref="timePopupRef" @change="changeTime"></timePopup>
</template>

<script setup>
  import { navigateToDepartment} from "@/utils/autoload";
  import timePopup from "@/components/timePopup/index.vue";
  import { ref, reactive, onMounted, toRefs } from "vue";
  import selectedLabel from "./selectedLabel.vue";
  import { useStore } from "vuex";
  const store = useStore();
  import { clickNavigateTo } from "@/utils/helper";
  import cascade from "@/components/moduleForm/cascade.vue";
  import multiplePicker from "@/components/multiplePicker/index.vue";
  import { getDictTreeListApi, getDictFrameListApi, dataModulerListApi, dataModulerFieldApi } from "@/api/crud";

  const props = defineProps({
    info: {
      type: Object,
      default: () => ({}),
    },
  });
  const { info } = toRefs(props);
  const data = reactive({
    typeText: "客户状态",
    timeText: "创建日期",
    manageRange: "管理范围",
    usersText: "我查看的",
    labelText: "标签筛选",
    typeIndex: 0,
    usersIndex: "0",
    show: false,
    member: ["user_id", "update_user_id", "owner_user_id", "creator", "salesman", "test_uid"],
    mode: "multiSelector",
    manageRangeText: "全部",
    where: {
      show_search_type: "0",
      scope_frame: "all",
    },
    labelData: [],
    cascadeData: [],
    addressData: [],
    switchList: [{
        name: "是",
        value: "1",
      },
      {
        name: "否",
        value: "0",
      },
    ],
    frameList: [],
    rowData: {}, // 当前点击的字段
    rowIndex: 0,
    index_field_name: '', // 一对一字段的主字段
    index_field_list: [], // 一对一下拉选项
    usersData: [{
        name: "我查看的",
        id: "0",
      },
      {
        name: "我负责的",
        id: "1",
      },
      {
        name: "我创建的",
        id: "2",
      },
    ],
  });

  const formData = reactive({
    show_search_type: "0",
    scope_frame: "all",
  });

  // 部门数据
  const getSelectPeople = computed(() => {
    return store.state.app.depSelectPeople;
  });
  // 一对一数据
  const getOneOnOne = computed(() => {
    return store.state.app.oneOnOneData;
  });

  watch(() => [getOneOnOne, getSelectPeople, info], (newvalue) => {
    const ids = [];
    const str = [];
    if (newvalue[0].value) {
      data.where[data.rowData.field_name_en] = newvalue[0].value.id;
    }
    if (newvalue[1].value.length > 0) {
      newvalue[1].value.map((item) => {
        ids.push(item.id);
        str.push(item.name);
      });
      data.where[data.rowData.field_name_en] = ids;
      info.value.seniorSearch[data.rowIndex].text = str.join(",");
    }
    confirmData();
    if (newvalue[2].value) {
      newvalue[2].value.seniorSearch.forEach(item => {
        if (item.form_value == 'input_select' && !data.member.includes(item.field_name_en) && item
          .field_name_en !== 'frame_id') {
          handleDropdownChange(item)
        }
      })
    }

  }, {
    deep: true,
  });

  // 管理范围
  const radioChange = (e, val) => {
    if (!val) {
      data.manageRangeText = e.detail.value[e.detail.value.length - 1].text;
      data.where.scope_frame = e.detail.value[e.detail.value.length - 1].value;
    } else {
      val.text = e.detail.value[e.detail.value.length - 1].text;
      data.where[val.field_name_en] = e.detail.value[e.detail.value.length - 1].value;
    }
    confirmData();
  };

  // 选择时间
  const timePopupRef = ref(null);
  const handleDaterange = (item, index) => {
    data.rowData = item;
    data.rowIndex = index;
     timePopupRef.value.popupOpen();
  }

  // 下拉样式一点一数据
  const handleDropdownChange = async (selectedOption) => {
    getHeader(selectedOption.id),
      getTableList(selectedOption.id)
  };

  const getHeader = (id) => {
    dataModulerFieldApi(id).then((res) => {
      data.index_field_name = res.data.index_field_name
    });
  };

  // 获取实体列表
  const getTableList = (val) => {
    dataModulerListApi(val, { page: 0, limit: 0 }).then((res) => {
      data.index_field_list = res.data.list
    });
  };

  const timeRef = ref(null);
  let emit = defineEmits(["confirmData"]);
  onMounted(() => {
    confirmData();
    getDict();
    getFrameList();
  });

  const confirmData = () => {
    emit("confirmData", data.where);
  };

  const getDict = () => {
    let obj = {
      type_id: 2,
    };
    getDictTreeListApi(obj).then((res) => {
      data.addressData = res.data;
    });
  };
  const selectedLabelRef = ref(null);

  const changeLabel = (e, index) => {
    data.rowData = e;
    data.rowIndex = index;
    data.labelData = e.data_dict;
    selectedLabelRef.value.popupOpen(data.where[e.field_name_en], e.text || []);
  };
  // 选择一对一
  const openOne = (val, index) => {
    data.rowData = val;
    data.rowIndex = index;
    clickNavigateTo(`/pages/module/oneOnOne?id=${val.id}&&keyName=${info.value.crudInfo.table_name_en}`);
  };

  // 选择部门
  const openFrame = (val, index) => {
    data.rowData = val;
    data.rowIndex = index;
    const query = `isShow=true&isFirst=1&isSelect=1&mode=${data.mode}&showPerson=false`;
    navigateToDepartment(query, `/pages/module/list?tablename=${info.value.crudInfo.table_name_en}`);
  };
  // 选择人员
  const openMember = (val, index) => {
    data.rowData = val;
    data.rowIndex = index;
    const query = `isShow=true&isFirst=1&isSelect=1&mode=${data.mode}&showPerson=true`;
    navigateToDepartment(query, `/pages/module/list?tablename=${info.value.crudInfo.table_name_en}`);
  };

  // 获取管理范围数据
  const getFrameList = () => {
    let obj = {
      scope: 1,
    };
    getDictFrameListApi(obj).then((res) => {
      data.frameList = res.data;
    });
  };

  // 省市区回调
  const cityChange = (e, item, index) => {
    let len = e.detail.value;
    let text = [];
    let id = [];
    len.map((item) => {
      text.push(item.text);
      id.push(item.value);
    });
    info.value.seniorSearch[index].text = text[0];
    data.where[item.field_name_en] = id;
    confirmData();
  };
  const cascdeRef = ref(null);
  // 级联复选
  const changeCascde = (e, index) => {
    data.cascadeData = e.data_dict;
    data.rowData = e;
    data.rowIndex = index;
    cascdeRef.value.popupOpen([]);
  };

  // 关闭弹窗
  const ontouchcancel = () => {
    setTimeout(() => {
      data.show = false;
    }, 200);
  };
  // 级联复选回调
  const cascadeSubmit = (e) => {
    data.where[data.rowData.field_name_en] = e.value;
    confirmData();
  };

  const changeCascade = (item) => {
    info.value.seniorSearch[data.rowIndex].text = item.name;
  };

  // 复选
  const changeCheck = (e, index) => {
    data.rowIndex = index;
    data.rowData = e;
    data.show = true;
  };

  // 复选回调
  const multiplePickerChange = (e, val) => {
    data.where[val.field_name_en] = e.value;
    val.text = e.selected;
    confirmData();
    ontouchcancel();
  };

  // 标签选择回调
  const changeItem = (e, name) => {
    data.where[data.rowData.field_name_en] = e;
    info.value.seniorSearch[data.rowIndex].text = name;
    confirmData();
  };

  // 单选
  const pickerChange = (e, val, type) => {
    const selectIndex = e.detail.value;

    if (type == "switch") {
      data.where[val.field_name_en] = data.switchList[selectIndex].value
      val.text = data.switchList[selectIndex].name;
    } else if (type == "one") {
      data.where[val.field_name_en] = data.index_field_list[selectIndex].id
      val.text = data.index_field_list[selectIndex][data.index_field_name];
    } else {
      data.where[val.field_name_en] = val.data_dict[selectIndex].value
      val.text = val.data_dict[selectIndex].name;
    }
    confirmData();
  };

  // 类型选择
  const changeUsers = (e) => {
    const len = e.detail.value;
    data.usersText = data.usersData[len].name;
    data.where.show_search_type = data.usersData[len].id;
    confirmData();
  };

  // 清除日期
  const clickClear = (item, getOneOnOne) => {
    if (getOneOnOne) {
      getOneOnOne.name = "";
      getOneOnOne.id = "";
    }
    item.text = "";
    data.where[item.field_name_en] = "";
    confirmData();
  };

  // 选择时间
  const changeTime = (e) => {
      data.where[data.rowData.field_name_en] = e.time;
  info.value.seniorSearch[data.rowIndex].text =e.timeText; 
    confirmData();
  };
</script>

<style scoped lang="scss">
  .content {
    width: 100%;
    display: flex;
    /* 水平：两端均分，垂直：居中 */
    justify-content: space-between;
    align-items: center;
    padding-top: 22rpx;
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    /* 注意：uni-app 中建议统一用 rpx，避免 px/rpx 混用 */
    font-size: 24rpx !important;
    color: #606266;
    /* 可选：防止子元素溢出 */
    box-sizing: border-box;

    .picker-selector {
      display: flex;
      align-items: center;
      font-family: PingFang SC, PingFang SC;
      font-weight: 400;
      font-size: 24rpx;
      color: #606266;

      .icon-zhankai1 {
        font-size: 20rpx;
        color: #c0c4cc;
      }

      .icon-shenpizhongxin-jujue {
        font-size: 20rpx;
        color: #c0c4cc;
      }
    }
  }

  .search-default-label {
    font-size: 12px;
  }

  .content>* {
    /* 子元素宽度均分，适配任意数量子元素 */
    flex: 1;
    padding: 0 10rpx;
    display: flex;
    justify-content: center;
  }

  .ellipsis {
    font-size: 12px;
    margin-right: 4px;
    text-align: center;
  }
</style>
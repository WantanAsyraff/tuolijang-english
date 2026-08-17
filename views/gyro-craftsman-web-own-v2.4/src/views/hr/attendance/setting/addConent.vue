<!-- 新增考勤设置页面 -->
<template>
<div class="divBox">
  <!-- <el-card :body-style="{ padding: '14px' }" class="station-header">
    <el-row>
      <el-col :span="24">
        <el-page-header :content="type == 'add' ? '新增考勤组页面' : '编辑考勤组页面'">
          <div slot="title" @click="backFn">
            <i class="el-icon-arrow-left"></i>
            返回
          </div>
        </el-page-header>
      </el-col>
    </el-row>
  </el-card> -->

  <el-card class="card-box">
    <el-row>
      <el-col :span="24" class="ml14">
        <div class="back-title">
          <i class="el-icon-arrow-left"></i>
          <span @click="backFn">
            {{ type == 'add' ? $('ui.hrAttendanceSettingAddConentAddAttendanceGroup') : $('ui.hrAttendanceSettingAddConentEditAttendanceGroup') }}
          </span>
        </div>
      </el-col>
    </el-row>
    <!-- 步骤条 -->
    <div class="step">
      <span :class="activeIndex == 1 ? 'active' : ''" class="public">1</span>
      <span :class="activeIndex == 1 ? 'activeText' : ''" class="step-text">{{ $("ui.hrAttendanceSettingAddConentAttendanceInformation") }}</span>
      <span class="line-title" />
      <span :class="activeIndex == 2 ? 'active' : ''" class="public">2</span>
      <span :class="activeIndex == 2 ? 'activeText' : ''" class="step-text">{{ $("ui.hrAttendanceSettingAddConentAttendanceLocations") }}</span>
      <span class="line-title" />
      <span :class="activeIndex == 3 ? 'active' : ''" class="public">3</span>
      <span :class="activeIndex == 3 ? 'activeText' : ''" class="step-text">{{ $("ui.hrAttendanceSettingAddConentAttendanceRules") }}</span>
      <span class="line-title" />
      <span :class="activeIndex == 4 ? 'active' : ''" class="public">4</span>
      <span :class="activeIndex == 4 ? 'activeText' : ''" class="step-text">{{ $("ui.hrAttendanceSettingAddConentScheduleCycle") }}</span>
    </div>
    <!-- <div class="divisionLine" /> -->

    <div class="main">
      <div v-if="activeIndex == 1" class="alert">
        <el-alert
          class="cr-alert"
          :description="$('ui.hrAttendanceSettingAddConentUsingPreviousOrNextAtAnyStepAutomaticallySaves')"
          show-icon
          type="info"
        >
        </el-alert>
      </div>
      <el-form ref="ruleForm" class="demo-ruleForm" label-width="120px" @submit.native.prevent>
        <!-- 第一步 -->
        <template v-if="activeIndex == 1">
          <div class="from-item-title mb15">
            <span>{{ $('setting.info.essentialinformation') }}</span>
          </div>
          <!-- 基本信息 -->
          <div class="form-box">
            <div class="form-item">
              <el-form-item>
                <span slot="label"><span class="color-tab">* </span>{{ $("ui.hrAttendanceSettingAddConentAttendanceGroupName") }}</span>
                <el-input v-model="from.name" clearable :placeholder="$('ui.hrAttendanceSettingAddConentEnterAttendanceGroupName')" size="small" />
              </el-form-item>
            </div>
            <div class="form-item">
              <el-form-item>
                <span slot="label"><span class="color-tab">* </span>{{ $("ui.hrAttendanceSettingAddConentAttendancePersonnel") }}</span>
                <el-radio-group v-model="from.type">
                  <el-radio class="mb14" label="1">{{ $("ui.hrAttendanceSettingAddConentAddByDepartment") }}</el-radio>
                  <span class="tips"
                    >{{ $("ui.hrAttendanceSettingAddConentUseThisWhenEveryoneInADepartmentMustAttend") }}</span
                  >
                  <el-radio label="0">{{ $("ui.hrAttendanceSettingAddConentAddByEmployee") }}</el-radio
                  ><span class="tips"> {{ $("ui.hrAttendanceSettingAddConentUseThisWhenOnlySelectedIndividualsNeedToParticipate") }}</span>
                </el-radio-group>
              </el-form-item>
            </div>
            <div class="form-item">
              <el-form-item>
                <span slot="label"
                  ><span class="color-tab">* </span>{{ $("ui.hrAttendanceSettingAddConentAttendanceRequired") }}{{ from.type == 1 ? $('ui.businessHolidayQueryIndexDepartment') : $('ui.hrAttendanceSettingAddConentPersonnel') }}：</span
                >
                <select-member
                  v-if="from.type == 0"
                  :placeholder="$('ui.hrAttendanceSettingAddConentSelectPersonnelRequiringAttendance')"
                  :value="userList || []"
                  style="width: 100%"
                  @getSelectList="getSelectList($event, 3)"
                ></select-member>
                <select-department
                  v-if="from.type == 1"
                  :placeholder="$('ui.hrAttendanceSettingAddConentSelectDepartmentsRequiringAttendance')"
                  :value="frames || []"
                  style="width: 100%"
                  @changeMastart="changeMastart($event, 3)"
                ></select-department>

                <span v-if="eliminateList.length !== 0" class="tips"
                  >{{ $("ui.hrAttendanceSettingAddConentOfTheSelectedPeople") }}{{ eliminateList.length }}{{ $("ui.hrAttendanceSettingAddConentCouldNotBeAddedBecauseTheyAlreadyBelongTo") }}
                  <span class="color-doc" @click="openJoin">{{ $("ui.hrAttendanceSettingAddConentClickToView") }}</span></span
                >
              </el-form-item>
            </div>
            <div class="form-item">
              <el-form-item>
                <span slot="label">{{ $("ui.hrAttendanceSettingAddConentExcludedAttendanceMembers") }}</span>
                <select-member
                  :placeholder="$('ui.hrAttendanceSettingAddConentPleaseSelectPersonnel')"
                  :value="filterList || []"
                  style="width: 100%"
                  @getSelectList="getSelectList($event, 2)"
                ></select-member>

                <span class="tips">{{ $("ui.hrAttendanceSettingAddConentSelectPeopleExcludedFromThisAttendanceGroupWhoAttend") }}</span>
              </el-form-item>
            </div>

            <div class="form-item">
              <el-form-item>
                <span slot="label"><span class="color-tab">* </span>{{ $("ui.hrAttendanceSettingAddConentAttendanceGroupAdministrators") }}</span>
                <select-member
                  :placeholder="$('ui.hrAttendanceSettingAddConentPleaseSelectPersonnel')"
                  :value="adminList || []"
                  style="width: 100%"
                  @getSelectList="getSelectList($event, 1)"
                ></select-member>

                <span class="tips"
                  >{{ $("ui.hrAttendanceSettingAddConentAttendanceGroupAdministratorsCanEditRulesAndExportOr") }}</span
                >
              </el-form-item>
            </div>
          </div>
          <!-- <div class="line" /> -->

          <!-- 考勤班次 -->
          <div class="from-item-title mb15">
            <span>{{ $("ui.hrAttendanceSettingAddConentAttendanceShift") }}</span>
          </div>
          <div class="form-box">
            <div class="form-item">
              <el-form-item>
                <span slot="label"><span class="color-tab">* </span>{{ $("ui.hrAttendanceSettingAddConentAttendanceShift2") }}</span>
                <div class="select plan-footer-one" @click="openShift">
                  <div class="flex-box">
                    <div
                      v-for="(item, index) in shiftsList"
                      :key="index"
                      :style="{ backgroundColor: item.color }"
                      class="shift-tag"
                    >
                      {{ item.name }}
                      {{ item.times[0].first_day_after == 0 ? $('ui.hrAttendanceSettingAddConentToday') : $('ui.hrAttendanceSettingAddConentNextDay') }} {{ item.times[0].work_hours }} -
                      {{ item.times[0].second_day_after == 0 ? $('ui.hrAttendanceSettingAddConentToday') : $('ui.hrAttendanceSettingAddConentNextDay') }}{{ item.times[0].off_hours }}
                      <span v-if="item.times.length > 1"
                        >、 {{ item.times[1].first_day_after == 0 ? $('ui.hrAttendanceSettingAddConentToday') : $('ui.hrAttendanceSettingAddConentNextDay') }}
                        {{ item.times[1].work_hours }} - {{ item.times[1].second_day_after == 0 ? $('ui.hrAttendanceSettingAddConentToday') : $('ui.hrAttendanceSettingAddConentNextDay')
                        }}{{ item.times[1].off_hours }}
                      </span>
                    </div>
                  </div>
                </div>
                <span class="tips">{{ $("ui.hrAttendanceSettingAddConentWhenNoScheduleIsAssignedEmployeesMaySelectA") }}</span>
              </el-form-item>
            </div>
          </div>
        </template>

        <!-- 第二步 -->
        <template v-if="activeIndex == 2">
          <div class="from-item-title mb10 mt14">
            <span>{{ $("ui.hrAttendanceSettingAddConentSelectAttendanceMethod") }}</span>
          </div>
          <div class="form-box">
            <div class="form-item">
              <el-form-item label-width="100px">
                <span slot="label">{{ $("ui.hrAttendanceSettingAddConentAttendanceMethod") }}</span>
                <el-checkbox v-model="from.is_map">{{ $("ui.hrAttendanceSettingAddConentGeographicArea") }}</el-checkbox>
                <el-checkbox v-model="from.is_wifi"> {{ $("ui.hrAttendanceSettingAddConentWiFiInformation") }} </el-checkbox>
                <el-tooltip
                  class="item"
                  effect="dark"
                  :content="$('ui.hrAttendanceSettingAddConentWiFiClockInIsAvailableOnlyInThe')"
                  placement="top"
                >
                  <i class="el-icon-info"></i>
                </el-tooltip>
              </el-form-item>
              <el-form-item label-width="100px" v-if="from.is_wifi">
                <span slot="label">{{ $("ui.hrAttendanceSettingAddConentWiFiInformation2") }}</span>
                <div class="wifi-info-list">
                  <div v-for="(item, index) in from.wifi_data" :key="index">
                    <el-input v-model="item.name" size="small" style="width: 180px" :placeholder="$('ui.hrAttendanceSettingAddConentPleaseEnterWifiName')" />
                    <el-input
                      v-model="item.mac"
                      size="small"
                      style="width: 180px"
                      :placeholder="$('ui.hrAttendanceSettingAddConentPleaseEnterRouterMacAddress')"
                    />
                    <el-button size="small" type="danger" @click="deleteWifiFn(index)">{{ $("ui.chatIndexDelete") }}</el-button>
                  </div>
                  <el-button size="mini" type="primary" @click="addWifiFn">{{ $("ui.hrAttendanceSettingAddConentAddWiFiInformation") }}</el-button>
                </div>
              </el-form-item>
            </div>
          </div>
          <div class="from-item-title mb10 mt14" v-if="from.is_map">
            <span>{{ $("ui.hrAttendanceSettingAddConentSelectAttendanceArea") }}</span>
          </div>
          <div class="mapBox" v-if="from.is_map">
            <div class="map">
              <mapContainer :positionInfo="from" @select="selectMap"></mapContainer>
            </div>
            <div class="map pl60">
              <el-form class="invoice-body" label-width="110px" @submit.native.prevent>
                <div class="form-item">
                  <el-form-item>
                    <span slot="label">{{ $("ui.hrAttendanceSettingAddConentAttendanceAddress") }}</span>
                    <p>{{ from.address }}</p>
                  </el-form-item>
                </div>
                <div class="form-item">
                  <el-form-item>
                    <span slot="label">{{ $("ui.hrAttendanceSettingAddConentCoordinates") }}</span>
                    <p>{{ from.lng }} &emsp;{{ from.lat }}</p>
                  </el-form-item>
                </div>
                <div class="form-item">
                  <el-form-item>
                    <span slot="label">{{ $("ui.hrAttendanceSettingAddConentValidRadius") }}</span>
                    <el-input id="tipinput" v-model="from.effective_range" size="small" style="width: 100px">
                      <i slot="suffix" style="font-style: normal; margin-right: 10px">{{ $("ui.hrAttendanceSettingAddConentMeters") }}</i></el-input
                    >
                    <!-- <p>{{ from.effective_range }}米</p> -->
                  </el-form-item>
                </div>
              </el-form>
              <!-- <div class="line mt20" /> -->
              <div class="from-item-title mb15 mt14">
                <span>{{ $("ui.hrAttendanceSettingAddConentSetLocationName") }}</span>
              </div>
              <el-form-item>
                <span slot="label"><span class="color-tab">* </span>{{ $("ui.hrAttendanceSettingAddConentAttendanceLocationsName") }}</span>
                <el-input
                  v-model="from.location_name"
                  clearable
                  :placeholder="$('ui.hrAttendanceSettingAddConentPleaseEnterLocation')"
                  size="small"
                  style="width: 350px"
                />
                <div>
                  <span class="tips">{{ $("ui.hrAttendanceSettingAddConentThisNameAppearsInTheMobileClockInScreen") }}</span>
                </div>
              </el-form-item>
            </div>
          </div>
        </template>

        <!-- 第三步 -->
        <template v-if="activeIndex == 3">
          <div class="from-item-title mb15">
            <span>{{ $("ui.hrAttendanceSettingAddConentClockCorrection") }}</span>
          </div>
          <el-checkbox v-model="from.repair_allowed">{{ $("ui.hrAttendanceSettingAddConentAllowClockCorrections") }}</el-checkbox>
          <div v-if="from.repair_allowed" class="form-box">
            <div class="form-item">
              <el-form-item label-width="100px">
                <span slot="label">{{ $("ui.hrAttendanceSettingAddConentCorrectionType") }}</span>
                <el-checkbox-group v-model="from.repair_type">
                  <el-checkbox label="1">{{ $("ui.hrAttendanceSettingAddConentMissingClockIn") }}</el-checkbox>
                  <el-checkbox label="2">{{ $("ui.hrAttendanceSettingAddConentLate") }}</el-checkbox>
                  <el-checkbox label="3">{{ $("ui.hrAttendanceSettingAddConentSeverelyLate") }}</el-checkbox>
                  <el-checkbox label="4">{{ $("ui.hrAttendanceSettingAddConentEarlyLeave") }}</el-checkbox>
                  <el-checkbox label="5">{{ $("ui.hrAttendanceSettingAddConentLocationException") }}</el-checkbox>
                </el-checkbox-group>
              </el-form-item>
              <el-form-item label-width="100px">
                <span slot="label">{{ $("ui.hrAttendanceSettingAddConentCorrectionPeriod") }}</span>
                <el-checkbox v-model="from.is_limit_time">{{ $("ui.hrAttendanceSettingAddConentLimitCorrectionPeriod") }}</el-checkbox>
                <div v-if="from.is_limit_time" class="fz12">
                  {{ $("ui.hrAttendanceSettingAddConentCorrectionsCanBeRequestedForThePast") }}
                  <el-input-number
                    v-model="from.limit_time"
                    :max="365"
                    :min="0"
                    controls-position="right"
                    style="width: 120px"
                  ></el-input-number>
                  {{ $("ui.hrAttendanceSettingAddConentDaysEnter0ToAllowCorrectionsOnlyForThe") }}
                </div>
              </el-form-item>
              <el-form-item label-width="100px">
                <span slot="label">{{ $("ui.hrAttendanceSettingAddConentCorrectionCount") }}</span>
                <el-checkbox v-model="from.is_limit_number">{{ $("ui.hrAttendanceSettingAddConentLimitCorrectionCount") }}</el-checkbox>
                <div v-if="from.is_limit_number" class="fz12">
                  {{ $("ui.hrAttendanceSettingAddConentMonthlyCorrectionLimitPerPerson") }}
                  <el-input-number
                    v-model="from.limit_number"
                    :max="30"
                    :min="0"
                    controls-position="right"
                    style="width: 120px"
                  ></el-input-number>
                  {{ $("ui.hrAttendanceSettingAddConentSecond") }}
                </div>
              </el-form-item>
            </div>
          </div>
          <!-- <div class="line mt20" /> -->

          <!-- 拍照 -->
          <div class="from-item-title mb15">
            <span>{{ $("ui.hrAttendanceSettingAddConentPhotoRequiredForClockIn") }}</span>
          </div>
          <el-checkbox v-model="from.is_photo">{{ $("ui.hrAttendanceSettingAddConentPhotoRequiredForClockIn") }}</el-checkbox>
          <div class="pl24 mb20"><span class="tips">{{ $("ui.hrAttendanceSettingAddConentWhenEnabledEmployeesMustTakeAPhotoWhenClocking") }}</span></div>
          <!-- <div class="line" /> -->

          <!-- 外勤打卡 -->
          <div class="from-item-title mb15">
            <span>{{ $("ui.hrAttendanceSettingAddConentOffSiteClockIn") }}</span>
          </div>
          <el-checkbox v-model="from.is_external">{{ $("ui.hrAttendanceSettingAddConentAllowOffSiteClockIn") }}</el-checkbox>
          <div class="pl24 mb20">
            <span class="tips">{{ $("ui.hrAttendanceSettingAddConentWhenEnabledEmployeesMayClockInOffSiteOutside") }}</span>
          </div>
          <div v-if="from.is_external" class="mb20 pl24">
            <span
              >{{ $("ui.hrAttendanceSettingAddConentOffSiteClockInRemarksAreRequired") }}<el-switch v-model="from.is_external_note" active-text="开启" inactive-text="关闭">
              </el-switch
            ></span>
          </div>
          <div v-if="from.is_external" class="pl24">
            <span
              >{{ $("ui.hrAttendanceSettingAddConentPhotosAreRequiredForOffSiteClockIns") }}<el-switch v-model="from.is_external_photo" active-text="开启" inactive-text="关闭">
              </el-switch
            ></span>
          </div>
        </template>

        <!-- 第四步 -->
        <template v-if="activeIndex == 4">
          <div class="from-item-title mb15">
            <span>{{ $("ui.hrAttendanceSettingAddConentScheduleCycle") }}</span>
            <div class="tips">{{ $("ui.hrAttendanceSettingAddConentScheduleCyclesApplyToRecurringPatternsSuchAsOne") }}</div>
          </div>
          <div class="add-row" @click="addCycleFn"><span class="el-icon-plus"></span> {{ $("ui.hrAttendanceSettingAddCycleAddScheduleCycle") }}</div>
          <el-table :data="tableData" class="mb20" style="width: 100%">
            <el-table-column :label="$('ui.hrAttendanceSettingAddConentCycleName')" prop="name" width="180"> </el-table-column>
            <el-table-column :label="$('ui.hrAttendanceSettingAddConentCycleShifts')" prop="shifts" width="180">
              <template #default="{ row }">
                <span> {{ row.shifts.map((obj) => obj.name).join('、') }}</span>
              </template>
            </el-table-column>
            <el-table-column :label="$('ui.hrAttendanceSettingAddConentCycleDays')" prop="cycle"> </el-table-column>
            <el-table-column fixed="right" :label="$('ui.formDesignerFormWidgetContainerWidgetDetailsItemOperation')" width="180">
              <template slot-scope="scope">
                <el-button type="text" @click="editFn(scope.row)">{{ $("ui.formCommonOaLogEdit") }}</el-button>
                <el-button type="text" @click="deleteFn(scope.row)">{{ $("ui.chatIndexDelete") }}</el-button>
              </template>
            </el-table-column>
          </el-table>
        </template>
      </el-form>
    </div>
  </el-card>

  <!-- 底部按钮 -->
  <div class="cr-bottom-button">
    <el-button v-if="activeIndex !== 1" size="small" @click="activeIndex = activeIndex - 1">{{ $("ui.invoiceMergeInvoicePrevious") }}</el-button>
    <el-button v-if="activeIndex !== 4" size="small" type="primary" @click="nextStep()">{{ $("ui.invoiceMergeInvoiceNext") }}</el-button>
    <el-button v-if="activeIndex == 4" size="small" type="primary" @click="submit()">{{ $("ui.formDesignerFormWidgetFieldWidgetRichTextWidgetSave") }}</el-button>
  </div>

  <!-- 新增排班周期 -->
  <add-cycle ref="addCycle" @cycleList="getCycleList"></add-cycle>
  <!-- 选择班次弹窗 -->
  <shift-list ref="shiftList" @selected="selected"></shift-list>
  <!-- 打开未参与考勤组人员弹窗 -->
  <not-join ref="notJoin" @otherFiltersFn="otherFiltersFn"></not-join>
</div>
</template>
<script>
import { $ } from '@/lang'
import { roterPre } from '@/settings'
import { mapGetters } from 'vuex'
import {
  repeatCheckApi,
  saveAttendanceGroup,
  rosterCycleListApi,
  deleteGroupListApi,
  attendanceGroupDetailsApi,
  putAttendanceGroup,
  repeatCheckMemberApi
} from '@/api/config'
export default {
  name: 'newAttendance',
  components: {
    mapContainer: () => import('@/components/common/mapContainer'),
    addCycle: () => import('./components/addCycle'),
    selectDepartment: () => import('@/components/form-common/select-department'),
    selectMember: () => import('@/components/form-common/select-member'),
    shiftList: () => import('./components/shiftList'),
    notJoin: () => import('./components/notJoin')
  },
  computed: {
    ...mapGetters(['userInfo'])
  },
  data() {
    return {
      disabledList: [],
      type: 'add',
      from: {
        name: '',
        type: '1',
        members: [], // 考勤人员
        shifts: [], // 考勤班次
        admins: [], // 考勤管理员
        other_filters: [], // 其他考勤组排除人员
        filters: [], // 考勤组排除人员
        step: 'one',
        lng: 108.781332,
        lat: 34.333884,
        effective_range: 1000,
        address: '陕西省咸阳市秦都区上林街道西咸大厦',
        location_name: '西咸大厦',
        repair_allowed: true, //允许补卡
        repair_type: ['1', '2', '3', '4', '5'], // 补卡类型
        is_limit_time: true, // 限制补卡时间
        limit_time: 30,
        is_limit_number: true,
        limit_number: 3,
        is_map: false,
        is_photo: true,
        is_wifi: false,
        is_external: true,
        is_external_note: true,
        is_external_photo: true,
        wifi_data: []
      },
      shiftsList: [], // 选中班次列表
      userList: [], // 考勤组成员
      adminList: [], // 选择管理员
      filterList: [], // 排除管理员
      frames: [], // 考勤组部门
      listObj: [],
      eliminateList: [], // 获取未参加考勤组人员
      val: '',
      id: 0,
      activeIndex: 1,
      checkList: ['缺卡', ''],
      tableData: [],
      selectedFn: []
    }
  },
  created() {
    if (this.$route.query.id) {
      this.type = 'edit'
      this.id = this.$route.query.id
      this.getDateils()
    } else {
      // this.repeatCheckMember()
      this.type = 'add'
      let data = []
      data.push(this.userInfo.card)
      if (data.length > 0) {
        data.map((item) => {
          let val = {
            name: item.name,
            avatar: item.avatar,
            id: item.id
          }
          this.adminList.push(val)
        })
      }
    }
  },

  methods: {
    deleteWifiFn(index) {
      this.from.wifi_data.splice(index, 1)
    },
    addWifiFn() {
      this.from.wifi_data.push({
        name: '',
        mac: ''
      })
    },
    otherFiltersFn(data) {
      this.from.other_filters = data
    },

    // 获取部门人员禁用名单
    async repeatCheckMember() {
      let data = {
        type: this.from.type,
        filter_id: this.id
      }
      const result = await repeatCheckMemberApi(data)
      this.disabledList = result.data
    },
    // 获取详情
    getDateils() {
      attendanceGroupDetailsApi(this.id).then((res) => {
        this.from.name = res.data.name
        this.from.type = res.data.type + ''
        this.repeatCheckMember()
        if (res.data.admins) {
          res.data.admins.map((item) => {
            let val = {
              name: item.name,
              avatar: item.avatar,
              value: item.id
            }
            this.adminList.push(val)
          })
        }

        if (this.from.type == 0) {
          res.data.members.map((item) => {
            let val = {
              name: item.name,
              avatar: item.avatar,
              value: item.id
            }
            this.userList.push(val)
          })
        } else {
          res.data.members.map((item) => {
            let val = {
              name: item.name,
              avatar: item.avatar,
              id: item.id
            }
            this.frames.push(val)
          })
        }
        if (res.data.filters) {
          res.data.filters.map((item) => {
            let val = {
              name: item.name,
              avatar: item.avatar,
              value: item.id
            }
            this.filterList.push(val)
          })
        }
        this.from.lng = res.data.lng
        this.from.lat = res.data.lat
        this.from.effective_range = res.data.effective_range
        this.from.address = res.data.address
        this.from.location_name = res.data.location_name
        this.shiftsList = res.data.shifts
        this.from.is_external = res.data.is_external == 1 ? true : false
        this.from.is_external_note = res.data.is_external_note == 1 ? true : false
        this.from.is_external_photo = res.data.is_external_photo == 1 ? true : false
        this.from.is_limit_number = res.data.is_limit_number == 1 ? true : false
        this.from.is_limit_time = res.data.is_limit_time == 1 ? true : false
        this.from.is_photo = res.data.is_photo == 1 ? true : false
        this.from.is_map = res.data.is_map == 1 ? true : false
        this.from.is_wifi = res.data.is_wifi == 1 ? true : false
        this.from.limit_number = res.data.limit_number
        this.from.limit_time = res.data.limit_time
        this.from.wifi_data = res.data.wifi
        this.from.repair_allowed = res.data.repair_allowed == 1 ? true : false
        let members = []
        members = res.data.members.map((item) => item.id)
        repeatCheckApi({
          members: members,
          type: res.data.type,
          id: this.id ? this.id : 0
        }).then((res) => {
          this.eliminateList = res.data
        })
        this.arrToObj()
      })
    },
    // 格式化
    formatFn() {
      this.from.is_external = this.from.is_external == 1 ? true : false
      this.from.is_external_note = this.from.is_external_note == 1 ? true : false
      this.from.is_external_photo = this.from.is_external_photo == 1 ? true : false
      this.from.is_limit_number = this.from.is_limit_number == 1 ? true : false
      this.from.is_limit_time = this.from.is_limit_time == 1 ? true : false
      this.from.is_photo = this.from.is_photo == 1 ? true : false
      this.from.repair_allowed = this.from.repair_allowed == 1 ? true : false
    },
    // 返回
    backFn() {
      this.$router.push({
        path: `${roterPre}/hr/attendance/setting/team`
      })
    },
    // 选中考勤班次
    selected(data) {
      this.shiftsList = data
    },
    // 选中地图
    selectMap(data) {
      this.from.address = data.address
      // this.from.effective_range = data.effective_range
      this.from.lat = data.lat
      this.from.lng = data.lng
    },
    nextStep() {
      if (this.activeIndex == 1) {
        if (this.frames.length !== 0 && this.from.type == '1') {
          this.from.members = this.frames.map((item) => item.id)
        }
        if (this.userList.length !== 0 && this.from.type == '0') {
          this.from.members = this.userList.map((item) => item.value)
        }
        if (this.adminList.length !== 0) {
          this.from.admins = this.adminList.map((item) => item.value)
        }
        if (this.shiftsList.length !== 0) {
          this.from.shifts = this.shiftsList.map((item) => item.id)
        }
        let set = new Set(this.from.shifts)
        this.from.shifts = Array.from(set)
        if (!this.from.name) return this.$message.error($('ui.hrAttendanceSettingAddConentEnterAttendanceGroupName'))
        if (this.from.members.length == 0) return this.$message.error($('legacyScript.pleaseSelectAnEmployeeOrDepartment'))
        if (this.from.admins.length == 0) return this.$message.error($('legacyScript.pleaseSelectAttendanceGroupManagementPersonnel'))
        this.filterList.map((item) => {
          this.from.filters.push(item.value)
        })
        this.from.step = 'one'
        this.saveFn(this.from)
      } else if (this.activeIndex == 2) {
        if (!this.from.is_map && !this.from.is_wifi) {
          return this.$message.error($('legacyScript.pleaseSelectAtLeastOneAttendanceMethod'))
        }
        this.from.step = 'two'
        this.saveFn(this.from)
      } else if (this.activeIndex == 3) {
        this.from.is_photo = this.from.is_photo ? 1 : 0
        this.from.is_external = this.from.is_external ? 1 : 0
        this.from.repair_allowed = this.from.repair_allowed ? 1 : 0
        this.from.is_limit_time = this.from.is_limit_time ? 1 : 0
        this.from.is_limit_number = this.from.is_limit_number ? 1 : 0
        this.from.is_external_note = this.from.is_external_note ? 1 : 0
        this.from.is_external_photo = this.from.is_external_photo ? 1 : 0
        this.from.step = 'three'
        this.saveFn(this.from)
        this.getCycleList()
        this.formatFn()
      }
    },

    // 保存接口
    async saveFn(data) {
      const updateCycleList = () => {
        this.$refs.addCycle.getList()
      }

      if (this.activeIndex === 1 && this.type === 'add') {
        const res = await saveAttendanceGroup(data)
        if (res.status === 200) {
          this.id = res.data.id
          this.type = 'edit'
          this.activeIndex = this.activeIndex + 1
          this.$route.query.id = this.id
          updateCycleList()
        }
      } else {
        let wifi_data = []
        if (data.is_wifi) {
          wifi_data = data.wifi_data.filter((item) => item.name && item.mac)
          if (wifi_data.length <= 0) {
            return this.$message.error($('legacyScript.pleaseEnterAtLeastOneWiFiConfiguration'))
          }
        }
        const res = await putAttendanceGroup(this.id, data)
        if (wifi_data.length > 0) {
          data.wifi_data = wifi_data
        }
        if (res.status === 200) {
          this.activeIndex === 1 && updateCycleList()
          this.activeIndex = this.activeIndex + 1
        }
      }
    },

    // 打开考勤班次弹窗
    openShift() {
      this.$refs.shiftList.openBox(this.shiftsList)
    },

    // 打开排班周期弹窗
    addCycleFn() {
      this.$refs.addCycle.openBox(this.id, 'add')
    },

    // 获取排班周期列表
    getCycleList() {
      rosterCycleListApi(this.id).then((res) => {
        if (res.status == 200) {
          this.tableData = res.data
        } else {
          this.tableData = []
        }
      })
    },

    // 编辑排班周期
    editFn(data) {
      this.$refs.addCycle.openBox(data, 'edit')
    },

    deleteFn(val) {
      this.$modalSure('你确定要删除这条数据吗').then(() => {
        deleteGroupListApi(val.id).then((res) => {
          this.getCycleList()
        })
      })
    },

    // 打开未参与考勤组弹窗
    openJoin() {
      // let data = {
      //   type: this.from.type,
      //   filter_id: this.id
      // }
      this.$refs.notJoin.openBox(this.eliminateList)
    },

    // 保存
    submit() {
      this.backFn()
    },

    // 选择部门完成回调
    changeMastart(data) {
      let fromData = {}
      let member = []
      this.frames = data
      fromData.type = this.from.type
      this.frames.map((item) => {
        member.push(item.id)
      })
      fromData.members = member
      fromData.id = this.id ? this.id : 0
      repeatCheckApi(fromData).then((res) => {
        this.eliminateList = res.data
      })
    },

    // 选择成员回调
    getSelectList(data, type) {
      let fromData = {}
      let member = []
      if (type === 3) {
        this.userList = data
        this.userList.map((item) => {
          member.push(item.value)
        })
        fromData.type = this.from.type
        fromData.members = member
        fromData.id = this.id ? this.id : 0
        repeatCheckApi(fromData).then((res) => {
          this.eliminateList = res.data
        })
      } else if (type === 1) {
        this.adminList = data
      } else if (type === 2) {
        this.filterList = data
        this.filterList.map((item) => {
          this.from.filters.push(item.value)
        })
      }
    },

    handleActiveDepartment() {
      if (this.isEdit == 0) {
        return this.activeDepartment
      } else if (this.isEdit == 1) {
        return this.activeMastart
      } else {
        return {}
      }
    },
    close() {
      this.activeDepartment = null
      this.openStatus = false
    },

    // 数组转对象
    arrToObj() {
      const obj = {}
      this.frames.forEach((el, index) => {
        obj[el.id] = el
      })
      this.activeDepartment = obj
    }
  }
}
</script>
<style lang="scss" scoped>
.divBox {
  position: relative;

  height: 100%;
}
.cr-bottom-button {
  position: absolute;
  left: 0px;
  right: 0;
  bottom: -56px;
  width: 100%;
  border-radius: 0 0 8px 8px;
}
.pl60 {
  padding-top: 20px;
}
.color-ppt {
  color: #ff9900 !important;
}
.line {
  width: 102% !important;
}
.color-doc {
  cursor: pointer;
  color: #1890ff !important;
}
.fz13 {
  font-size: 12px;
  color: #909399;
}
::v-deep .el-radio {
  margin-right: 10px;
}
::v-deep .el-radio-group {
  // margin-top: 10px;
}

.invoice-body {
  ::v-deep .el-form-item {
    margin-bottom: 0;
  }
  ::v-deep .el-form-item__label {
    font-size: 12px !important;
    font-weight: 400;
    color: #909399 !important;
  }
  p {
    margin: 0;
    padding: 0;
    font-weight: 400 !important;
    color: #303133;
    font-size: 12px !important;
    margin-top: 10px;
    line-height: 18px;
  }
}
.card-box {
  font-size: 13px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  color: #303133;
  .main {
    height: calc(100vh - 239px);
    overflow-y: auto;
    scrollbar-width: none; /* firefox */
    -ms-overflow-style: none; /* IE 10+ */
    width: 800px;
    margin: 0 auto;
  }
}
.pl24 {
  margin-top: 6px;
  padding-left: 24px;
}
.header {
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  .add-row {
    width: 80px;
    height: 32px;
    text-align: center;
    line-height: 32px;
    border: 1px solid #1890ff;
    font-size: 12px;
    font-family: PingFang SC-Medium, PingFang SC;
    font-weight: 500;
    color: #1890ff;
    border-radius: 4px;
  }
  .title {
    font-size: 20px;
    font-weight: 700;
    background-color: #1890ff;
    font-family: PingFangSC-Semibold, PingFang SC;

    ::v-deep .el-input--medium {
      font-size: 20px;

      .el-input__inner {
        border: none;
      }
    }
  }
}

.el-icon-info {
  color: #909399;
}
.add-row {
  cursor: pointer;
  height: 32px;
  line-height: 32px;
  font-size: 12px;
  font-family: PingFang SC-Medium, PingFang SC;
  font-weight: 500;
  color: #1890ff;
  border-radius: 4px;
  margin-bottom: 10px;
}

::v-deep .el-icon-back {
  display: none;
}
.form-box {
  .form-item {
    width: 100%;
    margin-bottom: 20px;
    ::v-deep .el-form-item {
      margin-bottom: 0;
    }
    ::v-deep .el-checkbox__input.is-checked + .el-checkbox__label {
      color: #303133;
    }
    ::v-deep .el-form-item__label {
      font-size: 13px;
      font-family: PingFangSC-Regular, PingFang SC;
      font-weight: 400;
      color: #303133;
    }

    ::v-deep .el-input--small .el-input__inner {
      font-size: 12px;
    }
    ::v-deep .el-form-item--medium .el-radio {
      line-height: 0;
    }

    ::v-deep .el-form-item__content {
      width: calc(100% - 110px);
    }
    ::v-deep .el-select {
      width: 100%;
    }
    ::v-deep .el-textarea__inner {
      resize: none;
    }
  }
}
::v-deep .el-checkbox__label {
  font-size: 13px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  color: #303133;
}
.line {
  width: 110%;
  height: 1px;
  border-bottom: 1px dashed #dcdfe6;
  margin-bottom: 30px;
}
.divisionLine {
  width: 100%;
  height: 1px;
  border-bottom: 1px solid #dcdfe6;
  margin-top: 25px;
  margin-bottom: 30px;
}
.from-item-title {
  border-left: 3px solid #1890ff;
  span {
    padding-left: 10px;
    font-weight: bold;
    font-size: 14px;
  }
}
.tips {
  display: inline;
  font-size: 12px;
  color: #909399;
  font-size: 400 !important;
}
.fz12 {
  font-size: 12px;
}
.mapBox {
  height: 435px;
  padding: 20px;
  border-radius: 4px;

  .map {
    height: 400px;
  }
  .footer {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
  }
}

.active {
  background: #0091ff;
  border: none;
  color: #ffffff !important;
}
.activeText {
  font-size: 15px;
  font-weight: 500;
  color: #303133;
}
.line-title {
  display: inline-block;
  width: 120px;
  height: 4px;
  border-bottom: 1px solid #e9e9e9;
}
.step-text {
  margin-left: 8px;
  margin-right: 8px;
  font-size: 16px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  cursor: default;
  color: #909399;
}
.step {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 15px;
  margin-top: 20px;
}
.public {
  display: inline-block;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  text-align: center;
  line-height: 24px;
  font-size: 15px;
  font-family: PingFangSC-Semibold, PingFang SC;
  font-weight: 600;
  border-radius: 50%;
  border: 1px solid #dcdfe6;
  color: #c0c4cc;
  margin-left: 8px;
  // cursor: pointer;
}
::v-deep .el-card__body {
  padding: 20px 0;
}
.alert {
  width: 100%;
  padding: 20px 0;
  padding-top: 0;
}
::v-deep .el-alert {
  padding-left: 30px;
  border: 1px solid #1890ff;
  color: #1890ff;
  font-size: 13px;
  background-color: #edf7ff;
  line-height: 1;
  margin-bottom: 10px;
}
::v-deep .el-alert--info .el-alert__description {
  color: #303133;
  font-size: 13px;
  font-weight: 500;
}
::v-deep .el-alert__icon.is-big {
  font-size: 16px;
  width: 15px;
}

.back-title {
  display: flex;
  align-items: center;
  font-family: PingFang SC, PingFang SC;
  font-weight: 500;
  font-size: 18px;
  color: #303133;
  i {
    margin-right: 3px;
  }
}
.plan-footer-one {
  cursor: pointer;
  background-color: #fff;
  border-radius: 4px;
  border: 1px solid #dcdfe6;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  color: #606266;
  display: inline-block;
  min-height: 32px;
  line-height: 32px;
  outline: none;
  padding: 0 15px;
  transition: border-color 0.2s cubic-bezier(0.645, 0.045, 0.355, 1);
  width: 100%;
}

.flex-box {
  display: flex;
  flex-wrap: wrap;

  .shift-tag {
    margin: 4px;
    padding: 0 10px;
    font-size: 12px;
    height: 28px;
    color: #fff;
    border-radius: 4px;
    background-color: pink;
    line-height: 28px;
  }

  span {
    margin: 4px;
    margin-right: 6px;
  }
  span:last-of-type {
    margin-left: 0;
  }
}
</style>

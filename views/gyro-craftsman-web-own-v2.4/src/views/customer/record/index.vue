<template>
<div class="divBox">
  <el-card :body-style="{ padding: '20px 20px 20px 20px' }" class="normal-page el-card-flex">
    <oaFromBox
      v-if="search.length > 0"
      :search="search"
      :title="$route.meta.title"
      :total="total"
      :isCategory="false"
      :treeData="treeData"
      :isViewSearch="false"
      :category="`record`"
      :isAddBtn="false"
      @confirmData="confirmData"
    ></oaFromBox>

    <div class="flex-layout-table">
      <div class="mt10 table-box">
        <div class="table-wrapper">
          <div class="table-content">
            <el-table :data="tableData" style="width: 100%" height="100%">
              <el-table-column prop="title.name" :label="$('ui.customerRecordIndexCustomerLead')" width="220" show-overflow-tooltip>
                <template slot-scope="scope">
                  <div class="point" @click="openDetails(scope.row)">
                    {{ scope.row.title.eid ? scope.row.title.customer.name : scope.row.title.name }}
                  </div>
                </template>
              </el-table-column>
              <el-table-column prop="name" :label="$('ui.customerRecordIndexOpportunityName')" width="220" show-overflow-tooltip>
                <template #default="{ row }">
                  <div :class="!row.title.eid || 'point'" @click="row.title.eid && openOdds(row)">
                    {{ row.title.eid ? row.title.name || '--' : '--' }}
                  </div>
                </template>
              </el-table-column>
              <el-table-column prop="content" :label="$('ui.customerRecordIndexFollowUpContent')" show-overflow-tooltip>
                <template slot-scope="scope">
                  <div class="over-text3">{{ scope.row.content }}</div>
                </template>
              </el-table-column>
              <el-table-column :label="$('ui.customerRecordIndexCreator')" width="180" show-overflow-tooltip>
                <template #default="{ row }">
                  <div class="flex items-center">
                    <img
                      :src="row.card.avatar"
                      :alt="row.card.name"
                      style="width: 24px; height: 24px; border-radius: 50%; margin-right: 7px; vertical-align: bottom"
                    />
                    {{ row.card.name || '--' }}
                  </div>
                </template>
              </el-table-column>
              <el-table-column prop="created_at" :label="$('ui.customerRecordIndexFollowUpTime')" width="220"> </el-table-column>
              <el-table-column prop="address" :label="$('ui.formDesignerFormWidgetContainerWidgetDetailsItemOperation')" fixed="right" width="150">
                <template slot-scope="scope">
                  <el-button type="text" size="mini" @click="handleEdit(scope.row)">{{ $("ui.formCommonOaLogEdit") }}</el-button>
                  <el-button type="text" size="mini" @click="handleDelete(scope.row)">{{ $("ui.chatIndexDelete") }}</el-button>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </div>
        <div class="page-fixed">
          <el-pagination
            :current-page="where.page"
            :page-size="where.limit"
            :page-sizes="[15, 20, 30]"
            :total="total"
            layout="total, sizes,prev, pager, next, jumper"
            @size-change="handleSizeChange"
            @current-change="pageChange"
          />
        </div>
      </div>
    </div>
  </el-card>

  <!-- 客户详情 -->
  <edit-customer ref="editCustomer" :form-data="fromData" @isOkEdit="getTableData()"></edit-customer>
  <!-- 线索商机详情 -->
  <detailsDrawer ref="details" :formData="detailsFromData"></detailsDrawer>
  <!-- 跟进弹窗 -->
  <el-dialog :visible.sync="dialogShow" class="record" :title="$('ui.customerRecordIndexEditFollowUpRecord')" width="40%">
    <recordUpload :form-info="formInfo" @change="recordChange"></recordUpload>
  </el-dialog>
</div>
</template>
<script>
import { $ } from '@/lang'
import { getClientFollowApi, delClientFollowApi } from '@/api/client'
import { divTime } from '@/utils'
export default {
  name: 'RecordList',
  components: {
    detailsDrawer: () => import('../components/details'),
    oaFromBox: () => import('@/components/common/oaFromBox'),

    editCustomer: () => import('@/views/customer/list/components/editCustomer'),
    recordUpload: () => import('@/views/customer/list/components/recordUpload')
  },

  data() {
    return {
      treeData: [
        {
          label: $('legacyScript.ownedByMe'),
          id: 1
        },
        {
          label: $('legacyScript.ownedBySubordinates'),
          id: 2
        }
      ],
      fromData: {},
      detailsFromData: {},
      search: [
        {
          field_name: '客户/线索/跟进',
          field_name_en: 'name',
          form_value: 'input'
        },
        {
          field_name: '跟进时间',
          field_name_en: 'time',
          form_value: 'date_picker'
        }
      ],
      formInfo: {
        avatar: '',
        type: 'edit',
        show: 1,
        data: {},
        follow_id: 0
      },
      dialogShow: false,
      tableData: [],
      where: {
        page: 1,
        limit: 15
      },
      total: 0,
      loading: false
    }
  },

  mounted() {
    this.getTableData()
  },
  methods: {
    async getTableData() {
      this.loading = true
      const res = await getClientFollowApi(this.where)
      this.tableData = res.data.list
      this.total = res.data.count
      this.loading = false
    },
    openOdds(item) {
      this.detailsFromData = {
        title: $('legacyScript.viewOpportunity'),
        width: '1000px',
        data: item,
        eid: item.title.id,
        types: this.keyword,
        link_type: 'odds',
        odds_id: item.title.id
      }

      this.$refs.details.openBox(item.title.id, 'odds')
    },
    openDetails(item) {
      if (item.link_type === 'clue') {
        this.detailsFromData = {
          title: $('legacyScript.viewLeads'),
          width: '1000px',
          data: item,
          types: 'clue',
          link_type: 'clue'
        }
        this.fromData.data.id = item.eid

        this.$refs.details.openBox(item.eid, 'clue')
      } else if (item.link_type === 'customer') {
        this.fromData = {
          title: this.$('customer.editcustomer'),
          width: '1100px',
          data: item,
          link_type: 'customer',
          types: 'customer'
        }
        this.fromData.data.id = item.eid

        this.$refs.editCustomer.openBox(item.eid, 'customer')
      } else if (item.link_type === 'odds') {
        item.eid = item.title.eid
        this.fromData = {
          title: this.$('customer.editcustomer'),
          width: '1100px',
          data: item,
          link_type: 'customer',
          types: 'customer'
        }
        this.fromData.data.id = item.title.customer.id

        this.$refs.editCustomer.openBox(item.title.customer.id, 'customer')
      }
    },

    confirmData(data) {
      if (data == 'reset') {
        this.where = {
          page: 1,
          limit: 15,
          view_search: this.where.view_search
        }

        this.getTableData()
      } else {
        this.where.page = 1
        for (let key in data) {
          this.where[key] = data[key]
        }

        setTimeout(() => {
          this.getTableData()
        }, 100)
      }
    },
    recordChange() {
      this.dialogShow = false
      this.getTableData()
    },
    handleSizeChange(val) {
      this.where.limit = val
      this.where.page = 1
      this.getTableData()
    },
    pageChange(page) {
      this.where.page = page
      this.getTableData()
    },
    // 编辑
    handleEdit(data) {
      this.formInfo = {
        avatar: '',
        type: 'edit',
        show: 1,
        data: {},
        follow_id: 0
      }
      this.formInfo.editData = data
      this.formInfo.data.eid = data.eid
      this.formInfo.data.id = data.id
      this.formInfo.link_type = data.link_type
      this.dialogShow = true
    },
    // 删除
    async handleDelete(item) {
      await this.$modalSure('确定删除当前数据')
      await delClientFollowApi(item.id)
      if (this.where.page > 1 && this.tableData.length <= 1) {
        this.where.page--
      }
      await this.getTableData()
    }
  }
}
</script>

<style lang="scss" scoped>
.point {
  cursor: pointer;
  color: #1890ff;
}
::v-deep .el-input--small {
  width: 230px;
}
.record {
  ::v-deep .el-dialog__body {
    padding: 20px 20px 30px 20px;
  }
}
</style>

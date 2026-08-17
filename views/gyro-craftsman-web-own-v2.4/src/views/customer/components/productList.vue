<template>
  <div class="product-list mb20">
    <!-- 表格区域 -->
    <el-table :data="tableData" ref="table" row-key="unique" show-summary :summary-method="getSummaries"
      style="width: 100%" v-loading="loading">
      <!-- 操作列 -->
      <el-table-column :label="$("public.operation")" width="98px" v-if="type != 'edit'">
        <template slot-scope="scope">
          <div>
            <span class="iconfont icontuodong tuozhuai"></span>
            <span class="el-icon-circle-plus-outline" @click.stop="handleAdd(scope.$index)"></span>
            <span class="el-icon-delete" v-if="tableData.length > 1" @click.stop="handleDelete(scope.$index)"></span>
          </div>
        </template>
      </el-table-column>
      <!-- 产品信息列 -->
      <el-table-column :label="$("ui.customerSpecificationsProductInformation")" prop="image" width="210px">
        <template slot-scope="scope">
          <el-tooltip class="item" effect="dark" placement="top" :disabled="!scope.row.unique">
            <div slot="content" style="line-height: 24px" v-if="scope.row.unique">
              {{ scope.row.name || scope.row.product_name }} | {{ scope.row.sku || $('ui.customerProductAddProductSingleSpec') }}
            </div>
            <div class="flex lh-center">
              <img v-if="scope.row.unique && scope.row.image" :src="scope.row.image" class="product-img"
                @click="previewPicture(scope.row)" />
              <img v-if="scope.row.unique && !scope.row.image" src="../../../assets/images/bjt.png" class="product-img"
                @click="previewPicture(scope.row)" />
              <div v-if="!scope.row.unique && type != 'edit'" class="product-box"
                @click="openBox(scope.row, scope.$index)">
                <span class="el-icon-plus" />
              </div>
              <div v-if="scope.row.unique" class="over-text1">
                {{ scope.row.name || scope.row.product_name }}
              </div>
              <div v-if="scope.row.unique" class="over-text1">
                {{ scope.row.sku || $('ui.customerProductAddProductSingleSpec') }}
              </div>
            </div>
          </el-tooltip>
        </template>
      </el-table-column>
      <!-- 售价列 -->
      <el-table-column prop="ot_price" :label="$("legacy.4ea47b184e0109b9")" width="110px"></el-table-column>
      <!-- 成交数量列 -->
      <el-table-column prop="count" :label="$('ui.customerProductListDealQuantity')" width="120">
        <template slot-scope="scope">
          <span v-if="type === 'edit'">{{ scope.row.count }}</span>
          <el-input-number v-else v-model="scope.row.count" :min="0" :precision="0" size="small" class="priceBox"
            @change="handleCount(scope.row)" controls-position="right"></el-input-number>
        </template>
      </el-table-column>
      <!-- 折扣列 -->
      <el-table-column prop="discount" :label="$('ui.customerProductListDiscount')" width="120">
        <template slot-scope="scope">
          <span v-if="type === 'edit'">{{ scope.row.discount }}</span>
          <el-input-number v-else v-model="scope.row.discount" :min="0" :max="100" :precision="0" size="small"
            :controls="false" class="priceBox" @change="handleDiscountChange(scope.row)"
            controls-position="right"></el-input-number>
        </template>
      </el-table-column>
      <!-- 成交单价列 -->
      <el-table-column prop="price" :label="$('ui.customerProductListDealUnitPrice')" width="120">
        <template slot-scope="scope">
          <span v-if="type === 'edit'">{{ scope.row.price }}</span>
          <el-input-number v-else v-model="scope.row.price" :min="0" :precision="2" size="small" :controls="false"
            class="priceBox" controls-position="right" @change="handlePrice(scope.row)"></el-input-number>
        </template>
      </el-table-column>
      <!-- 成交总价列 -->
      <el-table-column prop="total_price" :label="$('ui.customerProductListDealTotal')" width="120">
        <template slot-scope="scope">
          <span v-if="type === 'edit'">{{ scope.row.total_price }}</span>
          <el-input-number v-else :controls="false" v-model="scope.row.total_price" :min="0" :precision="2"
            controls-position="right" size="small" class="priceBox"
            @change="handleTotalPrice(scope.row)"></el-input-number>
        </template>
      </el-table-column>
      <el-table-column prop="remark" :label="$('ui.xmindEditorToolbarNodeBtnListRemarks')" :fixed="remarkFixed ? 'right' : false" :width="remarkFixed ? 200 : undefined">
        <template slot-scope="scope">
          <span v-if="type === 'edit'">{{ scope.row.remark || '--' }}</span>
          <el-input v-else v-model="scope.row.remark" size="small" :placeholder="$('ui.customerProductListPleaseEnterRemarks')"></el-input>
        </template>
      </el-table-column>
    </el-table>

    <specifications ref="specifications" @getselectList="getselectList"></specifications>
    <image-viewer ref="imageViewer" :src-list="srcList"></image-viewer>
  </div>
</template>
<script>
import { $ } from '@/lang'
import Sortable from 'sortablejs'
export default {
  name: 'ProductList',
  components: {
    imageViewer: () => import('@/components/common/imageViewer'),
    specifications: () => import('./specifications')
  },
  props: {
    // 父组件传递的参数
    product: {
      type: Array,
      default: () => []
    },
    type: {
      type: String,
      default: 'add'
    },
    loading: {
      type: Boolean,
      default: false
    },
    remarkFixed: { // 备注栏是否固定
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      tableData: this.product,
      srcList: [],
      rowIndex: -1
    }
  },
  mounted() {
    setTimeout(() => {
      this.rowDrop()
    }, 500)
  },
  watch: {
    product: {
      handler(val) {
        this.tableData = val
      },
      deep: true
    },
    tableData: {
      handler(val) {
        // 计算产品总价
        const total = val.reduce((sum, item) => {
          if (item.unique && item.total_price) {
            return sum + Number(item.total_price)
          }
          return sum
        }, 0)
        this.$emit('productChange', total)
      },
      deep: true
    }
  },

  methods: {
    // 数量修改
    handleCount(row) {
      row.total_price = row.price * row.count
    },
    // 单价修改
    handlePrice(row) {
      row.discount = (row.price / row.ot_price) * 100
      row.total_price = row.price * row.count
    },
    // 折扣
    handleDiscountChange(row) {
      row.price = row.ot_price * (row.discount / 100)
      row.total_price = row.price * row.count
    },
    // 修改总价
    handleTotalPrice(row) {
      row.price = row.total_price / row.count
      row.discount = (row.price / row.ot_price) * 100
    },
    handleAdd(index) {
      if (this.tableData[index].unique == '') {
        return this.$message.error($('legacyScript.pleaseSelectAndFillInProductInformation'))
      }

      this.tableData.splice(index + 1, 0, {
        unique: '',
        image: '',
        name: '',
        sku: '',
        price: 0,
        count: 1,
        total_price: 0,
        discount: 0,
        ot_price: 0,
        is_show: true
      })
    },
    getSummaries(param) {
      const { columns, data } = param
      const sums = []
      const indexArr = this.type == 'edit' ? [2, 5] : [3, 6]
      const count = this.tableData.filter((item) => {
        const id = item.unique
        return id !== null && id !== undefined && id !== ''
      }).length
      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = `${this.$("ui.hrEnterprisePromotionTotal")} (${count})`
          return
        }
        if (indexArr.includes(index)) {
          const values = data.map((item) => Number(item[column.property]))
          if (!values.every((value) => isNaN(value))) {
            const sum = values.reduce((prev, curr) => {
              const value = Number(curr)
              if (!isNaN(value)) {
                return prev + curr
              } else {
                return prev
              }
            }, 0)
            // 保留两位小数
            if (index == 6) {
              sums[index] = sum.toFixed(2)
            } else {
              sums[index] = sum
            }

          } else {
            sums[index] = ''
          }
        }
      })

      return sums.map((sum, index) => {
        if (typeof sum === 'number' && index != 3) {

          return sum.toFixed(2)
        }
        return sum
      })
    },

    //预览图片
    previewPicture(row) {
      this.srcList = [row.image]
      this.$refs.imageViewer.openImageViewer(row.image)
    },
    getselectList(val, ids) {
      const uniqueSet = new Set()
      const filteredTableData = this.tableData.filter((item) => {
        if (item.unique) {
          uniqueSet.add(item.unique)
          return true
        }
        return false
      })

      // 处理新数据：过滤已存在项并转换格式
      const newItems = val
        .filter((item) => !uniqueSet.has(item.unique))
        .map((item) => ({
          unique: item.unique,
          image: item.image,
          name: item.name,
          sku: item.sku,
          price: item.price,
          count: 1,
          discount: 100,
          total_price: item.price,
          ot_price: item.price,
          remark: ''
        }))

      // 合并数据
      filteredTableData.splice(this.rowIndex, 0, ...newItems)
      this.tableData = filteredTableData
      // this.tableData =  [...filteredTableData, ...newItems]
      this.tableData = this.tableData.filter((item, i) => ids.includes(item.unique))
      this.$emit('getProductList', this.tableData)
    },
    // 处理删除操作
    handleDelete(index) {
      this.tableData.splice(index, 1)
    },
    openBox(row, index) {
      this.rowIndex = index
      this.$refs.specifications.openBox(row, this.tableData)
    },

    // 表格拖拽排序
    rowDrop() {
      const tbody = this.$refs.table.$el.querySelectorAll('.el-table__body-wrapper > table > tbody')[0]

      Sortable.create(tbody, {
        animation: 200,
        handle: '.icontuodong',
        onEnd: (e) => {
          // 调整数据顺序
          const movedItem = this.tableData.splice(e.oldIndex, 1)[0]
          this.tableData.splice(e.newIndex, 0, movedItem)
          this.$refs.table.doLayout()
        }
      })
    }
  }
}
</script>

<style scoped lang="scss">
.product-list {
  padding-bottom: 10px;
}

.product-box {
  cursor: pointer;
  width: 38px;
  height: 38px;
  border-radius: 8px;
  border: 1px solid #cccccc;
  background: #fbfbfb;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #909399;

  .el-icon-camera {
    font-size: 28px;
    color: #cccccc;
  }
}

.product-img {
  cursor: pointer;
  width: 38px;
  height: 38px;
  margin-right: 10px;
  float: left;
}

.product-desc {
  overflow: hidden;
}

.product-upload {
  margin-top: 10px;
}

.icontuodong {
  font-size: 14px;
  cursor: pointer;
  color: #909399;
}

.el-icon-circle-plus-outline {
  font-size: 14px;
  cursor: pointer;
  color: #909399;
  font-weight: 500;
  margin: 0 10px;
}

.el-icon-plus {
  font-size: 14px;
  cursor: pointer;
  color: #909399;
  font-weight: 500;
  margin: 0 10px;
}

.el-icon-delete {
  font-size: 14px;
  cursor: pointer;
  color: #909399;
  font-weight: 500;
}

.priceBox {
  width: 100%;
}

::v-deep .has-gutter {
  background: #fff;
}

.tuozhuai {
  cursor: move;
  display: inline-block;
}
</style>

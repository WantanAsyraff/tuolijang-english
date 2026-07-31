<template>
  <el-dialog v-model="isTablePopupVisible" :width="dialogSize.width" class="dialog-table-popup">
    <template #header="{ titleId }">
      <div class="my-header h-54px pl-20px flex items-center border-b border-#F2F6FC">
        <h4 :id="titleId" class="text-14px leading-20px font-bold">{{ $t("chat.dataTable") }}</h4>
      </div>
    </template>
    <div class="p-20px">
      <el-table :data="tableInfo.data" style="width: 100%;" :max-height="dialogSize.height"
        v-loading="tableInfo.loading">
        <el-table-column v-for="(fieldName, field) of tableInfo.tableFields" :key="field" :label="String(translateSystemText(fieldName))"
          show-overflow-tooltip :min-width="180" :prop="field">
          <template #default="{ row }">
            <span>{{ parseTableContent(row[field]) }}</span>
          </template>
        </el-table-column>
      </el-table>
      <div class="flex justify-end pt-20px">
        <el-pagination :layout="pageLayout" v-model:current-page="tableInfo.page"
          v-model:page-size="tableInfo.pageSize" :pager-count="pagerCount" :total="tableInfo.total" />
      </div>
    </div>
  </el-dialog>
</template>

<script setup lang="ts">
import { getChatRecordTableDataApi } from "@/api/chat";
import { useMediumScreen } from "@/composables/ui/useMediumScreen";
import { isMobile } from "@/config";
import { Message } from "@/utils/message";
import { translateSystemText } from "@/locale";

const { isMediumScreen } = useMediumScreen();
const isTablePopupVisible = ref(false);

const pagerCount = isMobile ? 5 : 7;
const pageLayout = computed(() => {
  return isMobile ? "prev,pager,next" : "prev,pager,next,jumper";
});

const tableInfo = ref({
  uuid: "",
  page: 1,
  pageSize: 10,
  total: 0,
  loading: false,
  loaded: false,
  data: [],
  tableFields: {}
});

const parseTableContent = (content: string) => {
  try {
    const json = JSON.parse(content);
    return JSON.stringify(json);
  } catch {
    return content;
  }
};

const dialogSize = computed(() => {
  return {
    width: isMediumScreen.value ? "calc(100% - 24px)" : "65%",
    height: isMediumScreen.value ? "calc(100% - 24px)" : "560"
  };
});

const getTableData = async () => {
  if (tableInfo.value.loading || tableInfo.value.loaded) return;
  tableInfo.value.loading = true;
  try {
    const { page, pageSize, uuid } = tableInfo.value;
    const res = await getChatRecordTableDataApi(uuid, page, pageSize);
    tableInfo.value.data = res.data.list;
    tableInfo.value.total = res.data.count;
    tableInfo.value.loaded = page * pageSize >= res.data.total;
    tableInfo.value.tableFields = res.data.table_fields;
  } catch (error: any) {
    Message.error(error.message);
  } finally {
    tableInfo.value.loading = false;
  }
};

const openTablePopup = (uuid: string) => {
  tableInfo.value.data = [];
  tableInfo.value.loaded = false;
  tableInfo.value.page = 1;
  tableInfo.value.uuid = uuid;
  isTablePopupVisible.value = true;
};

watch(isTablePopupVisible, (visible) => {
  if (!visible) {
    tableInfo.value.data = [];
    tableInfo.value.loaded = false;
    tableInfo.value.page = 1;
    tableInfo.value.uuid = "";
  }
});

watch([
  () => tableInfo.value.uuid,
  () => tableInfo.value.page,
  () => tableInfo.value.pageSize,
], ([uuid]) => {
  if (!uuid) return;
  getTableData();
});

defineExpose({
  openTablePopup
});

</script>

<style lang="scss">
.dialog-table-popup {
  --el-dialog-padding-primary: 0;
  --el-dialog-border-radius: 15px;

  .el-table {
    --el-table-header-bg-color: #F3F8FE;
  }

  .el-dialog__headerbtn {
    top: 5px;
    right: 4px;
  }

  .el-pagination__editor.el-input {
    @apply w-26px;

    .el-input__wrapper {
      @apply px-0;
    }
  }
}
</style>

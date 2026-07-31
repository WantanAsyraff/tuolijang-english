import { SELECT_CELL_EVENT, SELECT_ROW_EVENT, SELECT_COLUMN_EVENT, SELECT_SHIFT_EVENT, SELECT_CYCLE_EVENT } from "../constants/schedule";
import moment from "moment";

export const useScheduleEdit = (dateList: Ref<any[]>, members: Ref<any[]>, cycleList: Ref<any[]>, scheduleInfoMapByMonth: Ref<any>) => {
  /**
   * {
   *  [date]: {
   *    [uid]: shiftId
   * }
   */
  // 发生过修改的排班数据
  const editData = ref<any>({});

  // 选中的数据类型 cell -> 选中单元格  row -> 选中一行  column -> 选中一列
  type SelectType = typeof SELECT_CELL_EVENT | typeof SELECT_ROW_EVENT | typeof SELECT_COLUMN_EVENT;
  const selectType = ref<SelectType>();

  // 选中的数据相关信息，uid、date
  const selectInfo = ref<any>();

  // 周期班次id与班次id列表的映射
  const cycleIdToShiftMap = computed(() => {
    return cycleList.value.reduce((result: any, curr: any) => {
      result[curr.id] = curr.shifts.map((shift: any) => shift.id);
      return result;
    }, {});
  });

  /**
   * 检查日期是否是过去的日期
   * @param date 日期
   * @returns 是否是过去的日期
   */
  const isPastDate = (date: string) => {
    return moment(date).isBefore(moment(), "day");
  };

  /**
   * 缓存排班数据以供后续处理
   * @param date 日期
   * @param uid 用户id
   * @param shiftId 班次id
   */
  const setSchedultData = (date: string, uid: string, shiftId: string) => {
    if (!editData.value[date]) {
      editData.value[date] = {};
    }
    editData.value[date][uid] = shiftId;
  };

  /**
   * 生成获取周期班次id的函数
   * @returns
   */
  const generateGetShiftIdByCycleFunc = (cycleId: string) => {
    let index = 0;
    return () => {
      const shiftIdList = cycleIdToShiftMap.value[cycleId];
      const shiftId = shiftIdList[index];
      if (index < shiftIdList.length - 1) {
        index++;
      } else {
        index = 0;
      }
      return shiftId;
    };
  };

  /**
   * 选中排班数据
   * @param eventType 选中类型
   * @param params 选中数据
   */
  const handleSelectSchedule = (eventType: SelectType, params: any) => {
    selectType.value = eventType;
    selectInfo.value = params;
  };

  // 按照班次设置单元格排班
  const handleSetCellScheduleByShift = (shiftId: string) => {
    const { date, uid } = selectInfo.value;
    setSchedultData(date, uid, shiftId);
  };

  // 按照周期设置单元格排班
  const handleSetCellScheduleByCycle = (cycleId: string) => {
    const { date, uid } = selectInfo.value;
    const getShiftIdByCycle = generateGetShiftIdByCycleFunc(cycleId);
    setSchedultData(date, uid, getShiftIdByCycle());
  };

  // 按照班次设置行排班
  const handleSetRowScheduleByShift = (id: string) => {
    const { uid } = selectInfo.value;
    dateList.value.forEach((item) => {
      if (isPastDate(item.fullDate)) return;
      setSchedultData(item.fullDate, uid, id);
    });
  };

  // 按照周期设置行排班
  const handleSetRowScheduleByCycle = (cycleId: string) => {
    const { uid } = selectInfo.value;
    const getShiftIdByCycle = generateGetShiftIdByCycleFunc(cycleId);
    dateList.value.forEach((item) => {
      if (isPastDate(item.fullDate)) return;
      setSchedultData(item.fullDate, uid, getShiftIdByCycle());
    });
  };

  // 按照班次设置列排班
  const handleSetColumnScheduleByShift = (id: string) => {
    const { date } = selectInfo.value;
    members.value.forEach((item) => {
      setSchedultData(date, item.id, id);
    });
  };

  // 按照周期设置列排班
  const handleSetColumnScheduleByCycle = (cycleId: string) => {
    const { date } = selectInfo.value;
    const getShiftIdByCycle = generateGetShiftIdByCycleFunc(cycleId);
    members.value.forEach((item) => {
      setSchedultData(date, item.id, getShiftIdByCycle());
    });
  };

  // 将缓存的排班数据同步到表格数据中
  const handleSyncSchedultToTable = () => {
    Object.entries(editData.value).forEach(([fullDate, uidMapVal]) => {
      const dateInstance = moment(fullDate);
      const month = dateInstance.format("YYYY-MM");
      const monthData = scheduleInfoMapByMonth.value[month];
      if (!monthData) return;

      const date = dateInstance.get("date");

      Object.entries(uidMapVal).forEach(([uid, shiftId]) => {
        const userData = monthData.arrange.find((item: any) => item.uid === Number(uid));
        if (!userData) return;
        userData.shifts[date - 1] = Number(shiftId);
      });
    });
  };

  // 清除缓存的排班数据
  const cleanEditData = () => {
    selectType.value = undefined;
    selectInfo.value = undefined;
    editData.value = {};
  };

  /**
   * 设置排班数据
   * @param eventType 事件类型
   * @param id 周期班次id
   */
  const handleSetScheduleInfo = (eventType: string, id: string) => {
    const allowEventTypes = [SELECT_SHIFT_EVENT, SELECT_CYCLE_EVENT];
    if (!allowEventTypes.includes(eventType) || !selectType.value || !selectInfo.value) return;

    if (eventType === SELECT_SHIFT_EVENT) {
      if (selectType.value === SELECT_CELL_EVENT) {
        handleSetCellScheduleByShift(id);
      } else if (selectType.value === SELECT_ROW_EVENT) {
        handleSetRowScheduleByShift(id);
      } else if (selectType.value === SELECT_COLUMN_EVENT) {
        handleSetColumnScheduleByShift(id);
      }
    } else if (eventType === SELECT_CYCLE_EVENT) {
      if (selectType.value === SELECT_CELL_EVENT) {
        handleSetCellScheduleByCycle(id);
      } else if (selectType.value === SELECT_ROW_EVENT) {
        handleSetRowScheduleByCycle(id);
      } else if (selectType.value === SELECT_COLUMN_EVENT) {
        handleSetColumnScheduleByCycle(id);
      }
    }

    handleSyncSchedultToTable();
    cleanEditData();
  };

  provide("scheduleEditInfo", {
    handleSelectSchedule,
    handleSetScheduleInfo,

    selectType,
    selectInfo
  });

  return {
    editData,
    cleanEditData
  };
};

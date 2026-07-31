import moment from "moment";
import { SELECT_CELL_EVENT, SELECT_COLUMN_EVENT, SELECT_ROW_EVENT } from "../constants/schedule";

export const useScheduleTableData = () => {
  const { scheduleInfoMapByMonth, realDate, cycleType } = inject("scheduleMixin") as any;
  const { selectType, selectInfo } = inject("scheduleEditInfo") as any;

  const tableData = computed(() => {
    const monthKeys = Object.keys(scheduleInfoMapByMonth.value);
    if (!monthKeys.length) return [];
    const dateInstance = moment(realDate.value);

    const getCellClass = (date: string, uid: string, index: number, length: number, parentIndex: number, parentLength: number) => {
      if (!selectInfo.value) return [];

      const isEqualUid = selectInfo.value.uid === Number(uid);
      const isEqualDate = selectInfo.value.date === date;

      const SELECTED_CELL_CLASS = "selected-cell";
      const BASE_CELL_CLASS = "active-cell";
      const ROW_CELL_CLASS = "row-cell";
      const COLUMN_CELL_CLASS = "column-cell";
      const FIRST_CELL_CLASS = "first-cell";
      const LAST_CELL_CLASS = "last-cell";
      const MIDDLE_CELL_CLASS = "middle-cell";

      if (selectType.value === SELECT_CELL_EVENT) {
        return isEqualUid && isEqualDate ? [SELECTED_CELL_CLASS, BASE_CELL_CLASS] : [];
      } else if (selectType.value === SELECT_ROW_EVENT) {
        if (!isEqualUid) return [];

        const isSameDate = moment(date).isSame(moment(), "day");

        const isFirst = isSameDate || index === 0;
        const isLast = index === length - 1;

        const classList = [ROW_CELL_CLASS, BASE_CELL_CLASS];
        isFirst && classList.push(FIRST_CELL_CLASS);
        isLast && classList.push(LAST_CELL_CLASS);
        !isFirst && !isLast && classList.push(MIDDLE_CELL_CLASS);

        return classList;
      } else if (selectType.value === SELECT_COLUMN_EVENT) {
        if (!isEqualDate) return [];

        const isFirst = parentIndex === 0;
        const isLast = parentIndex === parentLength - 1;

        const classList = [COLUMN_CELL_CLASS, BASE_CELL_CLASS];
        isFirst && classList.push(FIRST_CELL_CLASS);
        isLast && classList.push(LAST_CELL_CLASS);
        !isFirst && !isLast && classList.push(MIDDLE_CELL_CLASS);

        return classList;
      }

      return [];
    };

    if (cycleType.value === "month") {
      const month = dateInstance.format("YYYY-MM");
      const monthData = scheduleInfoMapByMonth.value[month];
      if (!monthData) return [];
      return monthData.arrange.map((item: any, parentIndex: number) => {
        return {
          ...item,
          shifts: item.shifts.map((value: any, index: number) => {
            const date = `${month}-${(index + 1).toString().padStart(2, "0")}`;
            return {
              value,
              date,
              class: getCellClass(date, item.uid, index, item.shifts.length, parentIndex, monthData.arrange.length)
            };
          })
        };
      });
    } else {
      const monthByWeekStart = dateInstance.startOf("week").format("YYYY-MM");
      const monthByWeekEnd = dateInstance.endOf("week").format("YYYY-MM");
      const monthData = scheduleInfoMapByMonth.value[monthByWeekStart];
      const monthDataByNextMonth = scheduleInfoMapByMonth.value[monthByWeekEnd];
      if (!monthData) return [];
      if (monthByWeekStart !== monthByWeekEnd && !monthDataByNextMonth) return [];
      const result: any[] = [];

      for (let i = 0; i < monthData.members.length; i++) {
        const instance = moment(realDate.value).startOf("week");

        const member = monthData.members[i];
        const weekLength = 7;
        const shifts: any[] = Array.from({ length: weekLength }, (_, index) => {
          if (index > 0) {
            instance.add(1, "day");
          }
          const month = instance.format("YYYY-MM");
          const date = instance.get("date");
          const data = scheduleInfoMapByMonth.value[month];
          if (!data) {
            console.error("monthData is null");
          };

          const value = data.arrange[i].shifts[date - 1];
          if (value === undefined) {
            console.error("value is null");
          };
          const fullDate = instance.format("YYYY-MM-DD");
          return {
            value,
            date: fullDate,
            class: getCellClass(fullDate, member.id, index, weekLength, i, monthData.members.length)
          };
        });

        const userData = {
          uid: member.id,
          shifts
        };

        result.push(userData);
      }

      return result;
    }
  });

  return {
    tableData
  };
};

import moment from "moment";

export const useScheduleDate = (cycleType: Ref<string>, scheduleInfoMapByMonth: Ref<Record<string, any>>) => {
  const realDate = ref("");

  const updateRealDate = (date: string) => {
    realDate.value = date;
  };

  const dateRestStatusMap = computed(() => {
    return Object.keys(scheduleInfoMapByMonth.value)
      .reduce((acc, monthKey) => {
        const monthData = scheduleInfoMapByMonth.value[monthKey];
        const calendar = monthData.calendar;
        calendar.forEach((item: any) => {
          acc[item.date] = item.is_rest;
        });
        return acc;
      }, {} as any);
  });

  const dateList = computed(() => {
    let list = [];
    if (!realDate.value) return [];

    let instance = moment(realDate.value);
    if (cycleType.value === "month") {
      // 获取当月天数
      const dayCount = instance.daysInMonth();
      const startWeekIndex = instance.startOf("month").day();

      list = Array.from(Array(dayCount))
        .map((_, index) => {
          const fullDate = instance.format("YYYY-MM-DD");
          const isRest = dateRestStatusMap.value[fullDate];
          const result = {
            weekIndex: (startWeekIndex + index) % 7,
            fullDate,
            date: index + 1,
            isRest,
          };
          instance.add(1, "day");
          return result;
        });
    } else {
      instance = instance.startOf("week");
      list = Array.from(Array(7))
        .map(() => {
          const fullDate = instance.format("YYYY-MM-DD");
          const isRest = dateRestStatusMap.value[fullDate];
          const result = {
            weekIndex: instance.day(),
            fullDate,
            date: instance.date(),
            isRest,
          };
          instance.add(1, "day");
          return result;
        });
    }
    return list;
  });

  return {
    realDate,
    dateList,
    updateRealDate
  };
};

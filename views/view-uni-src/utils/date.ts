import moment from "moment";

/**
 * 获取当天时间
 * @param separater 日期连接符
 * @returns example: 2020-12-12
 */
export const getCurrentDate = (separater: string = "-") => {
  const timeInstance = new Date();

  return [
    timeInstance.getFullYear(),
    timeInstance.getMonth() + 1,
    timeInstance.getDate()
  ].join(separater);
};

enum WeekText {
  星期日,
  星期一,
  星期二,
  星期三,
  星期四,
  星期五,
  星期六
}

export const getWeekText = (dateStr: string) => {
  const momentInstance = moment(dateStr);
  const currentMomentInstance = moment(new Date());

  const week = momentInstance.day();
  const date = momentInstance.format("YYYY/MM/DD");
  const currentDate = currentMomentInstance.format("YYYY/MM/DD");
  const lastDate = currentMomentInstance.clone().subtract(1, "day").format("YYYY/MM/DD");

  if (date === currentDate) {
    return "今天";
  } else if (date === lastDate) {
    return "昨天";
  } else {
    return WeekText[week];
  }
};

/**
 * 将接收到的日期字符串格式化为月日时间
 * @param dateStr 日期字符串
 * @returns example: 12月12日
 */
export const getFormatMonthAndDate = (dateStr: string) => {
  const momentInstance = moment(dateStr);

  const month = momentInstance.get("month");
  const date = momentInstance.get("date");

  return `${month + 1}月${date}日`;
};

/**
 * 将接收到的日期字符串格式化为时分
 * @param dateStr 日期字符串
 * @returns example: 12:24
 */
export const getFormatHourAndMinute = (dateStr: string) => {
  const momentInstance = moment(dateStr);

  const hour = momentInstance.get("hour");
  const minute = momentInstance.get("minute");

  return `${(hour + "").padStart(2, "0")}:${(minute + "").padStart(2, "0")}`;
};

/**
 * 获取当前月的日期范围
 * @returns string
 */
export const getCurrentMonthRange = () => {
  const start = moment().startOf("month").format("YYYY/MM/DD");
  const end = moment().endOf("month").format("YYYY/MM/DD");

  return [start, end];
};


export const hourTimeRange = [
  Array.from({ length: 5 }, (_, i) => i.toString() + '小时'),
  Array.from({ length: 60 }, (_, i) => i.toString() + '分钟'),
];

Object.freeze(hourTimeRange);

export const formatHourTime = (value: number[]) => {
  const result = [];
  if (value[0] !== 0) {
    result.push(hourTimeRange[0][value[0]]);
  }
  result.push(hourTimeRange[1][value[1]]);
  return result.join('');
}

/**
 * 计算两个时间差(小时分钟)
 * @param {startDate} 开始时间 9:00
 * @param {endDate} 结束时间 18:00
 * @returns {String}
 */
export const getInervalHour = (startDate: number, endDate: number) => {
  startDate = new Date(`1970-01-01 ${startDate}`).getTime();
  endDate = new Date(`1970-01-01 ${endDate}`).getTime();
  let dateDiff = endDate - startDate
  let residue1 = dateDiff % (24 * 3600 * 1000)
  let hours = Math.floor(residue1 / (3600 * 1000))
  let residue2 = residue1 % (3600 * 1000)
  let minutes = Math.floor(residue2 / (60 * 1000))
  return [hours, minutes]
}

/**
 * 计算两个时间差(小时分钟)
 * @param {startDate} 开始时间 9:00
 * @param {endDate} 结束时间 18:00
 * @returns {String}
 */
export const getInervalTwoHour = (startDate: number, endDate: number, start: number, end: number) => {
  startDate = new Date(`1970-01-01 ${startDate}`).getTime();
  endDate = new Date(`1970-01-01 ${endDate}`).getTime();
 

  let dateDiff2 = 0;
  if (start && end) {
    start = new Date(`1970-01-01 ${start}`).getTime();
    end = new Date(`1970-01-01 ${end}`).getTime();
    dateDiff2 = end - start;
  }

  let dateDiff = endDate - startDate - dateDiff2
  let residue1 = dateDiff % (24 * 3600 * 1000)
  let hours = Math.floor(residue1 / (3600 * 1000))
  let residue2 = residue1 % (3600 * 1000)
  let minutes = Math.floor(residue2 / (60 * 1000))
  return [hours, minutes]
}

/**
 * 计算两个时间和(小时分钟)
 * @param {startDate} 开始时间 9:00
 * @param {endDate} 结束时间 18:00
 * @returns {String}
 */
export const getHour = (startDate: number, endDate: number, start: number, end: number) => {
  startDate = new Date(`1970-01-01 ${startDate}`).getTime();
  endDate = new Date(`1970-01-01 ${endDate}`).getTime();

  let dateDiff2 = 0;
  if (start && end) {
    start = new Date(`1970-01-01 ${start}`).getTime();
    end = new Date(`1970-01-01 ${end}`).getTime();
    dateDiff2 = end - start;
  }

  let dateDiff = endDate - startDate + dateDiff2
  let residue1 = dateDiff % (24 * 3600 * 1000)
  let hours = Math.floor(residue1 / (3600 * 1000))
  let residue2 = residue1 % (3600 * 1000)
  let minutes = Math.floor(residue2 / (60 * 1000))
  return [hours, minutes]
}

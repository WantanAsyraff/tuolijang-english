import * as filters from "@/filters";
import { formatMalaysiaPattern } from "@/utils/malaysia-date-time";

export function registerFilters(Vue) {
  Vue.filter("dateformat", function (dataStr, pattern = "YYYY-MM-DD") {
    if (dataStr) {
      return formatMalaysiaPattern(dataStr, pattern);
    } else {
      return dataStr;
    }
  });

  Object.keys(filters).forEach((key) => {
    Vue.filter(key, filters[key]);
  });
}

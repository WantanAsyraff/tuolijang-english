import moment from "moment";
import * as filters from "@/filters";

export function registerFilters(Vue) {
  Vue.filter("dateformat", function (dataStr, pattern = "YYYY-MM-DD") {
    if (dataStr) {
      return moment(dataStr).format(pattern);
    } else {
      return dataStr;
    }
  });

  Object.keys(filters).forEach((key) => {
    Vue.filter(key, filters[key]);
  });
}

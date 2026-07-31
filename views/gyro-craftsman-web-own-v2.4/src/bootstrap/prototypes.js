import moment from "moment";
import "moment/locale/zh-cn";
import ElementResizeDetectorMaker from "element-resize-detector";
import modalForm from "@/libs/modal-form";
import { modalSure } from "@/libs/public";
import { EventBus } from "@/libs/bus";
import pickerOptions from "@/libs/pickerOptions";
import { getDefaultAvatar, getAvatarSrc, setImageDefaultAvatar } from "@/utils/avatar";
import { processResourceUrl } from "@/utils/resourceUtil";
import { getLanguage } from "@/lang";
import { translateRuntimeText } from "@/utils/i18ns";

moment.locale(getLanguage() === "en" ? "en" : "zh-cn");

export function registerPrototypes(Vue) {
  Vue.prototype.$ts = function translateSourceText(text, englishValue) {
    return translateRuntimeText(text, this, englishValue);
  };
  Vue.prototype.$modalForm = modalForm;
  Vue.prototype.$modalSure = modalSure;
  Vue.prototype.$bus = EventBus;
  Vue.prototype.$vue = Vue;
  Vue.prototype.$moment = moment;
  Vue.prototype.$erd = ElementResizeDetectorMaker();
  Vue.prototype.$pickerOptionsTimeEle = pickerOptions;
  Vue.prototype.tableHeight = "calc(100vh - 269px)";
  Vue.prototype.$processResourceUrl = processResourceUrl;
  Vue.prototype.$getDefaultAvatar = getDefaultAvatar;
  Vue.prototype.$getAvatarSrc = getAvatarSrc;
  Vue.prototype.$handleAvatarError = (event, user) => setImageDefaultAvatar(event && event.target, user);
}

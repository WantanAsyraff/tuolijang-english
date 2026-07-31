import { debounce } from "@/utils/helper";
import store from "@/store";
import { messageCateApi, loginInfo, loginMenus } from "@/api/user";
import { bootstrapAuthenticatedSession } from "@/app/bootstrap/authenticated";
import { socketService } from "@/app/services/socket";
import { getServerConfigList, syncActiveServerConfig } from "@/utils/serverConfig";

type ToLoginOptions = {
  forceManualLogin?: boolean;
  forceLogout?: boolean;
};

// function prePage() {
//   const pages = getCurrentPages();
//   const prePage = pages[pages.length - 1];
//   return prePage?.route;
// }

export const toLogin = debounce(_toLogin, 800);

const getCurrentPageFullPath = () => {
  const pages = getCurrentPages();
  const currentPage = pages[pages.length - 1];
  return currentPage?.$vm?.$page?.fullPath || "/pages/index/index";
};

function _toLogin(options: ToLoginOptions | boolean = {}) {
  const forceManualLogin = typeof options === "boolean" ? options : !!options.forceManualLogin;
  const forceLogout = typeof options === "object" ? !!options.forceLogout : options;
  if (store.state.app.isLogin && !forceLogout) return;

  socketService.disconnect();
  const currentPath = getCurrentPageFullPath();
  syncActiveServerConfig();
  store.commit("logout");
  const serverConfigInfoData = getServerConfigList();
  if (serverConfigInfoData.length) {
    uni.setStorageSync("serverConfigInfo", serverConfigInfoData);
  }
  // // #ifdef APP-PLUS
  // path = '/' + path
  // if (!serverConfigInfoData.length) {
  // // serverConfigInfoData.unshift(defaultEnterpriseList);
  // uni.setStorageSync('serverConfigInfo', JSON.stringify(serverConfigInfoData))
  // uni.reLaunch({
  //  url: "/pages/users/login/config",
  // })
  // return
  // }
  // // #endif

  // #ifdef H5

  // const path = location.pathname + location.search;
  // console.log(serverConfigInfoData)
  // if (serverConfigInfoData.length == 0) {
  // // serverConfigInfoData.unshift(defaultEnterpriseList);
  // uni.setStorageSync('serverConfigInfo', JSON.stringify(serverConfigInfoData))
  // uni.reLaunch({
  // url: "/pages/users/login/config",
  // })
  // return
  // }
  // #endif
  store.commit("setLoginBackUrl", currentPath);

  // #ifdef H5
  const isWxWork = /wxwork/i.test(typeof navigator !== "undefined" ? navigator.userAgent : "");
  if (isWxWork && !forceManualLogin) {
    import("@/libs/wxwork-auth").then(({ wxworkAuth }) => {
      wxworkAuth();
    });
    return;
  }
  // #endif

  uni.redirectTo({
    url: "/pages/users/login/index"
  });
}

export const getInfo = async () => {
  const res = await loginInfo();
  store.commit("loginInfo", res.data);
};
const getMenus = async () => {
  const res = await loginMenus();
  store.commit("loginMenu", res.data);
};
const getNum = () => {
  messageCateApi().then((res: any) => {
    const arr = res.data;
    let num = 0;

    arr.map((item: any) => {
      num += Number(item.count);
    });

    if (num === 0) {
      uni.removeTabBarBadge({
        index: 1,
      });
    } else {
      uni.setTabBarBadge({
        index: 1, // 人脉页面在底部菜单栏的索引
        text: num > 99 ? "99+" : num + "", // 要显示的文本（必须是字符串类型）
      });
    }
  });
};

// 登录成功后页面跳转
export const loginSuccess = async (redirect: boolean = true) => {
  await Promise.all([getInfo(), getMenus()]);
  getNum();
  let backUrl = store.state.app.loginBackUrl || "/pages/index/index";
  if (backUrl.indexOf("/pages/users/login/index") !== -1) {
    backUrl = "/pages/index/index";
  }
  store.commit("setLoginBackUrl", "");
  bootstrapAuthenticatedSession();
  // backUrl = '/pages/index/index'
  redirect && await uni.reLaunch({
    url: backUrl,
  });
};

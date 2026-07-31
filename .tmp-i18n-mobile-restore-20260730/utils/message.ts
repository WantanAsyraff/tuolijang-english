type IconType = "success" | "loading" | "error" | "none" | "fail" | "exception";

const message = {
  success: (success: string, icon: IconType = "success", mask: boolean = false) => {
    uni.showToast({
      title: success,
      icon: "none",
      mask: mask,
      position: "bottom"
    });
  },
  error: (error: string, icon: IconType = "none", mask: boolean = false) => {
    uni.showToast({
      title: error,
      icon: "none",
      mask: mask,
      position: "bottom"
    });
  }
};

export default message;

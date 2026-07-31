import { translateSystemText } from "@/locale";

type IconType = "success" | "loading" | "error" | "none" | "fail" | "exception";

const message = {
  success: (success: string, icon: IconType = "success", mask: boolean = false) => {
    uni.showToast({
      title: String(translateSystemText(success)),
      icon: "none",
      mask: mask,
      position: "bottom"
    });
  },
  error: (error: string, icon: IconType = "none", mask: boolean = false) => {
    uni.showToast({
      title: String(translateSystemText(error)),
      icon: "none",
      mask: mask,
      position: "bottom"
    });
  }
};

export default message;

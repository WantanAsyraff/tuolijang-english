import { translateSystemText } from "@/locale";

const showMessage = (message: string, type: "success" | "warning" | "info" | "error") => {
  return ElMessage({
    message: translateSystemText(message) as string,
    type,
    customClass: "el-message-custom-offset"
  });
};

export const Message = {
  success: (message: string) => showMessage(message, "success"),
  warning: (message: string) => showMessage(message, "warning"),
  info: (message: string) => showMessage(message, "info"),
  error: (message: string) => showMessage(message, "error"),
};

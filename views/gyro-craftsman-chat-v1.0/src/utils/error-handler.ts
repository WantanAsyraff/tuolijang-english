import { AuthError, RequestError } from "@/types/error";
import { Message } from "@/utils/message";
import { translate } from "@/locale";

export const handleError = (error: any) => {
  if (error instanceof AuthError) {
    Message.error(error.message);
  } else if (error instanceof RequestError) {
    Message.error(error.message);
  } else {
    Message.error(translate("error.internalError"));
  }
};

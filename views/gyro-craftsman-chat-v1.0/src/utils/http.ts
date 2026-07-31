import axios, { type AxiosRequestConfig } from "axios";
import { useUserStore } from "@/pinia/stores/useUserStore";
import { apiBaseUrl, apiPrefix } from "@/config";
import { useLoginDialogStore } from "@/pinia/stores/ui/useLoginDialogStore";
import { useRootStore } from "@/pinia/stores/useRootStore";
import { AuthError, RequestError } from "@/types/error";
import { getLanguage, translate } from "@/locale";

const instance = axios.create({
  baseURL: `${apiBaseUrl}${apiPrefix}`,
  timeout: 10000,
});

interface HttpOptions {
  requireAuth: boolean;
}

const defaultHttpOptions: HttpOptions = {
  requireAuth: true,
};

const loginFailStatusCodeSet = new Set([
  410000, 410001, 410002, 40000, 410003
]);

const baseRequest = (options: AxiosRequestConfig, httpOptions: HttpOptions) => {
  type Task = Promise<any> & {
    cancel: () => void;
  };

  const controller = new AbortController();
  const task = new Promise(async (resolve, reject) => {
    const { requireAuth } = httpOptions;
    const headers = { ...(options.headers || {}) } as Record<string, string>;
    headers.laravel_lang = getLanguage();
    const store = useUserStore();

    if (requireAuth) {
      if (store.token) {
        headers.Authorization = `Bearer ${store.token}`;
      } else {
        const loginDialogStore = useLoginDialogStore();
        loginDialogStore.handleSetLoginDialogOpen();
        return reject(new AuthError(translate("error.loginRequired")));
      }
    }

    options.headers = headers;
    options.signal = controller.signal;

    try {
      const response = await instance(options);
      const { data } = response;
      if (response.status === 200) {
        if (loginFailStatusCodeSet.has(data.status)) {
          useRootStore().reset();
          const loginDialogStore = useLoginDialogStore();
          loginDialogStore.handleSetLoginDialogOpen();
          return reject(new AuthError(translate("error.loginExpired")));
        } else if (data.status === 200) {
          return resolve(data);
        } else {
          return reject(new RequestError(data.message || translate("error.requestFailed")));
        }
      }
      return reject(new RequestError(data.message || translate("error.requestFailed")));
    } catch (err: any) {
      reject(new RequestError(err?.message || translate("error.requestFailed")));
    }
  }) as Task;

  task.cancel = () => controller.abort();

  return task;
};

const generateQueryMethod = (method: string) => {
  return (url: string, params: any = {}, options: HttpOptions = defaultHttpOptions, axiosOptions: AxiosRequestConfig = {}) => {
    return baseRequest(Object.assign({ url, params }, {
      method: method,
      ...axiosOptions
    }), options);
  };
};

const generateMutationMethod = (method: string) => {
  return (url: string, data: any = {}, options: HttpOptions = defaultHttpOptions, axiosOptions: AxiosRequestConfig = {}) => {
    return baseRequest(Object.assign({ url, data }, {
      method: method,
      ...axiosOptions
    }), options);
  };
};
export const http = {
  get: generateQueryMethod("get"),
  post: generateMutationMethod("post"),
  put: generateMutationMethod("put"),
  delete: generateQueryMethod("delete"),
};

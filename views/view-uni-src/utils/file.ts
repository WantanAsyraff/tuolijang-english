import appI18n from '@/locale';
import { TOKENNAME, BASEAPI, FORM_TYPE } from "@/config/app";
import store from "../store";
import { uploadTypes } from "@/utils/helper";
import { ensureValidAccessToken } from "@/utils/request";
import { getActiveApiUrl } from "@/utils/serverConfig";
import { getLanguage } from "@/locale";

/**
 * 附件上传
 * h5上传文件与图片
 * app打开文件管理上传
 */
export const uploadFlie = (url: string = "common/upload", formData: object = {}, size: number = 6): Promise<any> => {
  const fileSize = size * 1024 * 1024;
  return new Promise((resolve, reject) => {
    // #ifdef H5
    uni.chooseFile({
      count: 1, // 默认100
      extension: uploadTypes,
      success: (res: any) => {
        const tempFilePaths = res.tempFilePaths[0];
        const tempFiles = res.tempFiles[0];

        if (tempFiles.size > fileSize) {
          reject("图片或文件大小不能超过" + size + "MB");
        } else {
          uploadFiles(tempFilePaths, url);
        }
      },
      fail: () => {
        reject("图片或文件选择失败");
      }
    });
    //  #endif

    // #ifndef H5
    // APP端：使用 chooseFile 选择任意类型附件
    uni.chooseFile({
      count: 1,
      extension: uploadTypes,
      success: (res: any) => {
        const tempFilePaths = res.tempFilePaths[0];
        const tempFiles = res.tempFiles[0];

        if (tempFiles.size > fileSize) {
          reject("文件大小不能超过" + size + "MB");
        } else {
          uploadFiles(tempFilePaths, url);
        }
      },
      fail: () => {
        // chooseFile 失败时降级为选择图片
        uni.chooseImage({
          count: 1,
          sourceType: ["album"],
          success: (chooseImageRes: any) => {
            const tempFilePaths = chooseImageRes.tempFilePaths[0];
            const tempFiles = chooseImageRes.tempFiles[0];

            if (tempFiles.size > fileSize) {
              reject("图片大小不能超过" + size + "MB");
            } else {
              uploadFiles(tempFilePaths, url);
            }
          },
          fail: () => {
            reject("文件选择失败");
          }
        });
      }
    });
    // #endif

    // 文件上传
    const uploadFiles = (tempFilePaths: any, url: string) => {
      uni.showLoading({
        title: appI18n.global.t('ui.utilsFileTsUploadCenter')
      });

      ensureValidAccessToken()
        .then(() => {
          uni.uploadFile({
            url: `${getActiveApiUrl()}${BASEAPI}${url}`,
            header: {
              [TOKENNAME]: "Bearer " + store.state.app.token,
              "Form-type": FORM_TYPE,
              "laravel_lang": getLanguage()
            },
            filePath: tempFilePaths,
            name: "file",
            formData: formData,
            success: (uploadFileRes) => {
              uni.hideLoading();
              const res = JSON.parse(uploadFileRes.data);
              resolve(res);
            },
            fail: () => {
              uni.hideLoading();
              reject("上传失败，请稍后再试！");
            }
          });
        })
        .catch((error) => {
          uni.hideLoading();
          reject(error.message || "登录已过期，请重新登录");
        });
    };
  });
};

/**
 * 图片上传
 * @param url 上传的URL，默认为'common/upload'
 * @param formData 表单数据对象
 * @param size 图片大小限制（单位：MB），默认为2MB
 * @param sourceType 图片选择来源类型，默认为['album']
 */
export const uploadImage = (url: string = "common/upload", formData: object = {}, size: number = 2, sourceType: string[] = ["camera", "album"]): Promise<any> => {
  const fileSize = size * 1024 * 1024;
  return new Promise((resolve, reject) => {
    uni.chooseImage({
      count: 1,
      sourceType: sourceType,
      success: (chooseImageRes: any) => {
        const tempFilePaths = chooseImageRes.tempFilePaths[0];
        const tempFiles = chooseImageRes.tempFiles[0];
        // #ifndef H5
        if (tempFiles.size > fileSize) {
          reject("图片或文件大小不能超过" + size + "MB");
        } else {
          uploadFiles(tempFilePaths, url);
        }
        // uni.compressImage({
        //   src: tempFilePaths,
        //   quality: 50, // 压缩质量，范围0～100，数值越小，质量越低，压缩率越高（仅对jpg有效）
        //   success: res => {
        //     console.log('app压缩图片', res)
        //     uni.readFile({
        //       filePath: res.tempFilePath, // 图片文件路径
        //       encoding: 'base64', // 文件内容的编码格式，这里使用base64编码
        //       success: function (res) {
        //         // 读取成功，res.data为文件的base64编码字符串
        //         var base64Image = res.data;
        //         uploadFiles(base64Image, url)
        //       }
        //     });
        //   }
        // })

        // #endif
        // #ifdef H5
        // 图片压缩
        uni.getImageInfo({
          src: tempFilePaths,
          success: function (image) {
            compressImages(tempFilePaths, image);
            if (tempFiles.size > fileSize) {
              reject("图片或文件大小不能超过" + size + "MB");
            } else {
              uploadFiles(tempFilePaths, url);
            }
          }
        });
        // #endif
      },
      fail: () => {
        reject("图片选择失败");
      }
    });

    // 文件上传
    const uploadFiles = (tempFilePaths: any, url: string) => {
      uni.showLoading({
        title: appI18n.global.t('ui.utilsFileTsUploadCenter')
      });
      ensureValidAccessToken()
        .then(() => {
          uni.uploadFile({
            url: `${getActiveApiUrl()}${BASEAPI}${url}`,
            header: {
              [TOKENNAME]: "Bearer " + store.state.app.token,
              "Form-type": FORM_TYPE,
              "laravel_lang": getLanguage()
            },
            filePath: tempFilePaths,
            name: "file",
            formData: formData,
            success: (uploadFileRes: { data: string }) => {
              uni.hideLoading();
              const res = JSON.parse(uploadFileRes.data);
              resolve(res);
            },
            fail: () => {
              uni.hideLoading();
              reject("上传失败，请稍后再试！");
            }
          });
        })
        .catch((error) => {
          uni.hideLoading();
          reject(error.message || "登录已过期，请重新登录");
        });
    };
  });
};

// 压缩文件
const compressImages = (tempFilePaths, image) => {
  let canvasWidth = image.width; // 图片原始长宽
  let canvasHeight = image.height;
  const base = canvasWidth / canvasHeight;
  // 设置画布最大宽度
  if (canvasWidth > 800) {
    canvasWidth = 800;
    canvasHeight = Math.floor(canvasWidth / base);
  }
  const img = new Image();
  img.src = tempFilePaths; // 要压缩的图片
  const canvas = document.createElement("canvas");
  const ctx = canvas.getContext("2d");
  canvas.width = canvasWidth;
  canvas.height = canvasHeight;
  ctx.clearRect(0, 0, canvasWidth, canvasHeight);
  ctx.drawImage(img, 0, 0, canvasWidth, canvasHeight);
  // 指定格式 PNG
  return canvas.toDataURL("image/png");
};

/**
 * 转换文件大小
 */
export const formatBytes = (bytes: number, decimals: number = 2): string => {
  if (bytes === 0) return "0 KB"; // 0时显示0 KB

  const k = 1024;
  const dm = decimals < 0 ? 0 : decimals;
  const sizes = ["KB", "MB", "GB"]; // 从KB开始，去掉Bytes

  // 计算单位索引（最小为0，对应KB）
  let i = Math.floor(Math.log(bytes) / Math.log(k)) - 1;
  i = Math.max(i, 0); // 确保索引不小于0（最小为KB）

  // 计算对应单位的数值（强制转换为KB及以上单位）
  const value = parseFloat((bytes / Math.pow(k, i + 1)).toFixed(dm));

  return `${value} ${sizes[i]}`;
};

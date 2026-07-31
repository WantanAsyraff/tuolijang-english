

const wrapperUniFuncPromise = (func: Function) => {

  return new Promise((resolve, reject) => {
    func({
      success: (res: any) => {
        resolve(res);
      },
      fail: (err: any) => {
        reject(err);
      }
    })
  })
}

export const startWifiModule = () => {
  return wrapperUniFuncPromise(uni.startWifi);
}

export const getConnectedWifiInfo = () => {
  return wrapperUniFuncPromise(uni.getConnectedWifi);
}

export const stopWifiModule = () => {
  return wrapperUniFuncPromise(uni.stopWifi);
}

export const getWifiInfo = async () => {
  try {
    await startWifiModule();
    const wifiInfo = await getConnectedWifiInfo();
    await stopWifiModule();
    return wifiInfo;
  } catch (err) {
    return null;
  }
}

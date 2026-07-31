import request from './request'

/**
 * @description 快捷回复分组列表
 */
export function getWorkReplyGroupApi() {
  return request.get(`work/reply_temp_group`)
}

/**
 * @description 添加快捷回复分组接口
 */
export function getWorkReplyGroupCreateApi() {
  return request.get(`work/reply_temp_group/create`)
}
/**
 * @description 编辑快捷回复分组接口
 */
export function getWorkReplyGroupEditApi(id) {
  return request.get(`work/reply_temp_group/${id}/edit`)
}
/**
 * @description 删除快捷回复分组接口
 */
export function getWorkReplyGroupDelApi(id) {
  return request.delete(`work/reply_temp_group/${id}`)
}
/**
 * @description 获取快捷回复列表
 */
export function getWorkReplyListApi(data) {
  return request.get(`work/reply_temp`, data)
}
/**
 * @description 快捷回复保存接口
 */
export function workReplySaveApi(data) {
  return request.post(`work/reply_temp`, data)
}
/**
 * @description 获取快捷回复详情接口
 */
export function workReplyDetailsApi(id) {
  return request.get(`work/reply_temp/${id}/edit`)
}
/**
 * @description 修改快捷回复保存
 */
export function workReplyPutApi(id, data) {
  return request.put(`work/reply_temp/${id}`, data)
}
/**
 * @description 删除快捷回复接口
 */
export function workReplyDelApi(id) {
  return request.delete(`work/reply_temp/${id}`)
}

/**
 * @description 快捷回复下载模板
 */
export function getWorkReplyImportApi() {
  return request.get(`work/reply_temp/import/temp`)
}
/**
 * @description 快捷回复导入
 */
export function workReplyImportApi(data) {
  return request.post(`work/reply_temp/import`, data)
}

/**
 * @description 群发素材分组列表
 */
export function workMassTempGroupApi() {
  return request.get(`work/mass_messaging_temp_group`)
}
/**
 * @description 添加群发素材分组接口
 */
export function workMassTempGroupCreateApi() {
  return request.get(`work/mass_messaging_temp_group/create`)
}
/**
 * @description 添加群发素材分组接口
 */
export function workMassTempGroupEditApi(id) {
  return request.get(`work/mass_messaging_temp_group/${id}/edit`)
}
/**
 * @description 删除群发素材分组接口
 */
export function workMassTempGroupDelApi(id) {
  return request.delete(`work/mass_messaging_temp_group/${id}`)
}

/**
 * @description 群发素材列表接口
 */
export function workMassTempListApi(data) {
  return request.get(`work/mass_messaging_temp`, data)
}

/**
 * @description 群发素材新增保存接口
 */
export function workMassTempSaveApi(data) {
  return request.post(`work/mass_messaging_temp`, data)
}

/**
 * @description 群发素材编辑保存接口
 */
export function workMassTempEditApi(id, data) {
  return request.put(`work/mass_messaging_temp/${id}`, data)
}
/**
 * @description 群发素材获取接口
 */
export function workMassTempApi(id) {
  return request.get(`work/mass_messaging_temp/${id}/edit`)
}

/**
 * @description 删除群发素材接口
 */
export function workMassTempDelApi(id) {
  return request.delete(`work/mass_messaging_temp/${id}`)
}

/**
 * @description 群发消息列表
 */
export function getMassList(data) {
  return request.get(`work/mass_messaging`, data)
}

/**
 * @description 添加群发消息
 */
export function workMassSave(data) {
  return request.post(`work/mass_messaging`, data)
}

/**
 * @description 获取群发消息详情
 */
export function getWorkMassEdit(id) {
  return request.get(`work/mass_messaging/${id}/edit`)
}

/**
 * @description 修改群发消息
 */
export function putWorkMassEdit(id, data) {
  return request.put(`work/mass_messaging/${id}`, data)
}

/**
 * @description 删除群发消息
 */
export function delWorkMass(id) {
  return request.delete(`work/mass_messaging/${id}`)
}

/**
 * @description 企微客户群列表
 */
export function getWorkMassGroupChat(data) {
  return request.post(`work/mass_messaging/group_chat`, data)
}

/**
 * @description 待发送客户数
 */
export function getWorkMassCustomerCount(data) {
  return request.post(`work/mass_messaging/customer_count`, data)
}

/**
 * @description 修改群发状态
 */
export function getWorkMassStatus(id, data) {
  return request.get(`work/mass_messaging/status/${id}`, data)
}

/**
 * @description 修改群发状态
 */
export function getWorkMassRemind(id) {
  return request.get(`work/mass_messaging/remind/${id}`)
}
/**
 * @description 获取群发结果
 */
export function getWorkMassResult(id, data) {
  return request.get(`work/mass_messaging/result/${id}`, data)
}

/**
 * @description 获取网页元信息
 */
export function getUrlMetadataApi(data) {
  return request.post(`work/reply_temp/url_metadata`, data)
}

/**
 * @description 获上传图片路径获取临时id
 */
export function getMediaUploadByUrlApi(data) {
  return request.post(`work/media/upload-by-url`, data)
}
/**
 * @description 获取企微客户群聊列表
 */
export function getGroupChat(data) {
  return request.post(`work/channel_code/group_chat`, data)
}

import request from '@/api/request'

/**
 * AI--添加应用
 * @return {*}
 */
export function chatSaveApplicationsApi(data) {
  return request.post(`chat/applications`, data)
}

/**
 * AI--编辑保存应用
 * @return {*}
 */
export function chatPutApplicationsApi(id, data) {
  return request.put(`chat/applications/${id}`, data)
}

/**
 * AI--编辑发布应用
 * @return {*}
 */
export function chatReleasesApplicationsApi(id, data) {
  return request.get(`chat/applications/${id}`, data)
}

/**
 * AI--应用列表
 * @return {*}
 */
export function getApplicationsListApi(data) {
  return request.get(`chat/applications`, data)
}

/**
 * AI--MCP服务列表
 * @return {*}
 */
export function getMcpListApi(data) {
  return request.get(`chat/mcp`, data)
}

/**
 * AI--获取MCP服务详情
 * @return {*}
 */
export function getMcpServiceApi(id) {
  return request.get(`chat/mcp/${id}`)
}

/**
 * AI--编辑MCP服务详情
 * @return {*}
 */
export function putMcpServiceApi(id, data) {
  return request.put(`chat/mcp/${id}`, data)
}

/**
 * AI--删除MCP服务
 * @return {*}
 */
export function delMcpServiceApi(id) {
  return request.delete(`chat/mcp/${id}`)
}

/**
 * AI--保存MCP服务
 * @return {*}
 */
export function saveMcpServiceApi(data) {
  return request.post(`chat/mcp`, data)
}

/**
 * AI--删除应用列表
 * @return {*}
 */
export function delApplicationsApi(id) {
  return request.delete(`chat/applications/${id}`)
}

/**
 * AI--获取数据库设置的提示文字
 * @return {*}
 */
export function getApplicationstoolTipApi(data) {
  return request.post(`chat/applications/database/tooltip`, data)
}

/**
 * AI--应用详情
 * @return {*}
 */
export function getApplicationsInfoApi(id) {
  return request.get(`chat/applications/${id}/edit`)
}

/**
 * AI--修改应用状态
 * @return {*}
 */
export function getApplicationsStatusApi(id, data) {
  return request.get(`chat/applications/${id}`, data)
}

/**
 * AI--获取模型下拉选择数据
 * @return {*}
 */
export function getModelsSelectListApi() {
  return request.get(`chat/models/list`)
}

/**
 * AI--获取模型列表
 * @return {*}
 */
export function getModelsListApi(data) {
  return request.get(`chat/models`, data)
}

/**
 * AI--新增保存模型
 * @return {*}
 */
export function saveModelsApi(data) {
  return request.post(`chat/models`, data)
}

/**
 * AI--获取模型详情
 * @return {*}
 */
export function getModelsInfoApi(id) {
  return request.get(`chat/models/${id}/edit`)
}

/**
 * AI--删除模型
 * @return {*}
 */
export function delModelsApi(id) {
  return request.delete(`chat/models/${id}`)
}

/**
 * AI--编辑保存模型
 * @return {*}
 */
export function editModelsApi(id, data) {
  return request.put(`chat/models/${id}`, data)
}

/**
 * AI--供应商
 * @return {*}
 */
export function getModelsOptionsApi() {
  return request.get(`chat/models/options`)
}

/**
 * AI--模型类型
 * @return {*}
 */
export function getModelsSelectApi(data) {
  return request.get(`chat/models/select`, data)
}

/**
 * AI--数据库列表
 * @return {*}
 */
export function getDatabesListApi(data) {
  return request.get(`chat/applications/databes/list`, data)
}

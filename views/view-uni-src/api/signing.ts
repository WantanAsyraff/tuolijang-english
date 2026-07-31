import request from "../utils/request";

/**
 * 获取合同合约列表数据
 * get
 * @return {data}
 * @returns {*}
 */
export function getContractDocApi(data: object) {
  return request.get("client/contract_doc", data);
}
/**
 * 删除合同合约列表数据
 * delete
 * @return {data}
 * @returns {*}
 */
export function deleteContractDocApi(id: number) {
  return request.delete(`client/contract_doc/${id}`,);
}
/**
 * 根据id获取客户详情
 * get
 * @return {data}
 * @returns {*}
 */
export function getCustomerDetailApi(id: number) {
  return request.get(`client/customer/base/${id}`);
}

/**
 * 合同合约详情数据
 * get
 * @return {data}
 * @returns {*}
 */
export function getContractDocEditApi(id: number,data: object) {
  return request.get(`client/contract_doc/${id}/edit`,data);
}
/**
 * 撤销合同合约
 * get
 * @return {data}
 * @returns {*}
 */
export function contractDocCancelApi(id: number) {
  return request.get(`client/contract_doc/cancel/${id}`,);
}

/**
 * 新增合同合约
 * get
 * @return {data}
 * @returns {*}
 */
export function contractDocAddApi(data: object) {
  return request.post(`client/contract_doc`,data);
}
/**
 * 修改合同合约
 * get
 * @return {data}
 * @returns {*}
 */
export function contractDocUpdateApi(id: number,data: object) {
  return request.put(`client/contract_doc/${id}`,data);
}
/**
 * 获取关联签约订单
 * get
 * @return {data}
 * @returns {*}
 */
export function getContractDocOrdersApi(id: number,data: object) {
  return request.get(`client/contract_doc/orders/${id}`,data);
}
/**
 * 获取审批流程
 * get
 * @return {data}
 * @returns {*}
 */
export function getContractDocProcessApi(data: object) {
  return request.post(`client/contract_doc/process`,data);
}
/**
 * 获取合同文件转换结果
 * get
 * @return {data}
 * @returns {*}
 */
export function getContractDocTaskApi(taskId: number) {
  return request.get(`client/contract_doc/task/${taskId}`);
}
/**
 * 关联签约订单
 * get
 * @return {data}
 * @returns {*}
 */
export function linkOrderApi(id: number,data:object) {
  return request.post(`client/contract_doc/link_order/${id}`,data);
}
/**
 * 线下签约文件上传
 * get
 * @return {data}
 * @returns {*}
 */
export function signFileUploadApi(id: number,data:object) {
  return request.post(`client/contract_doc/sign/${id}`,data);
}
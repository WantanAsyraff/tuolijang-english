-- 客户自定义表单字段调整
-- 1. 订单表单关联商机字段展示名：商机名称 -> 商机编号
-- 2. 同步订单表 oid 字段注释

UPDATE `eb_form_data`
SET
    `key_name` = '商机编号',
    `placeholder` = '请选择商机编号',
    `updated_at` = NOW()
WHERE `types` = 2
  AND `key` = 'oid'
  AND `deleted_at` IS NULL;

ALTER TABLE `eb_contract`
    MODIFY COLUMN `oid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '商机编号';

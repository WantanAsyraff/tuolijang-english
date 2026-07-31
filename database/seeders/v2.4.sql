INSERT INTO `eb_chat_app_mcp_services` (`name`, `info`, `type`, `service_url`, `headers`, `config_json`, `status`, `is_default`, `sort`, `created_at`, `updated_at`)
SELECT '客户MCP服务', '客户、线索、商机、订单、合同、发票、客户账目、联系人、跟进记录', 'sse', '/mcp/customer', '[]', '{"transport":"sse","module":"customer","headers":[],"timeout":30}', 1, 1, 0, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `eb_chat_app_mcp_services` WHERE `is_default` = 1 AND `service_url` = '/mcp/customer');

INSERT INTO `eb_chat_app_mcp_services` (`name`, `info`, `type`, `service_url`, `headers`, `config_json`, `status`, `is_default`, `sort`, `created_at`, `updated_at`)
SELECT '考勤MCP服务', '考勤打卡、排班、申请和统计', 'sse', '/mcp/attendance', '[]', '{"transport":"sse","module":"attendance","headers":[],"timeout":30}', 1, 1, 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `eb_chat_app_mcp_services` WHERE `is_default` = 1 AND `service_url` = '/mcp/attendance');

INSERT INTO `eb_chat_app_mcp_services` (`name`, `info`, `type`, `service_url`, `headers`, `config_json`, `status`, `is_default`, `sort`, `created_at`, `updated_at`)
SELECT '绩效MCP服务', '绩效列表、详情、统计和趋势', 'sse', '/mcp/assess', '[]', '{"transport":"sse","module":"assess","headers":[],"timeout":30}', 1, 1, 2, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `eb_chat_app_mcp_services` WHERE `is_default` = 1 AND `service_url` = '/mcp/assess');

INSERT INTO `eb_chat_app_mcp_services` (`name`, `info`, `type`, `service_url`, `headers`, `config_json`, `status`, `is_default`, `sort`, `created_at`, `updated_at`)
SELECT '汇报MCP服务', '工作汇报列表、详情和统计', 'sse', '/mcp/report', '[]', '{"transport":"sse","module":"report","headers":[],"timeout":30}', 1, 1, 3, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `eb_chat_app_mcp_services` WHERE `is_default` = 1 AND `service_url` = '/mcp/report');

INSERT INTO `eb_chat_app_mcp_services` (`name`, `info`, `type`, `service_url`, `headers`, `config_json`, `status`, `is_default`, `sort`, `created_at`, `updated_at`)
SELECT '日程MCP服务', '日程列表和详情', 'sse', '/mcp/schedule', '[]', '{"transport":"sse","module":"schedule","headers":[],"timeout":30}', 1, 1, 4, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `eb_chat_app_mcp_services` WHERE `is_default` = 1 AND `service_url` = '/mcp/schedule');

INSERT INTO `eb_system_menus` (`pid`, `icon`, `menu_name`, `api`, `methods`, `unique_auth`, `menu_path`, `menu_type`, `crud_id`, `uni_path`, `uni_img`, `position`, `paths`, `component`, `level`, `other`, `sort`, `entid`, `type`, `is_show`, `status`, `created_at`, `updated_at`, `deleted_at`, `uniqued`, `parent_uniqued`, `crud_app_id`, `crud_dashboard_id`)
SELECT 0, 'CRMEB-daihuifupinglun-mianxing', '待办', '', '', 'menus6a2a83b36e4ee', '/user/todo', 0, 0, '', '', 0, '', 'user/todo/index', 0, '', 51, 0, 'M', 1, 1, '2026-06-09 09:34:23', '2026-06-11 17:45:23', NULL, '8b1ae69989f9e7d646636ce009f0832e', NULL, 0, 0
WHERE NOT EXISTS (SELECT 1 FROM `eb_system_menus` WHERE `uniqued` = '8b1ae69989f9e7d646636ce009f0832e');

SELECT `id` INTO @todoMenuId FROM `eb_system_menus` WHERE `uniqued` = '8b1ae69989f9e7d646636ce009f0832e' LIMIT 1;

UPDATE `eb_system_role`
SET `rules` = JSON_ARRAY_INSERT(`rules`, '$[0]', CAST(@todoMenuId AS CHAR))
WHERE @todoMenuId IS NOT NULL AND JSON_CONTAINS(`rules`, JSON_QUOTE(CAST(@todoMenuId AS CHAR)), '$') = 0;

UPDATE `eb_enterprise_role`
SET `rules` = JSON_ARRAY_INSERT(`rules`, '$[0]', CAST(@todoMenuId AS CHAR))
WHERE @todoMenuId IS NOT NULL AND JSON_CONTAINS(`rules`, JSON_QUOTE(CAST(@todoMenuId AS CHAR)), '$') = 0;

UPDATE `eb_enterprise_role`
SET `rule_unique` = JSON_ARRAY_INSERT(`rule_unique`, '$[0]', 'menus6a2a83b36e4ee')
WHERE JSON_CONTAINS(`rule_unique`, JSON_QUOTE('menus6a2a83b36e4ee'), '$') = 0;

ALTER TABLE `eb_customer` CHANGE `customer_label` `customer_label` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '客户标签';
UPDATE `eb_customer` SET `customer_label` = NULL, `created_at` = `created_at` WHERE LOWER(TRIM(`customer_label`)) IN ('', 'null', 'nil', 'n/a', '[]', '{}', '""', '"null"', '''null''', '[""]', '[null]', '["null"]') OR TRIM(`customer_label`) = CHAR(39,39);
UPDATE `eb_customer`
SET `customer_label` = CONCAT('["', REPLACE(REPLACE(REPLACE(TRIM(BOTH ',' FROM `customer_label`), '\\', '\\\\'), '"', '\\"'), ',', '","'), '"]'),
    `created_at` = `created_at`
WHERE `customer_label` IS NOT NULL
  AND JSON_VALID(`customer_label`) = 0
  AND `customer_label` LIKE '%,%';
UPDATE `eb_customer`
SET `customer_label` = JSON_ARRAY(`customer_label`),
    `created_at` = `created_at`
WHERE `customer_label` IS NOT NULL
  AND JSON_VALID(`customer_label`) = 0;
UPDATE `eb_customer`
SET `customer_label` = JSON_ARRAY(JSON_UNQUOTE(`customer_label`)),
    `created_at` = `created_at`
WHERE `customer_label` IS NOT NULL
  AND JSON_VALID(`customer_label`) = 1
  AND JSON_TYPE(`customer_label`) <> 'ARRAY';
UPDATE `eb_customer`
SET `customer_label` = NULL,
    `created_at` = `created_at`
WHERE `customer_label` IS NOT NULL
  AND JSON_VALID(`customer_label`) = 1
  AND JSON_LENGTH(`customer_label`) = 0;
ALTER TABLE `eb_customer` CHANGE `customer_label` `customer_label` JSON NULL COMMENT '客户标签';

ALTER TABLE `eb_customer` CHANGE `area_cascade` `area_cascade` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '省市区';
UPDATE `eb_customer` SET `area_cascade` = NULL, `created_at` = `created_at` WHERE LOWER(TRIM(`area_cascade`)) IN ('', 'null', 'nil', 'n/a', '[]', '{}', '""', '"null"', '''null''', '[""]', '[null]', '["null"]') OR TRIM(`area_cascade`) = CHAR(39,39);
UPDATE `eb_customer`
SET `area_cascade` = CONCAT('["', REPLACE(REPLACE(REPLACE(TRIM(BOTH ',' FROM `area_cascade`), '\\', '\\\\'), '"', '\\"'), ',', '","'), '"]'),
    `created_at` = `created_at`
WHERE `area_cascade` IS NOT NULL
  AND JSON_VALID(`area_cascade`) = 0
  AND `area_cascade` LIKE '%,%';
UPDATE `eb_customer`
SET `area_cascade` = JSON_ARRAY(`area_cascade`),
    `created_at` = `created_at`
WHERE `area_cascade` IS NOT NULL
  AND JSON_VALID(`area_cascade`) = 0;
UPDATE `eb_customer`
SET `area_cascade` = JSON_ARRAY(JSON_UNQUOTE(`area_cascade`)),
    `created_at` = `created_at`
WHERE `area_cascade` IS NOT NULL
  AND JSON_VALID(`area_cascade`) = 1
  AND JSON_TYPE(`area_cascade`) <> 'ARRAY';
UPDATE `eb_customer`
SET `area_cascade` = NULL,
    `created_at` = `created_at`
WHERE `area_cascade` IS NOT NULL
  AND JSON_VALID(`area_cascade`) = 1
  AND JSON_LENGTH(`area_cascade`) = 0;
ALTER TABLE `eb_customer` CHANGE `area_cascade` `area_cascade` JSON NULL COMMENT '省市区';
UPDATE `eb_customer`
SET `member` = NULL,
    `created_at` = `created_at`
WHERE `member` IS NOT NULL
  AND (
    JSON_TYPE(`member`) <> 'ARRAY'
    OR JSON_LENGTH(`member`) = 0
    OR (JSON_LENGTH(`member`) = 1 AND JSON_TYPE(JSON_EXTRACT(`member`, '$[0]')) = 'NULL')
  );
-- idx_member 由 DataUpdateHandler 在确认 member 全部为 JSON 标量数组后创建，避免历史脏数据导致升级失败。

ALTER TABLE `eb_customer_clue` CHANGE `customer_label` `customer_label` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '客户标签';
UPDATE `eb_customer_clue` SET `customer_label` = NULL WHERE LOWER(TRIM(`customer_label`)) IN ('', 'null', 'nil', 'n/a', '[]', '{}', '""', '"null"', '''null''', '[""]', '[null]', '["null"]') OR TRIM(`customer_label`) = CHAR(39,39);
UPDATE `eb_customer_clue`
SET `customer_label` = CONCAT('["', REPLACE(REPLACE(REPLACE(TRIM(BOTH ',' FROM `customer_label`), '\\', '\\\\'), '"', '\\"'), ',', '","'), '"]')
WHERE `customer_label` IS NOT NULL
  AND JSON_VALID(`customer_label`) = 0
  AND `customer_label` LIKE '%,%';
UPDATE `eb_customer_clue`
SET `customer_label` = JSON_ARRAY(`customer_label`)
WHERE `customer_label` IS NOT NULL
  AND JSON_VALID(`customer_label`) = 0;
UPDATE `eb_customer_clue`
SET `customer_label` = JSON_ARRAY(JSON_UNQUOTE(`customer_label`))
WHERE `customer_label` IS NOT NULL
  AND JSON_VALID(`customer_label`) = 1
  AND JSON_TYPE(`customer_label`) <> 'ARRAY';
UPDATE `eb_customer_clue`
SET `customer_label` = NULL
WHERE `customer_label` IS NOT NULL
  AND JSON_VALID(`customer_label`) = 1
  AND JSON_LENGTH(`customer_label`) = 0;
ALTER TABLE `eb_customer_clue` CHANGE `customer_label` `customer_label` JSON NULL COMMENT '客户标签';

ALTER TABLE `eb_customer_clue` CHANGE `area_cascade` `area_cascade` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '省市区';
UPDATE `eb_customer_clue` SET `area_cascade` = NULL WHERE LOWER(TRIM(`area_cascade`)) IN ('', 'null', 'nil', 'n/a', '[]', '{}', '""', '"null"', '''null''', '[""]', '[null]', '["null"]') OR TRIM(`area_cascade`) = CHAR(39,39);
UPDATE `eb_customer_clue`
SET `area_cascade` = CONCAT('["', REPLACE(REPLACE(REPLACE(TRIM(BOTH ',' FROM `area_cascade`), '\\', '\\\\'), '"', '\\"'), ',', '","'), '"]')
WHERE `area_cascade` IS NOT NULL
  AND JSON_VALID(`area_cascade`) = 0
  AND `area_cascade` LIKE '%,%';
UPDATE `eb_customer_clue`
SET `area_cascade` = JSON_ARRAY(`area_cascade`)
WHERE `area_cascade` IS NOT NULL
  AND JSON_VALID(`area_cascade`) = 0;
UPDATE `eb_customer_clue`
SET `area_cascade` = JSON_ARRAY(JSON_UNQUOTE(`area_cascade`))
WHERE `area_cascade` IS NOT NULL
  AND JSON_VALID(`area_cascade`) = 1
  AND JSON_TYPE(`area_cascade`) <> 'ARRAY';
UPDATE `eb_customer_clue`
SET `area_cascade` = NULL
WHERE `area_cascade` IS NOT NULL
  AND JSON_VALID(`area_cascade`) = 1
  AND JSON_LENGTH(`area_cascade`) = 0;
ALTER TABLE `eb_customer_clue` CHANGE `area_cascade` `area_cascade` JSON NULL COMMENT '省市区';

ALTER TABLE `eb_contract` CHANGE `contract_category` `contract_category` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '订单分类';
UPDATE `eb_contract` SET `contract_category` = NULL WHERE LOWER(TRIM(`contract_category`)) IN ('', 'null', 'nil', 'n/a', '[]', '{}', '""', '"null"', '''null''', '[""]', '[null]', '["null"]') OR TRIM(`contract_category`) = CHAR(39,39);
UPDATE `eb_contract`
SET `contract_category` = JSON_UNQUOTE(`contract_category`)
WHERE `contract_category` IS NOT NULL
  AND JSON_VALID(`contract_category`) = 1
  AND JSON_TYPE(`contract_category`) = 'STRING'
  AND JSON_VALID(JSON_UNQUOTE(`contract_category`)) = 1
  AND JSON_TYPE(JSON_UNQUOTE(`contract_category`)) = 'ARRAY';
UPDATE `eb_contract`
SET `contract_category` = CONCAT('["', REPLACE(REPLACE(REPLACE(TRIM(BOTH '/' FROM `contract_category`), '\\', '\\\\'), '"', '\\"'), '/', '","'), '"]')
WHERE `contract_category` IS NOT NULL
  AND JSON_VALID(`contract_category`) = 0
  AND `contract_category` LIKE '%/%'
  AND `contract_category` NOT LIKE '%[%'
  AND `contract_category` NOT LIKE '%]%'
  AND `contract_category` NOT LIKE '%{%'
  AND `contract_category` NOT LIKE '%}%';
UPDATE `eb_contract`
SET `contract_category` = CONCAT('["', REPLACE(REPLACE(REPLACE(TRIM(BOTH ',' FROM `contract_category`), '\\', '\\\\'), '"', '\\"'), ',', '","'), '"]')
WHERE `contract_category` IS NOT NULL
  AND JSON_VALID(`contract_category`) = 0
  AND `contract_category` LIKE '%,%';
UPDATE `eb_contract`
SET `contract_category` = JSON_ARRAY(`contract_category`)
WHERE `contract_category` IS NOT NULL
  AND JSON_VALID(`contract_category`) = 0;
UPDATE `eb_contract`
SET `contract_category` = NULL
WHERE `contract_category` IS NOT NULL
  AND JSON_VALID(`contract_category`) = 1
  AND JSON_TYPE(`contract_category`) NOT IN ('ARRAY', 'INTEGER', 'DOUBLE', 'STRING', 'BOOLEAN');
UPDATE `eb_contract`
SET `contract_category` = JSON_ARRAY(JSON_UNQUOTE(`contract_category`))
WHERE `contract_category` IS NOT NULL
  AND JSON_VALID(`contract_category`) = 1
  AND JSON_TYPE(`contract_category`) IN ('INTEGER', 'DOUBLE', 'STRING', 'BOOLEAN');
UPDATE `eb_contract`
SET `contract_category` = NULL
WHERE `contract_category` IS NOT NULL
  AND JSON_VALID(`contract_category`) = 1
  AND JSON_LENGTH(`contract_category`) = 0;
ALTER TABLE `eb_contract` CHANGE `contract_category` `contract_category` JSON NULL COMMENT '订单分类';
UPDATE `eb_customer` SET `customer_status`=0 WHERE `customer_status` = '';
ALTER TABLE `eb_customer` CHANGE `customer_status` `customer_status` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '0' COMMENT '客户状态';
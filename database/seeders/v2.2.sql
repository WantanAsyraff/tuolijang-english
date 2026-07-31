UPDATE `eb_form_data` SET `type`='radio',`input_type`='radio' WHERE `key`='customer_status';
ALTER TABLE `eb_customer_liaison` ADD `userid` VARCHAR(64) NOT NULL AFTER `liaison_job`, ADD `external_userid` VARCHAR(64) NOT NULL AFTER `userid`;
ALTER TABLE `eb_customer_clue` ADD `claim_time` TIMESTAMP NULL COMMENT '领取时间' AFTER `return_num`;
UPDATE `eb_customer` SET `area_cascade` = NULL, `created_at` = `created_at` WHERE JSON_LENGTH(`area_cascade`) = 0;
ALTER TABLE `eb_customer` CHANGE `area_cascade` `area_cascade` JSON NULL COMMENT '省市区';
UPDATE `eb_contract` SET `area_cascade` = NULL WHERE JSON_LENGTH(`area_cascade`) = 0;
ALTER TABLE `eb_contract` CHANGE `area_cascade` `area_cascade` JSON NULL COMMENT '省市区';
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
ALTER TABLE `eb_contract` CHANGE `contract_cate` `contract_cate` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '合同分类copy';
ALTER TABLE `eb_attendance_shift` ADD `types` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '班次类型:0、自定义;2、默认班次;1、休息;' AFTER `uid`;
UPDATE `eb_attendance_shift` SET `types`=1 WHERE `id` = 1;
UPDATE `eb_attendance_shift` SET `types`=2 WHERE `id` = 2;
ALTER TABLE `eb_attendance_clock_record` CHANGE `image` `image` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图片';
INSERT INTO `eb_system_menus` (`icon`, `menu_name`, `api`, `methods`, `unique_auth`, `menu_path`, `menu_type`, `crud_id`, `uni_path`, `uni_img`, `position`, `component`, `level`, `other`, `sort`, `entid`, `type`, `is_show`, `status`, `uniqued`, `parent_uniqued`) VALUES
('', '客户群聊', '', '', 'menus692d3c7ce69b1', '/customer/weChatMass/clientGroupChat', 0, 0, '', '', 0, 'customer/weChatMass/clientGroupChat', 2, '', 2, 0, 'M', 1, 1, '02a13838f52a788c4312acfaafea0e21', '5b539eddc3a6e8c847278cd453d0fd38'),
('', '朋友圈', '', '', 'menus692d3c7fa9a9d', '/customer/weChatMass/wechatMoments', 0, 0, '', '', 0, 'customer/weChatMass/wechatMoments', 2, '', 1, 0, 'M', 1, 1, '20db8be886ac466d2fd6ee0dc0ae65e1', '5b539eddc3a6e8c847278cd453d0fd38'),
('', '字典设置', '', '', 'menus692d3ccc06577', '/develop/dictionary/optionSetting', 0, 0, '', '', 0, 'develop/dictionary/optionSetting', 2, '', 0, 0, 'M', 0, 1, '3a56ecb9279d42ea32ee3a8f35a996d2', '8aea85af28c0be542d9c7cd24331bbc3'),
('iconic_shujuzidian', '群发工具', '', '', 'menus692d3c63c8fab', '/customer/weChatMass', 0, 0, '', '', 0, '', 1, '', 2, 0, 'M', 1, 1, '5b539eddc3a6e8c847278cd453d0fd38', '91ec1f9324753048c0096d036a694f86'),
('', '客户消息', '', '', 'menus692d3c7a11240', '/customer/weChatMass/clientMass', 0, 0, '', '', 0, 'customer/weChatMass/clientMass', 2, '', 3, 0, 'M', 1, 1, '807c6bc66f950137f2771f1be40ed218', '5b539eddc3a6e8c847278cd453d0fd38'),
('', '添加群发页面', '', '', 'menus692d3c847ab6f', '/customer/weChatMass/addGroupPosting', 0, 0, '', '', 0, 'customer/weChatMass/addGroupPosting', 2, '', 0, 0, 'M', 0, 1, 'abcd75e7290800693bb4e16fe80233b0', '5b539eddc3a6e8c847278cd453d0fd38'),
('iconkaoheliucheng', '快捷回复', '', '', 'menus692d3c88c34db', 'customer/quickReply', 0, 0, '/pages/customer/quickReply/index', 'http://dev.oa.crmeb.net/uploads/attach/2025/10/f121d202510071804079580.jpg', 0, 'customer/quickReply/index', 1, '', 1, 0, 'M', 1, 1, 'ac46f415b652f3f2ca373ff63e4bf7fc', '91ec1f9324753048c0096d036a694f86'),
('', '群发素材', '', '', 'menus692d3c8210d05', '/customer/weChatMass/mass', 0, 0, '', '', 0, 'customer/weChatMass/mass', 2, '', 0, 0, 'M', 1, 1, 'ffbb4d9704451366fa51921fe8080239', '5b539eddc3a6e8c847278cd453d0fd38');
UPDATE eb_system_menus child INNER JOIN eb_system_menus parent ON child.parent_uniqued = parent.uniqued SET child.pid = parent.id WHERE child.parent_uniqued IS NOT NULL;

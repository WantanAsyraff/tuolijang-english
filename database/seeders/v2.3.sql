ALTER TABLE `eb_customer` CHANGE `b37a3f16` `customer_tel` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '联系电话';
UPDATE `eb_form_data` SET `key`='customer_tel' WHERE `key`='b37a3f16';
INSERT INTO `eb_dict_data` (`id`, `name`, `value`, `pid`, `type_id`, `type_name`, `level`, `color`, `sort`, `status`, `is_default`, `mark`, `created_at`, `updated_at`) VALUES (4051, '服务器续费', '111', '110', 9, 'contract_type', 2, '', 0, 1, 0, NULL, NULL, NULL);
UPDATE `eb_attendance_apply_record` SET `others` = NULL WHERE `others` = '';
ALTER TABLE `eb_customer_record` ADD INDEX( `eid`, `link_type`);

INSERT INTO `eb_system_menus` (`pid`, `icon`, `menu_name`, `api`, `methods`, `unique_auth`, `menu_path`, `menu_type`, `crud_id`, `uni_path`, `uni_img`, `position`, `paths`, `component`, `level`, `other`, `sort`, `entid`, `type`, `is_show`, `status`, `created_at`, `updated_at`, `deleted_at`, `uniqued`, `parent_uniqued`, `crud_app_id`, `crud_dashboard_id`) VALUES (42, 'iconwotijiaode', '合同签约', '', '', 'menus699fa7caeca5c', '/customer/signing', 0, 0, '', '', 0, '42', 'customer/signing/index', 1, '', 778, 0, 'M', 1, 1, '2025-12-18 15:02:14', '2026-02-26 09:54:19', NULL, '721bf64d598795daa55e275c16d64dd6', '91ec1f9324753048c0096d036a694f86', 0, 0);

UPDATE `eb_system_menus` SET `menu_name` = '合同订单' WHERE `uniqued` = 'b244c0995f52181a7b82e45f8d43dbaa';
UPDATE `eb_system_menus` SET `menu_name` = '应用管理' WHERE `uniqued` = 'a62d417ac96c020f7804ad7bf3834b07';
UPDATE `eb_system_menus` SET `menu_name` = '订单收支' WHERE `uniqued` = '6fa5fa5bcb3b1d190790b907081f6dbb';

UPDATE eb_form_data a JOIN eb_form_cate b ON a.cate_id = b.id SET a.key_name = replace(key_name,'合同','订单'), a.placeholder = replace(placeholder,'合同','订单'), a.updated_at = NOW() WHERE b.types = 2 AND b.deleted_at IS NULL AND b.STATUS = 1 AND a.deleted_at IS NULL AND a.STATUS = 1 AND a.key_name LIKE '%合同%';

UPDATE eb_dict_type SET name = '订单分类' WHERE ident = 'contract_type';
UPDATE eb_dict_type SET name = '订单状态' WHERE ident = 'contract_status';

DELETE from `eb_form_data` WHERE `key` = 'customer_status' AND `dict_ident` = 'customer_status';
DELETE from `eb_form_data` WHERE `key` = 'status' AND `dict_ident` = 'odds_status';
DELETE from `eb_form_data` WHERE `key` = 'contract_status' AND `dict_ident` = 'contract_status';
DELETE from `eb_form_data` WHERE `key` = 'signing_status' AND `dict_ident` = 'signing_status';
UPDATE `eb_form_data` SET `key` = 'odds_customer' WHERE `key` = 'eid';

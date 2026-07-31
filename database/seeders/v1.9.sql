DELETE FROM `eb_system_menus` WHERE `unique_auth`='menus6760ceb7470b4';
DELETE FROM `eb_system_menus` WHERE `unique_auth`='menus676e0b7f1f08e';
INSERT INTO `eb_system_menus` (`pid`, `icon`, `menu_name`, `api`, `methods`, `unique_auth`, `menu_path`, `menu_type`, `crud_id`, `uni_path`, `uni_img`, `position`, `path`, `component`, `level`, `other`, `sort`, `entid`, `type`, `is_show`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(315, 'iconbangong-xiaoxizhongxin-cebian', '消息中心', '', '', 'menus6760ce9f69cd5', '/setting/enterprise/new', 0, 0, '', '', 0, '315', '', 1, '', 0, 0, 'M', 1, 1, '2024-12-16 17:06:39', '2024-12-20 00:14:07', NULL);
SELECT @foreignId := LAST_INSERT_ID();
INSERT INTO `eb_system_menus` (`pid`, `icon`, `menu_name`, `api`, `methods`, `unique_auth`, `menu_path`, `menu_type`, `crud_id`, `uni_path`, `uni_img`, `position`, `path`, `component`, `level`, `other`, `sort`, `entid`, `type`, `is_show`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(@foreignId, '', '消息设置', '', '', 'menus6760ceb7470b4', '/setting/enterprise/news/index', 0, 0, '', '', 0, CONCAT('315/', @foreignId), 'setting/enterprise/news/index', 2, '{"menu_name_en":"Message settings"}', 0, 0, 'M', 1, 1, NULL, '2024-12-20 00:14:07', NULL),
(@foreignId, '', '推送记录', '', '', 'menus6760cf6e86b25', '/setting/enterprise/news/record', 0, 0, '', '', 0,CONCAT('315/', @foreignId), 'setting/enterprise/news/record', 2, '', 0, 0, 'M', 1, 1, '2024-12-16 17:10:06', '2024-12-20 00:14:07', NULL),
(334, '', '防火墙', '', '', 'menus676126726926c', '/setting/system/firewall', 0, 0, '', '', 0, '315/334', 'setting/system/firewall', 2, '', 0, 0, 'M', 1, 1, '2024-12-16 23:20:37', '2024-12-20 00:14:07', NULL);
ALTER TABLE `eb_dict_data` ADD `color` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '标识颜色' AFTER `level`;

ALTER TABLE `blogs` ADD COLUMN `auto_version` varchar(20) NULL;
UPDATE `blogs` SET `auto_version` = '4.90.7' WHERE `id` = '214';
UPDATE `blogs` SET `auto_version` = '4.90.7' WHERE `id` = '215';
ALTER TABLE `users_rights` ADD COLUMN `xkis_ActivityLog` tinyint(1) NOT NULL DEFAULT 0;
UPDATE `blogs` SET `auto_version` = '4.90.7' WHERE `id` = '214';
UPDATE `blogs` SET `auto_version` = '4.90.7' WHERE `id` = '215';
UPDATE `users_rights` SET `xkis_ActivityLog` = '1' WHERE `id` = '1';

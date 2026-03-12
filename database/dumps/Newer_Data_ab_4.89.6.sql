ALTER TABLE `users_rights` ADD COLUMN `xkis_ActivityLog` tinyint(1) NOT NULL DEFAULT 0;
UPDATE `users_rights` SET `xkis_ActivityLog` = '1' WHERE `id` = '1';

ALTER TABLE `users_rights` ADD COLUMN `xkis_UnusedImages` tinyint(1) NOT NULL DEFAULT 0;
UPDATE `users_rights` SET `xkis_UnusedImages` = '1' WHERE `id` = '1';

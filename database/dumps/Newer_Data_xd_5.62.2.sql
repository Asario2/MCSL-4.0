ALTER TABLE `privacy` ADD COLUMN `created_at` datetime NULL;
ALTER TABLE `privacy` ADD COLUMN `updated_at` datetime NULL;
ALTER TABLE `private_messages_text` ADD COLUMN `name` varchar(255) NULL;
UPDATE `private_messages_text` SET `name` = '123asda' WHERE `id` = '1277';
ALTER TABLE `users_rights` ADD COLUMN `xkis_FontographerToolz` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `users_rights` ADD COLUMN `xkis_HackingLog` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `xgen_activitylog` ADD COLUMN `dom` varchar(50) NULL;

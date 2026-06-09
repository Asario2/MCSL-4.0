ALTER TABLE `private_messages_text` ADD COLUMN `name` varchar(255) NULL;
UPDATE `private_messages_text` SET `name` = '123asda' WHERE `id` = '1277';
ALTER TABLE `xgen_activitylog` ADD COLUMN `dom` varchar(50) NULL;


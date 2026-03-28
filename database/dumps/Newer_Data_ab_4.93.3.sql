ALTER TABLE `blogs` ADD COLUMN `auto_version` varchar(20) NULL;
UPDATE `blogs` SET `auto_version` = '4.90.7' WHERE `id` = '215';

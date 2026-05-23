ALTER TABLE `users` ADD COLUMN `xis_aiImage` tinyint(2) NULL DEFAULT 0;
ALTER TABLE `users_rights` ADD COLUMN `xkis_UnusedImages` tinyint(1) NOT NULL DEFAULT 0;

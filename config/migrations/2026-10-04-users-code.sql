# Store the invitation code used to sign up
ALTER TABLE `bm_users` ADD COLUMN `code` varchar(255) DEFAULT NULL;

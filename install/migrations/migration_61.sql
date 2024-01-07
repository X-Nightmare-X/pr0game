-- raise diplomatic text size
ALTER TABLE `%PREFIX%diplo` CHANGE COLUMN `accept_text` `accept_text` text CHARACTER SET utf8mb4;
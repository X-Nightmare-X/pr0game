-- Create Table users_comments
CREATE TABLE `%PREFIX%users_comments` (
    `id` int(11) unsigned NOT NULL,
    `comment` varchar(255) NOT NULL DEFAULT '',
    `created_at` int(11) NOT NULL,
    KEY `comment` (`id`,`comment`,`created_at`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
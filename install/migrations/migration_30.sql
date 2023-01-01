-- add config for recaptcha

ALTER TABLE `%PREFIX%config` ADD `recaptchaPrivKey` varchar(255) DEFAULT '';
ALTER TABLE `%PREFIX%config` ADD `recaptchaPubKey` varchar(255) DEFAULT '';
CREATE TABLE `%PREFIX%recaptcha` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `userId`    int(11) unsigned NOT NULL,
    `success`   tinyint(1) NOT NULL DEFAULT '0',
    `time`      int(11) NOT NULL DEFAULT '0',
    `score`     DECIMAL(2,1) NOT NULL DEFAULT '0.0',
    `action`    varchar(255) DEFAULT '',
    `url`       varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
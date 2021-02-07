CREATE TABLE `roles` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `created_at_ts` int(11) NOT NULL DEFAULT '0',
    `name` varchar(100) NOT NULL DEFAULT '',
    `designation` varchar(100) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `designation` (`designation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `roles` (`id`, `name`, `designation`) VALUES (1, 'Администраторы', 'ADMINS');

CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(100) DEFAULT NULL,
    `passw` varchar(50) DEFAULT NULL,
    `name` varchar(100) DEFAULT NULL,
    `first_name` varchar(100) DEFAULT NULL,
    `last_name` varchar(100) DEFAULT NULL,
    `photo` varchar(100) NOT NULL DEFAULT '',
    `birthday` varchar(20) DEFAULT NULL,
    `phone` varchar(100) DEFAULT NULL,
    `city` varchar(100) DEFAULT NULL,
    `address` varchar(250) DEFAULT NULL,
    `company` varchar(200) DEFAULT NULL,
    `comment` mediumtext,
    `confirm` smallint(6) NOT NULL DEFAULT '0',
    `confirm_code` varchar(50) DEFAULT '',
    `provider` varchar(100) NOT NULL DEFAULT '',
    `provider_uid` varchar(255) DEFAULT '',
    `profile_url` varchar(1000) DEFAULT '',
    `created_at_ts` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    KEY `confirm_code` (`confirm_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users_roles` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `created_at_ts` int(11) NOT NULL DEFAULT '0',
    `user_id` int(11) NOT NULL,
    `role_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_id_role_id` (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sessions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `created_at_ts` int(11) NOT NULL DEFAULT '0',
    `user_id` int(10) unsigned NOT NULL,
    `session` varchar(64) NOT NULL DEFAULT '',
    `hostname` varchar(128) NOT NULL DEFAULT '',
    `timestamp` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `session` (`session`),
    KEY `timestamp` (`timestamp`),
    KEY `uid` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

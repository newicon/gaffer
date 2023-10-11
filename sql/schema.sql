CREATE TABLE `page` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `stub` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` varchar(255) NOT NULL,
    `content` text,
    `active` tinyint(1) DEFAULT '0',
    `redirect` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `passwordHash` varchar(255) NOT NULL,
    `status` enum('active','inactive') DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

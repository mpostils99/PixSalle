SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE IF NOT EXISTS `pixsalle`;
USE `pixsalle`;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users`
(
    `id`        INT                                                     NOT NULL AUTO_INCREMENT,
    `userName`  VARCHAR(255)                                                    , 
    `email`     VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `password`  VARCHAR(255)                                            NOT NULL,
    `phone`     VARCHAR(255)                                                    ,
    `pictureName` VARCHAR(255)                                                    ,
    `money`     INT                                                             ,
    `createdAt` DATETIME                                                NOT NULL,
    `updatedAt` DATETIME                                                NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `userMembership`
(
    `user_id`        INT                                                NOT NULL,
    `type`     VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    PRIMARY KEY (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES users (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `portfolio`
(
    `id`     INT                                                     NOT NULL AUTO_INCREMENT,
    `title`           VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `user_id`        INT                                                NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES users (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `album`
(
    `id`     INT                                                     NOT NULL AUTO_INCREMENT,
    `name`           VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `portfolio_id`        INT                                                NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`portfolio_id`) REFERENCES portfolio (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pictures`
(
    `id`     INT                                                     NOT NULL AUTO_INCREMENT,
    `url`           VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `album_id`        INT                                                NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`album_id`) REFERENCES album (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `posts`
(
    `id`     INT                                                     NOT NULL AUTO_INCREMENT,
    `title`           VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `content`           VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `userId`        INT                                                NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`userId`) REFERENCES users (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
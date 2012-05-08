-- scripts/sql/schema.mysql.sql
--
-- You will need load your database schema with this SQL.
DROP DATABASE IF EXISTS tmtwdev_hwgen;
CREATE DATABASE tmtwdev_hwgen;
USE tmtwdev_hwgen;

CREATE TABLE `users` (
    `id`                INTEGER         NOT NULL AUTO_INCREMENT,
    `username`          VARCHAR(255)    NOT NULL,
    `password`          VARCHAR(255)    NOT NULL,
    `firstname`         VARCHAR(255)    NOT NULL,
    `lastname`          VARCHAR(255)    NOT NULL,
    `email`             VARCHAR(255)    NOT NULL,
    `loginAttempts`     INTEGER         NOT NULL DEFAULT 0,
    `frozenUntil`       DATETIME        		 DEFAULT NULL,
    `locked`            TINYINT        	NOT NULL DEFAULT FALSE,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- Used to store php sessions in the database for better performance and scalability
CREATE TABLE `sessions` (
    `id`                CHAR(32)        NOT NULL,
    `modified`          INTEGER         NOT NULL,
    `lifetime`          INTEGER         NOT NULL,
    `data`              TEXT,
    PRIMARY KEY (`id`)
);

CREATE TABLE `roles` (
    `id`                INTEGER         NOT NULL auto_increment,
    `name`              VARCHAR(255)    NOT NULL,
    CONSTRAINT PK_ROLES PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `privileges` (
    `id`                INTEGER         NOT NULL auto_increment,
    `roleId`            INTEGER         NOT NULL,
    `module`            VARCHAR(255)    NOT NULL,
    `controller`        VARCHAR(255)    NOT NULL,
    `action`            VARCHAR(255)    NOT NULL,
    CONSTRAINT PK_PRIVILEGES PRIMARY KEY (`id`),
    CONSTRAINT FK_privileges_roles FOREIGN KEY (`roleId`) REFERENCES `roles` (`id`)  ON DELETE CASCADE
) ENGINE=MyISAM;

CREATE TABLE `user_roles` (
    `userId`            INTEGER         NOT NULL,
    `roleId`            INTEGER         NOT NULL,
    CONSTRAINT PK_userRoles PRIMARY KEY (`userId`, `roleId`),
    CONSTRAINT FK_userRoles_users FOREIGN KEY (`userId`) REFERENCES `users` (`id`)  ON DELETE CASCADE,
    CONSTRAINT FK_userRoles_roles FOREIGN KEY (`roleId`) REFERENCES `roles` (`id`)  ON DELETE CASCADE
) ENGINE=MyISAM;
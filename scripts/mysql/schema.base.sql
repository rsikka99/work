-- scripts/sql/schema.mysql.sql
--
-- You will need load your database schema with this SQL.
SET storage_engine=InnoDB;

CREATE TABLE `users` (
    `id`                	INTEGER         NOT NULL AUTO_INCREMENT,
    `username`          	VARCHAR(255)    NOT NULL,
    `password`          	VARCHAR(255)    NOT NULL,
    `firstname`         	VARCHAR(255)    NOT NULL,
    `lastname`          	VARCHAR(255)    NOT NULL,
    `email`             	VARCHAR(255)    NOT NULL,
    `loginAttempts`     	INTEGER         NOT NULL DEFAULT 0,
    `frozenUntil`       	DATETIME        		 DEFAULT NULL,
    `locked`            	TINYINT        	NOT NULL DEFAULT FALSE,
    `eulaAccepted`      	DATETIME        		 DEFAULT NULL,
    `resetPassword`     	TINYINT        	NOT NULL DEFAULT FALSE,
    `passwordResetRequest`  DATETIME        		 DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Used to store php sessions in the database for better performance and scalability
CREATE TABLE `sessions` (
    `id`                CHAR(32)        NOT NULL,
    `modified`          INTEGER         NOT NULL,
    `lifetime`          INTEGER         NOT NULL,
    `data`              TEXT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `roles` (
    `id`                INTEGER         NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(255)    NOT NULL,
    CONSTRAINT PK_ROLES PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `privileges` (
    `id`                INTEGER         NOT NULL AUTO_INCREMENT,
    `roleId`            INTEGER         NOT NULL,
    `module`            VARCHAR(255)    NOT NULL,
    `controller`        VARCHAR(255)    NOT NULL,
    `action`            VARCHAR(255)    NOT NULL,
    CONSTRAINT PK_PRIVILEGES PRIMARY KEY (`id`),
    CONSTRAINT FK_privileges_roles FOREIGN KEY (`roleId`) REFERENCES `roles` (`id`)  ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `user_roles` (
    `userId`            INTEGER         NOT NULL,
    `roleId`            INTEGER         NOT NULL,
    CONSTRAINT PK_userRoles PRIMARY KEY (`userId`, `roleId`),
    CONSTRAINT FK_userRoles_users FOREIGN KEY (`userId`) REFERENCES `users` (`id`)  ON DELETE CASCADE,
    CONSTRAINT FK_userRoles_roles FOREIGN KEY (`roleId`) REFERENCES `roles` (`id`)  ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE `log_types` (
	`id`                INTEGER         NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(255)    NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `logs` (
	`id`                INTEGER         NOT NULL AUTO_INCREMENT,
    `logTypeId`         INTEGER 	    NOT NULL DEFAULT 1,
    `priority`          INTEGER 	    NOT NULL DEFAULT 6,
    `message`           TEXT   	 		NOT NULL,
    `timestamp`         TIMESTAMP		NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `userId`         	INTEGER					 DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`logTypeId`) REFERENCES `log_types` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;
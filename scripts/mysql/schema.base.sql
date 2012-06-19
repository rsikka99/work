SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `manufacturers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `manufacturers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `fullname` VARCHAR(255) NOT NULL ,
  `displayname` VARCHAR(255) NOT NULL ,
  `isDeleted` TINYINT(4) NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `fullname` (`fullname` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `clients`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `clients` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `address` VARCHAR(255) NOT NULL ,
  `phoneNumber` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(255) NOT NULL ,
  `password` VARCHAR(255) NOT NULL ,
  `firstname` VARCHAR(255) NOT NULL ,
  `lastname` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `loginAttempts` INT(11) NOT NULL DEFAULT '0' ,
  `frozenUntil` DATETIME NULL DEFAULT NULL ,
  `locked` TINYINT(4) NOT NULL DEFAULT '0' ,
  `eulaAccepted` DATETIME NULL DEFAULT NULL ,
  `resetPasswordOnNextLogin` TINYINT(4) NOT NULL DEFAULT '0' ,
  `passwordResetRequest` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `username` (`username` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COMMENT = 'The users table stores basic information on a user' ;


-- -----------------------------------------------------
-- Table `log_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `log_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `logs`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `logTypeId` INT(11) NOT NULL DEFAULT '1' ,
  `priority` INT(11) NOT NULL DEFAULT '6' ,
  `message` TEXT NOT NULL ,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `userId` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `logs_ibfk_1` (`logTypeId` ASC) ,
  CONSTRAINT `logs_ibfk_1`
    FOREIGN KEY (`logTypeId` )
    REFERENCES `log_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 370
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `roles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `privileges`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `privileges` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `roleId` INT(11) NOT NULL ,
  `module` VARCHAR(255) NOT NULL ,
  `controller` VARCHAR(255) NOT NULL ,
  `action` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `privileges_ibfk_!` (`roleId` ASC) ,
  CONSTRAINT `privileges_ibfk_!`
    FOREIGN KEY (`roleId` )
    REFERENCES `roles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sessions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sessions` (
  `id` CHAR(32) NOT NULL ,
  `modified` INT(11) NOT NULL ,
  `lifetime` INT(11) NOT NULL ,
  `data` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `user_roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `user_roles` (
  `userId` INT(11) NOT NULL ,
  `roleId` INT(11) NOT NULL ,
  PRIMARY KEY (`userId`) ,
  INDEX `FK_userRoles_roles` (`roleId` ASC) ,
  CONSTRAINT `FK_userRoles_users`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE CASCADE,
  CONSTRAINT `FK_userRoles_roles`
    FOREIGN KEY (`roleId` )
    REFERENCES `roles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

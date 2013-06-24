-- -----------------------------------------------------
-- Table `device_swap_reason_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `device_swap_reason_categories` (
    `id` INT(11) NOT NULL AUTO_INCREMENT ,
    `name` VARCHAR(255) NOT NULL ,
    PRIMARY KEY (`id`) );


-- -----------------------------------------------------
-- Table `device_swap_reasons`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `device_swap_reasons` (
    `id` INT(11) NOT NULL AUTO_INCREMENT ,
    `dealerId` INT(11) NOT NULL ,
    `deviceSwapReasonCategoryId` INT(11) NOT NULL ,
    `reason` VARCHAR(255) NOT NULL ,
    PRIMARY KEY (`id`) ,
    INDEX `dealer_swap_reasons_ibfk1_idx` (`dealerId` ASC) ,
    INDEX `dealer_swap_reasons_ibfk2_idx` (`deviceSwapReasonCategoryId` ASC) ,
    CONSTRAINT `dealer_swap_reasons_ibfk1`
    FOREIGN KEY (`dealerId` )
    REFERENCES `dealers` (`id` )
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `dealer_swap_reasons_ibfk2`
    FOREIGN KEY (`deviceSwapReasonCategoryId` )
    REFERENCES `device_swap_reason_categories` (`id` )
        ON DELETE NO ACTION
        ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `device_swap_reason_defaults`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `device_swap_reason_defaults` (
    `deviceSwapReasonCategoryId` INT(11) NOT NULL ,
    `dealerId` INT NOT NULL ,
    `deviceSwapReasonId` INT(11) NOT NULL ,
    PRIMARY KEY (`deviceSwapReasonCategoryId`, `dealerId`) ,
    INDEX `device_swap_defaults_ibk2_idx` (`deviceSwapReasonId` ASC) ,
    INDEX `device_swap_reason_category_defaults_ibkf1_idx` (`dealerId` ASC) ,
    CONSTRAINT `device_swap_reason_defaults_ibfk2`
    FOREIGN KEY (`deviceSwapReasonId` )
    REFERENCES `device_swap_reasons` (`id` )
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `device_swap_reason_defaults_ibkf1`
    FOREIGN KEY (`deviceSwapReasonCategoryId` )
    REFERENCES `device_swap_reason_categories` (`id` )
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `device_swap_reason_defaults_ibkf3`
    FOREIGN KEY (`dealerId` )
    REFERENCES `dealers` (`id` )
        ON DELETE NO ACTION
        ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `device_instance_device_swap_reasons`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `device_instance_device_swap_reasons` (
    `hardwareOptimizationId` INT(11) NOT NULL ,
    `deviceInstanceId` INT(11) NOT NULL ,
    `deviceSwapReasonId` INT(11) NOT NULL ,
    PRIMARY KEY (`hardwareOptimizationId`, `deviceInstanceId`) ,
    INDEX `device_instance_device_swap_reasons_ibkf1_idx` (`hardwareOptimizationId` ASC) ,
    INDEX `device_instance_device_swap_reasons_ibkf2_idx` (`deviceInstanceId` ASC) ,
    INDEX `device_instance_device_swap_reasons_ibkf3_idx` (`deviceSwapReasonId` ASC) ,
    CONSTRAINT `device_instance_device_swap_reasons_ibkf1`
    FOREIGN KEY (`hardwareOptimizationId` )
    REFERENCES `hardware_optimizations` (`id` )
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `device_instance_device_swap_reasons_ibkf2`
    FOREIGN KEY (`deviceInstanceId` )
    REFERENCES `device_instances` (`id` )
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `device_instance_device_swap_reasons_ibkf3`
    FOREIGN KEY (`deviceSwapReasonId` )
    REFERENCES `device_swap_reasons` (`id` )
        ON DELETE CASCADE
        ON UPDATE CASCADE);

INSERT INTO `device_swap_reason_categories` (`id`, `name`) VALUES
(1, 'Flagged Devices'),
(2, 'Device Has Replacement Device');

INSERT INTO `device_swap_reasons` (`id`, `dealerId`, `deviceSwapReasonCategoryId`, `reason`) VALUES
(1, 1, 1, 'Device not consistent with MPS program.  AMPV is significant.'),
(2, 1, 2, 'Device has a high cost per page.'),
(3, 2, 1, 'Device not consistent with MPS program.  AMPV is significant.'),
(4, 2, 2, 'Device has a high cost per page.');

INSERT INTO device_swap_reason_defaults (`deviceSwapReasonCategoryId`, `dealerId`, `deviceSwapReasonId`) VALUES
(1, 1, 1),
(2, 1, 2),
(1, 2, 3),
(2, 2, 4);
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `clients`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `clients` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `accountNumber` VARCHAR(255) NOT NULL ,
  `companyName` VARCHAR(255) NOT NULL ,
  `legalName` VARCHAR(255) NULL ,
  `employeeCount` INT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


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
  INDEX `logs_ibfk_1_idx` (`logTypeId` ASC) ,
  CONSTRAINT `logs_ibfk_1`
    FOREIGN KEY (`logTypeId` )
    REFERENCES `log_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 410
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `manufacturers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `manufacturers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `fullname` VARCHAR(255) NOT NULL ,
  `displayname` VARCHAR(255) NOT NULL ,
  `isDeleted` TINYINT(4) NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `fullname` (`fullname` ASC) ,
  INDEX `displayname` (`displayname` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 27
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_rms_providers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_rms_providers` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pgen_rms_uploads`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_rms_uploads` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `clientId` INT NOT NULL ,
  `rmsProviderId` INT NOT NULL ,
  `fileName` VARCHAR(255) NOT NULL ,
  `validRowCount` INT NOT NULL ,
  `invalidRowCount` INT NOT NULL ,
  `uploadDate` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `rms_uploads_ibfk_1_idx` (`clientId` ASC) ,
  INDEX `rms_uploads_ibfk_2_idx` (`rmsProviderId` ASC) ,
  CONSTRAINT `rms_uploads_ibfk_1`
    FOREIGN KEY (`clientId` )
    REFERENCES `clients` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `rms_uploads_ibfk_2`
    FOREIGN KEY (`rmsProviderId` )
    REFERENCES `pgen_rms_providers` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


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
AUTO_INCREMENT = 13
DEFAULT CHARACTER SET = latin1
COMMENT = 'The users table stores basic information on a user';


-- -----------------------------------------------------
-- Table `pgen_rms_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_rms_devices` (
  `rmsProviderId` INT(11) NOT NULL ,
  `rmsModelId` INT(11) NOT NULL ,
  `manufacturer` VARCHAR(255) NULL ,
  `modelName` VARCHAR(255) NULL ,
  `dateCreated` DATETIME NOT NULL ,
  `userId` INT(11) NULL ,
  PRIMARY KEY (`rmsProviderId`, `rmsModelId`) ,
  INDEX `rms_devices_ibfk_1_idx` (`userId` ASC) ,
  INDEX `rms_devices_ibfk_2_idx` (`rmsProviderId` ASC) ,
  CONSTRAINT `rms_devices_ibfk_1`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `rms_devices_ibfk_2`
    FOREIGN KEY (`rmsProviderId` )
    REFERENCES `pgen_rms_providers` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 103
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_rms_upload_rows`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_rms_upload_rows` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `rmsProviderId` INT(11) NOT NULL ,
  `rmsModelId` INT(11) NULL ,
  `fullDeviceName` VARCHAR(255) NOT NULL ,
  `hasCompleteInformation` TINYINT(4) NOT NULL DEFAULT 0 ,
  `modelName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `manufacturer` VARCHAR(255) NOT NULL DEFAULT '' ,
  `manufacturerId` INT(11) NULL ,
  `cost` DOUBLE NULL ,
  `dutyCycle` INT(11) NULL ,
  `isColor` TINYINT(4) NOT NULL DEFAULT 0 ,
  `isCopier` TINYINT(4) NOT NULL DEFAULT 0 ,
  `isFax` TINYINT(4) NOT NULL DEFAULT 0 ,
  `isLeased` TINYINT(4) NOT NULL DEFAULT 0 ,
  `isDuplex` TINYINT(4) NULL ,
  `isScanner` TINYINT(4) NOT NULL DEFAULT 0 ,
  `launchDate` DATETIME NULL ,
  `leasedTonerYield` INT(11) NULL ,
  `ppmBlack` DOUBLE NULL ,
  `ppmColor` DOUBLE NULL ,
  `serviceCostPerPage` DOUBLE NULL ,
  `tonerConfigId` INT(11) NOT NULL DEFAULT 1 ,
  `wattsPowerNormal` DOUBLE NULL ,
  `wattsPowerIdle` DOUBLE NULL ,
  `oemBlackTonerSku` VARCHAR(255) NULL ,
  `oemBlackTonerYield` INT(11) NULL ,
  `oemBlackTonerCost` DOUBLE NULL ,
  `oemCyanTonerSku` VARCHAR(255) NULL ,
  `oemCyanTonerYield` INT(11) NULL ,
  `oemCyanTonerCost` DOUBLE NULL ,
  `oemMagentaTonerSku` VARCHAR(255) NULL ,
  `oemMagentaTonerYield` INT(11) NULL ,
  `oemMagentaTonerCost` DOUBLE NULL ,
  `oemYellowTonerSku` VARCHAR(255) NULL ,
  `oemYellowTonerYield` INT(11) NULL ,
  `oemYellowTonerCost` DOUBLE NULL ,
  `oemThreeColorTonerSku` VARCHAR(255) NULL ,
  `oemThreeColorTonerYield` INT(11) NULL ,
  `oemThreeColorTonerCost` DOUBLE NULL ,
  `oemFourColorTonerSku` VARCHAR(255) NULL ,
  `oemFourColorTonerYield` INT(11) NULL ,
  `oemFourColorTonerCost` DOUBLE NULL ,
  `compBlackTonerSku` VARCHAR(255) NULL ,
  `compBlackTonerYield` INT(11) NULL ,
  `compBlackTonerCost` DOUBLE NULL ,
  `compCyanTonerSku` VARCHAR(255) NULL ,
  `compCyanTonerYield` INT(11) NULL ,
  `compCyanTonerCost` DOUBLE NULL ,
  `compMagentaTonerSku` VARCHAR(255) NULL ,
  `compMagentaTonerYield` INT(11) NULL ,
  `compMagentaTonerCost` DOUBLE NULL ,
  `compYellowTonerSku` VARCHAR(255) NULL ,
  `compYellowTonerYield` INT(11) NULL ,
  `compYellowTonerCost` DOUBLE NULL ,
  `compThreeColorTonerSku` VARCHAR(255) NULL ,
  `compThreeColorTonerYield` INT(11) NULL ,
  `compThreeColorTonerCost` DOUBLE NULL ,
  `compFourColorTonerSku` VARCHAR(255) NULL ,
  `compFourColorTonerYield` INT(11) NULL ,
  `compFourColorTonerCost` DOUBLE NULL ,
  `tonerLevelBlack` VARCHAR(255) NULL ,
  `tonerLevelCyan` VARCHAR(255) NULL ,
  `tonerLevelMagenta` VARCHAR(255) NULL ,
  `tonerLevelYellow` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `pgen_rms_upload_rows_ibfk_1_idx` (`rmsProviderId` ASC) ,
  INDEX `pgen_rms_upload_rows_ibfk_2_idx` (`rmsProviderId` ASC, `rmsModelId` ASC) ,
  INDEX `pgen_rms_upload_rows_ibfk_3_idx` (`manufacturerId` ASC) ,
  CONSTRAINT `pgen_rms_upload_rows_ibfk_1`
    FOREIGN KEY (`rmsProviderId` )
    REFERENCES `pgen_rms_providers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `pgen_rms_upload_rows_ibfk_2`
    FOREIGN KEY (`rmsProviderId` , `rmsModelId` )
    REFERENCES `pgen_rms_devices` (`rmsProviderId` , `rmsModelId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `pgen_rms_upload_rows_ibfk_3`
    FOREIGN KEY (`manufacturerId` )
    REFERENCES `manufacturers` (`id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 477
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_device_instances`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_device_instances` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `rmsUploadId` INT(11) NOT NULL ,
  `rmsUploadRowId` INT(11) NOT NULL ,
  `ipAddress` VARCHAR(255) NOT NULL DEFAULT '' ,
  `isExcluded` TINYINT(4) NOT NULL DEFAULT 0 ,
  `mpsDiscoveryDate` DATETIME NULL ,
  `reportsTonerLevels` TINYINT(4) NOT NULL DEFAULT 0 ,
  `serialNumber` VARCHAR(255) NOT NULL DEFAULT '' ,
  `useUserData` TINYINT(4) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `rmsUploadRowId` (`rmsUploadRowId` ASC) ,
  INDEX `device_instances_ibfk_1_idx` (`rmsUploadId` ASC) ,
  CONSTRAINT `device_instances_ibfk_1`
    FOREIGN KEY (`rmsUploadId` )
    REFERENCES `pgen_rms_uploads` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `device_instances_ibfk_2`
    FOREIGN KEY (`rmsUploadRowId` )
    REFERENCES `pgen_rms_upload_rows` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_device_instance_meters`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_device_instance_meters` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `deviceInstanceId` INT(11) NOT NULL ,
  `meterType` ENUM('LIFE','COLOR','COPY BLACK','BLACK','PRINT BLACK','PRINT COLOR','COPY COLOR','SCAN','FAX') NULL ,
  `startMeter` INT(11) NOT NULL ,
  `endMeter` INT(11) NOT NULL ,
  `monitorStartDate` DATETIME NOT NULL ,
  `monitorEndDate` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `device_instance_id` (`deviceInstanceId` ASC, `meterType` ASC) ,
  CONSTRAINT `proposalgenerator_device_instance_meters_ibfk_1`
    FOREIGN KEY (`deviceInstanceId` )
    REFERENCES `pgen_device_instances` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_part_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_part_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_toner_colors`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_toner_colors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_toners`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_toners` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `sku` VARCHAR(255) NOT NULL ,
  `cost` DOUBLE NOT NULL ,
  `yield` INT(11) NOT NULL ,
  `partTypeId` INT(11) NOT NULL ,
  `manufacturerId` INT(11) NOT NULL ,
  `tonerColorId` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `sku` (`sku` ASC) ,
  UNIQUE INDEX `sku_2` (`sku` ASC, `manufacturerId` ASC, `partTypeId` ASC, `yield` ASC, `tonerColorId` ASC) ,
  INDEX `part_type_id` (`partTypeId` ASC) ,
  INDEX `toner_color_id` (`tonerColorId` ASC) ,
  INDEX `proposalgenerator_toners_ibfk_2_idx` (`manufacturerId` ASC) ,
  CONSTRAINT `proposalgenerator_toners_ibfk_1`
    FOREIGN KEY (`partTypeId` )
    REFERENCES `pgen_part_types` (`id` ),
  CONSTRAINT `proposalgenerator_toners_ibfk_3`
    FOREIGN KEY (`tonerColorId` )
    REFERENCES `pgen_toner_colors` (`id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `proposalgenerator_toners_ibfk_2`
    FOREIGN KEY (`manufacturerId` )
    REFERENCES `manufacturers` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 15
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_toner_configs`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_toner_configs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name` (`name` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_master_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_master_devices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `cost` DOUBLE NULL ,
  `dateCreated` DATETIME NOT NULL ,
  `dutyCycle` INT(11) NULL ,
  `isCopier` TINYINT(4) NOT NULL DEFAULT 0 ,
  `isDuplex` TINYINT(4) NOT NULL DEFAULT 0 ,
  `isFax` TINYINT(4) NOT NULL DEFAULT 0 ,
  `isLeased` TINYINT(4) NOT NULL DEFAULT 0 ,
  `isReplacementDevice` TINYINT(4) NOT NULL DEFAULT 0 ,
  `isScanner` TINYINT(4) NOT NULL DEFAULT 0 ,
  `launchDate` DATETIME NOT NULL ,
  `manufacturerId` INT(11) NOT NULL ,
  `modelName` VARCHAR(255) NOT NULL ,
  `leasedTonerYield` INT(11) NULL ,
  `ppmBlack` DOUBLE NULL ,
  `ppmColor` DOUBLE NULL ,
  `serviceCostPerPage` DOUBLE NULL ,
  `tonerConfigId` INT(11) NOT NULL ,
  `wattsPowerNormal` DOUBLE NULL ,
  `wattsPowerIdle` DOUBLE NULL ,
  `reportsTonerLevels` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `toner_config_id` (`tonerConfigId` ASC) ,
  INDEX `proposalgenerator_master_devices_ibfk_1_idx` (`manufacturerId` ASC) ,
  CONSTRAINT `proposalgenerator_master_devices_ibfk_2`
    FOREIGN KEY (`tonerConfigId` )
    REFERENCES `pgen_toner_configs` (`id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `proposalgenerator_master_devices_ibfk_1`
    FOREIGN KEY (`manufacturerId` )
    REFERENCES `manufacturers` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_device_toners`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_device_toners` (
  `toner_id` INT(11) NOT NULL ,
  `master_device_id` INT(11) NOT NULL ,
  PRIMARY KEY (`toner_id`, `master_device_id`) ,
  INDEX `master_device_id` (`master_device_id` ASC) ,
  CONSTRAINT `proposalgenerator_device_toners_ibfk_1`
    FOREIGN KEY (`toner_id` )
    REFERENCES `pgen_toners` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `proposalgenerator_device_toners_ibfk_2`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `pgen_master_devices` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_rms_master_matchups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_rms_master_matchups` (
  `rmsProviderId` INT(11) NOT NULL ,
  `rmsModelId` INT(11) NOT NULL ,
  `masterDeviceId` INT(11) NOT NULL ,
  PRIMARY KEY (`rmsProviderId`, `rmsModelId`) ,
  CONSTRAINT `pgen_rms_master_matchups_ibfk_1`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `pgen_master_devices` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `pgen_rms_master_matchups_ibfk_2`
    FOREIGN KEY (`rmsProviderId` , `rmsModelId` )
    REFERENCES `pgen_rms_devices` (`rmsProviderId` , `rmsModelId` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_pricing_configs`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_pricing_configs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `color_toner_part_type_id` INT(11) NULL DEFAULT NULL ,
  `mono_toner_part_type_id` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name` (`name` ASC) ,
  INDEX `color_toner_part_type_id` (`color_toner_part_type_id` ASC) ,
  INDEX `mono_toner_part_type_id` (`mono_toner_part_type_id` ASC) ,
  CONSTRAINT `proposalgenerator_pricing_configs_ibfk_1`
    FOREIGN KEY (`color_toner_part_type_id` )
    REFERENCES `pgen_part_types` (`id` ),
  CONSTRAINT `proposalgenerator_pricing_configs_ibfk_2`
    FOREIGN KEY (`mono_toner_part_type_id` )
    REFERENCES `pgen_part_types` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_replacement_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_replacement_devices` (
  `masterDeviceId` INT(11) NOT NULL ,
  `replacementCategory` ENUM('BLACK & WHITE','BLACK & WHITE MFP','COLOR','COLOR MFP') NULL DEFAULT NULL ,
  `printSpeed` INT(11) NULL DEFAULT NULL ,
  `resolution` INT(11) NULL DEFAULT NULL ,
  `monthlyRate` DOUBLE NULL DEFAULT NULL ,
  PRIMARY KEY (`masterDeviceId`) ,
  CONSTRAINT `proposalgenerator_replacement_devices_ibfk_1`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `pgen_master_devices` (`id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `assessments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `assessments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `userId` INT(11) NOT NULL ,
  `clientId` INT NOT NULL ,
  `rmsUploadId` INT(11) NULL ,
  `userPricingOverride` TINYINT(4) NULL DEFAULT '0' ,
  `stepName` VARCHAR(255) NULL ,
  `dateCreated` DATETIME NOT NULL ,
  `lastModified` DATETIME NOT NULL ,
  `reportDate` DATETIME NULL DEFAULT NULL ,
  `devicesModified` TINYINT(4) NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `user_id` (`userId` ASC) ,
  INDEX `proposalgenerator_reports_ibfk_3_idx` (`clientId` ASC) ,
  INDEX `assessments_ibfk_3_idx` (`rmsUploadId` ASC) ,
  CONSTRAINT `assessments_ibfk_1`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `assessments_ibfk_2`
    FOREIGN KEY (`clientId` )
    REFERENCES `clients` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `assessments_ibfk_3`
    FOREIGN KEY (`rmsUploadId` )
    REFERENCES `pgen_rms_uploads` (`id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_report_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_report_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `actualPageCoverageMono` DOUBLE NULL DEFAULT NULL ,
  `actualPageCoverageColor` DOUBLE NULL DEFAULT NULL ,
  `serviceCostPerPage` DOUBLE NULL DEFAULT NULL ,
  `adminCostPerPage` DOUBLE NULL DEFAULT NULL ,
  `assessmentReportMargin` DOUBLE NULL DEFAULT NULL ,
  `grossMarginReportMargin` DOUBLE NULL DEFAULT NULL ,
  `monthlyLeasePayment` DOUBLE NULL DEFAULT NULL ,
  `defaultPrinterCost` DOUBLE NULL DEFAULT NULL ,
  `leasedBwCostPerPage` DOUBLE NULL DEFAULT NULL ,
  `leasedColorCostPerPage` DOUBLE NULL DEFAULT NULL ,
  `mpsBwCostPerPage` DOUBLE NULL DEFAULT NULL ,
  `mpsColorCostPerPage` DOUBLE NULL DEFAULT NULL ,
  `kilowattsPerHour` DOUBLE NULL DEFAULT NULL ,
  `assessmentPricingConfigId` INT(11) NULL DEFAULT NULL ,
  `grossMarginPricingConfigId` INT(11) NULL DEFAULT NULL ,
  `reportDate` DATETIME NULL DEFAULT NULL ,
  `targetMonochrome` DOUBLE NULL DEFAULT NULL ,
  `targetColor` DOUBLE NULL DEFAULT NULL ,
  `costThreshold` DOUBLE NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `assessmentPricingConfigId` (`assessmentPricingConfigId` ASC) ,
  INDEX `grossMarginPricingConfigId` (`grossMarginPricingConfigId` ASC) ,
  CONSTRAINT `proposalgenerator_report_settings_ibfk_1`
    FOREIGN KEY (`assessmentPricingConfigId` )
    REFERENCES `pgen_pricing_configs` (`id` ),
  CONSTRAINT `proposalgenerator_report_settings_ibfk_2`
    FOREIGN KEY (`grossMarginPricingConfigId` )
    REFERENCES `pgen_pricing_configs` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_report_report_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_report_report_settings` (
  `reportId` INT(11) NOT NULL ,
  `reportSettingId` INT(11) NOT NULL ,
  PRIMARY KEY (`reportId`) ,
  INDEX `report_setting_id` (`reportSettingId` ASC) ,
  CONSTRAINT `proposalgenerator_report_report_settings_ibfk_1`
    FOREIGN KEY (`reportId` )
    REFERENCES `assessments` (`id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `proposalgenerator_report_report_settings_ibfk_2`
    FOREIGN KEY (`reportSettingId` )
    REFERENCES `pgen_report_settings` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_survey_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_survey_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `pageCoverageMono` DOUBLE NULL DEFAULT NULL ,
  `pageCoverageColor` DOUBLE NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_report_survey_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_report_survey_settings` (
  `reportId` INT(11) NOT NULL ,
  `surveySettingId` INT(11) NOT NULL ,
  PRIMARY KEY (`reportId`) ,
  INDEX `survey_setting_id` (`surveySettingId` ASC) ,
  CONSTRAINT `proposalgenerator_report_survey_settings_ibfk_1`
    FOREIGN KEY (`reportId` )
    REFERENCES `assessments` (`id` ),
  CONSTRAINT `proposalgenerator_report_survey_settings_ibfk_2`
    FOREIGN KEY (`surveySettingId` )
    REFERENCES `pgen_survey_settings` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_user_device_overrides`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_user_device_overrides` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `master_device_id` INT(11) NOT NULL ,
  `cost` DOUBLE NOT NULL ,
  `is_leased` TINYINT(4) NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `master_device_id` (`master_device_id` ASC) ,
  CONSTRAINT `proposalgenerator_user_device_overrides_ibfk_1`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `pgen_master_devices` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `proposalgenerator_user_device_overrides_ibfk_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_rms_user_matchups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_rms_user_matchups` (
  `rmsProviderId` INT(11) NOT NULL ,
  `rmsModelId` INT(11) NOT NULL ,
  `masterDeviceId` INT(11) NOT NULL ,
  `userId` INT(11) NOT NULL ,
  PRIMARY KEY (`rmsProviderId`, `rmsModelId`) ,
  INDEX `user_id` (`userId` ASC) ,
  INDEX `pgen_rms_user_matchups_ibfk_1_idx` (`masterDeviceId` ASC) ,
  CONSTRAINT `pgen_rms_user_matchups_ibfk_2`
    FOREIGN KEY (`rmsProviderId` , `rmsModelId` )
    REFERENCES `pgen_rms_devices` (`rmsProviderId` , `rmsModelId` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `pgen_rms_user_matchups_ibfk_3`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `pgen_rms_user_matchups_ibfk_1`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `pgen_master_devices` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_user_report_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_user_report_settings` (
  `userId` INT(11) NOT NULL ,
  `reportSettingId` INT(11) NOT NULL ,
  PRIMARY KEY (`userId`) ,
  INDEX `report_setting_id` (`reportSettingId` ASC) ,
  CONSTRAINT `proposalgenerator_user_report_settings_ibfk_1`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `proposalgenerator_user_report_settings_ibfk_2`
    FOREIGN KEY (`reportSettingId` )
    REFERENCES `pgen_report_settings` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_user_survey_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_user_survey_settings` (
  `userId` INT(11) NOT NULL ,
  `surveySettingId` INT(11) NOT NULL ,
  PRIMARY KEY (`userId`) ,
  INDEX `survey_setting_id` (`surveySettingId` ASC) ,
  CONSTRAINT `proposalgenerator_user_survey_settings_ibfk_1`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `proposalgenerator_user_survey_settings_ibfk_2`
    FOREIGN KEY (`surveySettingId` )
    REFERENCES `pgen_survey_settings` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_user_toner_overrides`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_user_toner_overrides` (
  `user_id` INT(11) NOT NULL ,
  `toner_id` INT(11) NOT NULL ,
  `cost` DOUBLE NOT NULL ,
  PRIMARY KEY (`toner_id`, `user_id`) ,
  INDEX `toner_id` (`toner_id` ASC) ,
  INDEX `proposalgenerator_user_toner_overrides_ibfk_2_idx` (`user_id` ASC) ,
  CONSTRAINT `proposalgenerator_user_toner_overrides_ibfk_1`
    FOREIGN KEY (`toner_id` )
    REFERENCES `pgen_toners` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `proposalgenerator_user_toner_overrides_ibfk_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_devices` (
  `masterDeviceId` INT(11) NOT NULL ,
  `dealerSku` VARCHAR(255) NULL ,
  `oemSku` VARCHAR(255) NOT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`masterDeviceId`) ,
  INDEX `quotegen_devices_ibfk_1_idx` (`masterDeviceId` ASC) ,
  CONSTRAINT `quotegen_devices_ibfk_1`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `pgen_master_devices` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_device_configurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_device_configurations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `masterDeviceId` INT(11) NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `masterDeviceId` (`masterDeviceId` ASC) ,
  CONSTRAINT `quotegen_device_configurations_ibfk_1`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `qgen_devices` (`masterDeviceId` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_options`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_options` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  `cost` DOUBLE NOT NULL ,
  `dealerSku` VARCHAR(255) NULL ,
  `oemSku` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_device_configuration_options`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_device_configuration_options` (
  `deviceConfigurationId` INT(11) NOT NULL ,
  `optionId` INT(11) NOT NULL ,
  `quantity` INT(11) NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`deviceConfigurationId`, `optionId`) ,
  INDEX `optionId` (`optionId` ASC) ,
  CONSTRAINT `quotegen_device_configuration_options_ibfk_1`
    FOREIGN KEY (`deviceConfigurationId` )
    REFERENCES `qgen_device_configurations` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `quotegen_device_configuration_options_ibfk_2`
    FOREIGN KEY (`optionId` )
    REFERENCES `qgen_options` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_device_options`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_device_options` (
  `masterDeviceId` INT(11) NOT NULL ,
  `optionId` INT(11) NOT NULL ,
  `includedQuantity` INT(11) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`masterDeviceId`, `optionId`) ,
  INDEX `optionId` (`optionId` ASC) ,
  CONSTRAINT `quotegen_device_options_ibfk_1`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `qgen_devices` (`masterDeviceId` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `quotegen_device_options_ibfk_2`
    FOREIGN KEY (`optionId` )
    REFERENCES `qgen_options` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_global_device_configurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_global_device_configurations` (
  `deviceConfigurationId` INT(11) NOT NULL ,
  PRIMARY KEY (`deviceConfigurationId`) ,
  CONSTRAINT `quotegen_user_device_configurations_ibfk_10`
    FOREIGN KEY (`deviceConfigurationId` )
    REFERENCES `qgen_device_configurations` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_leasing_schemas`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_leasing_schemas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COMMENT = 'Stores information on different leasing schemas';


-- -----------------------------------------------------
-- Table `qgen_global_leasing_schemas`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_global_leasing_schemas` (
  `leasingSchemaId` INT(11) NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`leasingSchemaId`) ,
  INDEX `quotegen_global_leasing_schemas_ibfk_1_idx` (`leasingSchemaId` ASC) ,
  CONSTRAINT `quotegen_global_leasing_schemas_ibfk_1`
    FOREIGN KEY (`leasingSchemaId` )
    REFERENCES `qgen_leasing_schemas` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'This table marks leasing schemas as global';


-- -----------------------------------------------------
-- Table `qgen_leasing_schema_ranges`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_leasing_schema_ranges` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `leasingSchemaId` INT(11) NOT NULL ,
  `startRange` DOUBLE NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `leasingSchemaId` (`leasingSchemaId` ASC) ,
  CONSTRAINT `quotegen_leasing_schema_ranges_ibfk_1`
    FOREIGN KEY (`leasingSchemaId` )
    REFERENCES `qgen_leasing_schemas` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8
COMMENT = 'Stores the available value ranges (start range) for a leasin';


-- -----------------------------------------------------
-- Table `qgen_leasing_schema_terms`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_leasing_schema_terms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `leasingSchemaId` INT(11) NOT NULL ,
  `months` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `leasingSchemaId` (`leasingSchemaId` ASC) ,
  CONSTRAINT `quotegen_leasing_schema_terms_ibfk_1`
    FOREIGN KEY (`leasingSchemaId` )
    REFERENCES `qgen_leasing_schemas` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 10
DEFAULT CHARACTER SET = utf8
COMMENT = 'Holds the terms available for a leasing schema';


-- -----------------------------------------------------
-- Table `qgen_leasing_schema_rates`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_leasing_schema_rates` (
  `leasingSchemaTermId` INT(11) NOT NULL ,
  `leasingSchemaRangeId` INT(11) NOT NULL ,
  `rate` DOUBLE NOT NULL ,
  PRIMARY KEY (`leasingSchemaTermId`, `leasingSchemaRangeId`) ,
  INDEX `leasingSchemaTermId` (`leasingSchemaTermId` ASC) ,
  INDEX `leasingSchemaRangeId` (`leasingSchemaRangeId` ASC) ,
  CONSTRAINT `quotegen_leasing_schema_rates_ibfk_2`
    FOREIGN KEY (`leasingSchemaTermId` )
    REFERENCES `qgen_leasing_schema_terms` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `quotegen_leasing_schema_rates_ibfk_3`
    FOREIGN KEY (`leasingSchemaRangeId` )
    REFERENCES `qgen_leasing_schema_ranges` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Stores the rates that coincide with the terms and ranges for';


-- -----------------------------------------------------
-- Table `qgen_option_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_option_categories` (
  `categoryId` INT(11) NOT NULL ,
  `optionId` INT(11) NOT NULL ,
  PRIMARY KEY (`categoryId`, `optionId`) ,
  INDEX `optionId` (`optionId` ASC) ,
  CONSTRAINT `quotegen_option_categories_ibfk_1`
    FOREIGN KEY (`categoryId` )
    REFERENCES `qgen_categories` (`id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `quotegen_option_categories_ibfk_2`
    FOREIGN KEY (`optionId` )
    REFERENCES `qgen_options` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_quotes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_quotes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `clientId` INT(11) NOT NULL ,
  `dateCreated` DATETIME NOT NULL ,
  `dateModified` DATETIME NOT NULL ,
  `quoteDate` DATETIME NOT NULL ,
  `userId` INT(11) NOT NULL ,
  `clientDisplayName` VARCHAR(45) NULL DEFAULT NULL ,
  `leaseRate` DOUBLE NULL ,
  `leaseTerm` INT(11) NULL ,
  `pageCoverageMonochrome` DOUBLE NOT NULL ,
  `pageCoverageColor` DOUBLE NOT NULL ,
  `pricingConfigId` INT(11) NOT NULL ,
  `quoteType` ENUM('purchased', 'leased') NOT NULL ,
  `monochromePageMargin` DOUBLE NOT NULL ,
  `colorPageMargin` DOUBLE NOT NULL ,
  `adminCostPerPage` DOUBLE NOT NULL ,
  `serviceCostPerPage` DOUBLE NOT NULL ,
  `monochromeOverageMargin` DOUBLE NOT NULL ,
  `colorOverageMargin` DOUBLE NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `clientId` (`clientId` ASC) ,
  INDEX `quotegen_quotes_ibfk_2_idx` (`userId` ASC) ,
  INDEX `quotegen_quotes_ibfk_3_idx` (`pricingConfigId` ASC) ,
  CONSTRAINT `quotegen_quotes_ibfk_1`
    FOREIGN KEY (`clientId` )
    REFERENCES `clients` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `quotegen_quotes_ibfk_2`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `quotegen_quotes_ibfk_3`
    FOREIGN KEY (`pricingConfigId` )
    REFERENCES `pgen_pricing_configs` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COMMENT = 'Primary table for a quote. Stores basic information';


-- -----------------------------------------------------
-- Table `qgen_quote_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_quote_devices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `quoteId` INT(11) NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `oemSku` VARCHAR(255) NOT NULL ,
  `dealerSku` VARCHAR(255) NULL ,
  `oemCostPerPageMonochrome` DOUBLE NOT NULL ,
  `oemCostPerPageColor` DOUBLE NOT NULL ,
  `compCostPerPageMonochrome` DOUBLE NOT NULL ,
  `compCostPerPageColor` DOUBLE NOT NULL ,
  `cost` DOUBLE NOT NULL ,
  `packageCost` DOUBLE NOT NULL ,
  `packageMarkup` DOUBLE NOT NULL ,
  `residual` DOUBLE NOT NULL ,
  `margin` DOUBLE NOT NULL ,
  `tonerConfigId` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `quoteId` (`quoteId` ASC) ,
  CONSTRAINT `quotegen_quote_devices_ibfk_1`
    FOREIGN KEY (`quoteId` )
    REFERENCES `qgen_quotes` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_quote_device_options`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_quote_device_options` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `quoteDeviceId` INT(11) NOT NULL ,
  `oemSku` VARCHAR(255) NOT NULL ,
  `dealerSku` VARCHAR(255) NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  `cost` DOUBLE NOT NULL ,
  `quantity` INT(11) NOT NULL ,
  `includedQuantity` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `quoteDeviceId` (`quoteDeviceId` ASC) ,
  CONSTRAINT `quotegen_quote_device_options_ibfk_1`
    FOREIGN KEY (`quoteDeviceId` )
    REFERENCES `qgen_quote_devices` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 14
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_quote_device_configuration_options`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_quote_device_configuration_options` (
  `quoteDeviceOptionId` INT(11) NOT NULL ,
  `optionId` INT(11) NOT NULL ,
  `masterDeviceId` INT(11) NOT NULL ,
  PRIMARY KEY (`quoteDeviceOptionId`) ,
  INDEX `quotegen_quote_device_option_options_ibfk_1_idx` (`optionId` ASC, `masterDeviceId` ASC) ,
  INDEX `quotegen_quote_device_option_options_ibfk_2_idx` (`quoteDeviceOptionId` ASC) ,
  CONSTRAINT `quotegen_quote_device_option_options_ibfk_1`
    FOREIGN KEY (`optionId` , `masterDeviceId` )
    REFERENCES `qgen_device_options` (`optionId` , `masterDeviceId` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `quotegen_quote_device_option_options_ibfk_2`
    FOREIGN KEY (`quoteDeviceOptionId` )
    REFERENCES `qgen_quote_device_options` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `qgen_quote_device_configurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_quote_device_configurations` (
  `quoteDeviceId` INT(11) NOT NULL AUTO_INCREMENT ,
  `masterDeviceId` INT(11) NOT NULL ,
  PRIMARY KEY (`quoteDeviceId`, `masterDeviceId`) ,
  INDEX `deviceConfigurationId` (`masterDeviceId` ASC) ,
  INDEX `quoteDeviceId` (`quoteDeviceId` ASC) ,
  CONSTRAINT `quotegen_quote_device_configurations_ibfk_1`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `qgen_devices` (`masterDeviceId` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `quotegen_quote_device_configurations_ibfk_2`
    FOREIGN KEY (`quoteDeviceId` )
    REFERENCES `qgen_quote_devices` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_quote_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_quote_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `pageCoverageMonochrome` DOUBLE NULL DEFAULT NULL ,
  `pageCoverageColor` DOUBLE NULL DEFAULT NULL ,
  `deviceMargin` DOUBLE NULL DEFAULT NULL ,
  `pageMargin` DOUBLE NULL DEFAULT NULL ,
  `pricingConfigId` INT(11) NULL DEFAULT NULL ,
  `serviceCostPerPage` DOUBLE NULL ,
  `adminCostPerPage` DOUBLE NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `quotegen_quote_settings_ibfk1_idx` (`pricingConfigId` ASC) ,
  CONSTRAINT `quotegen_quote_settings_ibfk1`
    FOREIGN KEY (`pricingConfigId` )
    REFERENCES `pgen_pricing_configs` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_user_device_configurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_user_device_configurations` (
  `deviceConfigurationId` INT(11) NOT NULL ,
  `userId` INT(11) NOT NULL ,
  PRIMARY KEY (`deviceConfigurationId`, `userId`) ,
  INDEX `userId` (`userId` ASC) ,
  CONSTRAINT `quotegen_user_device_configurations_ibfk_1`
    FOREIGN KEY (`deviceConfigurationId` )
    REFERENCES `qgen_device_configurations` (`id` ),
  CONSTRAINT `quotegen_user_device_configurations_ibfk_2`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `qgen_user_quote_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_user_quote_settings` (
  `userId` INT(11) NOT NULL ,
  `quoteSettingId` INT(11) NOT NULL ,
  PRIMARY KEY (`userId`) ,
  INDEX `quoteSettingId` (`quoteSettingId` ASC) ,
  CONSTRAINT `quotegen_user_quote_settings_ibfk_1`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` ),
  CONSTRAINT `quotegen_user_quote_settings_ibfk_2`
    FOREIGN KEY (`quoteSettingId` )
    REFERENCES `qgen_quote_settings` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


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
  PRIMARY KEY (`userId`, `roleId`) ,
  INDEX `FK_userRoles_roles_idx` (`roleId` ASC) ,
  CONSTRAINT `FK_userRoles_users`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_userRoles_roles`
    FOREIGN KEY (`roleId` )
    REFERENCES `roles` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `qgen_quote_lease_terms`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_quote_lease_terms` (
  `quoteId` INT NOT NULL ,
  `leasingSchemaTermId` INT NOT NULL ,
  PRIMARY KEY (`quoteId`) ,
  INDEX `fk_qgen_quote_lease_terms_qgen_quotes1_idx` (`quoteId` ASC) ,
  INDEX `fk_qgen_quote_lease_terms_qgen_leasing_schema_terms1_idx` (`leasingSchemaTermId` ASC) ,
  UNIQUE INDEX `quoteId_UNIQUE` (`quoteId` ASC, `leasingSchemaTermId` ASC) ,
  CONSTRAINT `fk_qgen_quote_lease_terms_qgen_quotes1`
    FOREIGN KEY (`quoteId` )
    REFERENCES `qgen_quotes` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_qgen_quote_lease_terms_qgen_leasing_schema_terms1`
    FOREIGN KEY (`leasingSchemaTermId` )
    REFERENCES `qgen_leasing_schema_terms` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `qgen_quote_device_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_quote_device_groups` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `quoteId` INT NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `isDefault` TINYINT NOT NULL DEFAULT 0 ,
  `groupPages` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `quotegen_quote_device_groups_ibfk_1_idx` (`quoteId` ASC) ,
  CONSTRAINT `quotegen_quote_device_groups_ibfk_1`
    FOREIGN KEY (`quoteId` )
    REFERENCES `qgen_quotes` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `qgen_quote_device_group_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_quote_device_group_devices` (
  `quoteDeviceId` INT NOT NULL ,
  `quoteDeviceGroupId` INT NOT NULL ,
  `quantity` INT NOT NULL DEFAULT 1 ,
  `monochromePagesQuantity` INT NOT NULL ,
  `colorPagesQuantity` INT NOT NULL ,
  PRIMARY KEY (`quoteDeviceGroupId`, `quoteDeviceId`) ,
  INDEX `qgen_quote_device_group_devices_ibfk1_idx` (`quoteDeviceId` ASC) ,
  INDEX `qgen_quote_device_group_devices_ibfk2_idx` (`quoteDeviceGroupId` ASC) ,
  CONSTRAINT `qgen_quote_device_group_devices_ibfk1`
    FOREIGN KEY (`quoteDeviceId` )
    REFERENCES `qgen_quote_devices` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `qgen_quote_device_group_devices_ibfk2`
    FOREIGN KEY (`quoteDeviceGroupId` )
    REFERENCES `qgen_quote_device_groups` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `contacts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `contacts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `clientId` INT NOT NULL ,
  `firstName` VARCHAR(255) NOT NULL ,
  `lastName` VARCHAR(255) NOT NULL ,
  `countryCode` SMALLINT NULL ,
  `areaCode` SMALLINT NULL ,
  `exchangeCode` SMALLINT NULL ,
  `number` SMALLINT NULL ,
  `extension` SMALLINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `contacts_ibfk_1_idx` (`clientId` ASC) ,
  CONSTRAINT `contacts_ibfk_1`
    FOREIGN KEY (`clientId` )
    REFERENCES `clients` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `countries`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `countries` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `locale` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `addresses`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `addresses` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `clientId` INT NOT NULL ,
  `addressLine1` VARCHAR(255) NOT NULL ,
  `addressLine2` VARCHAR(255) NULL ,
  `city` VARCHAR(255) NOT NULL ,
  `region` VARCHAR(255) NOT NULL ,
  `postCode` VARCHAR(255) NOT NULL ,
  `countryId` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `addresses_ibfk_1_idx` (`clientId` ASC) ,
  INDEX `addresses_ibfk_2_idx` (`countryId` ASC) ,
  CONSTRAINT `addresses_ibfk_1`
    FOREIGN KEY (`clientId` )
    REFERENCES `clients` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `addresses_ibfk_2`
    FOREIGN KEY (`countryId` )
    REFERENCES `countries` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `regions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `regions` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `countryId` INT NOT NULL ,
  `region` CHAR(2) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `regions_ibfk_1_idx` (`countryId` ASC) ,
  CONSTRAINT `regions_ibfk_1`
    FOREIGN KEY (`countryId` )
    REFERENCES `countries` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pgen_device_instance_master_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_device_instance_master_devices` (
  `deviceInstanceId` INT NOT NULL ,
  `masterDeviceId` INT NOT NULL ,
  PRIMARY KEY (`deviceInstanceId`) ,
  INDEX `device_instance_master_devices_ibfk_1_idx` (`deviceInstanceId` ASC) ,
  INDEX `device_instance_master_devices_ibfk_2_idx` (`masterDeviceId` ASC) ,
  CONSTRAINT `device_instance_master_devices_ibfk_1`
    FOREIGN KEY (`deviceInstanceId` )
    REFERENCES `pgen_device_instances` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `device_instance_master_devices_ibfk_2`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `pgen_master_devices` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pgen_rms_excluded_rows`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_rms_excluded_rows` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `rmsUploadId` INT NOT NULL ,
  `rmsProviderId` INT NULL ,
  `rmsModelId` INT NULL ,
  `serialNumber` VARCHAR(255) NULL ,
  `ipAddress` VARCHAR(255) NULL ,
  `modelName` VARCHAR(255) NULL ,
  `manufacturerName` VARCHAR(255) NULL ,
  `reason` VARCHAR(255) NOT NULL ,
  `csvLineNumber` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `rms_excluded_rows_ibfk_1_idx` (`rmsUploadId` ASC) ,
  CONSTRAINT `rms_excluded_rows_ibfk_1`
    FOREIGN KEY (`rmsUploadId` )
    REFERENCES `pgen_rms_uploads` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hardware_optimizations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `hardware_optimizations` (
  `id` INT NOT NULL ,
  `clientId` INT(11) NOT NULL ,
  `rmsUploadId` INT(11) NULL ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `hardware_optimization_ibfk_1_idx` (`clientId` ASC) ,
  INDEX `hardware_optimization_ibfk_2_idx` (`rmsUploadId` ASC) ,
  CONSTRAINT `hardware_optimization_ibfk_1`
    FOREIGN KEY (`clientId` )
    REFERENCES `clients` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `hardware_optimization_ibfk_2`
    FOREIGN KEY (`rmsUploadId` )
    REFERENCES `pgen_rms_uploads` (`id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `device_instance_replacement_master_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `device_instance_replacement_master_devices` (
  `deviceInstanceId` INT(11) NOT NULL ,
  `hardwareOptimizationId` INT(11) NOT NULL ,
  `masterDeviceId` INT(11) NOT NULL ,
  INDEX `device_instance_replacement_master_devices_ibfk1_idx` (`masterDeviceId` ASC) ,
  INDEX `device_instance_replacement_master_devices_ibfk2_idx` (`deviceInstanceId` ASC) ,
  PRIMARY KEY (`deviceInstanceId`) ,
  UNIQUE INDEX `deviceInstanceId_UNIQUE` (`deviceInstanceId` ASC, `hardwareOptimizationId` ASC) ,
  INDEX `device_instance_replacement_master_devices_ibfk_3_idx` (`hardwareOptimizationId` ASC) ,
  CONSTRAINT `device_instance_replacement_master_devices_ibfk1`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `pgen_master_devices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `device_instance_replacement_master_devices_ibfk2`
    FOREIGN KEY (`deviceInstanceId` )
    REFERENCES `pgen_device_instances` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `device_instance_replacement_master_devices_ibfk_3`
    FOREIGN KEY (`hardwareOptimizationId` )
    REFERENCES `hardware_optimizations` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `assessment_surveys`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `assessment_surveys` (
  `reportId` INT NOT NULL ,
  `costOfInkAndToner` DOUBLE NULL ,
  `costOfLabor` DOUBLE NULL ,
  `costToExecuteSuppliesOrder` DOUBLE NOT NULL DEFAULT 50.00 ,
  `averageItHourlyRate` DOUBLE NOT NULL DEFAULT 40.00 ,
  `numberOfSupplyOrdersPerMonth` DOUBLE NOT NULL ,
  `hoursSpentOnIt` INT NULL ,
  `averageMonthlyBreakdowns` DOUBLE NULL ,
  `pageCoverageMonochrome` DOUBLE NOT NULL ,
  `pageCoverageColor` DOUBLE NOT NULL ,
  `percentageOfInkjetPrintVolume` DOUBLE NOT NULL ,
  `averageRepairTime` DOUBLE NOT NULL ,
  PRIMARY KEY (`reportId`) ,
  CONSTRAINT `assessment_surveys_ibfk_1`
    FOREIGN KEY (`reportId` )
    REFERENCES `assessments` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `user_sessions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `user_sessions` (
  `sessionId` CHAR(32) NOT NULL ,
  `userId` INT NOT NULL ,
  INDEX `user_sessions_ibfk_1` (`userId` ASC) ,
  UNIQUE INDEX `sessionId_UNIQUE` (`sessionId` ASC) ,
  UNIQUE INDEX `userId_UNIQUE` (`userId` ASC) ,
  PRIMARY KEY (`sessionId`) ,
  CONSTRAINT `user_sessions_ibfk_1`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `user_sessions_ibfk_2`
    FOREIGN KEY (`sessionId` )
    REFERENCES `sessions` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `health_checks`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `health_checks` (
  `id` INT NOT NULL ,
  `clientId` INT(11) NOT NULL ,
  `rmsUploadId` INT(11) NULL ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `health_check_ibfk_1_idx` (`clientId` ASC) ,
  INDEX `health_check_ibfk_2_idx` (`rmsUploadId` ASC) ,
  CONSTRAINT `health_check_ibfk_1`
    FOREIGN KEY (`clientId` )
    REFERENCES `clients` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `health_check_ibfk_2`
    FOREIGN KEY (`rmsUploadId` )
    REFERENCES `pgen_rms_uploads` (`id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

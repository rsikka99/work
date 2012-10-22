SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `clients`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `clients` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `accountNumber` VARCHAR(255) NOT NULL ,
  `companyName` VARCHAR(255) NOT NULL ,
  `legalName` VARCHAR(255) NULL ,
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
  INDEX `logs_ibfk_1` (`logTypeId` ASC) ,
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
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 9
DEFAULT CHARACTER SET = latin1;


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
DEFAULT CHARACTER SET = latin1, 
COMMENT = 'The users table stores basic information on a user' ;


-- -----------------------------------------------------
-- Table `pgen_question_sets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_question_sets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_reports`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_reports` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `userId` INT(11) NOT NULL ,
  `customerCompanyName` VARCHAR(255) NOT NULL ,
  `userPricingOverride` TINYINT(4) NULL DEFAULT '0' ,
  `reportStage` ENUM('company','general','finance','purchasing','it','users','verify','upload','mapdevices','summary','reportsettings','finished') NULL DEFAULT NULL ,
  `questionSetId` INT(11) NOT NULL ,
  `dateCreated` DATETIME NOT NULL ,
  `lastModified` DATETIME NOT NULL ,
  `reportDate` DATETIME NULL DEFAULT NULL ,
  `devicesModified` TINYINT(4) NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `user_id` (`userId` ASC) ,
  INDEX `questionset_id` (`questionSetId` ASC) ,
  CONSTRAINT `proposalgenerator_reports_ibfk_1`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` ),
  CONSTRAINT `proposalgenerator_reports_ibfk_2`
    FOREIGN KEY (`questionSetId` )
    REFERENCES `pgen_question_sets` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_questions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_questions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `description` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 31
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_date_answers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_date_answers` (
  `question_id` INT(11) NOT NULL ,
  `report_id` INT(11) NOT NULL ,
  `date_answer` DATETIME NOT NULL ,
  PRIMARY KEY (`question_id`, `report_id`) ,
  INDEX `report_id` (`report_id` ASC) ,
  CONSTRAINT `proposalgenerator_date_answers_ibfk_1`
    FOREIGN KEY (`report_id` )
    REFERENCES `pgen_reports` (`id` ),
  CONSTRAINT `proposalgenerator_date_answers_ibfk_2`
    FOREIGN KEY (`question_id` )
    REFERENCES `pgen_questions` (`id` ))
ENGINE = InnoDB
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
  `manufacturer_id` INT(11) NOT NULL ,
  `printer_model` VARCHAR(255) NOT NULL ,
  `toner_config_id` INT(11) NOT NULL ,
  `is_copier` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_fax` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_scanner` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_duplex` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_replacement_device` TINYINT(4) NOT NULL DEFAULT '0' ,
  `watts_power_normal` DOUBLE NULL DEFAULT NULL ,
  `watts_power_idle` DOUBLE NULL DEFAULT NULL ,
  `cost` DOUBLE NULL DEFAULT NULL ,
  `service_cost_per_page` DOUBLE NULL DEFAULT NULL ,
  `launch_date` DATETIME NOT NULL ,
  `date_created` DATETIME NOT NULL ,
  `duty_cycle` INT(11) NULL DEFAULT NULL ,
  `ppm_black` DOUBLE NULL DEFAULT NULL ,
  `ppm_color` DOUBLE NULL DEFAULT NULL ,
  `is_leased` TINYINT(4) NOT NULL DEFAULT '0' ,
  `leased_toner_yield` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `toner_config_id` (`toner_config_id` ASC) ,
  INDEX `proposalgenerator_master_devices_ibfk_1` (`manufacturer_id` ASC) ,
  CONSTRAINT `proposalgenerator_master_devices_ibfk_2`
    FOREIGN KEY (`toner_config_id` )
    REFERENCES `pgen_toner_configs` (`id` ),
  CONSTRAINT `proposalgenerator_master_devices_ibfk_1`
    FOREIGN KEY (`manufacturer_id` )
    REFERENCES `manufacturers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_upload_data_collector_rows`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_upload_data_collector_rows` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `report_id` INT(11) NOT NULL ,
  `devices_pf_id` INT(11) NOT NULL ,
  `startdate` DATETIME NOT NULL ,
  `enddate` DATETIME NOT NULL ,
  `printermodelid` INT(11) NOT NULL ,
  `ipaddress` VARCHAR(255) NULL DEFAULT NULL ,
  `serialnumber` VARCHAR(255) NULL DEFAULT NULL ,
  `modelname` VARCHAR(255) NOT NULL ,
  `manufacturer` VARCHAR(255) NOT NULL ,
  `is_color` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_copier` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_scanner` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_fax` TINYINT(4) NOT NULL DEFAULT '0' ,
  `ppm_black` DOUBLE NULL DEFAULT NULL ,
  `ppm_color` DOUBLE NULL DEFAULT NULL ,
  `date_introduction` DATETIME NULL DEFAULT NULL ,
  `date_adoption` DATETIME NULL DEFAULT NULL ,
  `discovery_date` DATETIME NULL DEFAULT NULL ,
  `black_prodcodeoem` VARCHAR(255) NULL DEFAULT NULL ,
  `black_yield` INT(11) NULL DEFAULT NULL ,
  `black_prodcostoem` DOUBLE NULL DEFAULT NULL ,
  `cyan_prodcodeoem` VARCHAR(255) NULL DEFAULT NULL ,
  `cyan_yield` INT(11) NULL DEFAULT NULL ,
  `cyan_prodcostoem` DOUBLE NULL DEFAULT NULL ,
  `magenta_prodcodeoem` VARCHAR(255) NULL DEFAULT NULL ,
  `magenta_yield` INT(11) NULL DEFAULT NULL ,
  `magenta_prodcostoem` DOUBLE NULL DEFAULT NULL ,
  `yellow_prodcodeoem` VARCHAR(255) NULL DEFAULT NULL ,
  `yellow_yield` INT(11) NULL DEFAULT NULL ,
  `yellow_prodcostoem` DOUBLE NULL DEFAULT NULL ,
  `duty_cycle` INT(11) NULL DEFAULT NULL ,
  `wattspowernormal` DOUBLE NULL DEFAULT NULL ,
  `wattspoweridle` DOUBLE NULL DEFAULT NULL ,
  `startmeterlife` INT(11) NULL DEFAULT NULL ,
  `endmeterlife` INT(11) NULL DEFAULT NULL ,
  `startmeterblack` INT(11) NULL DEFAULT NULL ,
  `endmeterblack` INT(11) NULL DEFAULT NULL ,
  `startmetercolor` INT(11) NULL DEFAULT NULL ,
  `endmetercolor` INT(11) NULL DEFAULT NULL ,
  `startmeterprintblack` INT(11) NULL DEFAULT NULL ,
  `endmeterprintblack` INT(11) NULL DEFAULT NULL ,
  `startmeterprintcolor` INT(11) NULL DEFAULT NULL ,
  `endmeterprintcolor` INT(11) NULL DEFAULT NULL ,
  `startmetercopyblack` INT(11) NULL DEFAULT NULL ,
  `endmetercopyblack` INT(11) NULL DEFAULT NULL ,
  `startmetercopycolor` INT(11) NULL DEFAULT NULL ,
  `endmetercopycolor` INT(11) NULL DEFAULT NULL ,
  `startmeterscan` INT(11) NULL DEFAULT NULL ,
  `endmeterscan` INT(11) NULL DEFAULT NULL ,
  `startmeterfax` INT(11) NULL DEFAULT NULL ,
  `endmeterfax` INT(11) NULL DEFAULT NULL ,
  `tonerlevel_black` VARCHAR(255) NULL DEFAULT NULL ,
  `tonerlevel_cyan` VARCHAR(255) NULL DEFAULT NULL ,
  `tonerlevel_magenta` VARCHAR(255) NULL DEFAULT NULL ,
  `tonerlevel_yellow` VARCHAR(255) NULL DEFAULT NULL ,
  `invalid_data` TINYINT(4) NULL DEFAULT '0' ,
  `is_excluded` TINYINT(4) NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `report_id` (`report_id` ASC) ,
  CONSTRAINT `proposalgenerator_upload_data_collector_rows_ibfk_1`
    FOREIGN KEY (`report_id` )
    REFERENCES `pgen_reports` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 477
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_device_instances`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_device_instances` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `report_id` INT(11) NOT NULL ,
  `master_device_id` INT(11) NOT NULL ,
  `upload_data_collector_row_id` INT(11) NOT NULL ,
  `serial_number` VARCHAR(255) NULL DEFAULT NULL ,
  `mps_monitor_startdate` DATETIME NOT NULL ,
  `mps_monitor_enddate` DATETIME NOT NULL ,
  `mps_discovery_date` DATETIME NULL DEFAULT NULL ,
  `jit_supplies_supported` TINYINT(4) NULL DEFAULT '0' ,
  `ip_address` VARCHAR(255) NULL DEFAULT NULL ,
  `is_excluded` TINYINT(4) NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `report_id` (`report_id` ASC) ,
  INDEX `master_device_id` (`master_device_id` ASC) ,
  INDEX `upload_data_collector_id` (`upload_data_collector_row_id` ASC) ,
  CONSTRAINT `proposalgenerator_device_instances_ibfk_1`
    FOREIGN KEY (`report_id` )
    REFERENCES `pgen_reports` (`id` ),
  CONSTRAINT `proposalgenerator_device_instances_ibfk_2`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `pgen_master_devices` (`id` ),
  CONSTRAINT `proposalgenerator_device_instances_ibfk_3`
    FOREIGN KEY (`upload_data_collector_row_id` )
    REFERENCES `pgen_upload_data_collector_rows` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_device_instance_meters`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_device_instance_meters` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `device_instance_id` INT(11) NOT NULL ,
  `meter_type` ENUM('LIFE','COLOR','COPY BLACK','BLACK','PRINT BLACK','PRINT COLOR','COPY COLOR','SCAN','FAX') NULL DEFAULT NULL ,
  `start_meter` INT(11) NOT NULL ,
  `end_meter` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `device_instance_id` (`device_instance_id` ASC, `meter_type` ASC) ,
  CONSTRAINT `proposalgenerator_device_instance_meters_ibfk_1`
    FOREIGN KEY (`device_instance_id` )
    REFERENCES `pgen_device_instances` (`id` ))
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
  `part_type_id` INT(11) NOT NULL ,
  `manufacturer_id` INT(11) NOT NULL ,
  `toner_color_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `sku` (`sku` ASC) ,
  UNIQUE INDEX `sku_2` (`sku` ASC, `manufacturer_id` ASC, `part_type_id` ASC, `yield` ASC, `toner_color_id` ASC) ,
  INDEX `part_type_id` (`part_type_id` ASC) ,
  INDEX `toner_color_id` (`toner_color_id` ASC) ,
  INDEX `proposalgenerator_toners_ibfk_2` (`manufacturer_id` ASC) ,
  CONSTRAINT `proposalgenerator_toners_ibfk_1`
    FOREIGN KEY (`part_type_id` )
    REFERENCES `pgen_part_types` (`id` ),
  CONSTRAINT `proposalgenerator_toners_ibfk_3`
    FOREIGN KEY (`toner_color_id` )
    REFERENCES `pgen_toner_colors` (`id` ),
  CONSTRAINT `proposalgenerator_toners_ibfk_2`
    FOREIGN KEY (`manufacturer_id` )
    REFERENCES `manufacturers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 15
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
    REFERENCES `pgen_toners` (`id` ),
  CONSTRAINT `proposalgenerator_device_toners_ibfk_2`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `pgen_master_devices` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_pf_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_pf_devices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `pf_model_id` INT(11) NOT NULL ,
  `pf_db_devicename` VARCHAR(255) NOT NULL ,
  `pf_db_manufacturer` VARCHAR(255) NULL DEFAULT NULL ,
  `date_created` DATETIME NOT NULL ,
  `created_by` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `pf_model_id` (`pf_model_id` ASC) ,
  INDEX `created_by` (`created_by` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 103
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_master_pf_device_matchups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_master_pf_device_matchups` (
  `master_device_id` INT(11) NOT NULL ,
  `pf_device_id` INT(11) NOT NULL ,
  PRIMARY KEY (`master_device_id`, `pf_device_id`) ,
  UNIQUE INDEX `pf_device_id` (`pf_device_id` ASC) ,
  CONSTRAINT `proposalgenerator_master_pf_device_matchups_ibfk_1`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `pgen_master_devices` (`id` ),
  CONSTRAINT `proposalgenerator_master_pf_device_matchups_ibfk_2`
    FOREIGN KEY (`pf_device_id` )
    REFERENCES `pgen_pf_devices` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_numeric_answers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_numeric_answers` (
  `question_id` INT(11) NOT NULL ,
  `report_id` INT(11) NOT NULL ,
  `numeric_answer` DOUBLE NOT NULL ,
  PRIMARY KEY (`question_id`, `report_id`) ,
  INDEX `report_id` (`report_id` ASC) ,
  CONSTRAINT `proposalgenerator_numeric_answers_ibfk_1`
    FOREIGN KEY (`report_id` )
    REFERENCES `pgen_reports` (`id` ),
  CONSTRAINT `proposalgenerator_numeric_answers_ibfk_2`
    FOREIGN KEY (`question_id` )
    REFERENCES `pgen_questions` (`id` ))
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
-- Table `pgen_questionset_questions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_questionset_questions` (
  `question_id` INT(11) NOT NULL ,
  `questionset_id` INT(11) NOT NULL ,
  PRIMARY KEY (`question_id`, `questionset_id`) ,
  INDEX `questionset_id` (`questionset_id` ASC) ,
  CONSTRAINT `proposalgenerator_questionset_questions_ibfk_1`
    FOREIGN KEY (`questionset_id` )
    REFERENCES `pgen_question_sets` (`id` ),
  CONSTRAINT `proposalgenerator_questionset_questions_ibfk_2`
    FOREIGN KEY (`question_id` )
    REFERENCES `pgen_questions` (`id` )
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_replacement_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_replacement_devices` (
  `master_device_id` INT(11) NOT NULL ,
  `replacement_category` ENUM('BLACK & WHITE','BLACK & WHITE MFP','COLOR','COLOR MFP') NULL DEFAULT NULL ,
  `print_speed` INT(11) NULL DEFAULT NULL ,
  `resolution` INT(11) NULL DEFAULT NULL ,
  `monthly_rate` DOUBLE NULL DEFAULT NULL ,
  PRIMARY KEY (`master_device_id`) ,
  CONSTRAINT `proposalgenerator_replacement_devices_ibfk_1`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `pgen_master_devices` (`id` ))
ENGINE = InnoDB
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
    REFERENCES `pgen_reports` (`id` ),
  CONSTRAINT `proposalgenerator_report_report_settings_ibfk_2`
    FOREIGN KEY (`reportSettingId` )
    REFERENCES `pgen_report_settings` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_survey_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_survey_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `page_coverage_mono` DOUBLE NULL DEFAULT NULL ,
  `page_coverage_color` DOUBLE NULL DEFAULT NULL ,
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
    REFERENCES `pgen_reports` (`id` ),
  CONSTRAINT `proposalgenerator_report_survey_settings_ibfk_2`
    FOREIGN KEY (`surveySettingId` )
    REFERENCES `pgen_survey_settings` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_textual_answers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_textual_answers` (
  `question_id` INT(11) NOT NULL ,
  `report_id` INT(11) NOT NULL ,
  `textual_answer` TEXT NOT NULL ,
  PRIMARY KEY (`question_id`, `report_id`) ,
  INDEX `report_id` (`report_id` ASC) ,
  CONSTRAINT `proposalgenerator_textual_answers_ibfk_1`
    FOREIGN KEY (`report_id` )
    REFERENCES `pgen_reports` (`id` ),
  CONSTRAINT `proposalgenerator_textual_answers_ibfk_2`
    FOREIGN KEY (`question_id` )
    REFERENCES `pgen_questions` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_ticket_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_ticket_categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_ticket_statuses`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_ticket_statuses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_tickets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_tickets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `category_id` INT(11) NOT NULL ,
  `status_id` INT(11) NOT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  `date_created` DATETIME NOT NULL ,
  `date_updated` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `user_id` (`user_id` ASC) ,
  INDEX `category_id` (`category_id` ASC) ,
  INDEX `status_id` (`status_id` ASC) ,
  CONSTRAINT `proposalgenerator_tickets_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` ),
  CONSTRAINT `proposalgenerator_tickets_ibfk_2`
    FOREIGN KEY (`category_id` )
    REFERENCES `pgen_ticket_categories` (`id` ),
  CONSTRAINT `proposalgenerator_tickets_ibfk_3`
    FOREIGN KEY (`status_id` )
    REFERENCES `pgen_ticket_statuses` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_ticket_comments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_ticket_comments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `ticket_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `content` TEXT NOT NULL ,
  `date_created` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `ticket_id` (`ticket_id` ASC) ,
  INDEX `user_id` (`user_id` ASC) ,
  CONSTRAINT `proposalgenerator_ticket_comments_ibfk_1`
    FOREIGN KEY (`ticket_id` )
    REFERENCES `pgen_tickets` (`id` ),
  CONSTRAINT `proposalgenerator_ticket_comments_ibfk_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_ticket_pf_requests`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_ticket_pf_requests` (
  `ticket_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `pf_device_id` INT(11) NOT NULL ,
  `manufacturer` VARCHAR(255) NOT NULL ,
  `printer_model` VARCHAR(255) NOT NULL ,
  `launch_date` DATETIME NULL DEFAULT NULL ,
  `cost` DOUBLE NULL DEFAULT NULL ,
  `service_cost_per_page` DOUBLE NULL DEFAULT NULL ,
  `toner_config` VARCHAR(255) NOT NULL ,
  `is_copier` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_fax` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_duplex` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_scanner` TINYINT(4) NOT NULL DEFAULT '0' ,
  `PPM_black` DOUBLE NULL DEFAULT NULL ,
  `PPM_color` DOUBLE NULL DEFAULT NULL ,
  `duty_cycle` INT(11) NULL DEFAULT NULL ,
  `watts_power_normal` DOUBLE NULL DEFAULT NULL ,
  `watts_power_idle` DOUBLE NULL DEFAULT NULL ,
  PRIMARY KEY (`ticket_id`) ,
  UNIQUE INDEX `user_id` (`user_id` ASC) ,
  INDEX `pf_device_id` (`ticket_id` ASC) ,
  INDEX `proposalgenerator_ticket_pf_requests_ibfk_3` (`pf_device_id` ASC) ,
  CONSTRAINT `proposalgenerator_ticket_pf_requests_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` ),
  CONSTRAINT `proposalgenerator_ticket_pf_requests_ibfk_2`
    FOREIGN KEY (`ticket_id` )
    REFERENCES `pgen_tickets` (`id` ),
  CONSTRAINT `proposalgenerator_ticket_pf_requests_ibfk_3`
    FOREIGN KEY (`pf_device_id` )
    REFERENCES `pgen_pf_devices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_tickets_viewed`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_tickets_viewed` (
  `ticket_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `date_viewed` DATETIME NOT NULL ,
  PRIMARY KEY (`ticket_id`, `user_id`) ,
  INDEX `user_id` (`user_id` ASC) ,
  CONSTRAINT `proposalgenerator_tickets_viewed_ibfk_1`
    FOREIGN KEY (`ticket_id` )
    REFERENCES `pgen_tickets` (`id` ),
  CONSTRAINT `proposalgenerator_tickets_viewed_ibfk_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_unknown_device_instances`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_unknown_device_instances` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `report_id` INT(11) NOT NULL ,
  `upload_data_collector_row_id` INT(11) NOT NULL ,
  `printermodelid` INT(11) NOT NULL ,
  `mps_monitor_startdate` DATETIME NOT NULL ,
  `mps_monitor_enddate` DATETIME NOT NULL ,
  `mps_discovery_date` DATETIME NULL DEFAULT NULL ,
  `install_date` DATETIME NULL DEFAULT NULL ,
  `device_manufacturer` VARCHAR(255) NOT NULL ,
  `printer_model` VARCHAR(255) NOT NULL ,
  `printer_serial_number` VARCHAR(255) NULL DEFAULT NULL ,
  `toner_config_id` VARCHAR(255) NOT NULL ,
  `is_copier` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_fax` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_duplex` TINYINT(4) NOT NULL DEFAULT '0' ,
  `is_scanner` TINYINT(4) NOT NULL DEFAULT '0' ,
  `watts_power_normal` DOUBLE NULL DEFAULT NULL ,
  `watts_power_idle` DOUBLE NULL DEFAULT NULL ,
  `cost` DOUBLE NULL DEFAULT NULL ,
  `launch_date` DATETIME NULL DEFAULT NULL ,
  `date_created` DATETIME NOT NULL ,
  `black_toner_sku` VARCHAR(255) NULL DEFAULT NULL ,
  `black_toner_cost` DOUBLE NULL DEFAULT NULL ,
  `black_toner_yield` INT(11) NULL DEFAULT NULL ,
  `cyan_toner_sku` VARCHAR(255) NULL DEFAULT NULL ,
  `cyan_toner_cost` DOUBLE NULL DEFAULT NULL ,
  `cyan_toner_yield` INT(11) NULL DEFAULT NULL ,
  `magenta_toner_sku` VARCHAR(255) NULL DEFAULT NULL ,
  `magenta_toner_cost` DOUBLE NULL DEFAULT NULL ,
  `magenta_toner_yield` INT(11) NULL DEFAULT NULL ,
  `yellow_toner_sku` VARCHAR(255) NULL DEFAULT NULL ,
  `yellow_toner_cost` DOUBLE NULL DEFAULT NULL ,
  `yellow_toner_yield` INT(11) NULL DEFAULT NULL ,
  `three_color_toner_sku` VARCHAR(255) NULL DEFAULT NULL ,
  `three_color_toner_cost` DOUBLE NULL DEFAULT NULL ,
  `three_color_toner_yield` INT(11) NULL DEFAULT NULL ,
  `four_color_toner_sku` VARCHAR(255) NULL DEFAULT NULL ,
  `four_color_toner_cost` DOUBLE NULL DEFAULT NULL ,
  `four_color_toner_yield` INT(11) NULL DEFAULT NULL ,
  `black_comp_sku` VARCHAR(255) NULL DEFAULT NULL ,
  `black_comp_cost` DOUBLE NULL DEFAULT NULL ,
  `black_comp_yield` INT(11) NULL DEFAULT NULL ,
  `cyan_comp_sku` VARCHAR(255) NULL DEFAULT NULL ,
  `cyan_comp_cost` DOUBLE NULL DEFAULT NULL ,
  `cyan_comp_yield` INT(11) NULL DEFAULT NULL ,
  `magenta_comp_sku` VARCHAR(255) NULL DEFAULT NULL ,
  `magenta_comp_cost` DOUBLE NULL DEFAULT NULL ,
  `magenta_comp_yield` INT(11) NULL DEFAULT NULL ,
  `yellow_comp_sku` VARCHAR(255) NULL DEFAULT NULL ,
  `yellow_comp_cost` DOUBLE NULL DEFAULT NULL ,
  `yellow_comp_yield` INT(11) NULL DEFAULT NULL ,
  `three_color_comp_sku` VARCHAR(255) NULL DEFAULT NULL ,
  `three_color_comp_cost` DOUBLE NULL DEFAULT NULL ,
  `three_color_comp_yield` INT(11) NULL DEFAULT NULL ,
  `four_color_comp_sku` VARCHAR(255) NULL DEFAULT NULL ,
  `four_color_comp_cost` DOUBLE NULL DEFAULT NULL ,
  `four_color_comp_yield` INT(11) NULL DEFAULT NULL ,
  `start_meter_life` INT(11) NULL DEFAULT NULL ,
  `end_meter_life` INT(11) NULL DEFAULT NULL ,
  `start_meter_black` INT(11) NULL DEFAULT NULL ,
  `end_meter_black` INT(11) NULL DEFAULT NULL ,
  `start_meter_color` INT(11) NULL DEFAULT NULL ,
  `end_meter_color` INT(11) NULL DEFAULT NULL ,
  `start_meter_printblack` INT(11) NULL DEFAULT NULL ,
  `end_meter_printblack` INT(11) NULL DEFAULT NULL ,
  `start_meter_printcolor` INT(11) NULL DEFAULT NULL ,
  `end_meter_printcolor` INT(11) NULL DEFAULT NULL ,
  `start_meter_copyblack` INT(11) NULL DEFAULT NULL ,
  `end_meter_copyblack` INT(11) NULL DEFAULT NULL ,
  `start_meter_copycolor` INT(11) NULL DEFAULT NULL ,
  `end_meter_copycolor` INT(11) NULL DEFAULT NULL ,
  `start_meter_fax` INT(11) NULL DEFAULT NULL ,
  `end_meter_fax` INT(11) NULL DEFAULT NULL ,
  `start_meter_scan` INT(11) NULL DEFAULT NULL ,
  `end_meter_scan` INT(11) NULL DEFAULT NULL ,
  `jit_supplies_supported` TINYINT(4) NULL DEFAULT '0' ,
  `is_excluded` TINYINT(4) NULL DEFAULT '0' ,
  `is_leased` TINYINT(4) NULL DEFAULT '0' ,
  `ip_address` VARCHAR(255) NULL DEFAULT NULL ,
  `duty_cycle` INT(11) NULL DEFAULT NULL ,
  `ppm_black` DOUBLE NULL DEFAULT NULL ,
  `ppm_color` DOUBLE NULL DEFAULT NULL ,
  `service_cost_per_page` DOUBLE NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `report_id` (`report_id` ASC) ,
  INDEX `user_id` (`user_id` ASC) ,
  INDEX `upload_data_collector_row_id` (`upload_data_collector_row_id` ASC) ,
  CONSTRAINT `proposalgenerator_unknown_device_instances_ibfk_1`
    FOREIGN KEY (`report_id` )
    REFERENCES `pgen_reports` (`id` ),
  CONSTRAINT `proposalgenerator_unknown_device_instances_ibfk_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` ),
  CONSTRAINT `proposalgenerator_unknown_device_instances_ibfk_3`
    FOREIGN KEY (`upload_data_collector_row_id` )
    REFERENCES `pgen_upload_data_collector_rows` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_user_device_overrides`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_user_device_overrides` (
  `user_id` INT(11) NOT NULL ,
  `master_device_id` INT(11) NOT NULL ,
  `cost` DOUBLE NOT NULL ,
  `is_leased` TINYINT(4) NULL DEFAULT '0' ,
  PRIMARY KEY (`user_id`) ,
  INDEX `master_device_id` (`master_device_id` ASC) ,
  CONSTRAINT `proposalgenerator_user_device_overrides_ibfk_1`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `pgen_master_devices` (`id` ),
  CONSTRAINT `proposalgenerator_user_device_overrides_ibfk_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pgen_user_pf_device_matchups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pgen_user_pf_device_matchups` (
  `pf_device_id` INT(11) NOT NULL ,
  `master_device_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`pf_device_id`, `master_device_id`, `user_id`) ,
  UNIQUE INDEX `pf_device_id` (`pf_device_id` ASC, `user_id` ASC) ,
  INDEX `master_device_id` (`master_device_id` ASC) ,
  INDEX `user_id` (`user_id` ASC) ,
  CONSTRAINT `proposalgenerator_user_pf_device_matchups_ibfk_1`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `pgen_master_devices` (`id` ),
  CONSTRAINT `proposalgenerator_user_pf_device_matchups_ibfk_2`
    FOREIGN KEY (`pf_device_id` )
    REFERENCES `pgen_pf_devices` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `proposalgenerator_user_pf_device_matchups_ibfk_3`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` ))
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
    REFERENCES `users` (`id` ),
  CONSTRAINT `proposalgenerator_user_report_settings_ibfk_2`
    FOREIGN KEY (`reportSettingId` )
    REFERENCES `pgen_report_settings` (`id` ))
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
    REFERENCES `users` (`id` ),
  CONSTRAINT `proposalgenerator_user_survey_settings_ibfk_2`
    FOREIGN KEY (`surveySettingId` )
    REFERENCES `pgen_survey_settings` (`id` ))
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
  INDEX `proposalgenerator_user_toner_overrides_ibfk_2` (`user_id` ASC) ,
  CONSTRAINT `proposalgenerator_user_toner_overrides_ibfk_1`
    FOREIGN KEY (`toner_id` )
    REFERENCES `pgen_toners` (`id` ),
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
  INDEX `quotegen_devices_ibfk_1` (`masterDeviceId` ASC) ,
  CONSTRAINT `quotegen_devices_ibfk_1`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `pgen_master_devices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
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
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Stores information on different leasing schemas' ;


-- -----------------------------------------------------
-- Table `qgen_global_leasing_schemas`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qgen_global_leasing_schemas` (
  `leasingSchemaId` INT(11) NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`leasingSchemaId`) ,
  INDEX `quotegen_global_leasing_schemas_ibfk_1` (`leasingSchemaId` ASC) ,
  CONSTRAINT `quotegen_global_leasing_schemas_ibfk_1`
    FOREIGN KEY (`leasingSchemaId` )
    REFERENCES `qgen_leasing_schemas` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'This table marks leasing schemas as global' ;


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
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Stores the available value ranges (start range) for a leasin' ;


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
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Holds the terms available for a leasing schema' ;


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
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Stores the rates that coincide with the terms and ranges for' ;


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
    REFERENCES `qgen_categories` (`id` ),
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
  INDEX `quotegen_quotes_ibfk_2` (`userId` ASC) ,
  INDEX `quotegen_quotes_ibfk_3` (`pricingConfigId` ASC) ,
  CONSTRAINT `quotegen_quotes_ibfk_1`
    FOREIGN KEY (`clientId` )
    REFERENCES `clients` (`id` ),
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
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Primary table for a quote. Stores basic information' ;


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
  INDEX `quotegen_quote_device_option_options_ibfk_1` (`optionId` ASC, `masterDeviceId` ASC) ,
  INDEX `quotegen_quote_device_option_options_ibfk_2` (`quoteDeviceOptionId` ASC) ,
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
  INDEX `quotegen_quote_settings_ibfk1` (`pricingConfigId` ASC) ,
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
  INDEX `FK_userRoles_roles` (`roleId` ASC) ,
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
  INDEX `fk_qgen_quote_lease_terms_qgen_quotes1` (`quoteId` ASC) ,
  INDEX `fk_qgen_quote_lease_terms_qgen_leasing_schema_terms1` (`leasingSchemaTermId` ASC) ,
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
  INDEX `quotegen_quote_device_groups_ibfk_1` (`quoteId` ASC) ,
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
  INDEX `qgen_quote_device_group_devices_ibfk1` (`quoteDeviceId` ASC) ,
  INDEX `qgen_quote_device_group_devices_ibfk2` (`quoteDeviceGroupId` ASC) ,
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
  `countryCode` SMALLINT NOT NULL ,
  `areaCode` SMALLINT NOT NULL ,
  `exchangeCode` SMALLINT NOT NULL ,
  `number` SMALLINT NOT NULL ,
  `extension` SMALLINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `contacts_ibfk_1` (`clientId` ASC) ,
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
  INDEX `addresses_ibfk_1` (`clientId` ASC) ,
  INDEX `addresses_ibfk_2` (`countryId` ASC) ,
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



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

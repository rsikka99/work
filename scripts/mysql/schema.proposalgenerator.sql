SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `proposalgenerator_toner_configs`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_toner_configs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name` (`name` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_master_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_master_devices` (
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
    REFERENCES `proposalgenerator_toner_configs` (`id` ),
  CONSTRAINT `proposalgenerator_master_devices_ibfk_1`
    FOREIGN KEY (`manufacturer_id` )
    REFERENCES `manufacturers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_part_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_part_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_pricing_configs`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_pricing_configs` (
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
    REFERENCES `proposalgenerator_part_types` (`id` ),
  CONSTRAINT `proposalgenerator_pricing_configs_ibfk_2`
    FOREIGN KEY (`mono_toner_part_type_id` )
    REFERENCES `proposalgenerator_part_types` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_question_sets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_question_sets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_reports`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_reports` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `customer_company_name` VARCHAR(255) NOT NULL ,
  `user_pricing_override` TINYINT(4) NULL DEFAULT '0' ,
  `report_stage` ENUM('company','general','finance','purchasing','it','users','verify','upload','mapdevices','summary','reportsettings','finished') NULL DEFAULT NULL ,
  `questionset_id` INT(11) NOT NULL ,
  `date_created` DATETIME NOT NULL ,
  `last_modified` DATETIME NOT NULL ,
  `report_date` DATETIME NULL DEFAULT NULL ,
  `devices_modified` TINYINT(4) NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `user_id` (`user_id` ASC) ,
  INDEX `questionset_id` (`questionset_id` ASC) ,
  CONSTRAINT `proposalgenerator_reports_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` ),
  CONSTRAINT `proposalgenerator_reports_ibfk_2`
    FOREIGN KEY (`questionset_id` )
    REFERENCES `proposalgenerator_question_sets` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_questions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_questions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `description` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 31
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_date_answers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_date_answers` (
  `question_id` INT(11) NOT NULL ,
  `report_id` INT(11) NOT NULL ,
  `date_answer` DATETIME NOT NULL ,
  PRIMARY KEY (`question_id`, `report_id`) ,
  INDEX `report_id` (`report_id` ASC) ,
  CONSTRAINT `proposalgenerator_date_answers_ibfk_1`
    FOREIGN KEY (`report_id` )
    REFERENCES `proposalgenerator_reports` (`id` ),
  CONSTRAINT `proposalgenerator_date_answers_ibfk_2`
    FOREIGN KEY (`question_id` )
    REFERENCES `proposalgenerator_questions` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_upload_data_collector_rows`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_upload_data_collector_rows` (
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
    REFERENCES `proposalgenerator_reports` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 477
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_device_instances`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_device_instances` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `report_id` INT(11) NOT NULL ,
  `master_device_id` INT(11) NOT NULL ,
  `upload_data_collector_id` INT(11) NOT NULL ,
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
  INDEX `upload_data_collector_id` (`upload_data_collector_id` ASC) ,
  CONSTRAINT `proposalgenerator_device_instances_ibfk_1`
    FOREIGN KEY (`report_id` )
    REFERENCES `proposalgenerator_reports` (`id` ),
  CONSTRAINT `proposalgenerator_device_instances_ibfk_2`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `proposalgenerator_master_devices` (`id` ),
  CONSTRAINT `proposalgenerator_device_instances_ibfk_3`
    FOREIGN KEY (`upload_data_collector_id` )
    REFERENCES `proposalgenerator_upload_data_collector_rows` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_device_instance_meters`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_device_instance_meters` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `device_instance_id` INT(11) NOT NULL ,
  `meter_type` ENUM('LIFE','COLOR','COPY BLACK','BLACK','PRINT BLACK','PRINT COLOR','COPY COLOR','SCAN','FAX') NULL DEFAULT NULL ,
  `start_meter` INT(11) NOT NULL ,
  `end_meter` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `device_instance_id` (`device_instance_id` ASC, `meter_type` ASC) ,
  CONSTRAINT `proposalgenerator_device_instance_meters_ibfk_1`
    FOREIGN KEY (`device_instance_id` )
    REFERENCES `proposalgenerator_device_instances` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_toner_colors`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_toner_colors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_toners`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_toners` (
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
    REFERENCES `proposalgenerator_part_types` (`id` ),
  CONSTRAINT `proposalgenerator_toners_ibfk_3`
    FOREIGN KEY (`toner_color_id` )
    REFERENCES `proposalgenerator_toner_colors` (`id` ),
  CONSTRAINT `proposalgenerator_toners_ibfk_2`
    FOREIGN KEY (`manufacturer_id` )
    REFERENCES `manufacturers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_device_toners`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_device_toners` (
  `toner_id` INT(11) NOT NULL ,
  `master_device_id` INT(11) NOT NULL ,
  PRIMARY KEY (`toner_id`, `master_device_id`) ,
  INDEX `master_device_id` (`master_device_id` ASC) ,
  CONSTRAINT `proposalgenerator_device_toners_ibfk_1`
    FOREIGN KEY (`toner_id` )
    REFERENCES `proposalgenerator_toners` (`id` ),
  CONSTRAINT `proposalgenerator_device_toners_ibfk_2`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `proposalgenerator_master_devices` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_pf_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_pf_devices` (
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
-- Table `proposalgenerator_master_pf_device_matchups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_master_pf_device_matchups` (
  `master_device_id` INT(11) NOT NULL ,
  `pf_device_id` INT(11) NOT NULL ,
  PRIMARY KEY (`master_device_id`, `pf_device_id`) ,
  UNIQUE INDEX `pf_device_id` (`pf_device_id` ASC) ,
  CONSTRAINT `proposalgenerator_master_pf_device_matchups_ibfk_1`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `proposalgenerator_master_devices` (`id` ),
  CONSTRAINT `proposalgenerator_master_pf_device_matchups_ibfk_2`
    FOREIGN KEY (`pf_device_id` )
    REFERENCES `proposalgenerator_pf_devices` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_numeric_answers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_numeric_answers` (
  `question_id` INT(11) NOT NULL ,
  `report_id` INT(11) NOT NULL ,
  `numeric_answer` DOUBLE NOT NULL ,
  PRIMARY KEY (`question_id`, `report_id`) ,
  INDEX `report_id` (`report_id` ASC) ,
  CONSTRAINT `proposalgenerator_numeric_answers_ibfk_1`
    FOREIGN KEY (`report_id` )
    REFERENCES `proposalgenerator_reports` (`id` ),
  CONSTRAINT `proposalgenerator_numeric_answers_ibfk_2`
    FOREIGN KEY (`question_id` )
    REFERENCES `proposalgenerator_questions` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_questionset_questions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_questionset_questions` (
  `question_id` INT(11) NOT NULL ,
  `questionset_id` INT(11) NOT NULL ,
  PRIMARY KEY (`question_id`, `questionset_id`) ,
  INDEX `questionset_id` (`questionset_id` ASC) ,
  CONSTRAINT `proposalgenerator_questionset_questions_ibfk_1`
    FOREIGN KEY (`questionset_id` )
    REFERENCES `proposalgenerator_question_sets` (`id` ),
  CONSTRAINT `proposalgenerator_questionset_questions_ibfk_2`
    FOREIGN KEY (`question_id` )
    REFERENCES `proposalgenerator_questions` (`id` )
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_replacement_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_replacement_devices` (
  `master_device_id` INT(11) NOT NULL ,
  `replacement_category` ENUM('BLACK & WHITE','BLACK & WHITE MFP','COLOR','COLOR MFP') NULL DEFAULT NULL ,
  `print_speed` INT(11) NULL DEFAULT NULL ,
  `resolution` INT(11) NULL DEFAULT NULL ,
  `monthly_rate` DOUBLE NULL DEFAULT NULL ,
  PRIMARY KEY (`master_device_id`) ,
  CONSTRAINT `proposalgenerator_replacement_devices_ibfk_1`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `proposalgenerator_master_devices` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_report_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_report_settings` (
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
    REFERENCES `proposalgenerator_pricing_configs` (`id` ),
  CONSTRAINT `proposalgenerator_report_settings_ibfk_2`
    FOREIGN KEY (`grossMarginPricingConfigId` )
    REFERENCES `proposalgenerator_pricing_configs` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_report_report_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_report_report_settings` (
  `report_id` INT(11) NOT NULL ,
  `report_setting_id` INT(11) NOT NULL ,
  PRIMARY KEY (`report_id`, `report_setting_id`) ,
  INDEX `report_setting_id` (`report_setting_id` ASC) ,
  CONSTRAINT `proposalgenerator_report_report_settings_ibfk_1`
    FOREIGN KEY (`report_id` )
    REFERENCES `proposalgenerator_reports` (`id` ),
  CONSTRAINT `proposalgenerator_report_report_settings_ibfk_2`
    FOREIGN KEY (`report_setting_id` )
    REFERENCES `proposalgenerator_report_settings` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_survey_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_survey_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `page_coverage_mono` DOUBLE NULL DEFAULT NULL ,
  `page_coverage_color` DOUBLE NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_report_survey_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_report_survey_settings` (
  `report_id` INT(11) NOT NULL ,
  `survey_setting_id` INT(11) NOT NULL ,
  PRIMARY KEY (`report_id`, `survey_setting_id`) ,
  INDEX `survey_setting_id` (`survey_setting_id` ASC) ,
  CONSTRAINT `proposalgenerator_report_survey_settings_ibfk_1`
    FOREIGN KEY (`report_id` )
    REFERENCES `proposalgenerator_reports` (`id` ),
  CONSTRAINT `proposalgenerator_report_survey_settings_ibfk_2`
    FOREIGN KEY (`survey_setting_id` )
    REFERENCES `proposalgenerator_survey_settings` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_textual_answers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_textual_answers` (
  `question_id` INT(11) NOT NULL ,
  `report_id` INT(11) NOT NULL ,
  `textual_answer` TEXT NOT NULL ,
  PRIMARY KEY (`question_id`, `report_id`) ,
  INDEX `report_id` (`report_id` ASC) ,
  CONSTRAINT `proposalgenerator_textual_answers_ibfk_1`
    FOREIGN KEY (`report_id` )
    REFERENCES `proposalgenerator_reports` (`id` ),
  CONSTRAINT `proposalgenerator_textual_answers_ibfk_2`
    FOREIGN KEY (`question_id` )
    REFERENCES `proposalgenerator_questions` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_ticket_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_ticket_categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_ticket_statuses`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_ticket_statuses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_tickets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_tickets` (
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
    REFERENCES `proposalgenerator_ticket_categories` (`id` ),
  CONSTRAINT `proposalgenerator_tickets_ibfk_3`
    FOREIGN KEY (`status_id` )
    REFERENCES `proposalgenerator_ticket_statuses` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_ticket_comments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_ticket_comments` (
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
    REFERENCES `proposalgenerator_tickets` (`id` ),
  CONSTRAINT `proposalgenerator_ticket_comments_ibfk_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_ticket_pf_requests`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_ticket_pf_requests` (
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
    REFERENCES `proposalgenerator_tickets` (`id` ),
  CONSTRAINT `proposalgenerator_ticket_pf_requests_ibfk_3`
    FOREIGN KEY (`pf_device_id` )
    REFERENCES `proposalgenerator_pf_devices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_tickets_viewed`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_tickets_viewed` (
  `ticket_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `date_viewed` DATETIME NOT NULL ,
  PRIMARY KEY (`ticket_id`, `user_id`) ,
  INDEX `user_id` (`user_id` ASC) ,
  CONSTRAINT `proposalgenerator_tickets_viewed_ibfk_1`
    FOREIGN KEY (`ticket_id` )
    REFERENCES `proposalgenerator_tickets` (`id` ),
  CONSTRAINT `proposalgenerator_tickets_viewed_ibfk_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_unknown_device_instances`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_unknown_device_instances` (
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
    REFERENCES `proposalgenerator_reports` (`id` ),
  CONSTRAINT `proposalgenerator_unknown_device_instances_ibfk_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` ),
  CONSTRAINT `proposalgenerator_unknown_device_instances_ibfk_3`
    FOREIGN KEY (`upload_data_collector_row_id` )
    REFERENCES `proposalgenerator_upload_data_collector_rows` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_user_device_overrides`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_user_device_overrides` (
  `user_id` INT(11) NOT NULL ,
  `master_device_id` INT(11) NOT NULL ,
  `cost` DOUBLE NOT NULL ,
  `is_leased` TINYINT(4) NULL DEFAULT '0' ,
  PRIMARY KEY (`user_id`) ,
  INDEX `master_device_id` (`master_device_id` ASC) ,
  CONSTRAINT `proposalgenerator_user_device_overrides_ibfk_1`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `proposalgenerator_master_devices` (`id` ),
  CONSTRAINT `proposalgenerator_user_device_overrides_ibfk_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_user_pf_device_matchups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_user_pf_device_matchups` (
  `pf_device_id` INT(11) NOT NULL ,
  `master_device_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  PRIMARY KEY (`pf_device_id`, `master_device_id`, `user_id`) ,
  UNIQUE INDEX `pf_device_id` (`pf_device_id` ASC, `user_id` ASC) ,
  INDEX `master_device_id` (`master_device_id` ASC) ,
  INDEX `user_id` (`user_id` ASC) ,
  CONSTRAINT `proposalgenerator_user_pf_device_matchups_ibfk_1`
    FOREIGN KEY (`master_device_id` )
    REFERENCES `proposalgenerator_master_devices` (`id` ),
  CONSTRAINT `proposalgenerator_user_pf_device_matchups_ibfk_2`
    FOREIGN KEY (`pf_device_id` )
    REFERENCES `proposalgenerator_pf_devices` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `proposalgenerator_user_pf_device_matchups_ibfk_3`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_user_report_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_user_report_settings` (
  `user_id` INT(11) NOT NULL ,
  `report_setting_id` INT(11) NOT NULL ,
  PRIMARY KEY (`user_id`, `report_setting_id`) ,
  INDEX `report_setting_id` (`report_setting_id` ASC) ,
  CONSTRAINT `proposalgenerator_user_report_settings_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` ),
  CONSTRAINT `proposalgenerator_user_report_settings_ibfk_2`
    FOREIGN KEY (`report_setting_id` )
    REFERENCES `proposalgenerator_report_settings` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_user_survey_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_user_survey_settings` (
  `user_id` INT(11) NOT NULL ,
  `survey_setting_id` INT(11) NOT NULL ,
  PRIMARY KEY (`user_id`, `survey_setting_id`) ,
  INDEX `survey_setting_id` (`survey_setting_id` ASC) ,
  CONSTRAINT `proposalgenerator_user_survey_settings_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` ),
  CONSTRAINT `proposalgenerator_user_survey_settings_ibfk_2`
    FOREIGN KEY (`survey_setting_id` )
    REFERENCES `proposalgenerator_survey_settings` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `proposalgenerator_user_toner_overrides`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `proposalgenerator_user_toner_overrides` (
  `user_id` INT(11) NOT NULL ,
  `toner_id` INT(11) NOT NULL ,
  `cost` DOUBLE NOT NULL ,
  INDEX `toner_id` (`toner_id` ASC) ,
  PRIMARY KEY (`toner_id`, `user_id`) ,
  CONSTRAINT `proposalgenerator_user_toner_overrides_ibfk_1`
    FOREIGN KEY (`toner_id` )
    REFERENCES `proposalgenerator_toners` (`id` ),
  CONSTRAINT `proposalgenerator_user_toner_overrides_ibfk_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

CREATE TABLE `proposalgenerator_part_types` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `name`                              VARCHAR(255)    NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_pricing_configs` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `name`                              VARCHAR(255)    NOT NULL,
    `color_toner_part_type_id`          INTEGER                     DEFAULT NULL,
    `mono_toner_part_type_id`           INTEGER                     DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE (`name`),
    FOREIGN KEY (`color_toner_part_type_id`) REFERENCES `proposalgenerator_part_types` (`id`),
    FOREIGN KEY (`mono_toner_part_type_id`)  REFERENCES `proposalgenerator_part_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `proposalgenerator_report_settings` (
    `id`                                    INTEGER            NOT NULL AUTO_INCREMENT,
    `actualPageCoverageMono`                DOUBLE                      DEFAULT NULL,
    `actualPageCoverageColor`               DOUBLE                      DEFAULT NULL,
    `serviceCostPerPage`                    DOUBLE                      DEFAULT NULL,
    `adminCostPerPage`                      DOUBLE                      DEFAULT NULL,
    `assessmentReportMargin`                DOUBLE                      DEFAULT NULL,
    `grossMarginReportMargin`               DOUBLE                      DEFAULT NULL,
    `monthlyLeasePayment`                   DOUBLE                      DEFAULT NULL,
    `defaultPrinterCost`                    DOUBLE                      DEFAULT NULL,
    `leasedBwCostPerPage`                   DOUBLE                      DEFAULT NULL,
    `leasedColorCostPerPage`                DOUBLE                      DEFAULT NULL,
    `mpsBwCostPerPage`                      DOUBLE                      DEFAULT NULL,
    `mpsColorCostPerPage`                   DOUBLE                      DEFAULT NULL,
    `kilowattsPerHour`                      DOUBLE                      DEFAULT NULL,
    `assessmentPricingConfigId`             INTEGER                     DEFAULT NULL,
    `grossMarginPricingConfigId`            INTEGER                     DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`assessmentPricingConfigId`)  REFERENCES `proposalgenerator_pricing_configs` (`id`),
    FOREIGN KEY (`grossMarginPricingConfigId`)  REFERENCES `proposalgenerator_pricing_configs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `proposalgenerator_survey_settings` (
    `id`                                    INTEGER            NOT NULL AUTO_INCREMENT,
    `page_coverage_mono`                    DOUBLE                      DEFAULT NULL,
    `page_coverage_color`                   DOUBLE                      DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_question_sets` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `name`                              VARCHAR(255),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- reports
-- Each report belongs to one user.
-- Defines reports (all properties associated with building
-- assessment and proposal output.
-- IMPORTANT NOTE: user_pricing_override flag is set to true
-- when reports are generated with user specific pricing
-- wherever found.  If flag is false, always use system 
-- pricing.
-- -----------------------------------------------------

CREATE TABLE `proposalgenerator_reports` (
    `id`                                        INTEGER         NOT NULL AUTO_INCREMENT,
    `user_id`                                   INTEGER         NOT NULL,
    `customer_company_name`                     VARCHAR(255)    NOT NULL,
    `user_pricing_override`                     TINYINT                     DEFAULT FALSE,
    `report_stage`                              ENUM('company', 'general', 'finance', 'purchasing', 'it', 'users', 'verify', 'upload', 'mapdevices', 'summary', 'reportsettings', 'finished'),
    `questionset_id`                            INTEGER         NOT NULL,
    `date_created`                              DATETIME        NOT NULL,
    `last_modified`                             DATETIME        NOT NULL,  
    `report_date`                               DATETIME                    DEFAULT NULL,
    `devices_modified`                          TINYINT                     DEFAULT FALSE,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`questionset_id`) REFERENCES `proposalgenerator_question_sets` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `manufacturers` (
    `id`                                    INTEGER         NOT NULL AUTO_INCREMENT,
    `fullname`                              VARCHAR(255)    NOT NULL,
    `displayname`                           VARCHAR(255)    NOT NULL,
    `is_deleted`                            TINYINT                     DEFAULT FALSE,
    UNIQUE(`fullname`),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- toner_config
-- Defines different types of color toner configurations
-- that can be assigned to printers.  
-- ie. "Separated Cyan, Magenta, Yellow, Black",
--     "3 Color", "4 Color", "Black Only" 
-- -----------------------------------------------------

CREATE TABLE `proposalgenerator_toner_configs` (
    `id`                                     INTEGER         NOT NULL AUTO_INCREMENT,
    `name`                                   VARCHAR(255)    NOT NULL, 
    UNIQUE(`name`),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- master_device
-- Keeps track of all printer models and the properties
-- associated with each model.
-- -----------------------------------------------------

CREATE TABLE `proposalgenerator_master_devices` (
    `id`                                    INTEGER         NOT NULL AUTO_INCREMENT,
    `manufacturer_id`                       INTEGER         NOT NULL,
    `printer_model`                         VARCHAR(255)    NOT NULL,  
    `toner_config_id`                       INTEGER         NOT NULL, 
    `is_copier`                             TINYINT         NOT NULL    DEFAULT FALSE,
    `is_fax`                                TINYINT         NOT NULL    DEFAULT FALSE,
    `is_scanner`                            TINYINT         NOT NULL    DEFAULT FALSE,
    `is_duplex`                             TINYINT         NOT NULL    DEFAULT FALSE,
    `is_replacement_device`                 TINYINT         NOT NULL    DEFAULT FALSE,
    `watts_power_normal`                    DOUBLE                      DEFAULT NULL,
    `watts_power_idle`                      DOUBLE                      DEFAULT NULL,
    `device_price`                          DOUBLE                      DEFAULT NULL,
    `service_cost_per_page`                 DOUBLE                      DEFAULT NULL,
    `launch_date`                           DATETIME        NOT NULL, 
    `date_created`                          DATETIME        NOT NULL,
    `duty_cycle`                            INTEGER                     DEFAULT NULL,
    `ppm_black`                             DOUBLE                      DEFAULT NULL,
    `ppm_color`                             DOUBLE                      DEFAULT NULL,
    `is_leased`                             TINYINT         NOT NULL    DEFAULT FALSE,
    `leased_toner_yield`                    INTEGER                       DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`manufacturer_id`, `printer_model`),
    FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturers` (`id`),
    FOREIGN KEY (`toner_config_id`) REFERENCES `proposalgenerator_toner_configs` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_replacement_devices` (
    `master_device_id`                      INTEGER         NOT NULL,
    `replacement_category`                  ENUM ('BLACK & WHITE','BLACK & WHITE MFP','COLOR','COLOR MFP'), 
    `print_speed`                           INTEGER                     DEFAULT NULL,
    `resolution`                            INTEGER                     DEFAULT NULL,
    `monthly_rate`                          DOUBLE                      DEFAULT NULL,   
    PRIMARY KEY (`master_device_id`),
    FOREIGN KEY (`master_device_id`) REFERENCES `proposalgenerator_master_devices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_pf_devices` (
    `id`                                    INTEGER         NOT NULL AUTO_INCREMENT,
    `pf_model_id`                           INTEGER         NOT NULL,
    `pf_db_devicename`                      VARCHAR(255)    NOT NULL,
    `pf_db_manufacturer`                    VARCHAR(255)                DEFAULT NULL,
    `date_created`                          DATETIME        NOT NULL,
    `created_by`                            INTEGER                     DEFAULT NULL,
    UNIQUE (`pf_model_id`),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `proposalgenerator_user_pf_device_matchups` (
    `pf_device_id`                          INTEGER         NOT NULL,
    `master_device_id`                      INTEGER         NOT NULL,
    `user_id`                               INTEGER         NOT NULL,
    UNIQUE (`pf_device_id`, `user_id`),
    PRIMARY KEY (`pf_device_id`, `master_device_id`, `user_id`),
    FOREIGN KEY (`master_device_id`) REFERENCES `proposalgenerator_master_devices` (`id`),
    FOREIGN KEY (`pf_device_id`) REFERENCES `proposalgenerator_pf_devices` (`id`)
            ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_master_pf_device_matchups` (
    `master_device_id`                        INTEGER         NOT NULL,
    `pf_device_id`                            INTEGER         NOT NULL,
    UNIQUE (`pf_device_id`),
    PRIMARY KEY (`master_device_id`, `pf_device_id`),
    FOREIGN KEY (`master_device_id`) REFERENCES `proposalgenerator_master_devices` (`id`),
    FOREIGN KEY (`pf_device_id`) REFERENCES `proposalgenerator_pf_devices` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_questions` (
    `id`                                    INTEGER         NOT NULL AUTO_INCREMENT,
    `description`                           TEXT                        DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_questionset_questions` (
    `question_id`                           INTEGER         NOT NULL,
    `questionset_id`                        INTEGER         NOT NULL,
    PRIMARY KEY (`question_id`, `questionset_id`),
    FOREIGN KEY (`questionset_id`) REFERENCES `proposalgenerator_question_sets` (`id`)
            ON DELETE RESTRICT,
    FOREIGN KEY (`question_id`) REFERENCES `proposalgenerator_questions` (`id`)
            ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_date_answers` (
    question_id                             INTEGER         NOT NULL,
    report_id                               INTEGER         NOT NULL,
    date_answer                             DATETIME        NOT NULL,
    PRIMARY KEY (`question_id`, `report_id`),
    FOREIGN KEY (`report_id`) REFERENCES `proposalgenerator_reports` (`id`)
            ON DELETE RESTRICT,
    FOREIGN KEY (`question_id`) REFERENCES `proposalgenerator_questions` (`id`)
            ON DELETE RESTRICT
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_numeric_answers` (
    question_id                             INTEGER         NOT NULL,
    report_id                               INTEGER         NOT NULL,
    numeric_answer                          DOUBLE          NOT NULL,
    PRIMARY KEY (`question_id`, `report_id`),
    FOREIGN KEY (`report_id`) REFERENCES `proposalgenerator_reports` (`id`)
            ON DELETE RESTRICT,
    FOREIGN KEY (`question_id`) REFERENCES `proposalgenerator_questions` (`id`)
            ON DELETE RESTRICT
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `proposalgenerator_textual_answers` (
    question_id                             INTEGER         NOT NULL,
    report_id                               INTEGER         NOT NULL,
    textual_answer                          TEXT            NOT NULL,
    PRIMARY KEY (`question_id`, `report_id`),
    FOREIGN KEY (`report_id`) REFERENCES `proposalgenerator_reports` (`id`)
            ON DELETE RESTRICT,
    FOREIGN KEY (`question_id`) REFERENCES `proposalgenerator_questions` (`id`)
            ON DELETE RESTRICT
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_toner_colors` (
    `id`                            INTEGER         NOT NULL AUTO_INCREMENT,
    `name`                          VARCHAR(255)    NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `proposalgenerator_toners` (
    `id`                            INTEGER         NOT NULL AUTO_INCREMENT, 
    `sku`                           VARCHAR(255)    NOT NULL,
    `price`                         DOUBLE          NOT NULL,
    `yield`                         INTEGER         NOT NULL,
    `part_type_id`                  INTEGER         NOT NULL,
    `manufacturer_id`               INTEGER         NOT NULL,
    `toner_color_id`                INTEGER         NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE(`sku`),
    UNIQUE(`sku`, `manufacturer_id`, `part_type_id`, `yield`,`toner_color_id`),  
    FOREIGN KEY (`part_type_id`) REFERENCES `proposalgenerator_part_types` (`id`),
    FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturers` (`id`),
    FOREIGN KEY (`toner_color_id`) REFERENCES `proposalgenerator_toner_colors` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- device_toner
-- Bridge talbe between "toner" and "master_device."
-- Deterines which toners belong to which printers,
-- and vice versa.
-- -----------------------------------------------------

CREATE TABLE `proposalgenerator_device_toners` (
    `toner_id`                      INTEGER         NOT NULL,
    `master_device_id`              INTEGER         NOT NULL,  
    PRIMARY KEY (`toner_id`, `master_device_id`),  
    FOREIGN KEY (`toner_id`) REFERENCES `proposalgenerator_toners` (`id`),
    FOREIGN KEY (`master_device_id`) REFERENCES `proposalgenerator_master_devices` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- user_device_override
-- Override table for user specific override of printer
-- cost.
-- -----------------------------------------------------

CREATE TABLE `proposalgenerator_user_device_overrides` (
    `user_id`                       INTEGER         NOT NULL,
    `master_device_id`              INTEGER         NOT NULL,
    `price`                         DOUBLE          NOT NULL, 
    `is_leased`                     TINYINT                     DEFAULT FALSE,  
    PRIMARY KEY (`user_id`, `master_device_id`),
    FOREIGN KEY (`master_device_id`) REFERENCES `proposalgenerator_master_devices` (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) 
    ON UPDATE CASCADE ON DELETE CASCADE  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- user_toner_override
-- Override table for user specific override of
-- toner costs.
-- -----------------------------------------------------

CREATE TABLE `proposalgenerator_user_toner_overrides` (
      `user_id`                     INTEGER         NOT NULL,
      `toner_id`                    INTEGER         NOT NULL,
      `price`                       DOUBLE          NOT NULL,
      PRIMARY KEY (`user_id`, `toner_id`),
      FOREIGN KEY (`toner_id`) REFERENCES `proposalgenerator_toners` (`id`),
      FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) 
        ON UPDATE CASCADE ON DELETE CASCADE  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- upload_data_collector
-- NOTE: This table just holds all information provided in the data upload.
--     - No Calculations are ever to be done using any of this data.
-- -----------------------------------------------------
CREATE TABLE `proposalgenerator_upload_data_collector_rows` (
    `id`                            INTEGER         NOT NULL AUTO_INCREMENT,
    `report_id`                     INTEGER         NOT NULL,
    `devices_pf_id`                 INTEGER         NOT NULL,
    `startdate`                     DATETIME        NOT NULL,
    `enddate`                       DATETIME        NOT NULL,
    `printermodelid`                INTEGER         NOT NULL,
    `ipaddress`                     VARCHAR(255)                DEFAULT NULL,
    `serialnumber`                  VARCHAR(255)                DEFAULT NULL,
    `modelname`                     VARCHAR(255)    NOT NULL,
    `manufacturer`                  VARCHAR(255)    NOT NULL,
    `is_color`                      TINYINT         NOT NULL    DEFAULT FALSE,
    `is_copier`                     TINYINT         NOT NULL    DEFAULT FALSE,
    `is_scanner`                    TINYINT         NOT NULL    DEFAULT FALSE,
    `is_fax`                        TINYINT         NOT NULL    DEFAULT FALSE,
    `ppm_black`                     DOUBLE                      DEFAULT NULL,
    `ppm_color`                     DOUBLE                      DEFAULT NULL,
    `date_introduction`             DATETIME                    DEFAULT NULL,
    `date_adoption`                 DATETIME                    DEFAULT NULL,
    `discovery_date`                DATETIME                    DEFAULT NULL,
    `black_prodcodeoem`             VARCHAR(255)                DEFAULT NULL,
    `black_yield`                   INTEGER                     DEFAULT NULL,
    `black_prodcostoem`             DOUBLE                      DEFAULT NULL,
    `cyan_prodcodeoem`              VARCHAR(255)                DEFAULT NULL,
    `cyan_yield`                    INTEGER                     DEFAULT NULL,
    `cyan_prodcostoem`              DOUBLE                      DEFAULT NULL,
    `magenta_prodcodeoem`           VARCHAR(255)                DEFAULT NULL,
    `magenta_yield`                 INTEGER                     DEFAULT NULL,
    `magenta_prodcostoem`           DOUBLE                      DEFAULT NULL,
    `yellow_prodcodeoem`            VARCHAR(255)                DEFAULT NULL,
    `yellow_yield`                  INTEGER                     DEFAULT NULL,
    `yellow_prodcostoem`            DOUBLE                      DEFAULT NULL,
    `duty_cycle`                    INTEGER                     DEFAULT NULL,
    `wattspowernormal`              DOUBLE                      DEFAULT NULL,
    `wattspoweridle`                DOUBLE                      DEFAULT NULL,
    `startmeterlife`                INTEGER                     DEFAULT NULL,
    `endmeterlife`                  INTEGER                     DEFAULT NULL,
    `startmeterblack`               INTEGER                     DEFAULT NULL,
    `endmeterblack`                 INTEGER                     DEFAULT NULL,
    `startmetercolor`               INTEGER                     DEFAULT NULL,
    `endmetercolor`                 INTEGER                     DEFAULT NULL,
    `startmeterprintblack`          INTEGER                     DEFAULT NULL,
    `endmeterprintblack`            INTEGER                     DEFAULT NULL,
    `startmeterprintcolor`          INTEGER                     DEFAULT NULL,
    `endmeterprintcolor`            INTEGER                     DEFAULT NULL,
    `startmetercopyblack`           INTEGER                     DEFAULT NULL,
    `endmetercopyblack`             INTEGER                     DEFAULT NULL,
    `startmetercopycolor`           INTEGER                     DEFAULT NULL,
    `endmetercopycolor`             INTEGER                     DEFAULT NULL,
    `startmeterscan`                INTEGER                     DEFAULT NULL,
    `endmeterscan`                  INTEGER                     DEFAULT NULL,
    `startmeterfax`                 INTEGER                     DEFAULT NULL,
    `endmeterfax`                   INTEGER                     DEFAULT NULL,
    `tonerlevel_black`              VARCHAR(255)                DEFAULT NULL,
    `tonerlevel_cyan`               VARCHAR(255)                DEFAULT NULL,
    `tonerlevel_magenta`            VARCHAR(255)                DEFAULT NULL,
    `tonerlevel_yellow`             VARCHAR(255)                DEFAULT NULL,
    `invalid_data`                  TINYINT                     DEFAULT FALSE,
    `is_excluded`                   TINYINT                     DEFAULT FALSE,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`report_id`) REFERENCES `proposalgenerator_reports` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- device_instance
-- Matches reports to the devices used to build the final 
-- report. Each report can belong to devices instances, and
-- each device can belong to many device instances.
-- NOTE: If report specific override for pricing is ever needed,
--       include it in this table.
--     - Device instance table is used to store variables specific
--       to an instance of a printer. Only variables that are not
--       already stored in the master device table are stored here.
--     - Each record links to the upload_data_collector table which
--       contains the initial data collected from the upload file.
-- -----------------------------------------------------
CREATE TABLE `proposalgenerator_device_instances` (
    `id`                            INTEGER         NOT NULL AUTO_INCREMENT,
    `report_id`                     INTEGER         NOT NULL,
    `master_device_id`              INTEGER         NOT NULL,
    `upload_data_collector_id`      INTEGER         NOT NULL,
    `serial_number`                 VARCHAR(255)                DEFAULT NULL,
    `mps_monitor_startdate`         DATETIME        NOT NULL,
    `mps_monitor_enddate`           DATETIME        NOT NULL,
    `mps_discovery_date`            DATETIME                    DEFAULT NULL,
    `jit_supplies_supported`        TINYINT                     DEFAULT FALSE,
    `ip_address`                    VARCHAR(255)                DEFAULT NULL,
    `is_excluded`                   TINYINT                     DEFAULT FALSE,  
    PRIMARY KEY (`id`),
    FOREIGN KEY (`report_id`) REFERENCES `proposalgenerator_reports` (`id`),
    FOREIGN KEY (`master_device_id`) REFERENCES `proposalgenerator_master_devices` (`id`),
    FOREIGN KEY (`upload_data_collector_id`) REFERENCES `proposalgenerator_upload_data_collector_rows` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `proposalgenerator_device_instance_meters` (
    `id`                            INTEGER         NOT NULL AUTO_INCREMENT,
    `device_instance_id`            INTEGER         NOT NULL,
    `meter_type`                    ENUM ('LIFE','COLOR','COPY BLACK','BLACK','PRINT BLACK','PRINT COLOR','COPY COLOR','SCAN','FAX'),
    `start_meter`                   INTEGER         NOT NULL,
    `end_meter`                     INTEGER         NOT NULL,
    UNIQUE (`device_instance_id`, `meter_type`),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`device_instance_id`) REFERENCES `proposalgenerator_device_instances` (`id`)
            ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_unknown_device_instances` (
    `id`                            INTEGER         NOT NULL AUTO_INCREMENT,
    `user_id`                       INTEGER         NOT NULL,
    `report_id`                     INTEGER         NOT NULL,
    `upload_data_collector_row_id`  INTEGER         NOT NULL,
    `printermodelid`                INTEGER         NOT NULL,
    `mps_monitor_startdate`         DATETIME        NOT NULL,
    `mps_monitor_enddate`           DATETIME        NOT NULL,
    `mps_discovery_date`            DATETIME                    DEFAULT NULL,
    `install_date`                  DATETIME                    DEFAULT NULL,
    `device_manufacturer`           VARCHAR(255)    NOT NULL,
    `printer_model`                 VARCHAR(255)    NOT NULL,
    `printer_serial_number`         VARCHAR(255)                DEFAULT NULL,
    `toner_config_id`               VARCHAR(255)    NOT NULL,
    `is_copier`                     TINYINT         NOT NULL    DEFAULT FALSE,
    `is_fax`                        TINYINT         NOT NULL    DEFAULT FALSE,
    `is_duplex`                     TINYINT         NOT NULL    DEFAULT FALSE,
    `is_scanner`                    TINYINT         NOT NULL    DEFAULT FALSE,
    `watts_power_normal`            DOUBLE                      DEFAULT NULL,
    `watts_power_idle`              DOUBLE                      DEFAULT NULL,
    `device_price`                  DOUBLE                      DEFAULT NULL,
    `launch_date`                   DATETIME                    DEFAULT NULL,  
    `date_created`                  DATETIME        NOT NULL,
    `black_toner_SKU`               VARCHAR(255)                DEFAULT NULL,
    `black_toner_price`             DOUBLE                      DEFAULT NULL,
    `black_toner_yield`             INTEGER                     DEFAULT NULL,
    `cyan_toner_SKU`                VARCHAR(255)                DEFAULT NULL,
    `cyan_toner_price`              DOUBLE                      DEFAULT NULL,
    `cyan_toner_yield`              INTEGER                     DEFAULT NULL,
    `magenta_toner_SKU`             VARCHAR(255)                DEFAULT NULL,
    `magenta_toner_price`           DOUBLE                      DEFAULT NULL,
    `magenta_toner_yield`           INTEGER                     DEFAULT NULL,
    `yellow_toner_SKU`              VARCHAR(255)                DEFAULT NULL,
    `yellow_toner_price`            DOUBLE                      DEFAULT NULL,
    `yellow_toner_yield`            INTEGER                     DEFAULT NULL,
    `3color_toner_SKU`              VARCHAR(255)                DEFAULT NULL,
    `3color_toner_price`            DOUBLE                      DEFAULT NULL,
    `3color_toner_yield`            INTEGER                     DEFAULT NULL,
    `4color_toner_SKU`              VARCHAR(255)                DEFAULT NULL,
    `4color_toner_price`            DOUBLE                      DEFAULT NULL,
    `4color_toner_yield`            INTEGER                     DEFAULT NULL,
    `black_comp_SKU`                VARCHAR(255)                DEFAULT NULL,
    `black_comp_price`              DOUBLE                      DEFAULT NULL,
    `black_comp_yield`              INTEGER                     DEFAULT NULL,
    `cyan_comp_SKU`                 VARCHAR(255)                DEFAULT NULL,
    `cyan_comp_price`               DOUBLE                      DEFAULT NULL,
    `cyan_comp_yield`               INTEGER                     DEFAULT NULL,
    `magenta_comp_SKU`              VARCHAR(255)                DEFAULT NULL,
    `magenta_comp_price`            DOUBLE                      DEFAULT NULL,
    `magenta_comp_yield`            INTEGER                     DEFAULT NULL,
    `yellow_comp_SKU`               VARCHAR(255)                DEFAULT NULL,
    `yellow_comp_price`             DOUBLE                      DEFAULT NULL,
    `yellow_comp_yield`             INTEGER                     DEFAULT NULL,
    `3color_comp_SKU`               VARCHAR(255)                DEFAULT NULL,
    `3color_comp_price`             DOUBLE                      DEFAULT NULL,
    `3color_comp_yield`             INTEGER                     DEFAULT NULL,
    `4color_comp_SKU`               VARCHAR(255)                DEFAULT NULL,
    `4color_comp_price`             DOUBLE                      DEFAULT NULL,
    `4color_comp_yield`             INTEGER                     DEFAULT NULL,
    `start_meter_life`              INTEGER                     DEFAULT NULL,
    `end_meter_life`                INTEGER                     DEFAULT NULL,
    `start_meter_black`             INTEGER                     DEFAULT NULL,
    `end_meter_black`               INTEGER                     DEFAULT NULL,
    `start_meter_color`             INTEGER                     DEFAULT NULL,
    `end_meter_color`               INTEGER                     DEFAULT NULL,
    `start_meter_printblack`        INTEGER                     DEFAULT NULL,
    `end_meter_printblack`          INTEGER                     DEFAULT NULL,
    `start_meter_printcolor`        INTEGER                     DEFAULT NULL,
    `end_meter_printcolor`          INTEGER                     DEFAULT NULL,
    `start_meter_copyblack`         INTEGER                     DEFAULT NULL,
    `end_meter_copyblack`           INTEGER                     DEFAULT NULL,
    `start_meter_copycolor`         INTEGER                     DEFAULT NULL,
    `end_meter_copycolor`           INTEGER                     DEFAULT NULL,
    `start_meter_fax`               INTEGER                     DEFAULT NULL,
    `end_meter_fax`                 INTEGER                     DEFAULT NULL,
    `start_meter_scan`              INTEGER                     DEFAULT NULL,
    `end_meter_scan`                INTEGER                     DEFAULT NULL,
    `jit_supplies_supported`        TINYINT                     DEFAULT FALSE,
    `is_excluded`                   TINYINT                     DEFAULT FALSE,
    `is_leased`                     TINYINT                     DEFAULT FALSE, 
    `ip_address`                    VARCHAR(255)                DEFAULT NULL,
    `duty_cycle`                    INTEGER                     DEFAULT NULL,
    `PPM_black`                     DOUBLE                      DEFAULT NULL,
    `PPM_color`                     DOUBLE                      DEFAULT NULL,
    `service_cost_per_page`         DOUBLE                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`report_id`)                       REFERENCES `proposalgenerator_reports` (`id`),
    FOREIGN KEY (`user_id`)                         REFERENCES `users` (`id`),
    FOREIGN KEY (`upload_data_collector_row_id`)    REFERENCES `proposalgenerator_upload_data_collector_rows` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_ticket_statuses` (
    `id`                            INTEGER         NOT NULL AUTO_INCREMENT,
    `name`                          VARCHAR(255)    NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_ticket_categories` (
    `id`                            INTEGER         NOT NULL AUTO_INCREMENT,
    `name`                          VARCHAR(255)    NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_tickets` (
    `id`                            INTEGER         NOT NULL AUTO_INCREMENT,
    `user_id`                       INTEGER         NOT NULL,
    `category_id`                   INTEGER         NOT NULL,
    `status_id`                     INTEGER         NOT NULL,
    `title`                         VARCHAR(255)    NOT NULL,
    `description`                   TEXT            NOT NULL,
    `date_created`                  DATETIME        NOT NULL,
    `date_updated`                  DATETIME        NOT NULL,
    PRIMARY KEY (`id`),  
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`category_id`) REFERENCES `proposalgenerator_ticket_categories` (`id`),
    FOREIGN KEY (`status_id`) REFERENCES `proposalgenerator_ticket_statuses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_tickets_viewed` (
    `ticket_id`                     INTEGER         NOT NULL AUTO_INCREMENT,
    `user_id`                       INTEGER         NOT NULL,
    `date_viewed`                   DATETIME        NOT NULL,
    PRIMARY KEY (`ticket_id`, `user_id`),
    FOREIGN KEY (`ticket_id`) REFERENCES `proposalgenerator_tickets` (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_ticket_comments` (
    `id`                            INTEGER         NOT NULL AUTO_INCREMENT,
    `ticket_id`                     INTEGER         NOT NULL,
    `user_id`                       INTEGER         NOT NULL,
    `content`                       TEXT            NOT NULL,
    `date_created`                  DATETIME        NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`ticket_id`) REFERENCES `proposalgenerator_tickets` (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
CREATE TABLE `proposalgenerator_ticket_pf_requests` (
    `ticket_id`                     INTEGER         NOT NULL,
    `user_id`                       INTEGER         NOT NULL, 
    `pf_device_id`                  INTEGER         NOT NULL,
    `manufacturer`                  VARCHAR(255)    NOT NULL,
    `printer_model`                 VARCHAR(255)    NOT NULL,
    `launch_date`                   DATETIME                    DEFAULT NULL,  
    `device_price`                  DOUBLE                      DEFAULT NULL,
    `service_cost_per_page`         DOUBLE                      DEFAULT NULL,
    `toner_config`                  VARCHAR(255)    NOT NULL,
    `is_copier`                     TINYINT         NOT NULL    DEFAULT FALSE,
    `is_fax`                        TINYINT         NOT NULL    DEFAULT FALSE,
    `is_duplex`                     TINYINT         NOT NULL    DEFAULT FALSE,
    `is_scanner`                    TINYINT         NOT NULL    DEFAULT FALSE,
    `PPM_black`                     DOUBLE                      DEFAULT NULL,
    `PPM_color`                     DOUBLE                      DEFAULT NULL,
    `duty_cycle`                    INTEGER                     DEFAULT NULL,
    `watts_power_normal`            DOUBLE                      DEFAULT NULL,
    `watts_power_idle`              DOUBLE                      DEFAULT NULL,
    PRIMARY KEY (`ticket_id`),
    UNIQUE (`user_id`, `pf_device_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`pf_device_id`) REFERENCES `proposalgenerator_pf_devices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- User
CREATE TABLE `proposalgenerator_user_report_settings` (
    `user_id`                                   INTEGER         NOT NULL,
    `report_setting_id`                         INTEGER         NOT NULL,
    PRIMARY KEY (`user_id`, `report_setting_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`report_setting_id`) REFERENCES `proposalgenerator_report_settings` (`id`)   
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `proposalgenerator_report_report_settings` (
    `report_id`                                 INTEGER         NOT NULL,
    `report_setting_id`                         INTEGER         NOT NULL,
    PRIMARY KEY (`report_id`, `report_setting_id`),
    FOREIGN KEY (`report_id`) REFERENCES `proposalgenerator_reports` (`id`),
    FOREIGN KEY (`report_setting_id`) REFERENCES `proposalgenerator_report_settings` (`id`)   
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- User Survey Settings
CREATE TABLE `proposalgenerator_user_survey_settings` (
    `user_id`                                   INTEGER         NOT NULL,
    `survey_setting_id`                         INTEGER         NOT NULL,
    PRIMARY KEY (`user_id`, `survey_setting_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`survey_setting_id`) REFERENCES `proposalgenerator_survey_settings` (`id`)   
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Report Survey Settings
CREATE TABLE `proposalgenerator_report_survey_settings` (
    `report_id`                                 INTEGER         NOT NULL,
    `survey_setting_id`                         INTEGER         NOT NULL,
    PRIMARY KEY (`report_id`, `survey_setting_id`),
    FOREIGN KEY (`report_id`) REFERENCES `proposalgenerator_reports` (`id`),
    FOREIGN KEY (`survey_setting_id`) REFERENCES `proposalgenerator_survey_settings` (`id`)   
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `quotegen_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_devices` (
  `masterDeviceId` INT(11) NOT NULL ,
  `sku` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`masterDeviceId`) ,
  INDEX `quotegen_devices_ibfk_1` (`masterDeviceId` ASC) ,
  CONSTRAINT `quotegen_devices_ibfk_1`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `proposalgenerator_master_devices` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_device_configurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_device_configurations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `masterDeviceId` INT(11) NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `masterDeviceId` (`masterDeviceId` ASC) ,
  CONSTRAINT `quotegen_device_configurations_ibfk_1`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `quotegen_devices` (`masterDeviceId` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_options`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_options` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  `price` DOUBLE NOT NULL ,
  `sku` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_device_configuration_options`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_device_configuration_options` (
  `deviceConfigurationId` INT(11) NOT NULL ,
  `optionId` INT(11) NOT NULL ,
  `quantity` INT(11) NOT NULL ,
  `includedQuantity` INT(11) NOT NULL ,
  PRIMARY KEY (`deviceConfigurationId`, `optionId`) ,
  INDEX `optionId` (`optionId` ASC) ,
  CONSTRAINT `quotegen_device_configuration_options_ibfk_1`
    FOREIGN KEY (`deviceConfigurationId` )
    REFERENCES `quotegen_device_configurations` (`id` ),
  CONSTRAINT `quotegen_device_configuration_options_ibfk_2`
    FOREIGN KEY (`optionId` )
    REFERENCES `quotegen_options` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_device_options`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_device_options` (
  `masterDeviceId` INT(11) NOT NULL ,
  `optionId` INT(11) NOT NULL ,
  PRIMARY KEY (`masterDeviceId`, `optionId`) ,
  INDEX `optionId` (`optionId` ASC) ,
  CONSTRAINT `quotegen_device_options_ibfk_1`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `quotegen_devices` (`masterDeviceId` ),
  CONSTRAINT `quotegen_device_options_ibfk_2`
    FOREIGN KEY (`optionId` )
    REFERENCES `quotegen_options` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_leasing_schemas`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_leasing_schemas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Stores information on different leasing schemas' ;


-- -----------------------------------------------------
-- Table `quotegen_leasing_schema_ranges`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_leasing_schema_ranges` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `leasingSchemaId` INT(11) NOT NULL ,
  `startRange` DOUBLE NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `leasingSchemaId` (`leasingSchemaId` ASC) ,
  CONSTRAINT `quotegen_leasing_schema_ranges_ibfk_1`
    FOREIGN KEY (`leasingSchemaId` )
    REFERENCES `quotegen_leasing_schemas` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Stores the available value ranges (start range) for a leasin' /* comment truncated */ ;


-- -----------------------------------------------------
-- Table `quotegen_leasing_schema_terms`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_leasing_schema_terms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `leasingSchemaId` INT(11) NOT NULL ,
  `months` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `leasingSchemaId` (`leasingSchemaId` ASC) ,
  CONSTRAINT `quotegen_leasing_schema_terms_ibfk_1`
    FOREIGN KEY (`leasingSchemaId` )
    REFERENCES `quotegen_leasing_schemas` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Holds the terms available for a leasing schema' ;


-- -----------------------------------------------------
-- Table `quotegen_leasing_schema_rates`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_leasing_schema_rates` (
  `leasingSchemaTermId` INT(11) NOT NULL ,
  `leasingSchemaRangeId` INT(11) NOT NULL ,
  `rate` DOUBLE NOT NULL ,
  INDEX `leasingSchemaTermId` (`leasingSchemaTermId` ASC) ,
  INDEX `leasingSchemaRangeId` (`leasingSchemaRangeId` ASC) ,
  PRIMARY KEY (`leasingSchemaTermId`, `leasingSchemaRangeId`) ,
  CONSTRAINT `quotegen_leasing_schema_rates_ibfk_2`
    FOREIGN KEY (`leasingSchemaTermId` )
    REFERENCES `quotegen_leasing_schema_terms` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `quotegen_leasing_schema_rates_ibfk_3`
    FOREIGN KEY (`leasingSchemaRangeId` )
    REFERENCES `quotegen_leasing_schema_ranges` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Stores the rates that coincide with the terms and ranges for' /* comment truncated */ ;


-- -----------------------------------------------------
-- Table `quotegen_option_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_option_categories` (
  `categoryId` INT(11) NOT NULL ,
  `optionId` INT(11) NOT NULL ,
  PRIMARY KEY (`categoryId`, `optionId`) ,
  INDEX `optionId` (`optionId` ASC) ,
  CONSTRAINT `quotegen_option_categories_ibfk_1`
    FOREIGN KEY (`categoryId` )
    REFERENCES `quotegen_categories` (`id` ),
  CONSTRAINT `quotegen_option_categories_ibfk_2`
    FOREIGN KEY (`optionId` )
    REFERENCES `quotegen_options` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_quotes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_quotes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `clientId` INT(11) NOT NULL ,
  `dateCreated` DATETIME NOT NULL ,
  `dateModified` DATETIME NOT NULL ,
  `quoteDate` DATETIME NOT NULL ,
  `userId` INT(11) NOT NULL ,
  `clientDisplayName` VARCHAR(45) NULL ,
  `leaseRate` DOUBLE NULL ,
  `leaseTerm` INT(11) NULL ,
  `pageCoverageMonochrome` DOUBLE NOT NULL ,
  `pageCoverageColor` DOUBLE NOT NULL ,
  `pricingConfigId` INT(11) NOT NULL ,
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
    REFERENCES `proposalgenerator_pricing_configs` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Primary table for a quote. Stores basic information' ;


-- -----------------------------------------------------
-- Table `quotegen_quote_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_quote_devices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `quoteId` INT(11) NOT NULL ,
  `margin` DOUBLE NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `sku` VARCHAR(255) NOT NULL ,
  `oemCostPerPageMonochrome` DOUBLE NOT NULL ,
  `oemCostPerPageColor` DOUBLE NOT NULL ,
  `compCostPerPageMonochrome` DOUBLE NOT NULL ,
  `compCostPerPageColor` DOUBLE NOT NULL ,
  `price` DOUBLE NOT NULL ,
  `quantity` INT(11) NOT NULL ,
  `packagePrice` DOUBLE NOT NULL ,
  `residual` DOUBLE NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `quoteId` (`quoteId` ASC) ,
  CONSTRAINT `quotegen_quote_devices_ibfk_1`
    FOREIGN KEY (`quoteId` )
    REFERENCES `quotegen_quotes` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_quote_device_configurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_quote_device_configurations` (
  `quoteDeviceId` INT(11) NOT NULL AUTO_INCREMENT ,
  `masterDeviceId` INT(11) NOT NULL ,
  INDEX `deviceConfigurationId` (`masterDeviceId` ASC) ,
  INDEX `quoteDeviceId` (`quoteDeviceId` ASC) ,
  PRIMARY KEY (`quoteDeviceId`, `masterDeviceId`) ,
  CONSTRAINT `quotegen_quote_device_configurations_ibfk_1`
    FOREIGN KEY (`masterDeviceId` )
    REFERENCES `quotegen_devices` (`masterDeviceId` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `quotegen_quote_device_configurations_ibfk_2`
    FOREIGN KEY (`quoteDeviceId` )
    REFERENCES `quotegen_quote_devices` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_quote_device_pages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_quote_device_pages` (
  `quoteDeviceId` INT(11) NOT NULL ,
  `costPerPageMonochrome` DOUBLE NOT NULL ,
  `costPerPageColor` DOUBLE NOT NULL ,
  `pageBillingPreference` ENUM('Per Page','Monthly') NULL DEFAULT 'Per Page' ,
  `margin` DOUBLE NOT NULL ,
  PRIMARY KEY (`quoteDeviceId`) ,
  INDEX `quoteDeviceId` (`quoteDeviceId` ASC) ,
  CONSTRAINT `quotegen_quote_device_pages_ibfk_1`
    FOREIGN KEY (`quoteDeviceId` )
    REFERENCES `quotegen_quote_devices` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_quote_device_monthly_pages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_quote_device_monthly_pages` (
  `quoteDeviceId` INT(11) NOT NULL ,
  `monochrome` INT(11) NOT NULL ,
  `color` INT(11) NOT NULL ,
  `price` DOUBLE NOT NULL ,
  PRIMARY KEY (`quoteDeviceId`) ,
  INDEX `quoteDeviceId` (`quoteDeviceId` ASC) ,
  CONSTRAINT `quotegen_quote_device_monthly_pages_ibfk_1`
    FOREIGN KEY (`quoteDeviceId` )
    REFERENCES `quotegen_quote_device_pages` (`quoteDeviceId` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_quote_device_options`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_quote_device_options` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `quoteDeviceId` INT(11) NOT NULL ,
  `sku` VARCHAR(255) NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  `price` DOUBLE NOT NULL ,
  `quantity` INT(11) NOT NULL ,
  `includedQuantity` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `quoteDeviceId` (`quoteDeviceId` ASC) ,
  CONSTRAINT `quotegen_quote_device_options_ibfk_1`
    FOREIGN KEY (`quoteDeviceId` )
    REFERENCES `quotegen_quote_devices` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_quote_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_quote_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `pageCoverageMonochrome` DOUBLE NULL ,
  `pageCoverageColor` DOUBLE NULL ,
  `deviceMargin` DOUBLE NULL ,
  `pageMargin` DOUBLE NULL ,
  `pricingConfigId` INT(11) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `quotegen_quote_settings_ibfk1` (`pricingConfigId` ASC) ,
  CONSTRAINT `quotegen_quote_settings_ibfk1`
    FOREIGN KEY (`pricingConfigId` )
    REFERENCES `proposalgenerator_pricing_configs` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_user_device_configurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_user_device_configurations` (
  `deviceConfigurationId` INT(11) NOT NULL ,
  `userId` INT(11) NOT NULL ,
  PRIMARY KEY (`deviceConfigurationId`, `userId`) ,
  INDEX `userId` (`userId` ASC) ,
  CONSTRAINT `quotegen_user_device_configurations_ibfk_1`
    FOREIGN KEY (`deviceConfigurationId` )
    REFERENCES `quotegen_device_configurations` (`id` ),
  CONSTRAINT `quotegen_user_device_configurations_ibfk_2`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_user_quote_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_user_quote_settings` (
  `userId` INT(11) NOT NULL ,
  `quoteSettingId` INT(11) NOT NULL ,
  PRIMARY KEY (`userId`) ,
  INDEX `quoteSettingId` (`quoteSettingId` ASC) ,
  CONSTRAINT `quotegen_user_quote_settings_ibfk_1`
    FOREIGN KEY (`userId` )
    REFERENCES `users` (`id` ),
  CONSTRAINT `quotegen_user_quote_settings_ibfk_2`
    FOREIGN KEY (`quoteSettingId` )
    REFERENCES `quotegen_quote_settings` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_global_device_configurations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_global_device_configurations` (
  `deviceConfigurationId` INT(11) NOT NULL ,
  PRIMARY KEY (`deviceConfigurationId`) ,
  CONSTRAINT `quotegen_user_device_configurations_ibfk_10`
    FOREIGN KEY (`deviceConfigurationId` )
    REFERENCES `quotegen_device_configurations` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `quotegen_global_leasing_schemas`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_global_leasing_schemas` (
  `leasingSchemaId` INT(11) NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`leasingSchemaId`) ,
  INDEX `quotegen_global_leasing_schemas_ibfk_1` (`leasingSchemaId` ASC) ,
  CONSTRAINT `quotegen_global_leasing_schemas_ibfk_1`
    FOREIGN KEY (`leasingSchemaId` )
    REFERENCES `quotegen_leasing_schemas` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'This table marks leasing schemas as global' ;


-- -----------------------------------------------------
-- Table `quotegen_quote_device_configuration_options`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `quotegen_quote_device_configuration_options` (
  `optionId` INT NOT NULL ,
  `quoteDeviceOptionId` INT NOT NULL ,
  PRIMARY KEY (`optionId`, `quoteDeviceOptionId`) ,
  INDEX `quotegen_quote_device_option_options_ibfk_1` (`optionId` ASC) ,
  INDEX `quotegen_quote_device_option_options_ibfk_2` (`quoteDeviceOptionId` ASC) ,
  CONSTRAINT `quotegen_quote_device_option_options_ibfk_1`
    FOREIGN KEY (`optionId` )
    REFERENCES `quotegen_options` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `quotegen_quote_device_option_options_ibfk_2`
    FOREIGN KEY (`quoteDeviceOptionId` )
    REFERENCES `quotegen_quote_device_options` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

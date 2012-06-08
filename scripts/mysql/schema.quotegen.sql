--
-- Clients (Needs to be put in the skeleton project
--
CREATE TABLE `quotegen_clients` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `userId`                            INTEGER         NOT NULL AUTO_INCREMENT,
    `name`                              VARCHAR(255)    NOT NULL,
    `address`                           VARCHAR(255)    NOT NULL,
    `phoneNumber`                       VARCHAR(255)    NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
--
-- QUOTE GEN LEASING SCHEMAs
--


CREATE TABLE `quotegen_leasing_schemas` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `name`                              VARCHAR(255)    NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `quotegen_leasing_schema_terms` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `leasingSchemaId`                   INTEGER         NOT NULL,
    `months`                            INTEGER         NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`leasingSchemaId`) REFERENCES `quotegen_leasing_schemas` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `quotegen_leasing_schema_ranges` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `leasingSchemaId`                   INTEGER         NOT NULL,
    `startRange`                        DOUBLE          NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`leasingSchemaId`) REFERENCES `quotegen_leasing_schemas` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `quotegen_leasing_schema_rates` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `leasingSchemaId`                   INTEGER         NOT NULL,
    `leasingSchemaTermId`               INTEGER         NOT NULL,
    `leasingSchemaRangeId`              INTEGER         NOT NULL,
    `rate`                              DOUBLE          NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`leasingSchemaId`) REFERENCES `quotegen_leasing_schemas` (`id`),
    FOREIGN KEY (`leasingSchemaTermId`) REFERENCES `quotegen_leasing_schema_terms` (`id`),
    FOREIGN KEY (`leasingSchemaRangeId`) REFERENCES `quotegen_leasing_schema_ranges` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- QUOTE GEN QUOTE SETTINGS
--
CREATE TABLE `quotegen_quote_settings` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `pageCoverageMonochrome`            DOUBLE          NOT NULL,
    `pageCoverageColor`                 DOUBLE          NOT NULL,
    `deviceMargin`                      DOUBLE          NOT NULL,
    `pageMargin`                        DOUBLE          NOT NULL,
    `tonerPreference`                   INTEGER         NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `quotegen_user_quote_settings` (
    `userId`                           INTEGER         NOT NULL,
    `quoteSettingId`                   INTEGER         NOT NULL,
    PRIMARY KEY (`userId`, `quoteSettingId`),
    FOREIGN KEY (`userId`) REFERENCES `users` (`id`),
    FOREIGN KEY (`quoteSettingId`) REFERENCES `quotegen_quote_settings` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- QUOTE GEN DEVICE CONFIGURATIONS
--
CREATE TABLE `quotegen_devices` (
    `masterDeviceId`                    INTEGER         NOT NULL,
    `sku`                               VARCHAR(255)    NOT NULL,
    PRIMARY KEY (`masterDeviceId`)
    -- FOREIGN KEY (`masterDeviceId`) REFERENCES `proposalgenerator_master_devices` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `quotegen_options` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `name`                              VARCHAR(255)    NOT NULL,
    `description`                       TEXT            NOT NULL,
    `price`                             DOUBLE          NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `quotegen_categories` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `name`                              VARCHAR(255)    NOT NULL,
    `description`                       TEXT            NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `quotegen_option_categories` (
    `categoryId`                        INTEGER         NOT NULL,
    `optionId`                          INTEGER         NOT NULL,
    PRIMARY KEY (`categoryId`, `optionId`),
    FOREIGN KEY (`categoryId`) REFERENCES `quotegen_categories` (`id`),
    FOREIGN KEY (`optionId`) REFERENCES `quotegen_options` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `quotegen_device_options` (
    `masterDeviceId`                    INTEGER         NOT NULL,
    `optionId`                          INTEGER         NOT NULL,
    PRIMARY KEY (`masterDeviceId`, `optionId`),
    FOREIGN KEY (`masterDeviceId`) REFERENCES `quotegen_devices` (`masterDeviceId`),
    FOREIGN KEY (`optionId`) REFERENCES `quotegen_options` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Device Configurations
CREATE TABLE `quotegen_device_configurations` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `masterDeviceId`                    INTEGER         NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`masterDeviceId`) REFERENCES `quotegen_devices` (`masterDeviceId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- Device Configuration Options
CREATE TABLE `quotegen_device_configuration_options` (
    `deviceConfigurationId`             INTEGER         NOT NULL,
    `optionId`                          INTEGER         NOT NULL,
    `quantity`                          INTEGER         NOT NULL,
    PRIMARY KEY (`deviceConfigurationId`),
    FOREIGN KEY (`deviceConfigurationId`) REFERENCES `quotegen_device_configurations` (`id`),
    FOREIGN KEY (`optionId`) REFERENCES `quotegen_options` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- User Device Configurations
CREATE TABLE `quotegen_user_device_configurations` (
    `deviceConfigurationId`             INTEGER         NOT NULL,
    `userId`                            INTEGER         NOT NULL,
    `name`                              VARCHAR(255)    NOT NULL,
    `description`                       VARCHAR(255)    NOT NULL,
    PRIMARY KEY (`deviceConfigurationId`),
    FOREIGN KEY (`deviceConfigurationId`) REFERENCES `quotegen_device_configurations` (`id`),
    FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


--
-- QUOTE GEN QUOTES
--

-- Quote
CREATE TABLE `quotegen_quotes` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `clientId`                          INTEGER         NOT NULL,
    `dateCreated`                       DATETIME        NOT NULL,
    `dateModified`                      DATETIME        NOT NULL,
    `quoteDate`                         DATETIME        NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`clientId`) REFERENCES `quotegen_clients` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Quote settings
CREATE TABLE `quotegen_quote_quote_settings` (
    `quoteId`                          INTEGER         NOT NULL,
    `quoteSettingId`                   INTEGER         NOT NULL,
    PRIMARY KEY (`quoteId`, `quoteSettingId`),
    FOREIGN KEY (`quoteId`) REFERENCES `quotegen_quotes` (`id`),
    FOREIGN KEY (`quoteSettingId`) REFERENCES `quotegen_quote_settings` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Quote Devices
CREATE TABLE `quotegen_quote_devices` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `quoteId`                           INTEGER         NOT NULL,
    `margin`                            DOUBLE          NOT NULL,
    `name`                              VARCHAR(255)    NOT NULL,
    `sku`                               VARCHAR(255)    NOT NULL,
    `oemCostPerPageMonochrome`          DOUBLE          NOT NULL,
    `oemCostPerPageColor`               DOUBLE          NOT NULL,
    `compCostPerPageMonochrome`         DOUBLE          NOT NULL,
    `compCostPerPageColor`              DOUBLE          NOT NULL,
    `price`                             DOUBLE          NOT NULL,
    `quantity`                          INTEGER         NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`quoteId`) REFERENCES `quotegen_quotes` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Quote Device Options
CREATE TABLE `quotegen_quote_device_options` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `quoteDeviceId`                     INTEGER         NOT NULL,
    `sku`                               VARCHAR(255)    NOT NULL,
    `name`                              VARCHAR(255)    NOT NULL,
    `description`                       TEXT            NOT NULL,
    `price`                             DOUBLE          NOT NULL,
    `quantity`                          INTEGER         NOT NULL,
    `includedQuantity`                  INTEGER         NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`quoteDeviceId`) REFERENCES `quotegen_quote_devices` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Linking table to match device configurations to quote devices.
-- This allows us to delete a device configuration without having to delete the quote device itself.
CREATE TABLE `quotegen_quote_device_configurations` (
    `quoteDeviceId`                     INTEGER         NOT NULL AUTO_INCREMENT,
    `deviceConfigurationId`             INTEGER         NOT NULL,
    FOREIGN KEY (`deviceConfigurationId`) REFERENCES `quotegen_device_configurations` (`id`),
    FOREIGN KEY (`quoteDeviceId`) REFERENCES `quotegen_quote_devices` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- Pages. Used to figure out how much to charge users for pages.
-- The CPP's are used as a flat cost per page, or the overage rate when there are monthly pages included.
CREATE TABLE `quotegen_quote_device_pages` (
    `quoteDeviceId`                     INTEGER         NOT NULL,
    `costPerPageMonochrome`             DOUBLE          NOT NULL,
    `costPerPageColor`                  DOUBLE          NOT NULL,
    `pageBillingPreference`             ENUM('Per Page', 'Monthly') DEFAULT 'Per Page',
    `margin`                            DOUBLE          NOT NULL,
    PRIMARY KEY (`quoteDeviceId`),
    FOREIGN KEY (`quoteDeviceId`) REFERENCES `quotegen_quote_devices` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- Monthly pages that can be bundled with leased devices
CREATE TABLE `quotegen_quote_device_monthly_pages` (
    `quoteDeviceId`                     INTEGER         NOT NULL,
    `monochrome`                        INTEGER         NOT NULL,
    `color`                             INTEGER         NOT NULL,
    `price`                             DOUBLE          NOT NULL,
    PRIMARY KEY (`quoteDeviceId`),
    FOREIGN KEY (`quoteDeviceId`) REFERENCES `quotegen_quote_device_pages` (`quoteDeviceId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- Pages. Used to figure out how much to charge users for pages.
-- The CPP's are used as a co
CREATE TABLE `quotegen_quote_device_residuals` (
    `quoteDeviceId`                     INTEGER         NOT NULL,
    `amount`                            DOUBLE          NOT NULL,
    PRIMARY KEY (`quoteDeviceId`),
    FOREIGN KEY (`quoteDeviceId`) REFERENCES `quotegen_quote_devices` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
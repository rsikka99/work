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
    PRIMARY KEY (`masterDeviceId`),
    FOREIGN KEY (`masterDeviceId`) REFERENCES `proposalgenerator_master_devices` (`id`)
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
    FOREIGN KEY (`masterDeviceId`) REFERENCES `quotegen_devices` (`id`),
    FOREIGN KEY (`optionId`) REFERENCES `quotegen_options` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Device Configurations
CREATE TABLE `quotegen_device_configurations` (
    `id`                                INTEGER         NOT NULL AUTO_INCREMENT,
    `masterDeviceId`                    INTEGER         NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`masterDeviceId`) REFERENCES `quotegen_devices` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- Device Configuration Options
CREATE TABLE `quotegen_device_configurations` (
    `deviceConfigurationId`             INTEGER         NOT NULL,
    `optionId`                          INTEGER         NOT NULL,
    `quantity`                          INTEGER         NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`deviceConfigurationId`) REFERENCES `quotegen_device_configurations` (`id`),
    FOREIGN KEY (`optionId`) REFERENCES `quotegen_device_options` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- User Device Configurations
CREATE TABLE `quotegen_user_device_configurations` (
    `deviceConfigurationId`             INTEGER         NOT NULL,
    `userId`                            INTEGER         NOT NULL,
    `name`                              VARCHAR(255)    NOT NULL,
    `description`                       VARCHAR(255)    NOT NULL,
    PRIMARY KEY (`id`),
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
    FOREIGN KEY (`deviceConfigurationId`) REFERENCES `quotegen_device_configurations` (`id`),
    FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
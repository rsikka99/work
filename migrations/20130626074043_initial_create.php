<?php

use Phinx\Migration\AbstractMigration;

class InitialCreate extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
     */

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS `images` (
    `id`       INT          NOT NULL AUTO_INCREMENT,
    `image`    MEDIUMBLOB   NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`));');

        $this->execute('CREATE TABLE IF NOT EXISTS `dealers` (
    `id`                INT          NOT NULL AUTO_INCREMENT,
    `dealerName`        VARCHAR(255) NOT NULL,
    `userLicenses`      INT          NOT NULL,
    `dateCreated`       DATE         NOT NULL,
    `dealerLogoImageId` INT          NULL,
    PRIMARY KEY (`id`),
    INDEX `dealers_ibfk_1_idx` (`dealerLogoImageId` ASC),
    CONSTRAINT `dealers_ibfk_1`
    FOREIGN KEY (`dealerLogoImageId`)
    REFERENCES `images` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `clients` (
    `id`            INT(11)      NOT NULL AUTO_INCREMENT,
    `dealerId`      INT(11)      NOT NULL,
    `accountNumber` VARCHAR(255) NOT NULL,
    `companyName`   VARCHAR(255) NOT NULL,
    `legalName`     VARCHAR(255) NULL,
    `employeeCount` INT          NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `clients_ibfk_1_idx` (`dealerId` ASC),
    CONSTRAINT `clients_ibfk_1`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `log_types` (
    `id`   INT(11)      NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `logs` (
    `id`        INT(11)   NOT NULL AUTO_INCREMENT,
    `logTypeId` INT(11)   NOT NULL DEFAULT \'1\',
    `priority`  INT(11)   NOT NULL DEFAULT \'6\',
    `message`   TEXT      NOT NULL,
    `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `userId`    INT(11)   NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `logs_ibfk_1_idx` (`logTypeId` ASC),
    CONSTRAINT `logs_ibfk_1`
    FOREIGN KEY (`logTypeId`)
    REFERENCES `log_types` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `manufacturers` (
    `id`          INT(11)      NOT NULL AUTO_INCREMENT,
    `fullname`    VARCHAR(255) NOT NULL,
    `displayname` VARCHAR(255) NOT NULL,
    `isDeleted`   TINYINT(4)   NULL DEFAULT \'0\',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `fullname` (`fullname` ASC),
    INDEX `displayname` (`displayname` ASC)
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `rms_providers` (
    `id`   INT          NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `rms_uploads` (
    `id`              INT          NOT NULL AUTO_INCREMENT,
    `clientId`        INT          NOT NULL,
    `rmsProviderId`   INT          NOT NULL,
    `fileName`        VARCHAR(255) NOT NULL,
    `validRowCount`   INT          NOT NULL,
    `invalidRowCount` INT          NOT NULL,
    `uploadDate`      DATETIME     NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `rms_uploads_ibfk_1_idx` (`clientId` ASC),
    INDEX `rms_uploads_ibfk_2_idx` (`rmsProviderId` ASC),
    CONSTRAINT `rms_uploads_ibfk_1`
    FOREIGN KEY (`clientId`)
    REFERENCES `clients` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `rms_uploads_ibfk_2`
    FOREIGN KEY (`rmsProviderId`)
    REFERENCES `rms_providers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `users` (
    `id`                       INT(11)      NOT NULL AUTO_INCREMENT,
    `dealerId`                 INT          NOT NULL,
    `email`                    VARCHAR(255) NOT NULL,
    `password`                 VARCHAR(255) NOT NULL,
    `firstname`                VARCHAR(255) NOT NULL,
    `lastname`                 VARCHAR(255) NOT NULL,
    `loginAttempts`            INT(11)      NOT NULL DEFAULT \'0\',
    `frozenUntil`              DATETIME     NULL DEFAULT NULL,
    `locked`                   TINYINT(4)   NOT NULL DEFAULT \'0\',
    `eulaAccepted`             DATETIME     NULL DEFAULT NULL,
    `resetPasswordOnNextLogin` TINYINT(4)   NOT NULL DEFAULT \'0\',
    `passwordResetRequest`     DATETIME     NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `users_ibfk1_idx` (`dealerId` ASC),
    CONSTRAINT `users_ibfk1`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `rms_devices` (
    `rmsProviderId` INT(11)      NOT NULL,
    `rmsModelId`    INT(11)      NOT NULL,
    `manufacturer`  VARCHAR(255) NULL,
    `modelName`     VARCHAR(255) NULL,
    `dateCreated`   DATETIME     NOT NULL,
    `userId`        INT(11)      NULL,
    PRIMARY KEY (`rmsProviderId`, `rmsModelId`),
    INDEX `rms_devices_ibfk_1_idx` (`userId` ASC),
    INDEX `rms_devices_ibfk_2_idx` (`rmsProviderId` ASC),
    CONSTRAINT `rms_devices_ibfk_1`
    FOREIGN KEY (`userId`)
    REFERENCES `users` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `rms_devices_ibfk_2`
    FOREIGN KEY (`rmsProviderId`)
    REFERENCES `rms_providers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `rms_upload_rows` (
    `id`                       INT(11)      NOT NULL AUTO_INCREMENT,
    `rmsProviderId`            INT(11)      NOT NULL,
    `rmsModelId`               INT(11)      NULL,
    `fullDeviceName`           VARCHAR(255) NOT NULL,
    `hasCompleteInformation`   TINYINT(4)   NOT NULL DEFAULT 0,
    `modelName`                VARCHAR(255) NOT NULL DEFAULT \'\',
    `manufacturer`             VARCHAR(255) NOT NULL DEFAULT \'\',
    `manufacturerId`           INT(11)      NULL,
    `cost`                     DOUBLE       NULL,
    `dutyCycle`                INT(11)      NULL,
    `isColor`                  TINYINT(4)   NOT NULL DEFAULT 0,
    `isCopier`                 TINYINT(4)   NOT NULL DEFAULT 0,
    `isFax`                    TINYINT(4)   NOT NULL DEFAULT 0,
    `isLeased`                 TINYINT(4)   NOT NULL DEFAULT 0,
    `isDuplex`                 TINYINT(4)   NULL,
    `isScanner`                TINYINT(4)   NOT NULL DEFAULT 0,
    `launchDate`               DATETIME     NULL,
    `leasedTonerYield`         INT(11)      NULL,
    `ppmBlack`                 DOUBLE       NULL,
    `ppmColor`                 DOUBLE       NULL,
    `partsCostPerPage`         DOUBLE       NULL,
    `laborCostPerPage`         DOUBLE       NULL,
    `tonerConfigId`            INT(11)      NOT NULL DEFAULT 1,
    `wattsPowerNormal`         DOUBLE       NULL,
    `wattsPowerIdle`           DOUBLE       NULL,
    `oemBlackTonerSku`         VARCHAR(255) NULL,
    `oemBlackTonerYield`       INT(11)      NULL,
    `oemBlackTonerCost`        DOUBLE       NULL,
    `oemCyanTonerSku`          VARCHAR(255) NULL,
    `oemCyanTonerYield`        INT(11)      NULL,
    `oemCyanTonerCost`         DOUBLE       NULL,
    `oemMagentaTonerSku`       VARCHAR(255) NULL,
    `oemMagentaTonerYield`     INT(11)      NULL,
    `oemMagentaTonerCost`      DOUBLE       NULL,
    `oemYellowTonerSku`        VARCHAR(255) NULL,
    `oemYellowTonerYield`      INT(11)      NULL,
    `oemYellowTonerCost`       DOUBLE       NULL,
    `oemThreeColorTonerSku`    VARCHAR(255) NULL,
    `oemThreeColorTonerYield`  INT(11)      NULL,
    `oemThreeColorTonerCost`   DOUBLE       NULL,
    `oemFourColorTonerSku`     VARCHAR(255) NULL,
    `oemFourColorTonerYield`   INT(11)      NULL,
    `oemFourColorTonerCost`    DOUBLE       NULL,
    `compBlackTonerSku`        VARCHAR(255) NULL,
    `compBlackTonerYield`      INT(11)      NULL,
    `compBlackTonerCost`       DOUBLE       NULL,
    `compCyanTonerSku`         VARCHAR(255) NULL,
    `compCyanTonerYield`       INT(11)      NULL,
    `compCyanTonerCost`        DOUBLE       NULL,
    `compMagentaTonerSku`      VARCHAR(255) NULL,
    `compMagentaTonerYield`    INT(11)      NULL,
    `compMagentaTonerCost`     DOUBLE       NULL,
    `compYellowTonerSku`       VARCHAR(255) NULL,
    `compYellowTonerYield`     INT(11)      NULL,
    `compYellowTonerCost`      DOUBLE       NULL,
    `compThreeColorTonerSku`   VARCHAR(255) NULL,
    `compThreeColorTonerYield` INT(11)      NULL,
    `compThreeColorTonerCost`  DOUBLE       NULL,
    `compFourColorTonerSku`    VARCHAR(255) NULL,
    `compFourColorTonerYield`  INT(11)      NULL,
    `compFourColorTonerCost`   DOUBLE       NULL,
    `tonerLevelBlack`          VARCHAR(255) NULL,
    `tonerLevelCyan`           VARCHAR(255) NULL,
    `tonerLevelMagenta`        VARCHAR(255) NULL,
    `tonerLevelYellow`         VARCHAR(255) NULL,
    PRIMARY KEY (`id`),
    INDEX `rms_upload_rows_ibfk_1_idx` (`rmsProviderId` ASC),
    INDEX `rms_upload_rows_ibfk_2_idx` (`rmsProviderId` ASC, `rmsModelId` ASC),
    INDEX `rms_upload_rows_ibfk_3_idx` (`manufacturerId` ASC),
    CONSTRAINT `rms_upload_rows_ibfk_1`
    FOREIGN KEY (`rmsProviderId`)
    REFERENCES `rms_providers` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `rms_upload_rows_ibfk_2`
    FOREIGN KEY (`rmsProviderId`, `rmsModelId`)
    REFERENCES `rms_devices` (`rmsProviderId`, `rmsModelId`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `rms_upload_rows_ibfk_3`
    FOREIGN KEY (`manufacturerId`)
    REFERENCES `manufacturers` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `device_instances` (
    `id`                     INT(11)      NOT NULL AUTO_INCREMENT,
    `rmsUploadId`            INT(11)      NOT NULL,
    `rmsUploadRowId`         INT(11)      NOT NULL,
    `ipAddress`              VARCHAR(255) NOT NULL DEFAULT \'\',
    `isExcluded`             TINYINT(4)   NOT NULL DEFAULT 0,
    `mpsDiscoveryDate`       DATETIME     NULL,
    `reportsTonerLevels`     TINYINT(4)   NOT NULL DEFAULT 0,
    `serialNumber`           VARCHAR(255) NOT NULL DEFAULT \'\',
    `useUserData`            TINYINT(4)   NOT NULL DEFAULT 0,
    `isManaged`              TINYINT(4)   NULL,
    `rmsDeviceId`            VARCHAR(255) NULL,
    `pageCoverageMonochrome` DOUBLE       NULL DEFAULT NULL,
    `pageCoverageCyan`       DOUBLE       NULL DEFAULT NULL,
    `pageCoverageMagenta`    DOUBLE       NULL DEFAULT NULL,
    `pageCoverageYellow`     DOUBLE       NULL DEFAULT NULL,
    `deviceSwapReasonId`     INT(11)      NULL,
    PRIMARY KEY (`id`),
    INDEX `device_instances_ibfk_2_idx` (`rmsUploadRowId` ASC),
    INDEX `device_instances_ibfk_1_idx` (`rmsUploadId` ASC),
    CONSTRAINT `device_instances_ibfk_1`
    FOREIGN KEY (`rmsUploadId`)
    REFERENCES `rms_uploads` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `device_instances_ibfk_2`
    FOREIGN KEY (`rmsUploadRowId`)
    REFERENCES `rms_upload_rows` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `device_instance_meters` (
    `id`               INT(11)                                                                                                 NOT NULL AUTO_INCREMENT,
    `deviceInstanceId` INT(11)                                                                                                 NOT NULL,
    `meterType`        ENUM(\'LIFE\', \'COLOR\', \'COPY BLACK\', \'BLACK\', \'PRINT BLACK\', \'PRINT COLOR\', \'COPY COLOR\', \'SCAN\', \'FAX\') NULL,
    `startMeter`       INT(11)                                                                                                 NOT NULL,
    `endMeter`         INT(11)                                                                                                 NOT NULL,
    `monitorStartDate` DATETIME                                                                                                NOT NULL,
    `monitorEndDate`   DATETIME                                                                                                NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `device_instance_id` (`deviceInstanceId` ASC, `meterType` ASC),
    CONSTRAINT `device_instance_meters_ibfk_1`
    FOREIGN KEY (`deviceInstanceId`)
    REFERENCES `device_instances` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `part_types` (
    `id`   INT(11)      NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `toner_colors` (
    `id`   INT(11)      NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `toners` (
    `id`             INT(11)      NOT NULL AUTO_INCREMENT,
    `sku`            VARCHAR(255) NOT NULL,
    `cost`           DOUBLE       NOT NULL,
    `yield`          INT(11)      NOT NULL,
    `partTypeId`     INT(11)      NOT NULL,
    `manufacturerId` INT(11)      NOT NULL,
    `tonerColorId`   INT(11)      NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `sku` (`sku` ASC, `manufacturerId` ASC),
    INDEX `toners_ibfk_1_idx` (`partTypeId` ASC),
    INDEX `toners_ibfk_3_idx` (`tonerColorId` ASC),
    INDEX `toners_ibfk_2_idx` (`manufacturerId` ASC),
    CONSTRAINT `toners_ibfk_1`
    FOREIGN KEY (`partTypeId`)
    REFERENCES `part_types` (`id`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    CONSTRAINT `toners_ibfk_3`
    FOREIGN KEY (`tonerColorId`)
    REFERENCES `toner_colors` (`id`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    CONSTRAINT `toners_ibfk_2`
    FOREIGN KEY (`manufacturerId`)
    REFERENCES `manufacturers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `toner_configs` (
    `id`   INT(11)      NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `name` (`name` ASC)
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `master_devices` (
    `id`                  INT(11)      NOT NULL AUTO_INCREMENT,
    `dateCreated`         DATETIME     NOT NULL,
    `dutyCycle`           INT(11)      NULL,
    `isCopier`            TINYINT(4)   NOT NULL DEFAULT 0,
    `isDuplex`            TINYINT(4)   NOT NULL DEFAULT 0,
    `isFax`               TINYINT(4)   NOT NULL DEFAULT 0,
    `isLeased`            TINYINT(4)   NOT NULL DEFAULT 0,
    `isReplacementDevice` TINYINT(4)   NOT NULL DEFAULT 0,
    `isScanner`           TINYINT(4)   NOT NULL DEFAULT 0,
    `launchDate`          DATETIME     NOT NULL,
    `manufacturerId`      INT(11)      NOT NULL,
    `modelName`           VARCHAR(255) NOT NULL,
    `leasedTonerYield`    INT(11)      NULL,
    `ppmBlack`            DOUBLE       NULL,
    `ppmColor`            DOUBLE       NULL,
    `partsCostPerPage`    DOUBLE       NULL,
    `laborCostPerPage`    DOUBLE       NULL,
    `tonerConfigId`       INT(11)      NOT NULL,
    `wattsPowerNormal`    DOUBLE       NULL,
    `wattsPowerIdle`      DOUBLE       NULL,
    `reportsTonerLevels`  TINYINT      NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    INDEX `master_devices_ibfk_2_idx` (`tonerConfigId` ASC),
    INDEX `master_devices_ibfk_1_idx` (`manufacturerId` ASC),
    CONSTRAINT `master_devices_ibfk_2`
    FOREIGN KEY (`tonerConfigId`)
    REFERENCES `toner_configs` (`id`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    CONSTRAINT `master_devices_ibfk_1`
    FOREIGN KEY (`manufacturerId`)
    REFERENCES `manufacturers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `device_toners` (
    `toner_id`         INT(11) NOT NULL,
    `master_device_id` INT(11) NOT NULL,
    PRIMARY KEY (`toner_id`, `master_device_id`),
    INDEX `device_toners_ibfk_2_idx` (`master_device_id` ASC),
    CONSTRAINT `device_toners_ibfk_1`
    FOREIGN KEY (`toner_id`)
    REFERENCES `toners` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `device_toners_ibfk_2`
    FOREIGN KEY (`master_device_id`)
    REFERENCES `master_devices` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `rms_master_matchups` (
    `rmsProviderId`  INT(11) NOT NULL,
    `rmsModelId`     INT(11) NOT NULL,
    `masterDeviceId` INT(11) NOT NULL,
    PRIMARY KEY (`rmsProviderId`, `rmsModelId`),
    CONSTRAINT `rms_master_matchups_ibfk_1`
    FOREIGN KEY (`masterDeviceId`)
    REFERENCES `master_devices` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `rms_master_matchups_ibfk_2`
    FOREIGN KEY (`rmsProviderId`, `rmsModelId`)
    REFERENCES `rms_devices` (`rmsProviderId`, `rmsModelId`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `pricing_configs` (
    `id`                       INT(11)      NOT NULL AUTO_INCREMENT,
    `name`                     VARCHAR(255) NOT NULL,
    `color_toner_part_type_id` INT(11)      NULL DEFAULT NULL,
    `mono_toner_part_type_id`  INT(11)      NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `name` (`name` ASC),
    INDEX `pricing_configs_ibfk_1_idx` (`color_toner_part_type_id` ASC),
    INDEX `pricing_configs_ibfk_2_idx` (`mono_toner_part_type_id` ASC),
    CONSTRAINT `pricing_configs_ibfk_1`
    FOREIGN KEY (`color_toner_part_type_id`)
    REFERENCES `part_types` (`id`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    CONSTRAINT `pricing_configs_ibfk_2`
    FOREIGN KEY (`mono_toner_part_type_id`)
    REFERENCES `part_types` (`id`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `replacement_devices` (
    `masterDeviceId`      INT(11)                                                          NOT NULL,
    `dealerId`            INT                                                              NOT NULL,
    `replacementCategory` ENUM(\'BLACK & WHITE\', \'BLACK & WHITE MFP\', \'COLOR\', \'COLOR MFP\') NULL DEFAULT NULL,
    `printSpeed`          INT(11)                                                          NULL,
    `resolution`          INT(11)                                                          NULL DEFAULT NULL,
    `monthlyRate`         DOUBLE                                                           NULL,
    INDEX `replacement_devices_ibfk_2_idx` (`dealerId` ASC),
    PRIMARY KEY (`masterDeviceId`, `dealerId`),
    CONSTRAINT `replacement_devices_ibfk_1`
    FOREIGN KEY (`masterDeviceId`)
    REFERENCES `master_devices` (`id`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    CONSTRAINT `replacement_devices_ibfk_2`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `assessment_settings` (
    `id`                          INT(11)  NOT NULL AUTO_INCREMENT,
    `actualPageCoverageMono`      DOUBLE   NULL DEFAULT NULL,
    `actualPageCoverageColor`     DOUBLE   NULL DEFAULT NULL,
    `laborCostPerPage`            DOUBLE   NULL DEFAULT NULL,
    `partsCostPerPage`            DOUBLE   NULL DEFAULT NULL,
    `adminCostPerPage`            DOUBLE   NULL DEFAULT NULL,
    `assessmentReportMargin`      DOUBLE   NULL DEFAULT NULL,
    `grossMarginReportMargin`     DOUBLE   NULL DEFAULT NULL,
    `monthlyLeasePayment`         DOUBLE   NULL DEFAULT NULL,
    `defaultPrinterCost`          DOUBLE   NULL DEFAULT NULL,
    `leasedBwCostPerPage`         DOUBLE   NULL DEFAULT NULL,
    `leasedColorCostPerPage`      DOUBLE   NULL DEFAULT NULL,
    `mpsBwCostPerPage`            DOUBLE   NULL DEFAULT NULL,
    `mpsColorCostPerPage`         DOUBLE   NULL DEFAULT NULL,
    `kilowattsPerHour`            DOUBLE   NULL DEFAULT NULL,
    `assessmentPricingConfigId`   INT(11)  NULL DEFAULT NULL,
    `grossMarginPricingConfigId`  INT(11)  NULL DEFAULT NULL,
    `reportDate`                  DATETIME NULL DEFAULT NULL,
    `targetMonochromeCostPerPage` DOUBLE   NULL DEFAULT NULL,
    `targetColorCostPerPage`      DOUBLE   NULL DEFAULT NULL,
    `costThreshold`               DOUBLE   NULL DEFAULT NULL,
    `replacementPricingConfigId`  INT(11)  NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `assessment_settings_ibfk_1_idx` (`assessmentPricingConfigId` ASC),
    INDEX `assessment_settings_ibfk_2_idx` (`grossMarginPricingConfigId` ASC),
    INDEX `assessment_settings_ibfk_3_idx` (`replacementPricingConfigId` ASC),
    CONSTRAINT `assessment_settings_ibfk_1`
    FOREIGN KEY (`assessmentPricingConfigId`)
    REFERENCES `pricing_configs` (`id`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    CONSTRAINT `assessment_settings_ibfk_2`
    FOREIGN KEY (`grossMarginPricingConfigId`)
    REFERENCES `pricing_configs` (`id`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    CONSTRAINT `assessment_settings_ibfk_3`
    FOREIGN KEY (`replacementPricingConfigId`)
    REFERENCES `pricing_configs` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `assessments` (
    `id`                  INT(11)      NOT NULL AUTO_INCREMENT,
    `clientId`            INT          NOT NULL,
    `dealerId`            INT          NOT NULL,
    `rmsUploadId`         INT(11)      NULL,
    `userPricingOverride` TINYINT(4)   NULL DEFAULT \'0\',
    `stepName`            VARCHAR(255) NULL,
    `dateCreated`         DATETIME     NOT NULL,
    `lastModified`        DATETIME     NOT NULL,
    `reportDate`          DATETIME     NULL DEFAULT NULL,
    `devicesModified`     TINYINT(4)   NULL DEFAULT \'0\',
    `assessmentSettingId` INT          NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `assessments_ibfk_1_idx` (`clientId` ASC),
    INDEX `assessments_ibfk_2_idx` (`rmsUploadId` ASC),
    INDEX `assessments_ibfk_3_idx` (`dealerId` ASC),
    INDEX `assessments_ibfk_4_idx` (`assessmentSettingId` ASC),
    CONSTRAINT `assessments_ibfk_1`
    FOREIGN KEY (`clientId`)
    REFERENCES `clients` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `assessments_ibfk_2`
    FOREIGN KEY (`rmsUploadId`)
    REFERENCES `rms_uploads` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `assessments_ibfk_3`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `assessments_ibfk_4`
    FOREIGN KEY (`assessmentSettingId`)
    REFERENCES `assessment_settings` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION)
;');

        $this->execute('CREATE TABLE IF NOT EXISTS `survey_settings` (
    `id`                INT(11) NOT NULL AUTO_INCREMENT,
    `pageCoverageMono`  DOUBLE  NULL DEFAULT NULL,
    `pageCoverageColor` DOUBLE  NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `report_survey_settings` (
    `reportId`        INT(11) NOT NULL,
    `surveySettingId` INT(11) NOT NULL,
    PRIMARY KEY (`reportId`),
    INDEX `report_survey_settings_ibfk_2_idx` (`surveySettingId` ASC),
    CONSTRAINT `report_survey_settings_ibfk_1`
    FOREIGN KEY (`reportId`)
    REFERENCES `assessments` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `report_survey_settings_ibfk_2`
    FOREIGN KEY (`surveySettingId`)
    REFERENCES `survey_settings` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `rms_user_matchups` (
    `rmsProviderId`  INT(11) NOT NULL,
    `rmsModelId`     INT(11) NOT NULL,
    `masterDeviceId` INT(11) NOT NULL,
    `userId`         INT(11) NOT NULL,
    PRIMARY KEY (`rmsProviderId`, `rmsModelId`),
    INDEX `rms_user_matchups_ibfk_3_idx` (`userId` ASC),
    INDEX `rms_user_matchups_ibfk_1_idx` (`masterDeviceId` ASC),
    CONSTRAINT `rms_user_matchups_ibfk_2`
    FOREIGN KEY (`rmsProviderId`, `rmsModelId`)
    REFERENCES `rms_devices` (`rmsProviderId`, `rmsModelId`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `rms_user_matchups_ibfk_3`
    FOREIGN KEY (`userId`)
    REFERENCES `users` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `rms_user_matchups_ibfk_1`
    FOREIGN KEY (`masterDeviceId`)
    REFERENCES `master_devices` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `categories` (
    `id`          INT(11)      NOT NULL AUTO_INCREMENT,
    `dealerId`    INT(11)      NOT NULL,
    `name`        VARCHAR(255) NOT NULL,
    `description` TEXT         NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `categories_ibfk_1_idx` (`dealerId` ASC),
    CONSTRAINT `categories_ibfk_1`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `devices` (
    `masterDeviceId` INT(11)      NOT NULL,
    `dealerId`       INT(11)      NOT NULL,
    `cost`           DOUBLE       NOT NULL,
    `dealerSku`      VARCHAR(255) NULL,
    `oemSku`         VARCHAR(255) NOT NULL,
    `description`    TEXT         NULL,
    PRIMARY KEY (`masterDeviceId`, `dealerId`),
    INDEX `devices_ibfk_1_idx` (`masterDeviceId` ASC),
    INDEX `devices_ibfk_2_idx` (`dealerId` ASC),
    CONSTRAINT `devices_ibfk_1`
    FOREIGN KEY (`masterDeviceId`)
    REFERENCES `master_devices` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `devices_ibfk_2`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `device_configurations` (
    `id`             INT(11)      NOT NULL AUTO_INCREMENT,
    `masterDeviceId` INT(11)      NOT NULL,
    `dealerId`       INT(11)      NOT NULL,
    `name`           VARCHAR(255) NOT NULL,
    `description`    VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `device_configurations_ibfk_1_idx` (`masterDeviceId` ASC),
    INDEX `device_configurations_ibfk_2_idx` (`dealerId` ASC),
    CONSTRAINT `device_configurations_ibfk_1`
    FOREIGN KEY (`masterDeviceId`)
    REFERENCES `devices` (`masterDeviceId`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `device_configurations_ibfk_2`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `options` (
    `id`          INT(11)      NOT NULL AUTO_INCREMENT,
    `dealerId`    INT(11)      NOT NULL,
    `name`        VARCHAR(255) NOT NULL,
    `description` TEXT         NOT NULL,
    `cost`        DOUBLE       NOT NULL,
    `dealerSku`   VARCHAR(255) NULL,
    `oemSku`      VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `qgen_options_ibfk_1_idx` (`dealerId` ASC),
    CONSTRAINT `options_ibfk_1`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `device_configuration_options` (
    `deviceConfigurationId` INT(11) NOT NULL,
    `optionId`              INT(11) NOT NULL,
    `quantity`              INT(11) NOT NULL DEFAULT 1,
    PRIMARY KEY (`deviceConfigurationId`, `optionId`),
    INDEX `device_configuration_options_ibfk_2_idx` (`optionId` ASC),
    CONSTRAINT `device_configuration_options_ibfk_1`
    FOREIGN KEY (`deviceConfigurationId`)
    REFERENCES `device_configurations` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `device_configuration_options_ibfk_2`
    FOREIGN KEY (`optionId`)
    REFERENCES `options` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `device_options` (
    `masterDeviceId`   INT(11) NOT NULL,
    `optionId`         INT(11) NOT NULL,
    `dealerId`         INT(11) NOT NULL,
    `includedQuantity` INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`masterDeviceId`, `optionId`),
    INDEX `device_options_ibfk_2_idx` (`optionId` ASC),
    INDEX `device_options_ibfk_3_idx` (`dealerId` ASC),
    CONSTRAINT `device_options_ibfk_1`
    FOREIGN KEY (`masterDeviceId`)
    REFERENCES `devices` (`masterDeviceId`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `device_options_ibfk_2`
    FOREIGN KEY (`optionId`)
    REFERENCES `options` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `device_options_ibfk_3`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `global_device_configurations` (
    `deviceConfigurationId` INT(11) NOT NULL,
    PRIMARY KEY (`deviceConfigurationId`),
    CONSTRAINT `global_device_configurations_ibfk_1`
    FOREIGN KEY (`deviceConfigurationId`)
    REFERENCES `device_configurations` (`id`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `leasing_schemas` (
    `id`       INT(11)      NOT NULL AUTO_INCREMENT,
    `dealerId` INT(11)      NOT NULL,
    `name`     VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `qgen_leasing_schemas_ibfk_1_idx` (`dealerId` ASC),
    CONSTRAINT `leasing_schemas_ibfk_1`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `global_leasing_schemas` (
    `leasingSchemaId` INT(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`leasingSchemaId`),
    INDEX `global_leasing_schemas_ibfk_1_idx` (`leasingSchemaId` ASC),
    CONSTRAINT `global_leasing_schemas_ibfk_1`
    FOREIGN KEY (`leasingSchemaId`)
    REFERENCES `leasing_schemas` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `leasing_schema_ranges` (
    `id`              INT(11) NOT NULL AUTO_INCREMENT,
    `leasingSchemaId` INT(11) NOT NULL,
    `startRange`      DOUBLE  NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `leasing_schema_ranges_ibfk_1_idx` (`leasingSchemaId` ASC),
    CONSTRAINT `leasing_schema_ranges_ibfk_1`
    FOREIGN KEY (`leasingSchemaId`)
    REFERENCES `leasing_schemas` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `leasing_schema_terms` (
    `id`              INT(11) NOT NULL AUTO_INCREMENT,
    `leasingSchemaId` INT(11) NOT NULL,
    `months`          INT(11) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `leasing_schema_terms_ibfk_1_idx` (`leasingSchemaId` ASC),
    CONSTRAINT `leasing_schema_terms_ibfk_1`
    FOREIGN KEY (`leasingSchemaId`)
    REFERENCES `leasing_schemas` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `leasing_schema_rates` (
    `leasingSchemaTermId`  INT(11) NOT NULL,
    `leasingSchemaRangeId` INT(11) NOT NULL,
    `rate`                 DOUBLE  NOT NULL,
    PRIMARY KEY (`leasingSchemaTermId`, `leasingSchemaRangeId`),
    INDEX `leasing_schema_rates_ibfk_1_idx` (`leasingSchemaTermId` ASC),
    INDEX `leasing_schema_rates_ibfk_2_idx` (`leasingSchemaRangeId` ASC),
    CONSTRAINT `leasing_schema_rates_ibfk_1`
    FOREIGN KEY (`leasingSchemaTermId`)
    REFERENCES `leasing_schema_terms` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `leasing_schema_rates_ibfk_2`
    FOREIGN KEY (`leasingSchemaRangeId`)
    REFERENCES `leasing_schema_ranges` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `option_categories` (
    `categoryId` INT(11) NOT NULL,
    `optionId`   INT(11) NOT NULL,
    PRIMARY KEY (`categoryId`, `optionId`),
    INDEX `optionId` (`optionId` ASC),
    CONSTRAINT `option_categories_ibfk_1`
    FOREIGN KEY (`categoryId`)
    REFERENCES `categories` (`id`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    CONSTRAINT `option_categories_ibfk_2`
    FOREIGN KEY (`optionId`)
    REFERENCES `options` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `quotes` (
    `id`                      INT(11)                     NOT NULL AUTO_INCREMENT,
    `clientId`                INT(11)                     NOT NULL,
    `dateCreated`             DATETIME                    NOT NULL,
    `dateModified`            DATETIME                    NOT NULL,
    `quoteDate`               DATETIME                    NOT NULL,
    `clientDisplayName`       VARCHAR(45)                 NULL,
    `leaseRate`               DOUBLE                      NULL,
    `leaseTerm`               INT(11)                     NULL,
    `pageCoverageMonochrome`  DOUBLE                      NOT NULL,
    `pageCoverageColor`       DOUBLE                      NOT NULL,
    `pricingConfigId`         INT(11)                     NOT NULL,
    `quoteType`               ENUM(\'purchased\', \'leased\') NOT NULL,
    `monochromePageMargin`    DOUBLE                      NOT NULL,
    `colorPageMargin`         DOUBLE                      NOT NULL,
    `adminCostPerPage`        DOUBLE                      NOT NULL,
    `monochromeOverageMargin` DOUBLE                      NOT NULL,
    `colorOverageMargin`      DOUBLE                      NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `quotes_ibfk_1_idx` (`clientId` ASC),
    INDEX `quotes_ibfk_2_idx` (`pricingConfigId` ASC),
    CONSTRAINT `quotes_ibfk_1`
    FOREIGN KEY (`clientId`)
    REFERENCES `clients` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `quotes_ibfk_2`
    FOREIGN KEY (`pricingConfigId`)
    REFERENCES `pricing_configs` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `quote_devices` (
    `id`                        INT(11)      NOT NULL AUTO_INCREMENT,
    `quoteId`                   INT(11)      NOT NULL,
    `name`                      VARCHAR(255) NOT NULL,
    `oemSku`                    VARCHAR(255) NOT NULL,
    `dealerSku`                 VARCHAR(255) NULL,
    `oemCostPerPageMonochrome`  DOUBLE       NOT NULL,
    `oemCostPerPageColor`       DOUBLE       NOT NULL,
    `compCostPerPageMonochrome` DOUBLE       NOT NULL,
    `compCostPerPageColor`      DOUBLE       NOT NULL,
    `cost`                      DOUBLE       NOT NULL,
    `packageCost`               DOUBLE       NOT NULL,
    `packageMarkup`             DOUBLE       NOT NULL,
    `residual`                  DOUBLE       NOT NULL,
    `margin`                    DOUBLE       NOT NULL,
    `tonerConfigId`             INT          NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `quote_devices_ibfk_1_idx` (`quoteId` ASC),
    CONSTRAINT `quote_devices_ibfk_1`
    FOREIGN KEY (`quoteId`)
    REFERENCES `quotes` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `quote_device_options` (
    `id`               INT(11)      NOT NULL AUTO_INCREMENT,
    `quoteDeviceId`    INT(11)      NOT NULL,
    `oemSku`           VARCHAR(255) NOT NULL,
    `dealerSku`        VARCHAR(255) NULL,
    `name`             VARCHAR(255) NOT NULL,
    `description`      TEXT         NOT NULL,
    `cost`             DOUBLE       NOT NULL,
    `quantity`         INT(11)      NOT NULL,
    `includedQuantity` INT(11)      NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `quote_device_options_ibfk_1_idx` (`quoteDeviceId` ASC),
    CONSTRAINT `quote_device_options_ibfk_1`
    FOREIGN KEY (`quoteDeviceId`)
    REFERENCES `quote_devices` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `quote_device_configuration_options` (
    `quoteDeviceOptionId` INT(11) NOT NULL,
    `optionId`            INT(11) NOT NULL,
    `masterDeviceId`      INT(11) NOT NULL,
    PRIMARY KEY (`quoteDeviceOptionId`),
    INDEX `quotegen_quote_device_option_options_ibfk_1_idx` (`optionId` ASC, `masterDeviceId` ASC),
    INDEX `quotegen_quote_device_option_options_ibfk_2_idx` (`quoteDeviceOptionId` ASC),
    CONSTRAINT `quote_device_option_options_ibfk_1`
    FOREIGN KEY (`optionId`, `masterDeviceId`)
    REFERENCES `device_options` (`optionId`, `masterDeviceId`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `quote_device_option_options_ibfk_2`
    FOREIGN KEY (`quoteDeviceOptionId`)
    REFERENCES `quote_device_options` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `quote_device_configurations` (
    `quoteDeviceId`  INT(11) NOT NULL AUTO_INCREMENT,
    `masterDeviceId` INT(11) NOT NULL,
    PRIMARY KEY (`quoteDeviceId`, `masterDeviceId`),
    INDEX `deviceConfigurationId` (`masterDeviceId` ASC),
    INDEX `quoteDeviceId` (`quoteDeviceId` ASC),
    CONSTRAINT `quote_device_configurations_ibfk_1`
    FOREIGN KEY (`masterDeviceId`)
    REFERENCES `devices` (`masterDeviceId`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `quote_device_configurations_ibfk_2`
    FOREIGN KEY (`quoteDeviceId`)
    REFERENCES `quote_devices` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `quote_settings` (
    `id`                     INT(11) NOT NULL AUTO_INCREMENT,
    `pageCoverageMonochrome` DOUBLE  NULL DEFAULT NULL,
    `pageCoverageColor`      DOUBLE  NULL DEFAULT NULL,
    `deviceMargin`           DOUBLE  NULL DEFAULT NULL,
    `pageMargin`             DOUBLE  NULL DEFAULT NULL,
    `pricingConfigId`        INT(11) NULL DEFAULT NULL,
    `adminCostPerPage`       DOUBLE  NULL,
    PRIMARY KEY (`id`),
    INDEX `quote_settings_ibfk_1_idx` (`pricingConfigId` ASC),
    CONSTRAINT `quote_settings_ibfk_1`
    FOREIGN KEY (`pricingConfigId`)
    REFERENCES `pricing_configs` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `user_device_configurations` (
    `deviceConfigurationId` INT(11) NOT NULL,
    `userId`                INT(11) NOT NULL,
    PRIMARY KEY (`deviceConfigurationId`, `userId`),
    INDEX `user_device_configurations_ibfk_2_idx` (`userId` ASC),
    CONSTRAINT `user_device_configurations_ibfk_1`
    FOREIGN KEY (`deviceConfigurationId`)
    REFERENCES `device_configurations` (`id`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    CONSTRAINT `user_device_configurations_ibfk_2`
    FOREIGN KEY (`userId`)
    REFERENCES `users` (`id`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `roles` (
    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(255) NOT NULL,
    `systemRole` TINYINT      NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `sessions` (
    `id`       CHAR(32) NOT NULL,
    `modified` INT(11)  NOT NULL,
    `lifetime` INT(11)  NOT NULL,
    `data`     TEXT     NULL,
    PRIMARY KEY (`id`)
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `user_roles` (
    `userId` INT(11) NOT NULL,
    `roleId` INT(11) NOT NULL,
    PRIMARY KEY (`userId`, `roleId`),
    INDEX `roles_ibfk_2_idx` (`roleId` ASC),
    CONSTRAINT `roles_ibfk_1`
    FOREIGN KEY (`userId`)
    REFERENCES `users` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `roles_ibfk_2`
    FOREIGN KEY (`roleId`)
    REFERENCES `roles` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `quote_lease_terms` (
    `quoteId`             INT NOT NULL,
    `leasingSchemaTermId` INT NOT NULL,
    PRIMARY KEY (`quoteId`),
    INDEX `quote_lease_terms_ibfk_1_idx` (`quoteId` ASC),
    INDEX `quote_lease_terms_ibfk_2_idx` (`leasingSchemaTermId` ASC),
    UNIQUE INDEX `quoteId_UNIQUE` (`quoteId` ASC, `leasingSchemaTermId` ASC),
    CONSTRAINT `quote_lease_terms_ibfk_1`
    FOREIGN KEY (`quoteId`)
    REFERENCES `quotes` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `quote_lease_terms_ibfk_2`
    FOREIGN KEY (`leasingSchemaTermId`)
    REFERENCES `leasing_schema_terms` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `quote_device_groups` (
    `id`         INT          NOT NULL AUTO_INCREMENT,
    `quoteId`    INT          NOT NULL,
    `name`       VARCHAR(255) NOT NULL,
    `isDefault`  TINYINT      NOT NULL DEFAULT 0,
    `groupPages` TINYINT      NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    INDEX `quote_device_groups_ibfk_1_idx` (`quoteId` ASC),
    CONSTRAINT `quote_device_groups_ibfk_1`
    FOREIGN KEY (`quoteId`)
    REFERENCES `quotes` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `quote_device_group_devices` (
    `quoteDeviceId`           INT NOT NULL,
    `quoteDeviceGroupId`      INT NOT NULL,
    `quantity`                INT NOT NULL DEFAULT 1,
    `monochromePagesQuantity` INT NOT NULL,
    `colorPagesQuantity`      INT NOT NULL,
    PRIMARY KEY (`quoteDeviceGroupId`, `quoteDeviceId`),
    INDEX `qgen_quote_device_group_devices_ibfk1_idx` (`quoteDeviceId` ASC),
    INDEX `qgen_quote_device_group_devices_ibfk2_idx` (`quoteDeviceGroupId` ASC),
    CONSTRAINT `quote_device_group_devices_ibfk1`
    FOREIGN KEY (`quoteDeviceId`)
    REFERENCES `quote_devices` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `quote_device_group_devices_ibfk2`
    FOREIGN KEY (`quoteDeviceGroupId`)
    REFERENCES `quote_device_groups` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `contacts` (
    `id`           INT(11)      NOT NULL AUTO_INCREMENT,
    `clientId`     INT          NOT NULL,
    `firstName`    VARCHAR(255) NOT NULL,
    `lastName`     VARCHAR(255) NOT NULL,
    `countryCode`  CHAR(4)      NULL,
    `areaCode`     CHAR(3)      NULL,
    `exchangeCode` CHAR(3)      NULL,
    `number`       CHAR(4)      NULL,
    `extension`    VARCHAR(255) NULL,
    PRIMARY KEY (`id`),
    INDEX `contacts_ibfk_1_idx` (`clientId` ASC),
    CONSTRAINT `contacts_ibfk_1`
    FOREIGN KEY (`clientId`)
    REFERENCES `clients` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `countries` (
    `id`     INT(11)      NOT NULL AUTO_INCREMENT,
    `name`   VARCHAR(255) NOT NULL,
    `locale` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `addresses` (
    `id`           INT          NOT NULL AUTO_INCREMENT,
    `clientId`     INT          NOT NULL,
    `addressLine1` VARCHAR(255) NOT NULL,
    `addressLine2` VARCHAR(255) NULL,
    `city`         VARCHAR(255) NOT NULL,
    `region`       VARCHAR(255) NOT NULL,
    `postCode`     VARCHAR(255) NOT NULL,
    `countryId`    INT          NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `addresses_ibfk_1_idx` (`clientId` ASC),
    INDEX `addresses_ibfk_2_idx` (`countryId` ASC),
    CONSTRAINT `addresses_ibfk_1`
    FOREIGN KEY (`clientId`)
    REFERENCES `clients` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `addresses_ibfk_2`
    FOREIGN KEY (`countryId`)
    REFERENCES `countries` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `regions` (
    `id`        INT     NOT NULL AUTO_INCREMENT,
    `countryId` INT     NOT NULL,
    `region`    CHAR(2) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `regions_ibfk_1_idx` (`countryId` ASC),
    CONSTRAINT `regions_ibfk_1`
    FOREIGN KEY (`countryId`)
    REFERENCES `countries` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `device_instance_master_devices` (
    `deviceInstanceId` INT NOT NULL,
    `masterDeviceId`   INT NOT NULL,
    PRIMARY KEY (`deviceInstanceId`),
    INDEX `device_instance_master_devices_ibfk_1_idx` (`deviceInstanceId` ASC),
    INDEX `device_instance_master_devices_ibfk_2_idx` (`masterDeviceId` ASC),
    CONSTRAINT `device_instance_master_devices_ibfk_1`
    FOREIGN KEY (`deviceInstanceId`)
    REFERENCES `device_instances` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `device_instance_master_devices_ibfk_2`
    FOREIGN KEY (`masterDeviceId`)
    REFERENCES `master_devices` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `rms_excluded_rows` (
    `id`               INT          NOT NULL AUTO_INCREMENT,
    `rmsUploadId`      INT          NOT NULL,
    `rmsProviderId`    INT          NULL,
    `rmsModelId`       INT          NULL,
    `serialNumber`     VARCHAR(255) NULL,
    `ipAddress`        VARCHAR(255) NULL,
    `modelName`        VARCHAR(255) NULL,
    `manufacturerName` VARCHAR(255) NULL,
    `reason`           VARCHAR(255) NOT NULL,
    `csvLineNumber`    INT(11)      NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `rms_excluded_rows_ibfk_1_idx` (`rmsUploadId` ASC),
    CONSTRAINT `rms_excluded_rows_ibfk_1`
    FOREIGN KEY (`rmsUploadId`)
    REFERENCES `rms_uploads` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `hardware_optimization_settings` (
    `id`                          INT    NOT NULL AUTO_INCREMENT,
    `costThreshold`               DOUBLE NULL,
    `dealerPricingConfigId`       INT    NULL,
    `targetColorCostPerPage`      DOUBLE NULL,
    `targetMonochromeCostPerPage` DOUBLE NULL,
    `replacementPricingConfigId`  INT    NULL,
    `adminCostPerPage`            DOUBLE NULL,
    `laborCostPerPage`            DOUBLE NULL,
    `partsCostPerPage`            DOUBLE NULL,
    `pageCoverageMonochrome`      DOUBLE NULL,
    `pageCoverageColor`           DOUBLE NULL,
    PRIMARY KEY (`id`),
    INDEX `hardware_optimization_settings_ibfk_2_idx` (`dealerPricingConfigId` ASC),
    INDEX `hardware_optimization_settings_ibfk_3_idx` (`replacementPricingConfigId` ASC),
    CONSTRAINT `hardware_optimization_settings_ibfk_2`
    FOREIGN KEY (`dealerPricingConfigId`)
    REFERENCES `pricing_configs` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `hardware_optimization_settings_ibfk_3`
    FOREIGN KEY (`replacementPricingConfigId`)
    REFERENCES `pricing_configs` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `hardware_optimizations` (
    `id`                            INT          NOT NULL AUTO_INCREMENT,
    `clientId`                      INT(11)      NOT NULL,
    `dealerId`                      INT          NOT NULL,
    `hardwareOptimizationSettingId` INT          NOT NULL,
    `dateCreated`                   DATETIME     NOT NULL,
    `lastModified`                  DATETIME     NOT NULL,
    `name`                          VARCHAR(255) NULL,
    `rmsUploadId`                   INT(11)      NULL,
    `stepName`                      VARCHAR(255) NULL,
    INDEX `hardware_optimization_ibfk_1_idx` (`clientId` ASC),
    INDEX `hardware_optimization_ibfk_2_idx` (`rmsUploadId` ASC),
    PRIMARY KEY (`id`),
    INDEX `hardware_optimization_ibfk_4_idx` (`dealerId` ASC),
    INDEX `hardware_optimization_ibfk_3_idx` (`hardwareOptimizationSettingId` ASC),
    CONSTRAINT `hardware_optimization_ibfk_1`
    FOREIGN KEY (`clientId`)
    REFERENCES `clients` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `hardware_optimization_ibfk_2`
    FOREIGN KEY (`rmsUploadId`)
    REFERENCES `rms_uploads` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `hardware_optimization_ibfk_3`
    FOREIGN KEY (`hardwareOptimizationSettingId`)
    REFERENCES `hardware_optimization_settings` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `hardware_optimization_ibfk_4`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `device_instance_replacement_master_devices` (
    `deviceInstanceId`       INT(11) NOT NULL,
    `hardwareOptimizationId` INT(11) NOT NULL,
    `masterDeviceId`         INT(11) NOT NULL,
    INDEX `device_instance_replacement_master_devices_ibfk_1_idx` (`masterDeviceId` ASC),
    INDEX `device_instance_replacement_master_devices_ibfk_2_idx` (`deviceInstanceId` ASC),
    PRIMARY KEY (`deviceInstanceId`, `hardwareOptimizationId`),
    UNIQUE INDEX `deviceInstanceId_UNIQUE` (`deviceInstanceId` ASC, `hardwareOptimizationId` ASC),
    INDEX `device_instance_replacement_master_devices_ibfk_3_idx` (`hardwareOptimizationId` ASC),
    CONSTRAINT `device_instance_replacement_master_devices_ibfk_1`
    FOREIGN KEY (`masterDeviceId`)
    REFERENCES `master_devices` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `device_instance_replacement_master_devices_ibfk_2`
    FOREIGN KEY (`deviceInstanceId`)
    REFERENCES `device_instances` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `device_instance_replacement_master_devices_ibfk_3`
    FOREIGN KEY (`hardwareOptimizationId`)
    REFERENCES `hardware_optimizations` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `assessment_surveys` (
    `reportId`                      INT    NOT NULL,
    `costOfInkAndToner`             DOUBLE NULL,
    `costOfLabor`                   DOUBLE NULL,
    `costToExecuteSuppliesOrder`    DOUBLE NOT NULL DEFAULT 50.00,
    `averageItHourlyRate`           DOUBLE NOT NULL DEFAULT 40.00,
    `numberOfSupplyOrdersPerMonth`  DOUBLE NOT NULL,
    `hoursSpentOnIt`                INT    NULL,
    `averageMonthlyBreakdowns`      DOUBLE NULL,
    `pageCoverageMonochrome`        DOUBLE NOT NULL,
    `pageCoverageColor`             DOUBLE NOT NULL,
    `percentageOfInkjetPrintVolume` DOUBLE NOT NULL,
    `averageRepairTime`             DOUBLE NOT NULL,
    PRIMARY KEY (`reportId`),
    CONSTRAINT `assessment_surveys_ibfk_1`
    FOREIGN KEY (`reportId`)
    REFERENCES `assessments` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `user_sessions` (
    `sessionId` CHAR(32) NOT NULL,
    `userId`    INT      NOT NULL,
    INDEX `user_sessions_ibfk_1_idx` (`userId` ASC),
    UNIQUE INDEX `user_sessions_sessionId_UNIQUE` (`sessionId` ASC),
    UNIQUE INDEX `user_sessions_userId_UNIQUE` (`userId` ASC),
    PRIMARY KEY (`sessionId`),
    CONSTRAINT `user_sessions_ibfk_1`
    FOREIGN KEY (`userId`)
    REFERENCES `users` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `user_sessions_ibfk_2`
    FOREIGN KEY (`sessionId`)
    REFERENCES `sessions` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `healthcheck_settings` (
    `id`                           INT(11)  NOT NULL AUTO_INCREMENT,
    `actualPageCoverageMono`       DOUBLE   NULL DEFAULT NULL,
    `actualPageCoverageColor`      DOUBLE   NULL DEFAULT NULL,
    `laborCostPerPage`             DOUBLE   NULL DEFAULT NULL,
    `partsCostPerPage`             DOUBLE   NULL DEFAULT NULL,
    `adminCostPerPage`             DOUBLE   NULL DEFAULT NULL,
    `healthcheckMargin`            DOUBLE   NULL DEFAULT NULL,
    `monthlyLeasePayment`          DOUBLE   NULL DEFAULT NULL,
    `defaultPrinterCost`           DOUBLE   NULL DEFAULT NULL,
    `leasedBwCostPerPage`          DOUBLE   NULL DEFAULT NULL,
    `leasedColorCostPerPage`       DOUBLE   NULL DEFAULT NULL,
    `mpsBwCostPerPage`             DOUBLE   NULL DEFAULT NULL,
    `mpsColorCostPerPage`          DOUBLE   NULL DEFAULT NULL,
    `kilowattsPerHour`             DOUBLE   NULL DEFAULT NULL,
    `healthcheckPricingConfigId`   INT(11)  NULL DEFAULT NULL,
    `reportDate`                   DATETIME NULL DEFAULT NULL,
    `costThreshold`                DOUBLE   NULL DEFAULT NULL,
    `pageCoverageMonochrome`       INT(11)  NULL DEFAULT NULL,
    `pageCoverageColor`            INT(11)  NULL DEFAULT NULL,
    `averageItHourlyRate`          DOUBLE   NULL DEFAULT NULL,
    `hoursSpentOnIt`               DOUBLE   NULL DEFAULT NULL,
    `costOfLabor`                  DOUBLE   NULL DEFAULT NULL,
    `costToExecuteSuppliesOrder`   DOUBLE   NULL DEFAULT NULL,
    `numberOfSupplyOrdersPerMonth` DOUBLE   NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `healthcheck_settings_ibfk_1_idx` (`healthcheckPricingConfigId` ASC),
    CONSTRAINT `healthcheck_settings_ibfk_1`
    FOREIGN KEY (`healthcheckPricingConfigId`)
    REFERENCES `pricing_configs` (`id`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `healthchecks` (
    `id`                   INT          NOT NULL AUTO_INCREMENT,
    `clientId`             INT(11)      NOT NULL,
    `dealerId`             INT          NOT NULL,
    `rmsUploadId`          INT(11)      NULL,
    `name`                 VARCHAR(255) NOT NULL,
    `stepName`             VARCHAR(255) NOT NULL,
    `dateCreated`          DATETIME     NOT NULL,
    `lastModified`         DATETIME     NOT NULL,
    `reportDate`           DATETIME     NULL,
    `healthcheckSettingId` INT          NOT NULL,
    `devicesModified`      TINYINT      NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    INDEX `health_check_ibfk_1_idx` (`clientId` ASC),
    INDEX `health_check_ibfk_2_idx` (`rmsUploadId` ASC),
    INDEX `health_check_ibfk_3_idx` (`dealerId` ASC),
    INDEX `health_check_ibfk_4_idx` (`healthcheckSettingId` ASC),
    CONSTRAINT `health_check_ibfk_1`
    FOREIGN KEY (`clientId`)
    REFERENCES `clients` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `health_check_ibfk_2`
    FOREIGN KEY (`rmsUploadId`)
    REFERENCES `rms_uploads` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `health_check_ibfk_3`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `health_check_ibfk_4`
    FOREIGN KEY (`healthcheckSettingId`)
    REFERENCES `healthcheck_settings` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `user_password_reset_requests` (
    `id`            INT          NOT NULL AUTO_INCREMENT,
    `dateRequested` DATETIME     NOT NULL,
    `resetToken`    VARCHAR(255) NOT NULL,
    `ipAddress`     VARCHAR(255) NOT NULL,
    `resetVerified` TINYINT(1)   NOT NULL,
    `userId`        INT          NOT NULL,
    `resetUsed`     TINYINT(1)   NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `user_password_reset_requests_ibfk_1_idx` (`userId` ASC),
    CONSTRAINT `user_password_reset_requests_ibfk_1`
    FOREIGN KEY (`userId`)
    REFERENCES `users` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `dealer_master_device_attributes` (
    `masterDeviceId`   INT    NOT NULL,
    `dealerId`         INT    NOT NULL,
    `laborCostPerPage` DOUBLE NULL,
    `partsCostPerPage` DOUBLE NULL,
    PRIMARY KEY (`masterDeviceId`, `dealerId`),
    INDEX `dealer_master_device_attributes_ibfk_1_idx` (`dealerId` ASC),
    INDEX `dealer_master_device_attributes_ibfk_2_idx` (`masterDeviceId` ASC),
    CONSTRAINT `dealer_master_device_attributes_ibfk_1`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `dealer_master_device_attributes_ibfk_2`
    FOREIGN KEY (`masterDeviceId`)
    REFERENCES `master_devices` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `dealer_toner_attributes` (
    `tonerId`   INT(11)      NOT NULL,
    `dealerId`  INT(11)      NOT NULL,
    `cost`      DOUBLE       NULL,
    `dealerSku` VARCHAR(255) NULL,
    PRIMARY KEY (`tonerId`, `dealerId`),
    INDEX `dealer_toner_attributes_ibfk_1_idx` (`tonerId` ASC),
    INDEX `dealer_toner_attributes_ibfk_2_idx` (`dealerId` ASC),
    CONSTRAINT `dealer_toner_attributes_ibfk_1`
    FOREIGN KEY (`tonerId`)
    REFERENCES `toners` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `dealer_toner_attributes_ibfk_2`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `user_viewed_clients` (
    `userId`     INT      NOT NULL,
    `clientId`   INT      NOT NULL,
    `dateViewed` DATETIME NULL,
    PRIMARY KEY (`userId`, `clientId`),
    INDEX `user_viewed_clients_ibfk_1_idx` (`clientId` ASC),
    INDEX `user_viewed_clients_ibfk_2_idx` (`userId` ASC),
    CONSTRAINT `user_viewed_clients_ibfk_1`
    FOREIGN KEY (`clientId`)
    REFERENCES `clients` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `user_viewed_clients_ibfk_2`
    FOREIGN KEY (`userId`)
    REFERENCES `users` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `rms_device_matchups` (
    `manufacturerId` INT          NOT NULL,
    `modelName`      VARCHAR(255) NOT NULL,
    `masterDeviceId` INT          NULL,
    INDEX `rms_device_matchups_ibfk1_idx` (`manufacturerId` ASC),
    INDEX `rms_device_matchups_ibfk2_idx` (`masterDeviceId` ASC),
    PRIMARY KEY (`manufacturerId`, `modelName`),
    CONSTRAINT `rms_device_matchups_ibfk1`
    FOREIGN KEY (`manufacturerId`)
    REFERENCES `manufacturers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `rms_device_matchups_ibfk2`
    FOREIGN KEY (`masterDeviceId`)
    REFERENCES `master_devices` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `dealer_settings` (
    `dealerId`                      INT NOT NULL,
    `assessmentSettingId`           INT NULL,
    `hardwareOptimizationSettingId` INT NULL,
    `healthcheckSettingId`          INT NULL,
    `surveySettingId`               INT NULL,
    `quoteSettingId`                INT NULL,
    PRIMARY KEY (`dealerId`),
    INDEX `dealer_settings_ibfk_2_idx` (`assessmentSettingId` ASC),
    INDEX `dealer_settings_ibfk_3_idx` (`surveySettingId` ASC),
    INDEX `dealer_settings_ibfk_4_idx` (`healthcheckSettingId` ASC),
    INDEX `dealer_settings_ibfk_5_idx` (`hardwareOptimizationSettingId` ASC),
    INDEX `dealer_settings_ibfk_6_idx` (`quoteSettingId` ASC),
    CONSTRAINT `dealer_settings_ibfk_1`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `dealer_settings_ibfk_2`
    FOREIGN KEY (`assessmentSettingId`)
    REFERENCES `assessment_settings` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `dealer_settings_ibfk_3`
    FOREIGN KEY (`surveySettingId`)
    REFERENCES `survey_settings` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `dealer_settings_ibfk_4`
    FOREIGN KEY (`healthcheckSettingId`)
    REFERENCES `healthcheck_settings` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `dealer_settings_ibfk_5`
    FOREIGN KEY (`hardwareOptimizationSettingId`)
    REFERENCES `hardware_optimization_settings` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `dealer_settings_ibfk_6`
    FOREIGN KEY (`quoteSettingId`)
    REFERENCES `quote_settings` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `user_settings` (
    `userId`                        INT NOT NULL,
    `assessmentSettingId`           INT NULL,
    `hardwareOptimizationSettingId` INT NULL,
    `healthcheckSettingId`          INT NULL,
    `surveySettingId`               INT NULL,
    `quoteSettingId`                INT NULL,
    PRIMARY KEY (`userId`),
    INDEX `user_settings_ibfk_2_idx` (`assessmentSettingId` ASC),
    INDEX `user_settings_ibfk_3_idx` (`surveySettingId` ASC),
    INDEX `user_settings_ibfk_4_idx` (`healthcheckSettingId` ASC),
    INDEX `user_settings_ibfk_5_idx` (`hardwareOptimizationSettingId` ASC),
    INDEX `user_settings_ibfk_6_idx` (`quoteSettingId` ASC),
    CONSTRAINT `user_settings_ibfk_1`
    FOREIGN KEY (`userId`)
    REFERENCES `users` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `user_settings_ibfk_2`
    FOREIGN KEY (`assessmentSettingId`)
    REFERENCES `assessment_settings` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `user_settings_ibfk_3`
    FOREIGN KEY (`surveySettingId`)
    REFERENCES `survey_settings` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `user_settings_ibfk_4`
    FOREIGN KEY (`healthcheckSettingId`)
    REFERENCES `healthcheck_settings` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `user_settings_ibfk_5`
    FOREIGN KEY (`hardwareOptimizationSettingId`)
    REFERENCES `hardware_optimization_settings` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `user_settings_ibfk_6`
    FOREIGN KEY (`quoteSettingId`)
    REFERENCES `quote_settings` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `device_swaps` (
    `masterDeviceId`   INT NOT NULL,
    `dealerId`         INT NOT NULL,
    `minimumPageCount` INT NULL,
    `maximumPageCount` INT NULL,
    PRIMARY KEY (`masterDeviceId`, `dealerId`),
    INDEX `device_swaps_ibkf1_idx` (`masterDeviceId` ASC),
    INDEX `device_swaps_ibkf2_idx` (`dealerId` ASC),
    CONSTRAINT `device_swaps_ibkf1`
    FOREIGN KEY (`masterDeviceId`)
    REFERENCES `master_devices` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `device_swaps_ibkf2`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `privileges` (
    `id`         INT          NOT NULL,
    `roleId`     INT          NOT NULL,
    `module`     VARCHAR(255) NOT NULL,
    `controller` VARCHAR(255) NOT NULL,
    `action`     VARCHAR(255) NOT NULL,
    `isAdmin`    TINYINT      NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    INDEX `privileges_ibfk_1_idx` (`roleId` ASC),
    CONSTRAINT `privileges_ibfk_1`
    FOREIGN KEY (`roleId`)
    REFERENCES `roles` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `device_swap_reason_categories` (
    `id`   INT(11)      NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `device_swap_reasons` (
    `id`                         INT(11)      NOT NULL AUTO_INCREMENT,
    `dealerId`                   INT(11)      NOT NULL,
    `deviceSwapReasonCategoryId` INT(11)      NOT NULL,
    `reason`                     VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `dealer_swap_reasons_ibfk1_idx` (`dealerId` ASC),
    INDEX `dealer_swap_reasons_ibfk2_idx` (`deviceSwapReasonCategoryId` ASC),
    CONSTRAINT `dealer_swap_reasons_ibfk1`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `dealer_swap_reasons_ibfk2`
    FOREIGN KEY (`deviceSwapReasonCategoryId`)
    REFERENCES `device_swap_reason_categories` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `device_swap_reason_defaults` (
    `deviceSwapReasonCategoryId` INT(11) NOT NULL,
    `dealerId`                   INT     NOT NULL,
    `deviceSwapReasonId`         INT(11) NOT NULL,
    PRIMARY KEY (`deviceSwapReasonCategoryId`, `dealerId`),
    INDEX `device_swap_defaults_ibk2_idx` (`deviceSwapReasonId` ASC),
    INDEX `device_swap_reason_category_defaults_ibkf1_idx` (`dealerId` ASC),
    CONSTRAINT `device_swap_reason_defaults_ibfk2`
    FOREIGN KEY (`deviceSwapReasonId`)
    REFERENCES `device_swap_reasons` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `device_swap_reason_defaults_ibkf1`
    FOREIGN KEY (`deviceSwapReasonCategoryId`)
    REFERENCES `device_swap_reason_categories` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `device_swap_reason_defaults_ibkf3`
    FOREIGN KEY (`dealerId`)
    REFERENCES `dealers` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `device_instance_device_swap_reasons` (
    `hardwareOptimizationId` INT(11) NOT NULL,
    `deviceInstanceId`       INT(11) NOT NULL,
    `deviceSwapReasonId`     INT(11) NOT NULL,
    PRIMARY KEY (`hardwareOptimizationId`, `deviceInstanceId`),
    INDEX `device_instance_device_swap_reasons_ibkf1_idx` (`hardwareOptimizationId` ASC),
    INDEX `device_instance_device_swap_reasons_ibkf2_idx` (`deviceInstanceId` ASC),
    INDEX `device_instance_device_swap_reasons_ibkf3_idx` (`deviceSwapReasonId` ASC),
    CONSTRAINT `device_instance_device_swap_reasons_ibkf1`
    FOREIGN KEY (`hardwareOptimizationId`)
    REFERENCES `hardware_optimizations` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `device_instance_device_swap_reasons_ibkf2`
    FOREIGN KEY (`deviceInstanceId`)
    REFERENCES `device_instances` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT `device_instance_device_swap_reasons_ibkf3`
    FOREIGN KEY (`deviceSwapReasonId`)
    REFERENCES `device_swap_reasons` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute('CREATE TABLE IF NOT EXISTS `hardware_optimization_quotes` (
    `hardwareOptimizationId` INT(11) NOT NULL,
    `quoteId`                INT(11) NOT NULL,
    PRIMARY KEY (`hardwareOptimizationId`, `quoteId`),
    INDEX `hardware_optimization_quotes_ibk1_idx` (`hardwareOptimizationId` ASC),
    INDEX `hardware_optimization_quotes_ibk2_idx` (`quoteId` ASC),
    CONSTRAINT `hardware_optimization_quotes_ibk1`
    FOREIGN KEY (`hardwareOptimizationId`)
    REFERENCES `hardware_optimizations` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `hardware_optimization_quotes_ibk2`
    FOREIGN KEY (`quoteId`)
    REFERENCES `quotes` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("DROP TABLE IF EXISTS `hardware_optimization_quotes`");
        $this->execute("DROP TABLE IF EXISTS `device_instance_device_swap_reasons`");
        $this->execute("DROP TABLE IF EXISTS `device_swap_reason_defaults`");
        $this->execute("DROP TABLE IF EXISTS `device_swap_reasons`");
        $this->execute("DROP TABLE IF EXISTS `device_swap_reason_categories`");
        $this->execute("DROP TABLE IF EXISTS `privileges`");
        $this->execute("DROP TABLE IF EXISTS `device_swaps`");
        $this->execute("DROP TABLE IF EXISTS `user_settings`");
        $this->execute("DROP TABLE IF EXISTS `dealer_settings`");
        $this->execute("DROP TABLE IF EXISTS `rms_device_matchups`");
        $this->execute("DROP TABLE IF EXISTS `user_viewed_clients`");
        $this->execute("DROP TABLE IF EXISTS `dealer_toner_attributes`");
        $this->execute("DROP TABLE IF EXISTS `dealer_master_device_attributes`");
        $this->execute("DROP TABLE IF EXISTS `user_password_reset_requests`");
        $this->execute("DROP TABLE IF EXISTS `healthchecks`");
        $this->execute("DROP TABLE IF EXISTS `healthcheck_settings`");
        $this->execute("DROP TABLE IF EXISTS `user_sessions`");
        $this->execute("DROP TABLE IF EXISTS `assessment_surveys`");
        $this->execute("DROP TABLE IF EXISTS `device_instance_replacement_master_devices`");
        $this->execute("DROP TABLE IF EXISTS `hardware_optimizations`");
        $this->execute("DROP TABLE IF EXISTS `hardware_optimization_settings`");
        $this->execute("DROP TABLE IF EXISTS `rms_excluded_rows`");
        $this->execute("DROP TABLE IF EXISTS `device_instance_master_devices`");
        $this->execute("DROP TABLE IF EXISTS `regions`");
        $this->execute("DROP TABLE IF EXISTS `addresses`");
        $this->execute("DROP TABLE IF EXISTS `countries`");
        $this->execute("DROP TABLE IF EXISTS `contacts`");
        $this->execute("DROP TABLE IF EXISTS `quote_device_group_devices`");
        $this->execute("DROP TABLE IF EXISTS `quote_device_groups`");
        $this->execute("DROP TABLE IF EXISTS `quote_lease_terms`");
        $this->execute("DROP TABLE IF EXISTS `user_roles`");
        $this->execute("DROP TABLE IF EXISTS `sessions`");
        $this->execute("DROP TABLE IF EXISTS `roles`");
        $this->execute("DROP TABLE IF EXISTS `user_device_configurations`");
        $this->execute("DROP TABLE IF EXISTS `quote_settings`");
        $this->execute("DROP TABLE IF EXISTS `quote_device_configurations`");
        $this->execute("DROP TABLE IF EXISTS `quote_device_configuration_options`");
        $this->execute("DROP TABLE IF EXISTS `quote_device_options`");
        $this->execute("DROP TABLE IF EXISTS `quote_devices`");
        $this->execute("DROP TABLE IF EXISTS `quotes`");
        $this->execute("DROP TABLE IF EXISTS `option_categories`");
        $this->execute("DROP TABLE IF EXISTS `leasing_schema_rates`");
        $this->execute("DROP TABLE IF EXISTS `leasing_schema_terms`");
        $this->execute("DROP TABLE IF EXISTS `leasing_schema_ranges`");
        $this->execute("DROP TABLE IF EXISTS `global_leasing_schemas`");
        $this->execute("DROP TABLE IF EXISTS `leasing_schemas`");
        $this->execute("DROP TABLE IF EXISTS `global_device_configurations`");
        $this->execute("DROP TABLE IF EXISTS `device_options`");
        $this->execute("DROP TABLE IF EXISTS `device_configuration_options`");
        $this->execute("DROP TABLE IF EXISTS `options`");
        $this->execute("DROP TABLE IF EXISTS `device_configurations`");
        $this->execute("DROP TABLE IF EXISTS `devices`");
        $this->execute("DROP TABLE IF EXISTS `categories`");
        $this->execute("DROP TABLE IF EXISTS `rms_user_matchups`");
        $this->execute("DROP TABLE IF EXISTS `report_survey_settings`");
        $this->execute("DROP TABLE IF EXISTS `survey_settings`");
        $this->execute("DROP TABLE IF EXISTS `assessments`");
        $this->execute("DROP TABLE IF EXISTS `assessment_settings`");
        $this->execute("DROP TABLE IF EXISTS `replacement_devices`");
        $this->execute("DROP TABLE IF EXISTS `pricing_configs`");
        $this->execute("DROP TABLE IF EXISTS `rms_master_matchups`");
        $this->execute("DROP TABLE IF EXISTS `device_toners`");
        $this->execute("DROP TABLE IF EXISTS `master_devices`");
        $this->execute("DROP TABLE IF EXISTS `toner_configs`");
        $this->execute("DROP TABLE IF EXISTS `toners`");
        $this->execute("DROP TABLE IF EXISTS `toner_colors`");
        $this->execute("DROP TABLE IF EXISTS `part_types`");
        $this->execute("DROP TABLE IF EXISTS `device_instance_meters`");
        $this->execute("DROP TABLE IF EXISTS `device_instances`");
        $this->execute("DROP TABLE IF EXISTS `rms_upload_rows`");
        $this->execute("DROP TABLE IF EXISTS `rms_devices`");
        $this->execute("DROP TABLE IF EXISTS `users`");
        $this->execute("DROP TABLE IF EXISTS `rms_uploads`");
        $this->execute("DROP TABLE IF EXISTS `rms_providers`");
        $this->execute("DROP TABLE IF EXISTS `manufacturers`");
        $this->execute("DROP TABLE IF EXISTS `logs`");
        $this->execute("DROP TABLE IF EXISTS `log_types`");
        $this->execute("DROP TABLE IF EXISTS `clients`");
        $this->execute("DROP TABLE IF EXISTS `dealers`");
        $this->execute("DROP TABLE IF EXISTS `images`");
    }
}
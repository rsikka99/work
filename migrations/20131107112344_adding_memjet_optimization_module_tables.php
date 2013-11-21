<?php

use Phinx\Migration\AbstractMigration;

class AddingMemjetOptimizationModuleTables extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS `memjet_optimization_settings` (
            `id`                                INT NOT NULL AUTO_INCREMENT,
            `lossThreshold`                     DOUBLE NULL,
            `targetColorCostPerPage`            DOUBLE NULL,
            `targetMonochromeCostPerPage`       DOUBLE NULL,
            `adminCostPerPage`                  DOUBLE NULL,
            `laborCostPerPage`                  DOUBLE NULL,
            `partsCostPerPage`                  DOUBLE NULL,
            `pageCoverageMonochrome`            DOUBLE NULL,
            `pageCoverageColor`                 DOUBLE NULL,
            `blackToColorRatio`                DOUBLE NULL,
            `dealerMonochromeRankSetId`         INT(11),
            `dealerColorRankSetId`              INT(11),
            `replacementMonochromeRankSetId`    INT(11),
            `replacementColorRankSetId`         INT(11),
            PRIMARY KEY (`id`),
            INDEX `memjet_optimization_settings_ibfk_1_idx` (`dealerMonochromeRankSetId` ASC),
            INDEX `memjet_optimization_settings_ibfk_2_idx` (`dealerColorRankSetId` ASC),
            INDEX `memjet_optimization_settings_ibfk_3_idx` (`replacementMonochromeRankSetId` ASC),
            INDEX `memjet_optimization_settings_ibfk_4_idx` (`replacementColorRankSetId` ASC),
            CONSTRAINT `memjet_optimization_settings_ibfk_1` FOREIGN KEY (`dealerMonochromeRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE,
            CONSTRAINT `memjet_optimization_settings_ibfk_2` FOREIGN KEY (`dealerColorRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE,
            CONSTRAINT `memjet_optimization_settings_ibfk_3` FOREIGN KEY (`replacementMonochromeRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE,
            CONSTRAINT `memjet_optimization_settings_ibfk_4` FOREIGN KEY (`replacementColorRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE
            );
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS `memjet_optimizations` (
            `id`                            INT          NOT NULL AUTO_INCREMENT,
            `clientId`                      INT(11)      NOT NULL,
            `dealerId`                      INT          NOT NULL,
            `memjetOptimizationSettingId`   INT          NOT NULL,
            `dateCreated`                   DATETIME     NOT NULL,
            `lastModified`                  DATETIME     NOT NULL,
            `name`                          VARCHAR(255) NULL,
            `rmsUploadId`                   INT(11)      NULL,
            `stepName`                      VARCHAR(255) NULL,
            INDEX `memjet_optimization_ibfk_1_idx` (`clientId` ASC),
            INDEX `memjet_optimization_ibfk_2_idx` (`rmsUploadId` ASC),
            PRIMARY KEY (`id`),
            INDEX `memjet_optimization_ibfk_4_idx` (`dealerId` ASC),
            INDEX `memjet_optimization_ibfk_3_idx` (`memjetOptimizationSettingId` ASC),
            CONSTRAINT `memjet_optimization_ibfk_1`
            FOREIGN KEY (`clientId`)
            REFERENCES `clients` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            CONSTRAINT `memjet_optimization_ibfk_2`
            FOREIGN KEY (`rmsUploadId`)
            REFERENCES `rms_uploads` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
            CONSTRAINT `memjet_optimization_ibfk_3`
            FOREIGN KEY (`memjetOptimizationSettingId`)
            REFERENCES `memjet_optimization_settings` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            CONSTRAINT `memjet_optimization_ibfk_4`
            FOREIGN KEY (`dealerId`)
            REFERENCES `dealers` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE);
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS `memjet_device_instance_replacement_master_devices` (
            `deviceInstanceId`      INT(11) NOT NULL,
            `memjetOptimizationId`  INT(11) NOT NULL,
            `masterDeviceId`        INT(11) NOT NULL,
            INDEX `memjet_device_instance_replacement_master_devices_ibfk_1_idx` (`masterDeviceId` ASC),
            INDEX `memjet_device_instance_replacement_master_devices_ibfk_2_idx` (`deviceInstanceId` ASC),
            PRIMARY KEY (`deviceInstanceId`, `memjetOptimizationId`),
            UNIQUE INDEX `deviceInstanceId_UNIQUE` (`deviceInstanceId` ASC, `memjetOptimizationId` ASC),
            INDEX `memjet_device_instance_replacement_master_devices_ibfk_3_idx` (`memjetOptimizationId` ASC),
            CONSTRAINT `memjet_device_instance_replacement_master_devices_ibfk_1`
            FOREIGN KEY (`masterDeviceId`)
            REFERENCES `master_devices` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
            CONSTRAINT `memjet_device_instance_replacement_master_devices_ibfk_2`
            FOREIGN KEY (`deviceInstanceId`)
            REFERENCES `device_instances` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
            CONSTRAINT `memjet_device_instance_replacement_master_devices_ibfk_3`
            FOREIGN KEY (`memjetOptimizationId`)
            REFERENCES `memjet_optimizations` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE);
        ');


        $this->execute("ALTER TABLE dealer_settings
            ADD COLUMN memjetOptimizationSettingId INT NULL,
            ADD INDEX `dealer_settings_ibfk_7_idx` (`memjetOptimizationSettingId` ASC),
            ADD CONSTRAINT `dealer_settings_ibfk_7`
            FOREIGN KEY (`memjetOptimizationSettingId`)
            REFERENCES `memjet_optimization_settings` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
        ");

        $this->execute("ALTER TABLE user_settings
            ADD COLUMN memjetOptimizationSettingId INT NULL,
            ADD INDEX `user_settings_ibfk_7_idx` (`memjetOptimizationSettingId` ASC),
            ADD CONSTRAINT `user_settings_ibfk_7`
            FOREIGN KEY (`memjetOptimizationSettingId`)
            REFERENCES `memjet_optimization_settings` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
        ");

        $this->execute('CREATE TABLE IF NOT EXISTS `memjet_optimization_quotes` (
    `memjetOptimizationId` INT(11) NOT NULL,
    `quoteId`                INT(11) NOT NULL,
    PRIMARY KEY (`memjetOptimizationId`, `quoteId`),
    INDEX `memjet_optimization_quotes_ibk1_idx` (`memjetOptimizationId` ASC),
    INDEX `memjet_optimization_quotes_ibk2_idx` (`quoteId` ASC),
    CONSTRAINT `memjet_optimization_quotes_ibk1`
    FOREIGN KEY (`memjetOptimizationId`)
    REFERENCES `memjet_optimizations` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `memjet_optimization_quotes_ibk2`
    FOREIGN KEY (`quoteId`)
    REFERENCES `quotes` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');

        $this->execute("INSERT INTO `memjet_optimization_settings` (`id`, `pageCoverageMonochrome`,`pageCoverageColor`,`lossThreshold`,`adminCostPerPage`, `laborCostPerPage`, `partsCostPerPage`, `targetColorCostPerPage`, `targetMonochromeCostPerPage`, `blackToColorRatio`) VALUES
            (1,  6, 24, 20, 0.0006, 0.002, 0.0015, 0.10, 0.02, 15);
        ");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {

        $this->execute("ALTER TABLE user_settings
            DROP FOREIGN KEY user_settings_ibfk_7,
			DROP KEY user_settings_ibfk_7_idx,
			DROP COLUMN memjetOptimizationSettingId;
            ");

        $this->execute("ALTER TABLE dealer_settings
            DROP FOREIGN KEY dealer_settings_ibfk_7,
            DROP KEY `dealer_settings_ibfk_7_idx`,
            DROP COLUMN `memjetOptimizationSettingId`
            ");

        $this->execute("ALTER TABLE memjet_optimizations
        DROP FOREIGN KEY memjet_optimization_ibfk_1,
        DROP FOREIGN KEY memjet_optimization_ibfk_2,
        DROP FOREIGN KEY memjet_optimization_ibfk_3,
        DROP FOREIGN KEY memjet_optimization_ibfk_4,
        DROP KEY memjet_optimization_ibfk_1_idx,
        DROP KEY memjet_optimization_ibfk_2_idx,
        DROP KEY memjet_optimization_ibfk_3_idx,
        DROP KEY memjet_optimization_ibfk_4_idx
        ");

        $this->execute('DROP TABLE IF EXISTS `memjet_optimization_settings`');
        $this->execute('DROP TABLE IF EXISTS `memjet_device_instance_replacement_master_devices`');
        $this->execute('DROP TABLE IF EXISTS `memjet_optimization_quotes`');
        $this->execute('DROP TABLE IF EXISTS `memjet_optimizations`');
    }
}
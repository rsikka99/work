<?php

use Phinx\Migration\AbstractMigration;

class AddTonerVendors extends AbstractMigration
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
        $this->execute('CREATE  TABLE IF NOT EXISTS `toner_vendor_ranking_sets` (
                        `id` INT NOT NULL AUTO_INCREMENT ,
                        PRIMARY KEY (`id`) );
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS `toner_vendor_rankings` (
                        `tonerVendorRankingSetId` INT(11) NOT NULL,
                        `manufacturerId`          INT(11) NOT NULL,
                        `rank`                    INT(11) NOT NULL,
                        PRIMARY KEY (`tonerVendorRankingSetId`, `manufacturerId`),
                        INDEX `toner_vendor_ranking_ibkf1_idx` (`tonerVendorRankingSetId` ASC),
                        INDEX `toner_vendor_ranking_ibkf2_idx` (`manufacturerId` ASC),
                        CONSTRAINT `toner_vendor_ranking_ibkf1`
                        FOREIGN KEY (`tonerVendorRankingSetId`)
                        REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE,
                        CONSTRAINT `toner_vendor_ranking_ibkf2`
                        FOREIGN KEY (`manufacturerId`)
                        REFERENCES `manufacturers` (`id`)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE);
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS `toner_vendor_manufacturers` (
                        `manufacturerId` INT(11) NOT NULL,
                        PRIMARY KEY (`manufacturerId`),
                        UNIQUE INDEX `manufacturerId_UNIQUE` (`manufacturerId` ASC),
                        CONSTRAINT `toner_vendor_manufacturers`
                        FOREIGN KEY (`manufacturerId`)
                        REFERENCES `manufacturers` (`id`)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE);
        ');

        $this->execute("INSERT INTO `toner_vendor_manufacturers` VALUES
                        (3),
                        (6),
                        (8),
                        (9),
                        (29);
        ");

        /**
         * Hardware Optimization Settings
         */
        $this->execute('ALTER TABLE `hardware_optimization_settings`
                        DROP FOREIGN KEY `hardware_optimization_settings_ibfk_2`,
                        DROP FOREIGN KEY `hardware_optimization_settings_ibfk_3`,
                        DROP COLUMN `dealerPricingConfigId`,
                        DROP COLUMN `replacementPricingConfigId`;
        ');

        $this->execute('ALTER TABLE `hardware_optimization_settings`
                        ADD COLUMN dealerMonochromeRankSetId INT(11),
                        ADD CONSTRAINT `hardware_optimization_settings_ibfk_1` FOREIGN KEY (`dealerMonochromeRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE,
                        ADD COLUMN dealerColorRankSetId INT(11),
                        ADD CONSTRAINT `hardware_optimization_settings_ibfk_2` FOREIGN KEY (`dealerColorRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE,
                        ADD COLUMN replacementMonochromeRankSetId INT(11),
                        ADD CONSTRAINT `hardware_optimization_settings_ibfk_3` FOREIGN KEY (`replacementMonochromeRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE,
                        ADD COLUMN replacementColorRankSetId INT(11),
                        ADD CONSTRAINT `hardware_optimization_settings_ibfk_4` FOREIGN KEY (`replacementColorRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE;
        ');

        /**
         * Assessment Settings
         */
        $this->execute('ALTER TABLE `assessment_settings`
                        DROP FOREIGN KEY `assessment_settings_ibfk_1`,
                        DROP FOREIGN KEY `assessment_settings_ibfk_2`,
                        DROP FOREIGN KEY `assessment_settings_ibfk_3`,
                        DROP COLUMN `assessmentPricingConfigId`,
                        DROP COLUMN `grossMarginPricingConfigId`,
                        DROP COLUMN `replacementPricingConfigId`;
        ');

        $this->execute('ALTER TABLE `assessment_settings`

                        ADD COLUMN `dealerMonochromeRankSetId` INT(11),
                        ADD CONSTRAINT `assessment_settings_ibfk_1` FOREIGN KEY (`dealerMonochromeRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE,
                        ADD COLUMN `dealerColorRankSetId` INT(11),
                        ADD CONSTRAINT `assessment_settings_ibfk_2` FOREIGN KEY (`dealerColorRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE,
                        ADD COLUMN `customerMonochromeRankSetId` INT(11),
                        ADD CONSTRAINT `assessment_settings_ibfk_3` FOREIGN KEY (`customerMonochromeRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE,
                        ADD COLUMN `customerColorRankSetId` INT(11),
                        ADD CONSTRAINT `assessment_settings_ibfk_4` FOREIGN KEY (`customerColorRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE;
        ');

        /**
         * Quote Settings
         */
        $this->execute('ALTER TABLE `quote_settings`
                        DROP FOREIGN KEY `quote_settings_ibfk_1`,
                        DROP COLUMN `pricingConfigId`;
        ');

        $this->execute('ALTER TABLE `quote_settings`
                        ADD COLUMN `dealerMonochromeRankSetId` INT(11),
                        ADD CONSTRAINT `quote_settings_ibfk_1` FOREIGN KEY (`dealerMonochromeRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE,
                        ADD COLUMN `dealerColorRankSetId` INT(11),
                        ADD CONSTRAINT `quote_settings_ibfk_2` FOREIGN KEY (`dealerColorRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE;
        ');

        /**
         * Quote Devices
         */
        $this->execute('ALTER TABLE `quote_devices`
                        DROP COLUMN `compCostPerPageMonochrome`,
                        DROP COLUMN `compCostPerPageColor`,
                        CHANGE `oemCostPerPageMonochrome` `costPerPageMonochrome` DOUBLE NOT NULL,
                        CHANGE `oemCostPerPageColor` `costPerPageColor` DOUBLE NOT NULL
        ');

        /**
         * Quotes
         */
        $this->execute('ALTER TABLE  `quotes`
                        DROP FOREIGN KEY `quotes_ibfk_2`,
                        DROP COLUMN `pricingConfigId`');

        $this->execute('ALTER TABLE `quotes`
                        ADD COLUMN `dealerMonochromeRankSetId` INT(11),
                        ADD CONSTRAINT `quotes_ibfk_2` FOREIGN KEY (`dealerMonochromeRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE,
                        ADD COLUMN `dealerColorRankSetId` INT(11),
                        ADD CONSTRAINT `quotes_ibfk_3` FOREIGN KEY (`dealerColorRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE;
        ');

        /**
         * Health Check Settings
         */
        $this->execute('ALTER TABLE `healthcheck_settings`
                        DROP FOREIGN KEY `healthcheck_settings_ibfk_1`,
                        DROP COLUMN `healthcheckPricingConfigId`,
                        DROP COLUMN `actualPageCoverageMono`,
                        DROP COLUMN `actualPageCoverageColor`,
                        DROP COLUMN `costThreshold`;
        ');

        $this->execute('ALTER TABLE `healthcheck_settings`
                        ADD COLUMN `customerMonochromeRankSetId` INT(11),
                        ADD CONSTRAINT `healthcheck_settings_ibfk_1` FOREIGN KEY (`customerMonochromeRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE,
                        ADD COLUMN `customerColorRankSetId` INT(11),
                        ADD CONSTRAINT `healthcheck_settings_ibfk_2` FOREIGN KEY (`customerColorRankSetId`) REFERENCES `toner_vendor_ranking_sets` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE;
        ');

        $this->execute('CREATE PROCEDURE getCheapestTonersForDevice(IN inMasterDeviceId       INT(11), IN inDealerId INT(11), IN inMonochromeTonerPreference TEXT,
                                            IN inColorTonerPreference TEXT)
    BEGIN
        SET inMonochromeTonerPreference = IF(CHAR_LENGTH(inMonochromeTonerPreference) > 0, CONCAT(inMonochromeTonerPreference, \',\'),
                                             inMonochromeTonerPreference);
        SET inColorTonerPreference = IF(CHAR_LENGTH(inColorTonerPreference) > 0, CONCAT(inColorTonerPreference, \',\'), inColorTonerPreference);

        SELECT
            *
        FROM (
                 SELECT
                    device_toners.toner_id as id,
                    toners.sku,
                    toners.cost,
                    COALESCE(dealer_toner_attributes.cost, toners.cost) as calculatedCost,
                    toners.yield,
                    COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield AS costPerPage,
                    toners.manufacturerId,
                    IF(master_devices.manufacturerId = toners.manufacturerId, 1, 0)    AS isOem,
                    toners.tonerColorId
                 FROM device_toners
                     LEFT JOIN toners ON toners.id = device_toners.toner_id
                     LEFT JOIN master_devices ON master_devices.id = device_toners.master_device_id
                     LEFT JOIN dealer_toner_attributes
                         ON dealer_toner_attributes.tonerId = device_toners.toner_id AND dealer_toner_attributes.dealerId = inDealerId
                 WHERE device_toners.master_device_id = inMasterDeviceId AND toners.tonerColorId != 1
                       AND find_in_set(toners.manufacturerId, CONCAT(inColorTonerPreference, master_devices.manufacturerId))
                       AND COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield =
                           (SELECT
                                min(COALESCE(dta.cost, t.cost) / t.yield)
                            FROM device_toners
                                LEFT JOIN toners AS t
                                    ON t.id = device_toners.toner_id
                                LEFT JOIN
                                master_devices AS md
                                    ON md.id = device_toners.master_device_id
                                LEFT JOIN dealer_toner_attributes AS dta
                                    ON dta.tonerId = device_toners.toner_id AND dta.dealerId = 1
                            WHERE
                                toners.tonerColorId = t.tonerColorId AND
                                device_toners.master_device_id = inMasterDeviceId AND
                                t.manufacturerId = toners.manufacturerId)
                 ORDER BY find_in_set(toners.manufacturerId, CONCAT(inColorTonerPreference, master_devices.manufacturerId)),
                     COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield ASC
             ) AS selectStatement1
        GROUP BY selectStatement1.tonerColorId

        UNION

        SELECT
            *
        FROM (
                 SELECT
                     device_toners.toner_id as id,
                     toners.sku,
                     toners.cost,
                     COALESCE(dealer_toner_attributes.cost, toners.cost) as calculatedCost,
                     toners.yield,
                     COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield AS costPerPage,
                     toners.manufacturerId,
                     IF(master_devices.manufacturerId = toners.manufacturerId, 1, 0)    AS isOem,
                     toners.tonerColorId
                 FROM device_toners
                     LEFT JOIN toners ON toners.id = device_toners.toner_id
                     LEFT JOIN master_devices ON master_devices.id = device_toners.master_device_id
                     LEFT JOIN dealer_toner_attributes
                         ON dealer_toner_attributes.tonerId = device_toners.toner_id AND dealer_toner_attributes.dealerId = inDealerId
                 WHERE device_toners.master_device_id = inMasterDeviceId AND toners.tonerColorId = 1
                       AND find_in_set(toners.manufacturerId, CONCAT(inMonochromeTonerPreference, master_devices.manufacturerId))
                       AND COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield =
                           (SELECT
                                min(COALESCE(dta.cost, t.cost) / t.yield)
                            FROM device_toners
                                LEFT JOIN toners AS t
                                    ON t.id = device_toners.toner_id
                                LEFT JOIN
                                master_devices AS md
                                    ON md.id = device_toners.master_device_id
                                LEFT JOIN dealer_toner_attributes AS dta
                                    ON dta.tonerId = device_toners.toner_id AND dta.dealerId = inDealerId
                            WHERE
                                toners.tonerColorId = t.tonerColorId AND
                                device_toners.master_device_id = inMasterDeviceId AND
                                t.manufacturerId = toners.manufacturerId)
                 ORDER BY find_in_set(toners.manufacturerId, CONCAT(inMonochromeTonerPreference, master_devices.manufacturerId)),
                     COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield ASC
             ) AS selectStatement2
        GROUP BY selectStatement2.tonerColorId;

    END;
    ');


        $this->execute('DROP TABLE `pricing_configs`;');

    }

    /**
     * Migrate Down.
     */
    public function down ()
    {

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
                    );
        ');

        $this->execute('INSERT INTO `pricing_configs` (`id`, `name`, `color_toner_part_type_id`, `mono_toner_part_type_id`) VALUES
(1, \'USE DEFAULT\', NULL, NULL),
(2, \'OEM\', 1, 1),
(3, \'COMP\', 2, 2),
(4, \'OEM Mono, COMP Color\', 2, 1),
(5, \'OEM Color, COMP Mono\', 1, 2);
');
        $this->execute('DROP PROCEDURE getCheapestTonersForDevice;');

        /**
         * Hardware Optimization Settings
         */
        $this->execute('ALTER TABLE `hardware_optimization_settings`
                        DROP FOREIGN KEY `hardware_optimization_settings_ibfk_1`,
                        DROP FOREIGN KEY `hardware_optimization_settings_ibfk_2`,
                        DROP FOREIGN KEY `hardware_optimization_settings_ibfk_3`,
                        DROP FOREIGN KEY `hardware_optimization_settings_ibfk_4`,
                        DROP COLUMN `dealerMonochromeRankSetId`,
                        DROP COLUMN `dealerColorRankSetId`,
                        DROP COLUMN `replacementMonochromeRankSetId`,
                        DROP COLUMN `replacementColorRankSetId`;
        ');

        $this->execute('ALTER TABLE `hardware_optimization_settings`
                        ADD COLUMN `dealerPricingConfigId` INT(11),
                        ADD CONSTRAINT `hardware_optimization_settings_ibfk_2` FOREIGN KEY (`dealerPricingConfigId`) REFERENCES `pricing_configs` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE,
                        ADD COLUMN `replacementPricingConfigId` INT(11),
                        ADD CONSTRAINT `hardware_optimization_settings_ibfk_3` FOREIGN KEY (`replacementPricingConfigId`) REFERENCES `pricing_configs` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE;
        ');


        /**
         * Assessment Settings
         */
        $this->execute('ALTER TABLE `assessment_settings`
                        DROP FOREIGN KEY `assessment_settings_ibfk_1`,
                        DROP FOREIGN KEY `assessment_settings_ibfk_2`,
                        DROP FOREIGN KEY `assessment_settings_ibfk_3`,
                        DROP FOREIGN KEY `assessment_settings_ibfk_4`,
                        DROP COLUMN `dealerMonochromeRankSetId`,
                        DROP COLUMN `dealerColorRankSetId`,
                        DROP COLUMN `customerMonochromeRankSetId`,
                        DROP COLUMN `customerColorRankSetId`;
        ');

        $this->execute('ALTER TABLE `assessment_settings`
                        ADD COLUMN `assessmentPricingConfigId` INT(11),
                        ADD CONSTRAINT `assessment_settings_ibfk_1` FOREIGN KEY (`assessmentPricingConfigId`) REFERENCES `pricing_configs` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE,
                        ADD COLUMN `grossMarginPricingConfigId` INT(11),
                        ADD CONSTRAINT `assessment_settings_ibfk_2` FOREIGN KEY (`grossMarginPricingConfigId`) REFERENCES `pricing_configs` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE,
                        ADD COLUMN `replacementPricingConfigId` INT(11),
                        ADD CONSTRAINT `assessment_settings_ibfk_3` FOREIGN KEY (`replacementPricingConfigId`) REFERENCES `pricing_configs` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE;
        ');

        /**
         * Quotes
         */
        $this->execute('ALTER TABLE `quotes`
                        DROP FOREIGN KEY `quotes_ibfk_2`,
                        DROP FOREIGN KEY `quotes_ibfk_3`,
                        DROP COLUMN `dealerMonochromeRankSetId`,
                        DROP COLUMN `dealerColorRankSetId`;
        ');

        $this->execute('ALTER TABLE  `quotes`
                        ADD COLUMN `pricingConfigId` INT(11),
                        ADD CONSTRAINT `quotes_ibfk_2` FOREIGN KEY (`pricingConfigId`) REFERENCES `pricing_configs` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE;
        ');

        /**
         * Quote Devices
         */
        $this->execute('ALTER TABLE `quote_devices`
                        ADD COLUMN `compCostPerPageMonochrome` DOUBLE NOT NULL,
                        ADD COLUMN `compCostPerPageColor` DOUBLE NOT NULL,
                        CHANGE `costPerPageMonochrome` `oemCostPerPageMonochrome` DOUBLE NOT NULL,
                        CHANGE `costPerPageColor` `oemCostPerPageColor` DOUBLE NOT NULL
        ');

        /**
         * Quote Settings
         */
        $this->execute('ALTER TABLE `quote_settings`
                        DROP FOREIGN KEY `quote_settings_ibfk_1`,
                        DROP FOREIGN KEY `quote_settings_ibfk_2`,
                        DROP COLUMN `dealerMonochromeRankSetId`,
                        DROP COLUMN `dealerColorRankSetId`;
        ');
        $this->execute('ALTER TABLE `quote_settings`
                        ADD COLUMN `pricingConfigId` INT(11),
                        ADD CONSTRAINT `quote_settings_ibfk_1` FOREIGN KEY (`pricingConfigId`) REFERENCES `pricing_configs` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE;
        ');

        /**
         * Health Check Settings
         */
        $this->execute('ALTER TABLE `healthcheck_settings`
                        DROP FOREIGN KEY `healthcheck_settings_ibfk_1`,
                        DROP FOREIGN KEY `healthcheck_settings_ibfk_2`,
                        DROP COLUMN `customerMonochromeRankSetId`,
                        DROP COLUMN `customerColorRankSetId`;
        ');
        $this->execute('ALTER TABLE `healthcheck_settings`
                        ADD COLUMN `actualPageCoverageMono` DOUBLE NULL DEFAULT NULL,
                        ADD COLUMN `actualPageCoverageColor` DOUBLE NULL DEFAULT NULL,
                        ADD COLUMN `costThreshold` DOUBLE NULL DEFAULT NULL,
                        ADD COLUMN `healthcheckPricingConfigId` INT(11),
                        ADD CONSTRAINT `healthcheck_settings_ibfk_1` FOREIGN KEY (`healthcheckPricingConfigId`) REFERENCES `pricing_configs` (`id`)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE;
        ');

        $this->execute('DROP TABLE IF EXISTS `toner_vendor_manufacturers`');
        $this->execute('DROP TABLE IF EXISTS `toner_vendor_rankings`');
        $this->execute('DROP TABLE IF EXISTS `toner_vendor_ranking_sets`');
    }
}
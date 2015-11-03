<?php

use Phinx\Migration\AbstractMigration;

class RmsUpdate extends AbstractMigration
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
    public function up()
    {
        $this->execute("drop TABLE IF EXISTS `rms_update`");
        $this->execute("CREATE TABLE IF NOT EXISTS `rms_update` (
  `clientId` int(20) NOT NULL,
  `assetId` varchar(255) DEFAULT NULL,
  `ipAddress` varchar(255) NOT NULL DEFAULT '',
  `serialNumber` varchar(255) NOT NULL DEFAULT '',
  `location` varchar(255) DEFAULT NULL,
  `masterDeviceId` int(11) DEFAULT NULL,
  `rmsProviderId` int(11) NOT NULL,
  `isColor` tinyint(4) DEFAULT '0',
  `isCopier` tinyint(4) DEFAULT '0',
  `isFax` tinyint(4) DEFAULT '0',
  `isLeased` tinyint(4) DEFAULT '0',
  `isDuplex` tinyint(4) DEFAULT '0',
  `isA3` tinyint(4) DEFAULT '0',
  `reportsTonerLevels` tinyint(4) DEFAULT '0',
  `launchDate` datetime DEFAULT NULL,
  `ppmBlack` double DEFAULT NULL,
  `ppmColor` double DEFAULT NULL,
  `wattsPowerNormal` double DEFAULT NULL,
  `wattsPowerIdle` double DEFAULT NULL,
  `tonerLevelBlack` varchar(255) DEFAULT NULL,
  `tonerLevelCyan` varchar(255) DEFAULT NULL,
  `tonerLevelMagenta` varchar(255) DEFAULT NULL,
  `tonerLevelYellow` varchar(255) DEFAULT NULL,
  `rawDeviceName` varchar(255) DEFAULT NULL,
  `pageCoverageMonochrome` double DEFAULT NULL,
  `pageCoverageCyan` double DEFAULT NULL,
  `pageCoverageMagenta` double DEFAULT NULL,
  `pageCoverageYellow` double DEFAULT NULL,
  `pageCoverageColor` decimal(10,0) DEFAULT NULL,
  `mpsDiscoveryDate` datetime DEFAULT NULL,
  `isManaged` tinyint(4) DEFAULT NULL,
  `monitorStartDate` datetime DEFAULT NULL,
  `monitorEndDate` datetime DEFAULT NULL,
  `startMeterBlack` int(11) DEFAULT NULL,
  `endMeterBlack` int(11) DEFAULT NULL,
  `startMeterColor` int(11) DEFAULT NULL,
  `endMeterColor` int(11) DEFAULT NULL,
  `startMeterPrintBlack` int(11) DEFAULT NULL,
  `endMeterPrintBlack` int(11) DEFAULT NULL,
  `startMeterPrintColor` int(11) DEFAULT NULL,
  `endMeterPrintColor` int(11) DEFAULT NULL,
  `startMeterCopyBlack` int(11) DEFAULT NULL,
  `endMeterCopyBlack` int(11) DEFAULT NULL,
  `startMeterCopyColor` int(11) DEFAULT NULL,
  `endMeterCopyColor` int(11) DEFAULT NULL,
  `startMeterFax` int(11) DEFAULT NULL,
  `endMeterFax` int(11) DEFAULT NULL,
  `startMeterScan` int(11) DEFAULT NULL,
  `endMeterScan` int(11) DEFAULT NULL,
  `startMeterPrintA3Black` int(11) DEFAULT NULL,
  `endMeterPrintA3Black` int(11) DEFAULT NULL,
  `startMeterPrintA3Color` int(11) DEFAULT NULL,
  `endMeterPrintA3Color` int(11) DEFAULT NULL,
  `startMeterLife` int(11) DEFAULT NULL,
  `endMeterLife` int(11) DEFAULT NULL,
  PRIMARY KEY (`clientId`,`assetId`,`ipAddress`,`serialNumber`),
  KEY `rms_upload_rows_ibfk_1_idx` (`rmsProviderId`),
  KEY `rms_update_ibfk_2` (`masterDeviceId`)
) ENGINE=InnoDB");
        $this->execute("
ALTER TABLE `rms_update`
  ADD CONSTRAINT `rms_update_ibfk_1` FOREIGN KEY (`rmsProviderId`) REFERENCES `rms_providers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rms_update_ibfk_2` FOREIGN KEY (`masterDeviceId`) REFERENCES `master_devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->execute("drop TABLE IF EXISTS `device_needs_toner`");

        $this->execute("CREATE TABLE IF NOT EXISTS `device_needs_toner` (
  `color` int(11) NOT NULL,
  `clientId` int(11) NOT NULL,
  `assetId` varchar(255) NOT NULL,
  `ipAddress` varchar(255) NOT NULL,
  `serialNumber` varchar(255) NOT NULL,
  `toner` int(11) NOT NULL DEFAULT '0',
  `masterDeviceId` int(11) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `firstReported` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tonerLevel` int(11) DEFAULT NULL,
  `daysLeft` int(11) DEFAULT NULL,
  `tonerOptions` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`color`,`clientId`,`assetId`,`ipAddress`,`serialNumber`),
  KEY `toner` (`toner`),
  KEY `masterDeviceId` (`masterDeviceId`),
  KEY `device_needs_toner_ibfk_1` (`clientId`)
) ENGINE=InnoDB;");

        $this->execute("ALTER TABLE `device_needs_toner`
  ADD CONSTRAINT `device_needs_toner_ibfk_1` FOREIGN KEY (`color`) REFERENCES `toner_colors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `device_needs_toner_ibfk_2` FOREIGN KEY (`clientId`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `device_needs_toner_ibfk_3` FOREIGN KEY (`masterDeviceId`) REFERENCES `master_devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `device_needs_toner_ibfk_4` FOREIGN KEY (`toner`) REFERENCES `toners` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

        $this->execute("alter table dealer_branding add `dealerEmail` varchar(255) null default null");
    }
    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("drop TABLE IF EXISTS `rms_update`");
        $this->execute("drop TABLE IF EXISTS `device_needs_toner`");
        $this->execute("alter table dealer_branding drop `dealerEmail`");
    }
}
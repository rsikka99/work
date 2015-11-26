<?php

use Phinx\Migration\AbstractMigration;

class Ecommerce extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("update features set id='ecommerce', name='E-commerce' where id='healthcheck_printiq'");

        $this->execute(
"alter table shop_settings add emailFromName varchar(255) null default null,
  add emailFromAddress varchar(255) null default null,
  add supplyNotifySubject varchar(255) null default null,
  add supplyNotifyMessage text null default null");

        $this->execute('alter table clients add priceLevel varchar(255) null default null');
        $this->execute('alter table clients add transactionType varchar(255) null default null');

        $this->execute('drop table if EXISTS `rms_device_instances`');
        $this->execute('
CREATE TABLE IF NOT EXISTS `rms_device_instances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clientId` int(11) NOT NULL,
  `ipAddress` varchar(50) NOT NULL,
  `serialNumber` varchar(255) NOT NULL,
  `assetId` varchar(255) NOT NULL,
  `masterDeviceId` int(11) DEFAULT NULL,
  `rawDeviceName` VARCHAR( 255 ) NULL,
  `fullDeviceName` VARCHAR( 255 ) NULL,
  `manufacturer` varchar(255) DEFAULT NULL,
  `modelName` varchar(255) DEFAULT NULL,
  `location` VARCHAR( 255 ) NULL,
  `reportDate` DATE NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientId_2` (`clientId`,`ipAddress`,`serialNumber`,`assetId`),
  KEY `masterDeviceId` (`masterDeviceId`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB');

        $this->execute('ALTER TABLE `rms_device_instances`
  ADD CONSTRAINT `rms_device_instances_ibfk_2` FOREIGN KEY (`masterDeviceId`) REFERENCES `master_devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rms_device_instances_ibfk_1` FOREIGN KEY (`clientId`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->execute("replace into rms_device_instances (reportDate, clientId,ipAddress,serialNumber,assetId,masterDeviceId,location,rawDeviceName,fullDeviceName,modelName,manufacturer)
select monitorEndDate, clientId,ipAddress,serialNumber,di.assetId, dimd.masterDeviceId, di.location, COALESCE(ur.rawDeviceName,ur.rawDeviceName,ur.fullDeviceName), ur.fullDeviceName, ur.modelName, ur.manufacturer
from device_instances di
  left join device_instance_master_devices dimd on di.id=dimd.deviceInstanceId
  left join rms_uploads u on di.rmsUploadId=u.id
  left join device_instance_meters mt on di.id=mt.deviceInstanceId
  left join master_devices md on dimd.masterDeviceId=md.id
  left join manufacturers m on md.manufacturerId=m.id
  left join rms_upload_rows ur on di.rmsUploadRowId=ur.id");

        $this->execute('truncate rms_realtime');
        $this->execute('ALTER TABLE `rms_realtime` drop `clientId`, drop `ipAddress`, drop `serialNumber`, drop `assetId`, drop `masterDeviceId`, drop `rawDeviceName`, drop `modelName`, drop `manufacturer`, drop `fullDeviceName`, drop `location`, DROP PRIMARY KEY');
        $this->execute('ALTER TABLE `rms_realtime` ADD `rmsDeviceInstanceId` INT NOT NULL FIRST, ADD INDEX ( `rmsDeviceInstanceId` )');
        $this->execute('alter table rms_realtime ADD PRIMARY KEY ( `rmsDeviceInstanceId` , `scanDate` )');
        $this->execute('ALTER TABLE `rms_realtime` ADD FOREIGN KEY (`rmsDeviceInstanceId`) REFERENCES `rms_device_instances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->execute('truncate rms_update');
        $this->execute('ALTER TABLE `rms_update` drop `clientId`, drop `ipAddress`, drop `serialNumber`, drop `assetId`, drop `masterDeviceId`, drop `rawDeviceName`, drop `location`, DROP PRIMARY KEY');
        $this->execute('ALTER TABLE `rms_update` ADD `rmsDeviceInstanceId` INT NOT NULL FIRST, ADD INDEX ( `rmsDeviceInstanceId` )');
        $this->execute('alter table rms_update ADD PRIMARY KEY ( `rmsDeviceInstanceId`)');
        $this->execute('ALTER TABLE `rms_update` ADD FOREIGN KEY ( `rmsDeviceInstanceId` ) REFERENCES `rms_device_instances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->execute('truncate device_needs_toner');
        $this->execute('ALTER TABLE `device_needs_toner` ADD `rmsDeviceInstanceId` INT NOT NULL FIRST, ADD INDEX ( `rmsDeviceInstanceId` )');
        $this->execute('ALTER TABLE `device_needs_toner` ADD FOREIGN KEY ( `rmsDeviceInstanceId` ) REFERENCES `rms_device_instances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->execute('ALTER TABLE `device_instances` ADD `rmsDeviceInstanceId` INT NULL AFTER `id`, ADD INDEX ( `rmsDeviceInstanceId` )');
        $this->execute('ALTER TABLE `device_instances` ADD FOREIGN KEY ( `rmsDeviceInstanceId` ) REFERENCES `rms_device_instances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->execute('update device_instances di set rmsDeviceInstanceId=(select id from rms_device_instances where clientId=(select clientId from rms_uploads where id=di.rmsUploadId) and ipAddress=di.ipAddress and serialNumber=di.serialNumber and (di.assetId is null or assetId=di.assetId) limit 1)');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute(
            "alter table device_instances DROP FOREIGN KEY `device_instances_ibfk_3`, drop rmsDeviceInstanceId"
        );

        $this->execute(
"alter table shop_settings drop emailFromName, drop emailFromAddress, drop supplyNotifySubject, drop supplyNotifyMessage"
        );

        $this->execute('alter table clients drop priceLevel');
        $this->execute('alter table clients drop transactionType');

        $this->execute('drop table if exists `rms_realtime`');
        $this->execute('
CREATE TABLE IF NOT EXISTS `rms_realtime` (
  `scanDate` date DEFAULT NULL,
  `clientId` int(11) NOT NULL,
  `assetId` varchar(255) NOT NULL,
  `ipAddress` varchar(255) NOT NULL,
  `serialNumber` varchar(255) NOT NULL,
  `rawDeviceName` varchar(255) DEFAULT NULL,
  `fullDeviceName` varchar(255) DEFAULT NULL,
  `manufacturer` varchar(255) DEFAULT NULL,
  `modelName` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `masterDeviceId` int(11) DEFAULT NULL,
  `rmsProviderId` int(11) DEFAULT NULL,
  `lifeCount` int(11) DEFAULT NULL,
  `lifeCountBlack` int(11) DEFAULT NULL,
  `lifeCountColor` int(11) DEFAULT NULL,
  `copyCountBlack` int(11) DEFAULT NULL,
  `copyCountColor` int(11) DEFAULT NULL,
  `printCountBlack` int(11) DEFAULT NULL,
  `printCountColor` int(11) DEFAULT NULL,
  `scanCount` int(11) DEFAULT NULL,
  `faxCount` int(11) DEFAULT NULL,
  `tonerLevelBlack` int(11) DEFAULT NULL,
  `tonerLevelCyan` int(11) DEFAULT NULL,
  `tonerLevelMagenta` int(11) DEFAULT NULL,
  `tonerLevelYellow` int(11) DEFAULT NULL,
  PRIMARY KEY (`scanDate`,`clientId`,`assetId`,`ipAddress`,`serialNumber`),
  KEY `masterDeviceId` (`masterDeviceId`),
  KEY `rmsProviderId` (`rmsProviderId`)
) ENGINE=InnoDB');

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

        $this->execute('ALTER TABLE `device_needs_toner` DROP FOREIGN KEY `device_needs_toner_ibfk_5`');
        $this->execute('ALTER TABLE `device_needs_toner` drop `rmsDeviceInstanceId`');

        $this->execute('drop table if EXISTS `rms_device_instances`');

    }
}
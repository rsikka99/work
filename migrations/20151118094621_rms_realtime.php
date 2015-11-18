<?php

use Phinx\Migration\AbstractMigration;

class RmsRealtime extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
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

        $this->execute(
'ALTER TABLE `rms_realtime` ADD FOREIGN KEY ( `clientId` ) REFERENCES `mpstoolbox_v2`.`clients` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->execute(
'ALTER TABLE `rms_realtime` ADD FOREIGN KEY ( `masterDeviceId` ) REFERENCES `mpstoolbox_v2`.`master_devices` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->execute(
'ALTER TABLE `rms_realtime` ADD FOREIGN KEY ( `rmsProviderId` ) REFERENCES `mpstoolbox_v2`.`rms_providers` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('drop table if exists `rms_realtime`');
    }
}
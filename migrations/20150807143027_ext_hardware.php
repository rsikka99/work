<?php

use Phinx\Migration\AbstractMigration;

class ExtHardware extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute(
"
drop table if EXISTS ext_hardware;
CREATE TABLE IF NOT EXISTS `ext_hardware` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NULL,
  `modelName` varchar(255) NOT NULL,
  `dateCreated` timestamp NOT NULL default current_timestamp,
  `dateUpdated` timestamp NULL,
  `launchDate` date DEFAULT NULL,
  `manufacturerId` int(11) NOT NULL,
  `userId` int(11) DEFAULT '1',
  `isSystemDevice` tinyint(1) NOT NULL DEFAULT '0',
  `imageFile` varchar(255) NULL,
  `imageUrl` varchar(255) NULL,
  `hardware_type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`manufacturerId`),
  KEY (`userId`)
) ENGINE=InnoDB;

ALTER TABLE `ext_hardware`
  ADD CONSTRAINT FOREIGN KEY (`manufacturerId`) REFERENCES `manufacturers` (`id`) ON DELETE restrict ON UPDATE CASCADE,
  ADD CONSTRAINT FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

drop table if exists `ext_dealer_hardware`;
create table if not exists `ext_dealer_hardware` (
  `id` int(11) not null,
  `dealerId` int(11) NOT NULL,
  `cost` decimal(10,2),
  `dealerSku` varchar(255),
  `oemSku` varchar(255),
  `description` varchar(255),
  `srp` decimal(10,2),
  `rent` decimal(10,2),
  `webId` bigint,
  `dataSheetUrl` varchar(255),
  `reviewsUrl` varchar(255),
  `online` tinyint,
  `onlineDescription` text,
  PRIMARY KEY (`id`, `dealerId`)
) ENGINE=InnoDB;

ALTER TABLE `ext_dealer_hardware`
  ADD CONSTRAINT FOREIGN KEY (`id`) REFERENCES `ext_hardware` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT FOREIGN KEY (`dealerId`) REFERENCES `dealers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

drop table if exists `ext_computer`;
create table if not exists `ext_computer` (
  `id` int(11) not null auto_increment,
  `webcam` tinyint NULL DEFAULT '0',
  `mediaDrive` tinyint NULL DEFAULT '0',
  `usb3` tinyint NULL DEFAULT '0',
  `usbDescription` varchar(255) NULL,
  `os` varchar(255) NULL,
  `ram` int NULL DEFAULT '0',
  `hdd` int NULL DEFAULT '0',
  `screenSize` float NULL DEFAULT '0',
  `hdDisplay` tinyint NULL DEFAULT '0',
  `ledDisplay` tinyint NULL DEFAULT '0',
  `weight` float NULL DEFAULT '0',
  `processorName` varchar(255) NULL,
  `processorSpeed` float NULL DEFAULT '0',
  `service` varchar(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

ALTER TABLE `ext_computer`
  ADD CONSTRAINT FOREIGN KEY (`id`) REFERENCES `ext_hardware` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

"
        );
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('
drop table ext_computer;
drop table ext_dealer_hardware;
drop table ext_hardware;
');
    }
}
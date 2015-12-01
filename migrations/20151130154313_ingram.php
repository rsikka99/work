<?php

use Phinx\Migration\AbstractMigration;

class Ingram extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('DROP TABLE IF EXISTS `ingram_products`');
        $this->execute("
CREATE TABLE IF NOT EXISTS `ingram_products` (
  `ingram_part_number` char(12) NOT NULL DEFAULT '',
  `vendor_number` char(4) DEFAULT NULL,
  `vendor_name` char(35) DEFAULT NULL,
  `ingram_part_description_line_1` char(31) DEFAULT NULL,
  `ingram_part_description_line_2` char(35) DEFAULT NULL,
  `retail_price` decimal(16,2) DEFAULT NULL,
  `vendor_part_number` char(20) DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `upc_code` char(13) DEFAULT NULL,
  `length` float DEFAULT NULL,
  `width` float DEFAULT NULL,
  `height` float DEFAULT NULL,
  `availability_flag` char(1) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `cpu_code` char(6) DEFAULT NULL,
  `media_type` char(4) DEFAULT NULL,
  `ingram_micro_category` char(4) DEFAULT NULL,
  `new_item_receipt_flag` char(1) DEFAULT NULL,
  `substitute_part_number` char(12) DEFAULT NULL,
  `tonerId` int(11) DEFAULT NULL,
  `masterDeviceId` int(11) DEFAULT NULL,
  `computerId` int(11) DEFAULT NULL,
  `peripheralId` int(11) DEFAULT NULL,
  PRIMARY KEY (`ingram_part_number`),
  KEY `masterDeviceId` (`masterDeviceId`),
  KEY `computerId` (`computerId`),
  KEY `peripheralId` (`peripheralId`),
  KEY `tonerId` (`tonerId`)
) ENGINE=InnoDB");

        $this->execute('DROP TABLE IF EXISTS `ingram_prices`');
        $this->execute('
CREATE TABLE IF NOT EXISTS `ingram_prices` (
  `dealerId` int(11) NOT NULL,
  `ingram_part_number` char(12) NOT NULL,
  `customer_price` decimal(16,2) DEFAULT NULL,
  `special_price_flag` char(1) DEFAULT NULL,
  `dt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dealerId`,`ingram_part_number`),
  KEY `ingram_part_number` (`ingram_part_number`)
) ENGINE=InnoDB');

        $this->execute('ALTER TABLE `ingram_prices`
  ADD CONSTRAINT `ingram_prices_ibfk_5` FOREIGN KEY (`dealerId`) REFERENCES `dealers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ingram_prices_ibfk_6` FOREIGN KEY (`ingram_part_number`) REFERENCES `ingram_products` (`ingram_part_number`) ON DELETE CASCADE ON UPDATE CASCADE;');

        $this->execute('ALTER TABLE `clients` ADD `ecomMonochromeRank` VARCHAR( 255 ) NULL, ADD `ecomColorRank` VARCHAR( 255 ) NULL, add `templateNum` tinyint not null default 1');

        $this->execute('ALTER TABLE `shop_settings` ADD `supplyNotifySubject2` VARCHAR( 255 ) NULL ,
ADD `supplyNotifyMessage2` TEXT NULL ,
ADD `supplyNotifySubject3` VARCHAR( 255 ) NULL ,
ADD `supplyNotifyMessage3` TEXT NULL ');

        $this->execute('CREATE TABLE IF NOT EXISTS `dealer_price_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dealerId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `margin` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dealerId` (`dealerId`)
) ENGINE=InnoDB');
        $this->execute('ALTER TABLE `dealer_price_levels` ADD CONSTRAINT `dealer_price_levels_ibfk_1` FOREIGN KEY (`dealerId`) REFERENCES `dealers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->execute('ALTER TABLE `clients` CHANGE `priceLevel` `priceLevelId` INT NULL DEFAULT NULL');
        $this->execute('ALTER TABLE `clients` ADD INDEX ( `priceLevelId` )');
        $this->execute('update `clients` set priceLevelId=null');
        $this->execute('ALTER TABLE `clients` ADD FOREIGN KEY ( `priceLevelId` ) REFERENCES `mpstoolbox_dev`.`dealer_price_levels` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE');

        $this->execute('alter table dealer_toner_attributes drop dealerSrp');
        $this->execute('ALTER TABLE `dealer_toner_attributes` ADD `level6` DECIMAL( 10, 2 ) NULL ,
ADD `level7` DECIMAL( 10, 2 ) NULL ,
ADD `level8` DECIMAL( 10, 2 ) NULL ,
ADD `level9` DECIMAL( 10, 2 ) NULL ,
ADD `distributor` VARCHAR( 255 ) NULL ');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('DROP TABLE IF EXISTS `ingram_prices`');
        $this->execute('DROP TABLE IF EXISTS `ingram_products`');
        $this->execute('ALTER TABLE `clients` drop `ecomMonochromeRank`, drop `ecomColorRank`, drop `templateNum`');

        $this->execute('ALTER TABLE `shop_settings` drop `supplyNotifySubject2`, drop `supplyNotifyMessage2`, drop `supplyNotifySubject3`, drop `supplyNotifyMessage3`');

        $this->execute('drop table `dealer_price_levels`');

        $this->execute('alter table dealer_toner_attributes add dealerSrp float null default null');
        $this->execute('alter table dealer_toner_attributes drop level6, drop level7, drop level8, drop level9, drop distributor');
    }
}
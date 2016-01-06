<?php

use Phinx\Migration\AbstractMigration;

class MasterDeviceServiceUpdate extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('DROP TABLE IF EXISTS `master_device_service`');

        $this->execute('
CREATE TABLE IF NOT EXISTS `master_device_service` (
  `id` int not null auto_increment,
  `masterDeviceId` int NOT NULL,
  `vpn` char(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `masterDeviceId` (`masterDeviceId`),
  CONSTRAINT `ingram_service_ibfk_1` FOREIGN KEY (`masterDeviceId`) REFERENCES `master_devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB');

        $this->execute('ALTER TABLE `ingram_products` ADD INDEX ( `vendor_part_number` ) ');
        $this->execute('ALTER TABLE `synnex_products` ADD INDEX ( `Manufacturer_Part` ) ');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
    }
}
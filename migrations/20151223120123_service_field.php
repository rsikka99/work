<?php

use Phinx\Migration\AbstractMigration;

class ServiceField extends AbstractMigration
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
  `supplier` int not null,
  `ingram_part_number` char(12) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ingram_part_number` (`ingram_part_number`),
  KEY `supplier` (`supplier`),
  KEY `masterDeviceId` (`masterDeviceId`)
) ENGINE=InnoDB');

        $this->execute('
ALTER TABLE `master_device_service`
  ADD CONSTRAINT `ingram_service_ibfk_1` FOREIGN KEY (`masterDeviceId`) REFERENCES `master_devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ingram_service_ibfk_2` FOREIGN KEY (`supplier`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ingram_service_ibfk_3` FOREIGN KEY (`ingram_part_number`) REFERENCES `ingram_products` (`ingram_part_number`) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('DROP TABLE IF EXISTS `master_device_service`');
    }
}
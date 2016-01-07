<?php

use Phinx\Migration\AbstractMigration;

class HardwareService extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('DROP TABLE IF EXISTS `ext_hardware_service`');

        $this->execute('
CREATE TABLE IF NOT EXISTS `ext_hardware_service` (
  `id` int not null auto_increment,
  `hardwareId` int NOT NULL,
  `vpn` char(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hardwareId` (`hardwareId`),
  CONSTRAINT FOREIGN KEY (`hardwareId`) REFERENCES `ext_hardware` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('DROP TABLE IF EXISTS `ext_hardware_service`');
    }
}
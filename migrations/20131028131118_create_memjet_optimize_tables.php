<?php

use Phinx\Migration\AbstractMigration;

class CreateMemjetOptimizeTables extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS `device_swaps_memjet` (
                            `masterDeviceId`   INT NOT NULL,
                            `minimumPageCount` INT NULL,
                            `maximumPageCount` INT NULL,
                            PRIMARY KEY (`masterDeviceId`),
                            INDEX `device_swaps_memjet_ibfk1_idx` (`masterDeviceId` ASC),
                            CONSTRAINT `device_swaps_memjet_ibfk1`
                            FOREIGN KEY (`masterDeviceId`)
                            REFERENCES `master_devices` (`id`)
                                ON DELETE CASCADE
                                ON UPDATE CASCADE
                    );');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("DROP TABLE `device_swaps_memjet");
    }
}
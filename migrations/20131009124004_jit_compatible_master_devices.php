<?php

use Phinx\Migration\AbstractMigration;

class JitCompatibleMasterDevices extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("
                        CREATE TABLE IF NOT EXISTS `jit_compatible_master_devices` (
                    `masterDeviceId`   INT    NOT NULL,
                    `dealerId`         INT    NOT NULL,
                    PRIMARY KEY (`masterDeviceId`, `dealerId`),
                    INDEX `jit_compatible_master_devices_ibfk_1_idx` (`dealerId` ASC),
                    INDEX `jit_compatible_master_devices_ibfk_2_idx` (`masterDeviceId` ASC),
                    CONSTRAINT `jit_compatible_master_devices`
                    FOREIGN KEY (`dealerId`)
                    REFERENCES `dealers` (`id`)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE,
                    CONSTRAINT `jit_compatible_master_devices_ibfk_2`
                    FOREIGN KEY (`masterDeviceId`)
                    REFERENCES `master_devices` (`id`)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE);
        ");

        $this->execute("ALTER TABLE device_instances
        ADD COLUMN `compatibleWithJitProgram` TINYINT
        ");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("DROP TABLE jit_compatible_master_devices");
        $this->execute("ALTER TABLE device_instances DROP COLUMN compatibleWithJitProgram");
    }
}
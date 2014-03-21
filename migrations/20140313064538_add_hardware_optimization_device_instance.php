<?php

use Phinx\Migration\AbstractMigration;

class AddHardwareOptimizationDeviceInstance extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        try
        {
            $this->getAdapter()->beginTransaction();

            // Your Migration Here
            $this->execute('CREATE TABLE `hardware_optimization_device_instances` (
    `deviceInstanceId`          INT NOT NULL,
    `hardwareOptimizationId`    INT NOT NULL,
    `action`                    ENUM(\'Keep\', \'Replace\', \'Retire\', \'Do Not Repair\') DEFAULT \'Keep\',
    `masterDeviceId` INT,
    `deviceSwapReasonId`        INT,
    PRIMARY KEY (`deviceInstanceId`, `hardwareOptimizationId`),
    CONSTRAINT `hardware_optimization_device_instances_ibfk_1`
    FOREIGN KEY (`deviceInstanceId`) REFERENCES `device_instances` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `hardware_optimization_device_instances_ibfk_2`
    FOREIGN KEY (`hardwareOptimizationId`) REFERENCES `hardware_optimizations` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `hardware_optimization_device_instances_ibfk_3`
    FOREIGN KEY (`masterDeviceId`) REFERENCES `master_devices` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `hardware_optimization_device_instances_ibfk_4`
    FOREIGN KEY (`deviceSwapReasonId`) REFERENCES `device_swap_reasons` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);');

            $this->execute('ALTER TABLE `device_instances` DROP COLUMN `deviceSwapReasonId`;');

            // Time to commit!
            $this->getAdapter()->commitTransaction();
        }
        catch (Exception $e)
        {
            $this->getAdapter()->rollbackTransaction();

            throw $e;
        }
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        try
        {
            $this->getAdapter()->beginTransaction();

            // Your Migration Here
            $this->execute('DROP TABLE `hardware_optimization_device_instances`;');

            $this->execute('ALTER TABLE `device_instances` ADD COLUMN `deviceSwapReasonId` INT DEFAULT NULL;');

            // Time to commit!
            $this->getAdapter()->commitTransaction();
        }
        catch (Exception $e)
        {
            $this->getAdapter()->rollbackTransaction();

            throw $e;
        }
    }
}
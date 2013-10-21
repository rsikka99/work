<?php

use Phinx\Migration\AbstractMigration;

class AddIsLeasedToDeviceInstances extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE device_instances ADD COLUMN `isLeased` TINYINT(4)   NOT NULL DEFAULT 0");
        $this->execute("UPDATE device_instances SET isLeased=
                            (
                                SELECT isLeased FROM master_devices
                                LEFT JOIN device_instance_master_devices ON device_instance_master_devices.masterDeviceId = master_devices.id
                                WHERE device_instances.id = device_instance_master_devices.deviceInstanceId
                            )
        ");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("ALTER TABLE device_instances DROP COLUMN `isLeased`");
    }
}
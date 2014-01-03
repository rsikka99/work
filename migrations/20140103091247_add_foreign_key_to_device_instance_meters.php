<?php

use Phinx\Migration\AbstractMigration;

class AddForeignKeyToDeviceInstanceMeters extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("SET foreign_key_checks = 0; ALTER TABLE device_instance_meters ADD CONSTRAINT `device_instance_meter_ibfk_1` FOREIGN KEY (`deviceInstanceId`) REFERENCES device_instances(id); SET foreign_key_checks = 1; ");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("SET foreign_key_checks = 0; ALTER TABLE device_instance_meters DROP FOREIGN KEY `device_instance_meter_ibfk_1`; SET foreign_key_checks = 1; ");
    }
}
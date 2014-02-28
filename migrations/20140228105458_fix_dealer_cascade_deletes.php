<?php

use Phinx\Migration\AbstractMigration;

class FixDealerCascadeDeletes extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE device_instance_meters DROP FOREIGN KEY device_instance_meter_ibfk_1");
        $this->execute("ALTER TABLE device_instance_meters ADD CONSTRAINT device_instance_meter_ibfk_1
                        FOREIGN KEY (`deviceInstanceId`)
                        REFERENCES `device_instances` (`id`)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
                    ");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        // Don't do anything due to the nature of this migration
    }
}
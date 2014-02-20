<?php

use Phinx\Migration\AbstractMigration;

class FixManufacturerCascadeDelete extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE device_swaps DROP FOREIGN KEY device_swaps_ibkf1");
        $this->execute("ALTER TABLE device_swaps ADD CONSTRAINT device_swaps_ibkf1
                        FOREIGN KEY (`masterDeviceId`)
                        REFERENCES `master_devices` (`id`)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
                    ");

        $this->execute("ALTER TABLE device_instance_replacement_master_devices DROP FOREIGN KEY device_instance_replacement_master_devices_ibfk_1");
        $this->execute("ALTER TABLE device_instance_replacement_master_devices ADD CONSTRAINT device_instance_replacement_master_devices_ibfk_1
                        FOREIGN KEY (`masterDeviceId`)
                        REFERENCES `master_devices` (`id`)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
                    ");

        $this->execute("ALTER TABLE memjet_device_instance_replacement_master_devices DROP FOREIGN KEY memjet_device_instance_replacement_master_devices_ibfk_1");
        $this->execute("ALTER TABLE memjet_device_instance_replacement_master_devices ADD CONSTRAINT memjet_device_instance_replacement_master_devices_ibfk_1
                        FOREIGN KEY (`masterDeviceId`)
                        REFERENCES `master_devices` (`id`)
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
<?php

use Phinx\Migration\AbstractMigration;

class FixDeleteClients extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
     * public function change()
     * {
     * }
     */

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE device_instance_device_swap_reasons DROP FOREIGN KEY device_instance_device_swap_reasons_ibkf1");
        $this->execute("ALTER TABLE device_instance_device_swap_reasons ADD CONSTRAINT device_instance_device_swap_reasons_ibkf1
                        FOREIGN KEY (`hardwareOptimizationId`)
                        REFERENCES `hardware_optimizations` (`id`)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
                    ");

        $this->execute("ALTER TABLE memjet_device_instance_device_swap_reasons DROP FOREIGN KEY memjet_device_instance_device_swap_reasons_ibkf1");
        $this->execute("ALTER TABLE memjet_device_instance_device_swap_reasons ADD CONSTRAINT memjet_device_instance_device_swap_reasons_ibkf1
                        FOREIGN KEY (`memjetOptimizationId`)
                        REFERENCES `memjet_optimizations` (`id`)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
                    ");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        // No Down function needed due to the nature of the up migration
    }
}
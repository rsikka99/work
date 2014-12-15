<?php

use Phinx\Migration\AbstractMigration;

class NewDeviceReplacementAction extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE hardware_optimization_device_instances CHANGE action action ENUM('Keep', 'Replace', 'Retire', 'Do Not Repair', 'Upgrade') DEFAULT 'Keep';");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("ALTER TABLE hardware_optimization_device_instances CHANGE action action ENUM('Keep', 'Replace', 'Retire', 'Do Not Repair') DEFAULT 'Keep';");
    }
}
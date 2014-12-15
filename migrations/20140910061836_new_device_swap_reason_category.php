<?php

use Phinx\Migration\AbstractMigration;

class NewDeviceSwapReasonCategory extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("INSERT INTO device_swap_reason_categories VALUES (3, 'Device has been upgraded');");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("DELETE FROM device_swap_reason_categories WHERE id=3;");
    }
}
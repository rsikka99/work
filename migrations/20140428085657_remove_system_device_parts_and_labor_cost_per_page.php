<?php

use Phinx\Migration\AbstractMigration;

class RemoveSystemDevicePartsAndLaborCostPerPage extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('ALTER TABLE master_devices
            DROP COLUMN partsCostPerPage,
            DROP COLUMN laborCostPerPage;
        ');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('ALTER TABLE master_devices
            ADD COLUMN partsCostPerPage DECIMAL DEFAULT NULL,
            ADD COLUMN laborCostPerPage DECIMAL DEFAULT NULL;
        ');
    }
}
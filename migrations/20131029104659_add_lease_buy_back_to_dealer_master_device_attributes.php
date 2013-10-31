<?php

use Phinx\Migration\AbstractMigration;

class AddLeaseBuyBackToDealerMasterDeviceAttributes extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("ALTER TABLE dealer_master_device_attributes ADD COLUMN leaseBuybackPrice DOUBLE NULL");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("ALTER TABLE dealer_master_device_attributes DROP COLUMN leaseBuybackPrice");
    }
}
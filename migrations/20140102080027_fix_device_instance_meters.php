<?php

use Phinx\Migration\AbstractMigration;

class FixDeviceInstanceMeters extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("UPDATE device_instance_meters SET endMeterLife=IFNULL(endMeterBlack+endMeterColor,IFNULL(endMeterBlack,endMeterColor))  WHERE endMeterLife IS NULL;");
        $this->execute("UPDATE device_instance_meters SET startMeterLife=IFNULL(startMeterBlack+startMeterColor,IFNULL(startMeterBlack,startMeterColor))  WHERE startMeterLife IS NULL;");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {

    }
}
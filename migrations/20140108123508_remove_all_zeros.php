<?php

use Phinx\Migration\AbstractMigration;

class RemoveAllZeros extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("UPDATE master_devices SET wattsPowerNormal = 650 WHERE wattsPowerNormal = 0");
        $this->execute("UPDATE master_devices SET wattsPowerIdle = 16 WHERE wattsPowerNormal = 0");
        $this->execute("UPDATE master_devices SET ppmBlack = NULL WHERE ppmBlack = 0");
        $this->execute("UPDATE master_devices SET ppmColor = NULL WHERE ppmColor = 0");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        // Do not need to undo due to the nature of the up changes.
    }
}
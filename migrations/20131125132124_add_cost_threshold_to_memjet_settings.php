<?php

use Phinx\Migration\AbstractMigration;

class AddCostThresholdToMemjetSettings extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE memjet_optimization_settings ADD COLUMN `costThreshold` DOUBLE NULL");

        $this->execute("UPDATE memjet_optimization_settings SET `costThreshold` = 10 where id = 1");

        $this->execute("UPDATE memjet_optimization_settings JOIN dealer_settings ON dealer_settings.memjetOptimizationSettingId=memjet_optimization_settings.id  SET memjet_optimization_settings.`costThreshold` = 10");

    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("ALTER TABLE memjet_optimization_settings DROP COLUMN `costThreshold`");
    }
}
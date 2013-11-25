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

        $this->execute("UPDATE memjet_optimization_settings SET `costThreshold`=10 where id = 1");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("ALTER TABLE memjet_optimization_settings DROP COLUMN `costThreshold`");
    }
}
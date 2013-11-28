<?php

use Phinx\Migration\AbstractMigration;

class AddNameToFeatures extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE features ADD COLUMN `name` VARCHAR(255) NOT NULL;");

        $this->execute("UPDATE features set name='Memjet Optimization' where id='hardware_optimization_memjet'");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("ALTER TABLE features DROP COLUMN `name`");
    }
}
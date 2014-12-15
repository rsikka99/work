<?php

use Phinx\Migration\AbstractMigration;

class AddPrintTracker extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('INSERT INTO `rms_providers` (`id`, `name`) VALUES
                (7, \'Print Tracker\');');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('DELETE FROM `rms_providers` WHERE id=7;');
    }
}
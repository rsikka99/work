<?php

use Phinx\Migration\AbstractMigration;

class AddedPrintFleetThree extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('INSERT INTO `rms_providers` (`id`, `name`) VALUES
                (6, \'PrintFleet 3.x\');');

        $this->execute('UPDATE `rms_providers` SET name = \'PrintFleet 2.x\' WHERE id = 1');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('DELETE FROM `rms_providers` WHERE id=6;');

        $this->execute('UPDATE `rms_providers` SET name = \'PrintFleet\' WHERE id = 1');
    }
}
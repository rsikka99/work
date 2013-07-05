<?php

use Phinx\Migration\AbstractMigration;

class RemovePartType extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('ALTER TABLE `toners`
                        DROP FOREIGN KEY `toners_ibfk_1`,
                        DROP COLUMN `partTypeId`;
        ');

        $this->execute('DROP TABLE `part_types`');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
    }
}
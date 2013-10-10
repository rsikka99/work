<?php

use Phinx\Migration\AbstractMigration;

class AddA3Support extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('ALTER TABLE `master_devices`
                        ADD COLUMN `isA3` TINYINT(4) NOT NULL DEFAULT 0;');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('ALTER TABLE `master_devices`
                        DROP COLUMN `isA3`;');
    }
}
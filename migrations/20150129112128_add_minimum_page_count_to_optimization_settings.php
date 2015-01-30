<?php

use Phinx\Migration\AbstractMigration;

class AddMinimumPageCountToOptimizationSettings extends AbstractMigration
{
    /**
     * Migrate Up.
     */

    public function up ()
    {
        $this->execute("ALTER TABLE `optimization_settings` ADD COLUMN `minimumPageCount` INT(11)");

    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("ALTER TABLE `optimization_settings` DROP COLUMN `minimumPageCount`");
    }
}
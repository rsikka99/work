<?php

use Phinx\Migration\AbstractMigration;

class RemoveDeviceInstanceJitFlag extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('ALTER TABLE `device_instances`
                        DROP COLUMN `compatibleWithJitProgram`;');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('ALTER TABLE `device_instances`
                        ADD COLUMN `compatibleWithJitProgram` TINYINT(1) DEFAULT 0;');
    }
}
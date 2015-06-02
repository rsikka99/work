<?php

use Phinx\Migration\AbstractMigration;

class Jira573v2 extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('master_devices');
        $table
            ->addColumn('isUSB', 'boolean', ['default'=>0])
            ->addColumn('isWired', 'boolean', ['default'=>0])
            ->addColumn('isWireless', 'boolean', ['default'=>0])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('master_devices');
        $table
            ->removeColumn('isUSB', 'boolean', ['default'=>0])
            ->removeColumn('isWired', 'boolean', ['default'=>0])
            ->removeColumn('isWireless', 'boolean', ['default'=>0])
            ->save();
    }
}
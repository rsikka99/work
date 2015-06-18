<?php

use Phinx\Migration\AbstractMigration;

class Jira573 extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('master_devices');
        $table
            ->addColumn('isSmartphone', 'boolean', ['default'=>0])
            ->addColumn('additionalTrays', 'boolean', ['default'=>0])
            ->addColumn('isPIN', 'boolean', ['default'=>0])
            ->addColumn('isAccessCard', 'boolean', ['default'=>0])
            ->addColumn('isWalkup', 'boolean', ['default'=>0])
            ->addColumn('isStapling', 'boolean', ['default'=>0])
            ->addColumn('isBinding', 'boolean', ['default'=>0])
            ->addColumn('isTouchscreen', 'boolean', ['default'=>0])
            ->addColumn('isADF', 'boolean', ['default'=>0])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('master_devices');
        $table
            ->removeColumn('isSmartphone', 'boolean', ['default'=>0])
            ->removeColumn('additionalTrays', 'boolean', ['default'=>0])
            ->removeColumn('isPIN', 'boolean', ['default'=>0])
            ->removeColumn('isAccessCard', 'boolean', ['default'=>0])
            ->removeColumn('isWalkup', 'boolean', ['default'=>0])
            ->removeColumn('isStapling', 'boolean', ['default'=>0])
            ->removeColumn('isBinding', 'boolean', ['default'=>0])
            ->removeColumn('isTouchscreen', 'boolean', ['default'=>0])
            ->removeColumn('isADF', 'boolean', ['default'=>0])
            ->save();
    }
}
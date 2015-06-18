<?php

use Phinx\Migration\AbstractMigration;

class Jira587 extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('devices');
        $table
            ->addColumn('srp', 'float', ['null'=>true])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('devices');
        $table
            ->removeColumn('srp')
            ->save();
    }
}

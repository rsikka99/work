<?php

use Phinx\Migration\AbstractMigration;

class Jira587v5 extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('devices');
        $table
            ->addColumn('online', 'boolean', ['null'=>true])
            ->addColumn('onlineDescription', 'text', ['null'=>true])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('devices');
        $table
            ->removeColumn('online')
            ->removeColumn('onlineDescription')
            ->save();

    }
}

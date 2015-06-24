<?php

use Phinx\Migration\AbstractMigration;

class Jira587v3 extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('devices');
        $table
            ->addColumn('webId', 'integer', ['null'=>true])
            ->save();

        $table = $this->table('dealer_toner_attributes');
        $table
            ->addColumn('webId', 'integer', ['null'=>true])
            ->save();

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('devices');
        $table
            ->removeColumn('webId')
            ->save();

        $table = $this->table('dealer_toner_attributes');
        $table
            ->removeColumn('webId')
            ->save();
    }
}

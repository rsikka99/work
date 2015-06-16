<?php

use Phinx\Migration\AbstractMigration;

class Jira587v2 extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('dealer_toner_attributes');
        $table
            ->addColumn('dealerSrp', 'float', ['null'=>true])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('dealer_toner_attributes');
        $table
            ->removeColumn('dealerSrp')
            ->save();
    }
}

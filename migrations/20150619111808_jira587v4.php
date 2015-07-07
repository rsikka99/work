<?php

use Phinx\Migration\AbstractMigration;

class Jira587v4 extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('devices');
        $table
            ->addColumn('rent', 'float', ['null'=>true])
            ->addColumn('pagesPerMonth', 'integer', ['null'=>true])
            ->addColumn('dataSheetUrl', 'string', ['null'=>true])
            ->addColumn('reviewsUrl', 'string', ['null'=>true])
            ->save();

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('devices');
        $table
            ->removeColumn('rent')
            ->removeColumn('pagesPerMonth')
            ->removeColumn('dataSheetUrl')
            ->removeColumn('reviewsUrl')
            ->save();

    }
}

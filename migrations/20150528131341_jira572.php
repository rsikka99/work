<?php

use Phinx\Migration\AbstractMigration;

class Jira572 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('toners');
        $table
            ->addColumn('name', 'string', ['null'=>true])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('toners');
        $table
            ->removeColumn('name', 'string')
            ->save();
    }
}

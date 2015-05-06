<?php

use Phinx\Migration\AbstractMigration;

class Jira550 extends AbstractMigration
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
        $table = $this->table('master_devices');
        $table
            ->addColumn('imageFile', 'string')
            ->addColumn('imageUrl', 'string')
            ->save();
        $table = $this->table('toners');
        $table
            ->addColumn('imageFile', 'string')
            ->addColumn('imageUrl', 'string')
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('master_devices');
        $table
            ->removeColumn('imageFile', 'string')
            ->removeColumn('imageUrl', 'string')
            ->save();
        $table = $this->table('toners');
        $table
            ->removeColumn('imageFile', 'string')
            ->removeColumn('imageUrl', 'string')
            ->save();
    }
}
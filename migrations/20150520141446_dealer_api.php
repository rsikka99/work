<?php

use Phinx\Migration\AbstractMigration;

class DealerApi extends AbstractMigration
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
        $table = $this->table('dealers');
        $table
            ->addColumn('api_key', 'string', array('null' => true))
            ->addColumn('api_secret', 'string', array('null' => true))
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('dealers');
        $table
            ->removeColumn('api_key')
            ->removeColumn('api_secret')
            ->save();
    }
}

<?php

use Phinx\Migration\AbstractMigration;

class Jira544 extends AbstractMigration
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
        $table = $this->table('dealer_settings');
        $table
            ->addColumn('quoteValid', 'integer', array('default' => '7'))
            ->addColumn('quoteCustom', 'string')
            ->save();

        $this->execute('update dealer_settings set quoteValid=30 where dealerId=13');
        $this->execute("update dealer_settings set quoteCustom='albuquerque' where dealerId=13");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('dealer_settings');
        $table
            ->removeColumn('quoteValid', 'int')
            ->removeColumn('quoteCustom', 'string')
            ->save();
    }
}
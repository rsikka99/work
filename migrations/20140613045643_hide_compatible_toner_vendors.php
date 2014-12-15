<?php

use Phinx\Migration\AbstractMigration;

class HideCompatibleTonerVendors extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $dealerTonerVendors = $this->table('dealer_toner_vendors', array('id' => false, 'primary_key' => array('dealerId', 'manufacturerId')));
        $dealerTonerVendors
            ->addColumn('dealerId', 'integer')
            ->addColumn('manufacturerId', 'integer')
            ->addForeignKey(array('dealerId'), 'dealers', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addForeignKey(array('manufacturerId'), 'toner_vendor_manufacturers', 'manufacturerId', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->dropTable('dealer_toner_vendors');
    }
}
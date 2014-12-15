<?php

use Phinx\Migration\AbstractMigration;

class AddDealerRmsProviders extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $dealerRmsProviders = $this->table('dealer_rms_providers', array('id' => false, 'primary_key' => array('dealerId', 'rmsProviderId')));
        $dealerRmsProviders
            ->addColumn('dealerId', 'integer')
            ->addForeignKey(array('dealerId'), 'dealers', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addColumn('rmsProviderId', 'integer')
            ->addForeignKey(array('rmsProviderId'), 'rms_providers', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'));

        $dealerRmsProviders->save();
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->dropTable('dealer_rms_providers');
    }
}
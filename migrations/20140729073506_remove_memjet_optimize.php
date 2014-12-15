<?php

use Phinx\Migration\AbstractMigration;

class RemoveMemjetOptimize extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->dropTable('device_swaps_memjet');
        $this->dropTable('memjet_device_instance_device_swap_reasons');
        $this->dropTable('memjet_device_instance_replacement_master_devices');
        $this->dropTable('memjet_device_swap_reason_defaults');
        $this->dropTable('memjet_optimization_quotes');
        $this->dropTable('memjet_device_swap_reasons');
        $this->dropTable('memjet_device_swap_reason_categories');
        $this->dropTable('memjet_optimizations');
        $this->dropTable('memjet_device_swaps_page_thresholds');


    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $device_swaps_memjet = $this->table('device_swaps_memjet', array('id' => false, 'primary_key' => array('masterDeviceId')));
        $device_swaps_memjet
            ->addColumn('masterDeviceId', 'integer')
            ->addForeignKey(array('masterDeviceId'), 'master_devices', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addColumn('minimumPageCount', 'integer', array('null' => true))
            ->addColumn('maximumPageCount', 'integer', array('null' => true))
            ->save();

        $memjet_optimizations = $this->table('memjet_optimizations');
        $memjet_optimizations
            ->addColumn('clientId', 'integer')
            ->addForeignKey(array('clientId'), 'clients', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addColumn('dealerId', 'integer')
            ->addForeignKey(array('dealerId'), 'dealers', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addColumn('dateCreated', 'datetime')
            ->addColumn('lastModified', 'datetime')
            ->addColumn('name', 'string', array('null' => true))
            ->addColumn('rmsUploadId', 'integer', array('null' => true))
            ->addForeignKey(array('rmsUploadId'), 'rms_uploads', 'id', array('delete' => 'SET_NULL', 'update' => 'CASCADE'))
            ->addColumn('stepName', 'string', array('null' => true))
            ->save();

        $memjet_device_instance_device_swap_reasons = $this->table('memjet_device_instance_device_swap_reasons', array('id' => false, 'primary_key' => array('memjetOptimizationId', 'deviceInstanceId')));
        $memjet_device_instance_device_swap_reasons
            ->addColumn('memjetOptimizationId', 'integer')
            ->addForeignKey(array('memjetOptimizationId'), 'memjet_optimizations', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addColumn('deviceInstanceId', 'integer')
            ->addForeignKey(array('deviceInstanceId'), 'device_instances', 'id', array('delete' => 'NO_ACTION', 'update' => 'NO_ACTION'))
            ->addColumn('deviceSwapReasonId', 'integer')
            ->save();

        $memjet_device_instance_replacement_master_devices = $this->table('memjet_device_instance_replacement_master_devices', array('id' => false, 'primary_key' => array('deviceInstanceId', 'memjetOptimizationId')));
        $memjet_device_instance_replacement_master_devices
            ->addColumn('deviceInstanceId', 'integer')
            ->addForeignKey(array('deviceInstanceId'), 'device_instances', 'id', array('delete' => 'NO_ACTION', 'update' => 'NO_ACTION'))
            ->addColumn('memjetOptimizationId', 'integer')
            ->addForeignKey(array('memjetOptimizationId'), 'memjet_optimizations', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addColumn('masterDeviceId', 'integer')
            ->addForeignKey(array('masterDeviceId'), 'master_devices', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->save();

        $memjet_device_swaps_page_thresholds = $this->table('memjet_device_swaps_page_thresholds', array('id' => false, 'primary_key' => array('masterDeviceId', 'dealerId')));
        $memjet_device_swaps_page_thresholds
            ->addColumn('masterDeviceId', 'integer')
            ->addForeignKey(array('masterDeviceId'), 'master_devices', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addColumn('dealerId', 'integer')
            ->addForeignKey(array('dealerId'), 'dealers', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addColumn('minimumPageCount', 'integer', array('null' => true))
            ->addColumn('maximumPageCount', 'integer', array('null' => true))
            ->save();

        $memjet_device_swap_reason_categories = $this->table('memjet_device_swap_reason_categories');
        $memjet_device_swap_reason_categories
            ->addColumn('name', 'string')
            ->save();

        $memjet_device_swap_reasons = $this->table('memjet_device_swap_reasons');
        $memjet_device_swap_reasons
            ->addColumn('dealerId', 'integer')
            ->addForeignKey(array('dealerId'), 'dealers', 'id', array('delete' => 'NO_ACTION', 'update' => 'NO_ACTION'))
            ->addColumn('deviceSwapReasonCategoryId', 'integer')
            ->addForeignKey(array('deviceSwapReasonCategoryId'), 'memjet_device_swap_reason_categories', 'id', array('delete' => 'NO_ACTION', 'update' => 'NO_ACTION'))
            ->addColumn('reason', 'string')
            ->save();

        $memjet_device_swap_reason_defaults = $this->table('memjet_device_swap_reason_defaults', array('id' => false, 'primary_key' => array('deviceSwapReasonCategoryId', 'dealerId')));
        $memjet_device_swap_reason_defaults
            ->addColumn('deviceSwapReasonCategoryId', 'integer')
            ->addForeignKey(array('deviceSwapReasonCategoryId'), 'memjet_device_swap_reason_categories', 'id', array('delete' => 'NO_ACTION', 'update' => 'NO_ACTION'))
            ->addColumn('dealerId', 'integer')
            ->addForeignKey(array('dealerId'), 'dealers', 'id', array('delete' => 'NO_ACTION', 'update' => 'NO_ACTION'))
            ->addColumn('deviceSwapReasonId', 'integer')
            ->addForeignKey(array('deviceSwapReasonId'), 'memjet_device_swap_reasons', 'id', array('delete' => 'NO_ACTION', 'update' => 'NO_ACTION'))
            ->save();

        $memjet_optimization_quotes = $this->table('memjet_optimization_quotes', array('id' => false, 'primary_key' => array('memjetOptimizationId', 'quoteId')));
        $memjet_optimization_quotes
            ->addColumn('memjetOptimizationId', 'integer')
            ->addForeignKey(array('memjetOptimizationId'), 'memjet_optimizations', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addColumn('quoteId', 'integer')
            ->addForeignKey(array('quoteId'), 'quotes', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->save();

    }
}
<?php

class Proposalgen_Model_DbTable_DeviceToner extends Zend_Db_Table_Abstract
{
    protected $_name = 'proposalgenerator_device_toners';
    protected $_primary = array (
            'toner_id', 
            'master_device_id' 
    );
    protected $_referenceMap = array (
            'MasterDevice' => array (
                    'columns' => 'master_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice', 
                    'refColumns' => 'id' 
            ), 
            'Toner' => array (
                    'columns' => 'toner_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Toner', 
                    'refColumns' => 'id' 
            ) 
    );
}
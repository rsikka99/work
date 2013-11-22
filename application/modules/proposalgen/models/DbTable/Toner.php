<?php

/**
 * Class Proposalgen_Model_DbTable_Toner
 */
class Proposalgen_Model_DbTable_Toner extends Zend_Db_Table_Abstract
{
    protected $_name = 'toners';
    protected $_primary = 'id';
    protected $_referenceMap = array(
        'TonerColor'   => array(
            'columns'       => 'toner_color_id',
            'refTableClass' => 'Proposalgen_Model_DbTable_TonerColor',
            'refColumns'    => 'id'
        ),
        'MasterDevice' => array(
            'columns'       => 'master_device_id',
            'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice',
            'refColumns'    => 'id'
        )
    );
}
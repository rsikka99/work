<?php

class Proposalgen_Model_DbTable_UploadDataCollectorRow extends Zend_Db_Table_Abstract
{
    protected $_name = 'upload_data_collector_rows';
    protected $_primary = 'id';
    protected $_referenceMap = array (
            'Reports' => array (
                    'columns' => 'report_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Reports', 
                    'refColumns' => 'id' 
            ) 
    );
}
<?php

class Proposalgen_Model_DbTable_UploadDataCollector extends Zend_Db_Table_Abstract
{
    protected $_name = 'upload_data_collector';
    protected $_primary = array (
            'upload_data_collector_id' 
    );
    protected $_referenceMap = array (
            'Reports' => array (
                    'columns' => 'report_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Reports', 
                    'refColumns' => 'report_id' 
            ) 
    );
}

?>

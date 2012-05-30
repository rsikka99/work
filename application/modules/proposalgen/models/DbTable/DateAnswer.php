<?php

class Proposalgen_Model_DbTable_DateAnswer extends Zend_Db_Table_Abstract
{
    protected $_name = 'proposalgenerator_date_answers';
    protected $_primary = 'id';
    protected $_referenceMap = array (
            'Reports' => array (
                    'columns' => 'report_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Report', 
                    'refColumns' => 'id' 
            ), 
            
            'Questions' => array (
                    'columns' => 'question_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Question', 
                    'refColumns' => 'id' 
            ) 
    );
}
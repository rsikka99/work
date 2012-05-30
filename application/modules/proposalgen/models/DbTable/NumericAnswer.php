<?php

class Proposalgen_Model_DbTable_NumericAnswer extends Zend_Db_Table_Abstract
{
    protected $_name = 'proposalgenerator_numeric_answers';
    protected $_primary = 'answer_numeric_id';
    protected $_referenceMap = array (
            'Reports' => array (
                    'columns' => 'report_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Reports', 
                    'refColumns' => 'id' 
            ), 
            
            'Questions' => array (
                    'columns' => 'question_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Questions', 
                    'refColumns' => 'id' 
            ) 
    );
}
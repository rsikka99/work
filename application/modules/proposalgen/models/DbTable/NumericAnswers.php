<?php

class Proposalgen_Model_DbTable_NumericAnswers extends Zend_Db_Table_Abstract
{
    protected $_name = 'answers_numeric';
    protected $_primary = 'answer_numeric_id';
    
    protected $_referenceMap = array (
            'Reports' => array (
                    'columns' => 'report_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Reports', 
                    'refColumns' => 'report_id' 
            ), 
            
            'Questions' => array (
                    'columns' => 'question_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Questions', 
                    'refColumns' => 'question_id' 
            ) 
    );
}
?>

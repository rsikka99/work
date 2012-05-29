<?php

class Proposalgen_Model_DbTable_DateAnswers extends Zend_Db_Table_Abstract
{
    protected $_name = 'answers_dates';
    protected $_primary = 'answer_date_id';
    
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

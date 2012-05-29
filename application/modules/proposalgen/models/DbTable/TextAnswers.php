<?php

class Proposalgen_Model_DbTable_TextAnswers extends Zend_Db_Table_Abstract
{
    protected $_name = 'answers_textual';
    protected $_primary = 'answer_textual_id';
    
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

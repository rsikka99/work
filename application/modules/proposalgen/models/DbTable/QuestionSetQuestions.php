<?php

class Proposalgen_Model_DbTable_QuestionSetQuestions extends Zend_Db_Table_Abstract
{
    protected $_name = 'questionset_questions';
    protected $_primary = array (
            'question_id', 
            'questionset_id' 
    );
    
    protected $_referenceMap = array (
            'Questions' => array (
                    'columns' => 'question_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Questions', 
                    'refColumns' => 'question_id' 
            ), 
            'QuestionSet' => array (
                    'columns' => 'questionset_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_QuestionSet', 
                    'refColumns' => 'questionset_id' 
            ) 
    );
}

?>

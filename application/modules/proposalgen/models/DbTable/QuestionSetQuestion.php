<?php

class Proposalgen_Model_DbTable_QuestionSetQuestion extends Zend_Db_Table_Abstract
{
    protected $_name = 'pgen_questionset_questions';
    protected $_primary = array (
            'question_id', 
            'questionset_id' 
    );
    protected $_referenceMap = array (
            'Questions' => array (
                    'columns' => 'question_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Questions', 
                    'refColumns' => 'id' 
            ), 
            'QuestionSet' => array (
                    'columns' => 'questionset_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_QuestionSet', 
                    'refColumns' => 'id' 
            ) 
    );
}
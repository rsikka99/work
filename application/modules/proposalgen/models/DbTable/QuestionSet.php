<?php

class Proposalgen_Model_DbTable_QuestionSet extends Zend_Db_Table_Abstract
{
    protected $_name = 'pgen_question_sets';
    protected $_primary = 'id';
    protected $_dependentTables = array (
            'Reports', 
            'QuestionSetQuestions' 
    );
}
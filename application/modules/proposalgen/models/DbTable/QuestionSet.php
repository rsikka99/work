<?php

class Proposalgen_Model_DbTable_QuestionSet extends Zend_Db_Table_Abstract
{
    protected $_name = 'question_set';
    protected $_primary = 'questionset_id';
    protected $_dependentTables = array (
            'Reports, QuestionSetQuestions' 
    );
    protected $_referenceMap = array (
            'Report' => array (
                    'columns' => array (
                            'questionset_id' 
                    ), 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Reports', 
                    'refColumns' => array (
                            'questionset_id' 
                    ) 
            ) 
    );
}
?>

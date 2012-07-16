<?php

class Proposalgen_Model_DbTable_Report extends Zend_Db_Table_Abstract
{
    protected $_name = 'pgen_reports';
    protected $_primary = 'id';
    protected $_dependentTables = array (
            'TextAnswers', 
            'NumericAnswers', 
            'DateAnswers', 
            'QuestionSet', 
            'UserReports' 
    );
}
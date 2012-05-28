<?php

class Proposalgen_Model_DbTable_Reports extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'reports';
    protected $_primary = 'report_id';
    protected $_dependentTables = array (
            'TextAnswers', 
            'NumericAnswers', 
            'DateAnswers', 
            'QuestionSet', 
            'UserReports' 
    );
}
?>

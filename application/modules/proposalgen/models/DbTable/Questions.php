<?php

class Proposalgen_Model_DbTable_Questions extends Zend_Db_Table_Abstract
{
    protected $_name = 'questions';
    protected $_primary = 'question_id';
    protected $_dependentTables = array (
            'DateAnswers', 
            'TextAnswers', 
            'NumericAnswers', 
            'QuestionSetQuestions' 
    );

    function fetchAllQuestionsWithAnswers ($reportID)
    {
        $query = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->joinLeft('answers_dates', 'questions.question_id = answers_dates.question_id', array (
                'date_answer' 
        ))
            ->joinLeft('answers_numeric', 'questions.question_id = answers_numeric.question_id', array (
                'numeric_answer' 
        ))
            ->joinLeft('answers_textual', 'questions.question_id = answers_textual.question_id', array (
                'textual_answer' 
        ))
            ->where('answers_dates.report_id = ?', $reportID)
            ->orWhere('answers_numeric.report_id = ?', $reportID)
            ->orWhere('answers_textual.report_id = ?', $reportID)
            ->setIntegrityCheck(false);
        $questionsWithAnswers = $this->fetchAll($query);
        return $questionsWithAnswers;
    }
}
?>

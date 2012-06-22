<?php

/**
 * Class Proposalgen_Model_TextualAnswer
 *
 * @author "Lee Robert"
 */
class Proposalgen_Model_TextualAnswer extends Tangent_Model_Abstract
{
    protected $QuestionId;
    protected $ReportId;
    protected $Answer;

    /**
     *
     * @return the $QuestionId
     */
    public function getQuestionId ()
    {
        if (! isset($this->QuestionId))
        {
            
            $this->QuestionId = null;
        }
        return $this->QuestionId;
    }

    /**
     *
     * @param field_type $QuestionId            
     */
    public function setQuestionId ($QuestionId)
    {
        $this->QuestionId = $QuestionId;
        return $this;
    }

    /**
     *
     * @return the $ReportId
     */
    public function getReportId ()
    {
        if (! isset($this->ReportId))
        {
            
            $this->ReportId = null;
        }
        return $this->ReportId;
    }

    /**
     *
     * @param field_type $ReportId            
     */
    public function setReportId ($ReportId)
    {
        $this->ReportId = $ReportId;
        return $this;
    }

    /**
     *
     * @return the $Answer
     */
    public function getAnswer ()
    {
        if (! isset($this->Answer))
        {
            
            $this->Answer = null;
        }
        return $this->Answer;
    }

    /**
     *
     * @param field_type $Answer            
     */
    public function setAnswer ($Answer)
    {
        $this->Answer = $Answer;
        return $this;
    }
}
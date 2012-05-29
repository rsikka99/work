<?php

/**
 * Class Proposalgen_Model_QuestionSetQuestion
 *
 * @author "Lee Robert"
 */
class Proposalgen_Model_QuestionSetQuestion extends Tangent_Model_Abstract
{
    protected $QuestionId;
    protected $QuestionSetId;

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
     * @return the $QuestionSetId
     */
    public function getQuestionSetId ()
    {
        if (! isset($this->QuestionSetId))
        {
            
            $this->QuestionSetId = null;
        }
        return $this->QuestionSetId;
    }

    /**
     *
     * @param field_type $QuestionSetId            
     */
    public function setQuestionSetId ($QuestionSetId)
    {
        $this->QuestionSetId = $QuestionSetId;
        return $this;
    }
}
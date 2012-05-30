<?php

/**
 * Class Proposalgen_Model_QuestionSet
 * 
 * @author "Lee Robert"
 */
class Proposalgen_Model_QuestionSet extends Tangent_Model_Abstract
{
    protected $QuestionId;
    protected $QuestionSetName;

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
     * @return the $QuestionSetName
     */
    public function getQuestionSetName ()
    {
        if (! isset($this->QuestionSetName))
        {
            
            $this->QuestionSetName = null;
        }
        return $this->QuestionSetName;
    }

    /**
     *
     * @param field_type $QuestionSetName            
     */
    public function setQuestionSetName ($QuestionSetName)
    {
        $this->QuestionSetName = $QuestionSetName;
        return $this;
    }
}
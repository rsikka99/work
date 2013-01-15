<?php
class Proposalgen_Model_QuestionSet extends Tangent_Model_Abstract
{
    /**
     * @var int
     */
    public $QuestionId;

    /**
     * @var string
     */
    public $QuestionSetName;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->QuestionId) && !is_null($params->QuestionId))
        {
            $this->QuestionId = $params->QuestionId;
        }

        if (isset($params->QuestionSetName) && !is_null($params->QuestionSetName))
        {
            $this->QuestionSetName = $params->QuestionSetName;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "QuestionId"      => $this->QuestionId,
            "QuestionSetName" => $this->QuestionSetName,
        );
    }
}
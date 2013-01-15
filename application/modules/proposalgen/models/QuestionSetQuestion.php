<?php
class Proposalgen_Model_QuestionSetQuestion extends Tangent_Model_Abstract
{
    /**
     * @var int
     */
    public $questionId;

    /**
     * @var int
     */
    public $questionSetId;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->questionId) && !is_null($params->questionId))
        {
            $this->questionId = $params->questionId;
        }

        if (isset($params->questionSetId) && !is_null($params->questionSetId))
        {
            $this->questionSetId = $params->questionSetId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "questionId"    => $this->questionId,
            "questionSetId" => $this->questionSetId,
        );
    }
}
<?php
class Proposalgen_Model_NumericAnswer extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $questionId;

    /**
     * @var int
     */
    public $reportId;

    /**
     * @var float
     */
    public $answer;

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

        if (isset($params->reportId) && !is_null($params->reportId))
        {
            $this->reportId = $params->reportId;
        }

        if (isset($params->answer) && !is_null($params->answer))
        {
            $this->answer = $params->answer;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "questionId" => $this->questionId,
            "reportId"   => $this->reportId,
            "answer"     => $this->answer,
        );
    }
}
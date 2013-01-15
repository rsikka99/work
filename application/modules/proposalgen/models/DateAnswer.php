<?php
class Proposalgen_Model_DateAnswer extends My_Model_Abstract
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
     * @var int
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

        if (isset($params->QuestionId) && !is_null($params->QuestionId))
        {
            $this->questionId = $params->QuestionId;
        }

        if (isset($params->ReportId) && !is_null($params->ReportId))
        {
            $this->reportId = $params->ReportId;
        }

        if (isset($params->Answer) && !is_null($params->Answer))
        {
            $this->answer = $params->Answer;
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
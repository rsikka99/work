<?php
class Proposalgen_Model_Question extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $questionId;

    /**
     * @var string
     */
    public $questionDescription;

    /**
     * @var string
     */
    public $dateAnswer;

    /**
     * @var float
     */
    public $numericAnswer;

    /**
     * @var string
     */
    public $textualAnswer;

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

        if (isset($params->questionDescription) && !is_null($params->questionDescription))
        {
            $this->questionDescription = $params->questionDescription;
        }

        if (isset($params->dateAnswer) && !is_null($params->dateAnswer))
        {
            $this->dateAnswer = $params->dateAnswer;
        }

        if (isset($params->numericAnswer) && !is_null($params->numericAnswer))
        {
            $this->numericAnswer = $params->numericAnswer;
        }

        if (isset($params->textualAnswer) && !is_null($params->textualAnswer))
        {
            $this->textualAnswer = $params->textualAnswer;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "questionId"          => $this->questionId,
            "questionDescription" => $this->questionDescription,
            "dateAnswer"          => $this->dateAnswer,
            "numericAnswer"       => $this->numericAnswer,
            "textualAnswer"       => $this->textualAnswer,
        );
    }
}
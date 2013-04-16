<?php
class Proposalgen_Model_Assessment_Assessment_Setting extends My_Model_Abstract
{
    /**
     * The report id
     *
     * @var int
     */
    public $assessmentId;

    /**
     * The setting id
     *
     * @var int
     */
    public $assessmentSettingId;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->assessmentId) && !is_null($params->assessmentId))
        {
            $this->assessmentId = $params->assessmentId;
        }

        if (isset($params->assessmentSettingId) && !is_null($params->assessmentSettingId))
        {
            $this->assessmentSettingId = $params->assessmentSettingId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "assessmentId"        => $this->assessmentId,
            "assessmentSettingId" => $this->assessmentSettingId,
        );
    }
}
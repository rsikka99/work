<?php
class Preferences_Model_User_Setting extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $reportSettingId;

    /**
     * @var int
     */
    public $surveySettingId;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }
        if (isset($params->reportSettingId) && !is_null($params->reportSettingId))
        {
            $this->reportSettingId = $params->reportSettingId;
        }
        if (isset($params->surveySettingId) && !is_null($params->surveySettingId))
        {
            $this->surveySettingId = $params->surveySettingId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "userId"          => $this->userId,
            "reportSettingId" => $this->reportSettingId,
            "surveySettingId" => $this->surveySettingId,
        );
    }
}
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
    public $assessmentSettingId;

    /**
     * @var Proposalgen_Model_Assessment_Setting
     */
    protected $_assessmentSetting;

    /**
     * @var int
     */
    public $healthcheckSettingId;

    /**
     * @var Proposalgen_Model_Healthcheck_Setting
     */
    protected $_healthcheckSetting;

    /**
     * @var int
     */
    public $surveySettingId;

    /**
     * @var Proposalgen_Model_Survey_Setting
     */
    protected $_surveySetting;


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
        if (isset($params->assessmentSettingId) && !is_null($params->assessmentSettingId))
        {
            $this->assessmentSettingId = $params->assessmentSettingId;
        }
        if (isset($params->healthcheckSettingId) && !is_null($params->healthcheckSettingId))
        {
            $this->healthcheckSettingId = $params->healthcheckSettingId;
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
            "userId"               => $this->userId,
            "assessmentSettingId"  => $this->assessmentSettingId,
            "healthcheckSettingId" => $this->healthcheckSettingId,
            "surveySettingId"      => $this->surveySettingId,
        );
    }

    /**
     * Gets the assessment settings
     *
     * @return Proposalgen_Model_Assessment_Setting
     */
    public function getAssessmentSettings ()
    {
        if (!isset($this->_assessmentSetting))
        {
            $this->_assessmentSetting = Proposalgen_Model_Mapper_Assessment_Setting::getInstance()->find($this->assessmentSettingId);

            if (!$this->_assessmentSetting instanceof Proposalgen_Model_Assessment_Setting)
            {
                // Insert a new copy of the system setting
                $this->_assessmentSetting = new Proposalgen_Model_Assessment_Setting();
                Proposalgen_Model_Mapper_Assessment_Setting::getInstance()->insert($this->_assessmentSetting);
                $this->assessmentSettingId = $this->_assessmentSetting->id;

                // Save ourselves
                Preferences_Model_Mapper_User_Setting::getInstance()->save($this);
            }
        }

        return $this->_assessmentSetting;
    }

    /**
     * Gets the healthcheck settings
     *
     * @return Proposalgen_Model_Healthcheck_Setting
     */
    public function getHealthcheckSettings ()
    {
        if (!isset($this->_healthcheckSetting))
        {
            $this->_healthcheckSetting = Proposalgen_Model_Mapper_Healthcheck_Setting::getInstance()->find($this->healthcheckSettingId);

            if (!$this->_healthcheckSetting instanceof Proposalgen_Model_Healthcheck_Setting)
            {
                // Insert a new copy of the system setting
                $this->_healthcheckSetting = new Proposalgen_Model_Healthcheck_Setting();
                Proposalgen_Model_Mapper_Healthcheck_Setting::getInstance()->insert($this->_healthcheckSetting);
                $this->healthcheckSettingId = $this->_healthcheckSetting->id;

                // Save ourselves
                Preferences_Model_Mapper_Dealer_Setting::getInstance()->save($this);
            }
        }

        return $this->_healthcheckSetting;
    }

    /**
     * Gets the survey settings
     *
     * @return Proposalgen_Model_Survey_Setting
     */
    public function getSurveySettings ()
    {
        if (!isset($this->_surveySetting))
        {
            $this->_surveySetting = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->find($this->surveySettingId);

            if (!$this->_surveySetting instanceof Proposalgen_Model_Survey_Setting)
            {
                // Insert a new copy of the system setting
                $this->_surveySetting = new Proposalgen_Model_Survey_Setting();
                Proposalgen_Model_Mapper_Survey_Setting::getInstance()->insert($this->_surveySetting);
                $this->surveySettingId = $this->_surveySetting->id;

                // Save ourselves
                Preferences_Model_Mapper_User_Setting::getInstance()->save($this);
            }
        }

        return $this->_surveySetting;
    }
}
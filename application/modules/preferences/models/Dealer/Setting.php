<?php
class Preferences_Model_Dealer_Setting extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $dealerId;

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
        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }
        if (isset($params->assessmentSettingId) && !is_null($params->assessmentSettingId))
        {
            $this->assessmentSettingId = $params->assessmentSettingId;
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
            "dealerId"            => $this->dealerId,
            "assessmentSettingId" => $this->assessmentSettingId,
            "surveySettingId"     => $this->surveySettingId,
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
                $this->_assessmentSetting = Proposalgen_Model_Mapper_Assessment_Setting::getInstance()->fetchSystemAssessmentSetting();
                Proposalgen_Model_Mapper_Assessment_Setting::getInstance()->insert($this->_assessmentSetting);
                $this->assessmentSettingId = $this->_assessmentSetting->id;

                // Save ourselves
                Preferences_Model_Mapper_Dealer_Setting::getInstance()->save($this);
            }
        }

        return $this->_assessmentSetting;
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
                $this->_surveySetting = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchSystemSurveySettings();
                Proposalgen_Model_Mapper_Survey_Setting::getInstance()->insert($this->_surveySetting);
                $this->surveySettingId = $this->_surveySetting->id;

                // Save ourselves
                Preferences_Model_Mapper_Dealer_Setting::getInstance()->save($this);
            }

        }

        return $this->_surveySetting;
    }
}
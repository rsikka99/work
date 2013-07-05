<?php
/**
 * Class Preferences_Model_Dealer_Setting
 */
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
     * @var Assessment_Model_Assessment_Setting
     */
    protected $_assessmentSetting;

    /**
     * @var int
     */
    public $hardwareOptimizationSettingId;

    /**
     * @var Hardwareoptimization_Model_Hardware_Optimization_Setting
     */
    protected $_hardwareOptimizationSetting;

    /**
     * @var int
     */
    public $healthcheckSettingId;

    /**
     * @var Healthcheck_Model_Healthcheck_Setting
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
     * @var int
     */
    public $quoteSettingId;

    /**
     * @var Quotegen_Model_QuoteSetting
     */
    protected $_quoteSetting;

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
        if (isset($params->hardwareOptimizationSettingId) && !is_null($params->hardwareOptimizationSettingId))
        {
            $this->hardwareOptimizationSettingId = $params->hardwareOptimizationSettingId;
        }
        if (isset($params->healthcheckSettingId) && !is_null($params->healthcheckSettingId))
        {
            $this->healthcheckSettingId = $params->healthcheckSettingId;
        }
        if (isset($params->surveySettingId) && !is_null($params->surveySettingId))
        {
            $this->surveySettingId = $params->surveySettingId;
        }
        if (isset($params->quoteSettingId) && !is_null($params->quoteSettingId))
        {
            $this->quoteSettingId = $params->quoteSettingId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "dealerId"                      => $this->dealerId,
            "assessmentSettingId"           => $this->assessmentSettingId,
            "hardwareOptimizationSettingId" => $this->hardwareOptimizationSettingId,
            "healthcheckSettingId"          => $this->healthcheckSettingId,
            "surveySettingId"               => $this->surveySettingId,
            "quoteSettingId"                => $this->quoteSettingId,
        );
    }

    /**
     * Gets the assessment settings
     *
     * @return Assessment_Model_Assessment_Setting
     */
    public function getAssessmentSettings ()
    {
        if (!isset($this->_assessmentSetting))
        {
            $this->_assessmentSetting = Assessment_Model_Mapper_Assessment_Setting::getInstance()->find($this->assessmentSettingId);

            if (!$this->_assessmentSetting instanceof Assessment_Model_Assessment_Setting)
            {
                // Insert a new copy of the system setting
                $this->_assessmentSetting = Assessment_Model_Mapper_Assessment_Setting::getInstance()->fetchSystemAssessmentSetting();
                Assessment_Model_Mapper_Assessment_Setting::getInstance()->insert($this->_assessmentSetting);
                $this->assessmentSettingId = $this->_assessmentSetting->id;

                // Save ourselves
                Preferences_Model_Mapper_Dealer_Setting::getInstance()->save($this);
            }
        }

        return $this->_assessmentSetting;
    }

    /**
     * Gets the healthcheck settings
     *
     * @return Healthcheck_Model_Healthcheck_Setting
     */
    public function getHealthcheckSettings ()
    {
        if (!isset($this->_healthcheckSetting))
        {
            $this->_healthcheckSetting = Healthcheck_Model_Mapper_Healthcheck_Setting::getInstance()->find($this->healthcheckSettingId);

            if (!$this->_healthcheckSetting instanceof Healthcheck_Model_Healthcheck_Setting)
            {
                // Insert a new copy of the system setting
                $this->_healthcheckSetting = Healthcheck_Model_Mapper_Healthcheck_Setting::getInstance()->fetchSystemHealthcheckSetting();
                Healthcheck_Model_Mapper_Healthcheck_Setting::getInstance()->insert($this->_healthcheckSetting);
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
                $this->_surveySetting = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchSystemSurveySettings();
                Proposalgen_Model_Mapper_Survey_Setting::getInstance()->insert($this->_surveySetting);
                $this->surveySettingId = $this->_surveySetting->id;

                // Save ourselves
                Preferences_Model_Mapper_Dealer_Setting::getInstance()->save($this);
            }
        }

        return $this->_surveySetting;
    }

    /**
     * Gets the hardware optimization settings
     *
     * @return Hardwareoptimization_Model_Hardware_Optimization_Setting
     */
    public function getHardwareOptimizationSettings ()
    {
        if (!isset($this->_hardwareOptimizationSetting))
        {
            $this->_hardwareOptimizationSetting = Hardwareoptimization_Model_Mapper_Hardware_Optimization_Setting::getInstance()->find($this->hardwareOptimizationSettingId);

            if (!$this->_hardwareOptimizationSetting instanceof Hardwareoptimization_Model_Hardware_Optimization_Setting)
            {
                // Insert a new copy of the system setting
                $this->_hardwareOptimizationSetting = Hardwareoptimization_Model_Mapper_Hardware_Optimization_Setting::getInstance()->fetchSystemSetting();
                Hardwareoptimization_Model_Mapper_Hardware_Optimization_Setting::getInstance()->insert($this->_hardwareOptimizationSetting);
                $this->hardwareOptimizationSettingId = $this->_hardwareOptimizationSetting->id;

                // Save ourselves
                Preferences_Model_Mapper_Dealer_Setting::getInstance()->save($this);
            }
        }

        return $this->_hardwareOptimizationSetting;
    }

    /**
     * Gets the Quote settings
     *
     * @return Quotegen_Model_QuoteSetting
     */
    public function getQuoteSettings ()
    {
        if (!isset($this->_quoteSetting))
        {
            $this->_quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->find($this->quoteSettingId);

            if (!$this->_quoteSetting instanceof Quotegen_Model_QuoteSetting)
            {
                // Insert a new copy of the system setting
                $this->_quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
                Quotegen_Model_Mapper_QuoteSetting::getInstance()->insert($this->_quoteSetting);
                $this->quoteSettingId = $this->_quoteSetting->id;

                // Save ourselves
                Preferences_Model_Mapper_Dealer_Setting::getInstance()->save($this);
            }
        }

        return $this->_quoteSetting;
    }
}
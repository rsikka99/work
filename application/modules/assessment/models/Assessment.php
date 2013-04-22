<?php
class Assessment_Model_Assessment extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $clientId;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var int
     */
    public $rmsUploadId;

    /**
     * @var int
     */
    public $userPricingOverride;

    /**
     * @var string
     */
    public $stepName;

    /**
     * @var string
     */
    public $dateCreated;

    /**
     * @var string
     */
    public $lastModified;

    /**
     * @var string
     */
    public $reportDate;

    /**
     * @var bool
     */
    public $devicesModified;

    /**
     * @var int
     */
    public $assessmentSettingId;

    // Non database fields
    /**
     * The report settings for this proposal
     *
     * @var Assessment_Model_Assessment_Setting
     */
    protected $_assessmentSettings;

    /**
     * @var Quotegen_Model_Client
     */
    protected $_client;

    /**
     * @var Assessment_Model_Assessment_Survey
     */
    protected $_survey;

    /**
     * @var Proposalgen_Model_Rms_Upload
     */
    protected $_rmsUpload;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }

        if (isset($params->clientId) && !is_null($params->clientId))
        {
            $this->clientId = $params->clientId;
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->rmsUploadId) && !is_null($params->rmsUploadId))
        {
            $this->rmsUploadId = $params->rmsUploadId;
        }

        if (isset($params->userPricingOverride) && !is_null($params->userPricingOverride))
        {
            $this->userPricingOverride = $params->userPricingOverride;
        }

        if (isset($params->stepName) && !is_null($params->stepName))
        {
            $this->stepName = $params->stepName;
        }

        if (isset($params->dateCreated) && !is_null($params->dateCreated))
        {
            $this->dateCreated = $params->dateCreated;
        }

        if (isset($params->lastModified) && !is_null($params->lastModified))
        {
            $this->lastModified = $params->lastModified;
        }

        if (isset($params->reportDate) && !is_null($params->reportDate))
        {
            $this->reportDate = $params->reportDate;
        }

        if (isset($params->devicesModified) && !is_null($params->devicesModified))
        {
            $this->devicesModified = $params->devicesModified;
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
            "id"                  => $this->id,
            "clientId"            => $this->clientId,
            "dealerId"            => $this->dealerId,
            "rmsUploadId"         => $this->rmsUploadId,
            "userPricingOverride" => $this->userPricingOverride,
            "stepName"            => $this->stepName,
            "dateCreated"         => $this->dateCreated,
            "lastModified"        => $this->lastModified,
            "reportDate"          => $this->reportDate,
            "devicesModified"     => $this->devicesModified,
            "assessmentSettingId" => $this->assessmentSettingId,
        );
    }

    /**
     * Gets the report settings for the report
     *
     * @return Assessment_Model_Assessment_Setting
     */
    public function getAssessmentSettings ()
    {
        if (!isset($this->_assessmentSettings))
        {
            $this->_assessmentSettings = Assessment_Model_Mapper_Assessment_Setting::getInstance()->fetchAssessmentSetting($this->id);
        }

        return $this->_assessmentSettings;
    }

    /**
     * Sets the report settings for the report
     *
     * @param Assessment_Model_Assessment_Setting $_reportSettings
     *
     * @return \Assessment_Model_Assessment
     */
    public function setAssessmentSettings ($_reportSettings)
    {
        $this->_assessmentSettings = $_reportSettings;

        return $this;
    }

    /**
     * Gets the client
     *
     * @return Quotegen_Model_Client
     */
    public function getClient ()
    {
        if (!isset($this->_client))
        {
            $this->_client = Quotegen_Model_Mapper_Client::getInstance()->find($this->clientId);
        }

        return $this->_client;
    }

    /**
     * Sets the client
     *
     * @param Quotegen_Model_Client $client
     *
     * @return Assessment_Model_Assessment
     */
    public function setClient ($client)
    {
        $this->_client = $client;

        return $this;
    }

    /**
     * Gets the survey
     *
     * @return Assessment_Model_Assessment_Survey
     */
    public function getSurvey ()
    {
        if (!isset($this->_survey))
        {
            $this->_survey = Assessment_Model_Mapper_Assessment_Survey::getInstance()->find($this->id);
        }

        return $this->_survey;
    }

    /**
     * Sets the survey
     *
     * @param Assessment_Model_Assessment_Survey $survey
     *
     * @return Assessment_Model_Assessment
     */
    public function setSurvey ($survey)
    {
        $this->_survey = $survey;

        return $this;
    }

    /**
     * Gets the rms upload
     *
     * @return Proposalgen_Model_Rms_Upload
     */
    public function getRmsUpload ()
    {
        if (!isset($this->_rmsUpload))
        {
            $this->_rmsUpload = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->find($this->rmsUploadId);
        }

        return $this->_rmsUpload;
    }

    /**
     * Sets the rms upload
     *
     * @param Proposalgen_Model_Rms_Upload $rmsUpload
     *
     * @return Assessment_Model_Assessment
     */
    public function setRmsUpload ($rmsUpload)
    {
        $this->_rmsUpload = $rmsUpload;

        return $this;
    }
}
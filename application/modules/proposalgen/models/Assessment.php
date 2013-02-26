<?php
class Proposalgen_Model_Assessment extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $clientId;


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

    // Non database fields
    /**
     * The report settings for this proposal
     *
     * @var Proposalgen_Model_Report_Setting
     */
    protected $_reportSettings;

    /**
     * The report steps for this proposal
     *
     * @var Proposalgen_Model_Assessment_Step
     */
    protected $_reportSteps;

    /**
     * @var Application_Model_User
     */
    protected $_user;

    /**
     * @var Quotegen_Model_Client
     */
    protected $_client;

    /**
     * @var Proposalgen_Model_Assessment_Survey
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

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->clientId) && !is_null($params->clientId))
        {
            $this->clientId = $params->clientId;
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

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                  => $this->id,
            "userId"              => $this->userId,
            "clientId"            => $this->clientId,
            "rmsUploadId"         => $this->rmsUploadId,
            "userPricingOverride" => $this->userPricingOverride,
            "stepName"            => $this->stepName,
            "dateCreated"         => $this->dateCreated,
            "lastModified"        => $this->lastModified,
            "reportDate"          => $this->reportDate,
            "devicesModified"     => $this->devicesModified,
        );
    }

    /**
     * Gets the report settings for the report
     *
     * @return Proposalgen_Model_Report_Setting
     */
    public function getReportSettings ()
    {
        if (!isset($this->_reportSettings))
        {
            $this->_reportSettings = Proposalgen_Model_Mapper_Report_Setting::getInstance()->fetchReportReportSetting($this->id);
        }

        return $this->_reportSettings;
    }

    /**
     * Sets the report settings for the report
     *
     * @param Proposalgen_Model_Report_Setting $_reportSettings
     *
     * @return \Proposalgen_Model_Assessment
     */
    public function setReportSettings ($_reportSettings)
    {
        $this->_reportSettings = $_reportSettings;

        return $this;
    }

    /**
     * Gets the report steps for this report
     *
     * @return Proposalgen_Model_Assessment_Step
     */
    public function getReportSteps ()
    {
        if (!isset($this->_reportSteps))
        {
            $stage = ($this->stepName) ? : Proposalgen_Model_Assessment_Step::STEP_SURVEY;

            $this->_reportSteps = Proposalgen_Model_Assessment_Step::getSteps();
            Proposalgen_Model_Assessment_Step::updateAccessibleSteps($this->_reportSteps, $stage);
        }

        return $this->_reportSteps;
    }

    /**
     * Sets the report steps for this report
     *
     * @param Proposalgen_Model_Assessment_Step $ReportSteps
     *
     * @return \Proposalgen_Model_Assessment
     */
    public function setReportSteps ($ReportSteps)
    {
        $this->_reportSteps = $ReportSteps;

        return $this;
    }

    /**
     * Getter for _user
     *
     * @return \Application_Model_User
     */
    public function getUser ()
    {
        if (!isset($this->_user) && $this->userId > 0)
        {
            $this->_user = Application_Model_Mapper_User::getInstance()->find($this->userId);
        }

        return $this->_user;
    }

    /**
     * Setter for _user
     *
     * @param \Application_Model_User $user
     */
    public function setUser ($user)
    {
        $this->_user = $user;
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
     * @return Proposalgen_Model_Assessment
     */
    public function setClient ($client)
    {
        $this->_client = $client;

        return $this;
    }

    /**
     * Gets the survey
     *
     * @return Proposalgen_Model_Assessment_Survey
     */
    public function getSurvey ()
    {
        if (!isset($this->_survey))
        {
            $this->_survey = Proposalgen_Model_Mapper_Assessment_Survey::getInstance()->find($this->id);
        }

        return $this->_survey;
    }

    /**
     * Sets the survey
     *
     * @param Proposalgen_Model_Assessment_Survey $survey
     *
     * @return Proposalgen_Model_Assessment
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
     * @return Proposalgen_Model_Assessment
     */
    public function setRmsUpload ($rmsUpload)
    {
        $this->_rmsUpload = $rmsUpload;

        return $this;
    }
}
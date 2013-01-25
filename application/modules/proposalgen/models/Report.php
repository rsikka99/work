<?php
class Proposalgen_Model_Report extends My_Model_Abstract
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
     * @var string
     */
    public $customerCompanyName;

    /**
     * @var int
     */
    public $userPricingOverride;

    /**
     * @var string
     */
    public $reportStage;

    /**
     * @var int
     */
    public $questionSetId;

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
     * @var Proposalgen_Model_Report_Step
     */
    protected $_reportSteps;

    /**
     * @var Application_Model_User
     */
    protected $_user;

    /**
     * @var Proposalgen_Model_DeviceInstance[]
     */

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

        if (isset($params->customerCompanyName) && !is_null($params->customerCompanyName))
        {
            $this->customerCompanyName = $params->customerCompanyName;
        }

        if (isset($params->userPricingOverride) && !is_null($params->userPricingOverride))
        {
            $this->userPricingOverride = $params->userPricingOverride;
        }

        if (isset($params->reportStage) && !is_null($params->reportStage))
        {
            $this->reportStage = $params->reportStage;
        }

        if (isset($params->questionSetId) && !is_null($params->questionSetId))
        {
            $this->questionSetId = $params->questionSetId;
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
            "customerCompanyName" => $this->customerCompanyName,
            "userPricingOverride" => $this->userPricingOverride,
            "reportStage"         => $this->reportStage,
            "questionSetId"       => $this->questionSetId,
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
     * @return \Proposalgen_Model_Report
     */
    public function setReportSettings ($_reportSettings)
    {
        $this->_reportSettings = $_reportSettings;

        return $this;
    }

    /**
     * Gets the report steps for this report
     *
     * @return Proposalgen_Model_Report_Step
     */
    public function getReportSteps ()
    {
        if (!isset($this->_reportSteps))
        {
            $stage = ($this->reportStage) ? : Proposalgen_Model_Report_Step::STEP_SURVEY_COMPANY;

            $this->_reportSteps = Proposalgen_Model_Report_Step::getSteps();
            Proposalgen_Model_Report_Step::updateAccessibleSteps($this->_reportSteps, $stage);
        }

        return $this->_reportSteps;
    }

    /**
     * Sets the report steps for this report
     *
     * @param Proposalgen_Model_Report_Step $ReportSteps
     *
     * @return \Proposalgen_Model_Report
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
}
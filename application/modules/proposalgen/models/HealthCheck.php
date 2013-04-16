<?php
class Proposalgen_Model_Healthcheck extends My_Model_Abstract
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
     * @var int
     */
    public $healthcheckSettingId;


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
     * @var Proposalgen_Model_Healthcheck_Step
     */
    protected $_reportSteps;

    /**
     * @var Quotegen_Model_Client
     */
    protected $_client;

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

        if (isset($params->healthcheckSettingId) && !is_null($params->healthcheckSettingId))
        {
            $this->healthcheckSettingId = $params->healthcheckSettingId;
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

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"           => $this->id,
            "clientId"     => $this->clientId,
            "dealerId"     => $this->dealerId,
            "healthcheckSettingId"    => $this->healthcheckSettingId,
            "rmsUploadId"  => $this->rmsUploadId,
            "stepName"     => $this->stepName,
            "dateCreated"  => $this->dateCreated,
            "lastModified" => $this->lastModified,
            "reportDate"   => $this->reportDate,
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
            $this->_reportSettings = Proposalgen_Model_Mapper_Healthcheck_Setting::getInstance()->fetchSetting($this->id);
        }

        return $this->_reportSettings;
    }

    /**
     * Sets the report settings for the report
     *
     * @param Proposalgen_Model_Report_Setting $_reportSettings
     *
     * @return Proposalgen_Model_Healthcheck
     */
    public function setReportSettings ($_reportSettings)
    {
        $this->_reportSettings = $_reportSettings;

        return $this;
    }

    /**
     * Gets the report steps for this report
     *
     * @return Proposalgen_Model_Healthcheck_Step
     */
    public function getReportSteps ()
    {
        if (!isset($this->_reportSteps))
        {
            $stage = ($this->stepName) ? : Proposalgen_Model_Healthcheck_Step::STEP_FLEETDATA_UPLOAD;

            $this->_reportSteps = Proposalgen_Model_Healthcheck_Step::getSteps();
            Proposalgen_Model_Healthcheck_Step::updateAccessibleSteps($this->_reportSteps, $stage);
        }

        return $this->_reportSteps;
    }

    /**
     * Sets the report steps for this report
     *
     * @param Proposalgen_Model_Healthcheck_Step $ReportSteps
     *
     * @return Proposalgen_Model_Healthcheck
     */
    public function setReportSteps ($ReportSteps)
    {
        $this->_reportSteps = $ReportSteps;

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
     * @return Proposalgen_Model_Healthcheck
     */
    public function setClient ($client)
    {
        $this->_client = $client;

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
     * @return Proposalgen_Model_Healthcheck
     */
    public function setRmsUpload ($rmsUpload)
    {
        $this->_rmsUpload = $rmsUpload;

        return $this;
    }
}
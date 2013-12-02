<?php
/**
 * Class Healthcheck_Model_Healthcheck
 */
class Healthcheck_Model_Healthcheck extends My_Model_Abstract
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

    /**
     * @var bool
     */
    public $devicesModified;

    /**
     * @var  string
     */
    public $name;

    // Non database fields
    /**
     * @var Healthcheck_Model_Healthcheck_Setting
     */
    protected $_healthcheckSettings;

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

        if (isset($params->devicesModified) && !is_null($params->devicesModified))
        {
            $this->devicesModified = $params->devicesModified;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                   => $this->id,
            "clientId"             => $this->clientId,
            "dealerId"             => $this->dealerId,
            "healthcheckSettingId" => $this->healthcheckSettingId,
            "rmsUploadId"          => $this->rmsUploadId,
            "stepName"             => $this->stepName,
            "dateCreated"          => $this->dateCreated,
            "lastModified"         => $this->lastModified,
            "reportDate"           => $this->reportDate,
            "devicesModified"      => $this->devicesModified,
            "name"                 => $this->name,
        );
    }

    /**
     * Gets the report settings for the report
     *
     * @return Healthcheck_Model_Healthcheck_Setting
     */
    public function getHealthcheckSettings ()
    {
        if (!isset($this->_healthcheckSettings))
        {
            $this->_healthcheckSettings = Healthcheck_Model_Mapper_Healthcheck_Setting::getInstance()->fetchSetting($this->id);
        }

        return $this->_healthcheckSettings;
    }

    /**
     * Sets the report settings for the report
     *
     * @param Healthcheck_Model_Healthcheck_Setting $_healthcheckSettings
     *
     * @return Healthcheck_Model_Healthcheck
     */
    public function setHealthcheckSettings ($_healthcheckSettings)
    {
        $this->_healthcheckSettings = $_healthcheckSettings;

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
     * @return Healthcheck_Model_Healthcheck
     */
    public function setClient ($client)
    {
        $this->_client = $client;

        return $this;
    }


    /**
     * Gets the RMS upload
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
     * Sets the RMS upload
     *
     * @param Proposalgen_Model_Rms_Upload $rmsUpload
     *
     * @return Healthcheck_Model_Healthcheck
     */
    public function setRmsUpload ($rmsUpload)
    {
        $this->_rmsUpload = $rmsUpload;

        return $this;
    }
}